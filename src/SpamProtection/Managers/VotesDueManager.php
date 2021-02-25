<?php


    namespace SpamProtection\Managers;


    use Exception;
    use msqg\QueryBuilder;
    use SpamProtection\Abstracts\PredictionVotesStatus;
    use SpamProtection\Abstracts\SearchMethods\PredictionVoteSearchMethod;
    use SpamProtection\Abstracts\VerdictResult;
    use SpamProtection\Abstracts\VotesDueRecordStatus;
    use SpamProtection\Exceptions\DatabaseException;
    use SpamProtection\Exceptions\InvalidSearchMethodException;
    use SpamProtection\Exceptions\NoPoolCurrentlyActiveExceptions;
    use SpamProtection\Exceptions\PredictionVotesNotFoundException;
    use SpamProtection\Exceptions\VotingPoolCurrentlyActiveException;
    use SpamProtection\Objects\VotesDueRecord;
    use SpamProtection\Objects\VotingPoolResults;
    use SpamProtection\SpamProtection;
    use TelegramClientManager\Abstracts\SearchMethods\TelegramClientSearchMethod;
    use TelegramClientManager\TelegramClientManager;
    use ZiProto\ZiProto;

    /**
     * Class VotesDueManager
     * @package SpamProtection\Managers
     */
    class VotesDueManager
    {

        /**
         * @var SpamProtection
         */
        private $spamProtection;

        /**
         * VotesDueManager constructor.
         * @param SpamProtection $spamProtection
         */
        public function __construct(SpamProtection $spamProtection)
        {
            $this->spamProtection = $spamProtection;
        }

        /**
         * Create a voting pool for the next 24 hours
         *
         * @return VotesDueRecord
         * @throws DatabaseException
         * @throws NoPoolCurrentlyActiveExceptions
         * @throws VotingPoolCurrentlyActiveException
         */
        public function createPool(): VotesDueRecord
        {
            try
            {
                $this->getCurrentPool();
            }
            catch(NoPoolCurrentlyActiveExceptions $e)
            {
                throw new VotingPoolCurrentlyActiveException("There's a voting pool that's still active at this time");
            }

            $Query = QueryBuilder::insert_into("votes_due", [
                "records" => $this->spamProtection->getDatabase()->real_escape_string(ZiProto::encode([])),
                "status" => VotesDueRecordStatus::CollectingData,
                "due_timestamp" => (int)time() + 86400, // Closes in 24 hours
                "created_timestamp" => (int)time(),
                "last_updated_timestamp" => (int)time()
            ]);

            $QueryResults = $this->spamProtection->getDatabase()->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->spamProtection->getDatabase()->error);
            }

            return $this->getCurrentPool(true);

        }

        /**
         * Gets the currently active pool
         *
         * @param bool $throw_exception
         * @return VotesDueRecord
         * @throws DatabaseException
         * @throws NoPoolCurrentlyActiveExceptions
         * @throws VotingPoolCurrentlyActiveException
         */
        public function getCurrentPool(bool $throw_exception=false): VotesDueRecord
        {
            $Query = QueryBuilder::select("votes_due", [
                "id",
                "records",
                "status",
                "due_timestamp",
                "created_timestamp",
                "last_updated_timestamp"
            ], "status", VotesDueRecordStatus::CollectingData);

            $QueryResults = $this->spamProtection->getDatabase()->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->spamProtection->getDatabase()->error);
            }
            else
            {
                if($QueryResults->num_rows !== 1)
                {
                    if($throw_exception)
                    {
                        throw new NoPoolCurrentlyActiveExceptions("No voting pool is currently active");
                    }
                    else
                    {
                        return $this->createPool();
                    }
                }

                $Row = $QueryResults->fetch_array(MYSQLI_ASSOC);
                $Row["records"] = ZiProto::decode($Row["records"]);
                return VotesDueRecord::fromArray($Row);
            }
        }

        /**
         * Updates an existing pool record
         *
         * @param VotesDueRecord $votesDueRecord
         * @return bool
         * @throws DatabaseException
         */
        public function updatePool(VotesDueRecord $votesDueRecord): bool
        {
            $Query = QueryBuilder::update("votes_due", [
                "records" => $this->spamProtection->getDatabase()->real_escape_string(ZiProto::encode($votesDueRecord->Records->toArray())),
                "status" => (int)$votesDueRecord->Status,
                "last_updated_timestamp" => (int)time()
            ]);
            $QueryResults = $this->spamProtection->getDatabase()->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->spamProtection->getDatabase()->error);
            }

            return true;
        }

        /**
         * Finalizes the results of the voting pool
         *
         * @param VotesDueRecord $votesDueRecord
         * @param TelegramClientManager $telegramClientManager
         * @param bool $createNewPool
         * @return VotingPoolResults|null
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws NoPoolCurrentlyActiveExceptions
         * @throws VotingPoolCurrentlyActiveException
         */
        public function finalizeResults(VotesDueRecord $votesDueRecord, TelegramClientManager $telegramClientManager, bool $createNewPool=true)
        {

            if(count($votesDueRecord->Records->Records) == 0)
            {
                $votesDueRecord->Status = VotesDueRecordStatus::NotEnoughData;
                $this->updatePool($votesDueRecord);

                if($createNewPool)
                    $this->createPool();

                return null;
            }

            $VotingPoolResults = new VotingPoolResults();
            $votesDueRecord->Status = VotesDueRecordStatus::BuildingReport;
            $this->updatePool($votesDueRecord);

            if($createNewPool)
                $this->createPool();

            foreach($votesDueRecord->Records->Records as $record)
            {
                try
                {
                    $votesRecord = $this->spamProtection->getPredictionVotesManager()->getPredictionVote(PredictionVoteSearchMethod::ById, $record);
                    $votesRecord->Status = PredictionVotesStatus::Closed;

                    $this->spamProtection->getPredictionVotesManager()->updatePredictionVote($votesRecord);

                    if($votesRecord->isSpam())
                    {
                        $VotingPoolResults->SpamDatasetPath = $this->appendToDataset($votesDueRecord->ID, $votesRecord->Content, "spam");
                        $VotingPoolResults->SpamCount += 1;
                    }
                    else
                    {
                        $VotingPoolResults->HamDatasetPath = $this->appendToDataset($votesDueRecord->ID, $votesRecord->Content, "ham");
                        $VotingPoolResults->HamCount += 1;
                    }


                    $FinalVerdict = $votesRecord->getFinalVerdict();
                    if($FinalVerdict == VerdictResult::Yay || $FinalVerdict == VerdictResult::Nay)
                    {
                        foreach($votesRecord->getPunishableVoters() as $rewardedVoter)
                        {
                            try
                            {
                                $RewardedVoter = $telegramClientManager->getTelegramClientManager()->getClient(
                                    TelegramClientSearchMethod::byId, $rewardedVoter
                                );
                                $UserStatus = SettingsManager::getUserStatus($RewardedVoter);
                                $UserStatus->ReputationPoints -= 1;
                                $RewardedVoter = SettingsManager::updateUserStatus($RewardedVoter, $UserStatus);
                                $telegramClientManager->getTelegramClientManager()->updateClient($RewardedVoter);
                            }
                            catch(Exception $e)
                            {
                                var_dump($e);
                            }
                        }

                        foreach($votesRecord->getRewardedVoters() as $rewardedVoter)
                        {
                            try
                            {
                                $RewardedVoter = $telegramClientManager->getTelegramClientManager()->getClient(
                                    TelegramClientSearchMethod::byId, $rewardedVoter
                                );
                                $UserStatus = SettingsManager::getUserStatus($RewardedVoter);
                                $UserStatus->ReputationPoints += 1;
                                $RewardedVoter = SettingsManager::updateUserStatus($RewardedVoter, $UserStatus);
                                $telegramClientManager->getTelegramClientManager()->updateClient($RewardedVoter);
                            }
                            catch(Exception $e)
                            {
                                var_dump($e);
                            }
                        }
                    }

                    // Update the final verdict!
                    switch($FinalVerdict)
                    {
                        case VerdictResult::Yay:
                            $VotingPoolResults->UserYayVotes += 1;
                            break;

                        case VerdictResult::Nay:
                            $VotingPoolResults->UserNayVotes += 1;
                            break;

                        case VerdictResult::CpuYay:
                            $VotingPoolResults->CpuYayVotes += 1;
                            break;

                        case VerdictResult::CpuNay:
                            $VotingPoolResults->CpuNayVotes += 1;

                            break;
                    }

                    $VotingPoolResults->VotingRecordsCount += 1;
                }
                catch (PredictionVotesNotFoundException $e)
                {
                    $VotingPoolResults->VotingRecordsFailureCount += 1;
                    // Skip this!
                    unset($e);
                }
            }

            $votesDueRecord->Status = VotesDueRecordStatus::Completed;
            $this->updatePool($votesDueRecord);

            return $VotingPoolResults;
        }

        /**
         * Appends the record to the dataset
         *
         * @param int $pool_id
         * @param string $content
         * @param string $category
         * @return string
         */
        public function appendToDataset(int $pool_id, string $content, string $category): string
        {
            $target_file = DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . $pool_id . "_" . $category . ".dat";
            file_put_contents($target_file, $this->normalizeContent($content) . "\n", FILE_APPEND | LOCK_EX);
            return $target_file;
        }

        /**
         * Noramlizes the content text into something that's readable
         *
         * @param string $content
         * @return string
         */
        private function normalizeContent(string $content): string
        {
            return str_ireplace("\n", "\\n", $content);
        }

    }