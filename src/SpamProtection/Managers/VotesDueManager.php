<?php


    namespace SpamProtection\Managers;


    use msqg\QueryBuilder;
    use SpamProtection\Abstracts\VotesDueRecordStatus;
    use SpamProtection\Exceptions\DatabaseException;
    use SpamProtection\Exceptions\NoPoolCurrentlyActiveExceptions;
    use SpamProtection\Exceptions\VotingPoolCurrentlyActiveException;
    use SpamProtection\Objects\VotesDueRecord;
    use SpamProtection\SpamProtection;
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
    }