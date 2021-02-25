<?php


    namespace SpamProtection\Managers;

    use msqg\QueryBuilder;
    use SpamProtection\Abstracts\PredictionVotesStatus;
    use SpamProtection\Abstracts\SearchMethods\PredictionVoteSearchMethod;
    use SpamProtection\Exceptions\DatabaseException;
    use SpamProtection\Exceptions\InvalidSearchMethodException;
    use SpamProtection\Exceptions\MessageTextCannotBeEmptyException;
    use SpamProtection\Exceptions\PredictionVotesNotFoundException;
    use SpamProtection\Objects\MessageLog;
    use SpamProtection\Objects\PredictionVotes;
    use SpamProtection\Objects\TelegramObjects\Message;
    use SpamProtection\Objects\VotesDueRecord;
    use SpamProtection\SpamProtection;
    use ZiProto\ZiProto;

    /**
     * Class PredictionVotesManager
     * @package SpamProtection\Managers
     */
    class PredictionVotesManager
    {
        /**
         * @var SpamProtection
         */
        private $spamProtection;

        /**
         * PredictionVotesManager constructor.
         * @param SpamProtection $spamProtection
         */
        public function __construct(SpamProtection $spamProtection)
        {
            $this->spamProtection = $spamProtection;
        }

        /**
         * Creates a new vote record in the database
         *
         * @param MessageLog $messageLog
         * @param Message $message
         * @param VotesDueRecord $votesDueRecord
         * @return PredictionVotes
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws MessageTextCannotBeEmptyException
         * @throws PredictionVotesNotFoundException
         */
        public function createNewVote(MessageLog $messageLog, Message $message, VotesDueRecord $votesDueRecord): PredictionVotes
        {
            if($message->Text == null)
            {
                throw new MessageTextCannotBeEmptyException();
            }

            $Query = QueryBuilder::insert_into("prediction_votes", [
                "message_hash" => $this->spamProtection->getDatabase()->real_escape_string($messageLog->MessageHash),
                "content" => $this->spamProtection->getDatabase()->real_escape_string(urlencode($message->Text)),
                "prediction_ham" => (float)$messageLog->HamPrediction,
                "prediction_spam" => (float)$messageLog->SpamPrediction,
                "votes" => $this->spamProtection->getDatabase()->real_escape_string(ZiProto::encode([])),
                "status" => (int)PredictionVotesStatus::Opened,
                "created_timestamp" => (int)time(),
                "last_updated_timestamp" => (int)time(),
                "due_timestamp" => $votesDueRecord->DueTimestamp
            ]);

            $QueryResults = $this->spamProtection->getDatabase()->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->spamProtection->getDatabase()->error);
            }

            return $this->getPredictionVote(PredictionVoteSearchMethod::ByMessageHash, $messageLog->MessageHash);
        }

        /**
         * Gets an existing prediction vote record from the database
         *
         * @param string $search_method
         * @param string $value
         * @return PredictionVotes
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws PredictionVotesNotFoundException
         */
        public function getPredictionVote(string $search_method, string $value): PredictionVotes
        {
            switch($search_method)
            {
                case PredictionVoteSearchMethod::ById:
                    $search_method = $this->spamProtection->getDatabase()->real_escape_string($search_method);
                    $value = (int)$value;
                    break;

                case PredictionVoteSearchMethod::ByMessageHash:
                    $search_method = $this->spamProtection->getDatabase()->real_escape_string($search_method);
                    $value = $this->spamProtection->getDatabase()->real_escape_string($value);
                    break;

                default:
                    throw new InvalidSearchMethodException("The search method '$search_method' is not valid for this method");
            }

            $Query = QueryBuilder::select("prediction_votes", [
                "id",
                "message_hash",
                "content",
                "prediction_spam",
                "prediction_ham",
                "votes",
                "status",
                "created_timestamp",
                "last_updated_timestamp",
                "due_timestamp"
            ], $search_method, $value);
            $QueryResults = $this->spamProtection->getDatabase()->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->spamProtection->getDatabase()->error);
            }
            else
            {
                if($QueryResults->num_rows !== 1)
                {
                    throw new PredictionVotesNotFoundException();
                }

                $Row = $QueryResults->fetch_array(MYSQLI_ASSOC);
                $Row["votes"] = ZiProto::decode($Row["votes"]);
                $Row["content"] = urldecode($Row["content"]);

                return PredictionVotes::fromArray($Row);
            }
        }

        /**
         * Updates a prediction vote record
         *
         * @param PredictionVotes $predictionVotes
         * @return bool
         * @throws DatabaseException
         */
        public function updatePredictionVote(PredictionVotes $predictionVotes): bool
        {
            $Query = QueryBuilder::update("prediction_votes", [
                "votes" => $this->spamProtection->getDatabase()->real_escape_string(ZiProto::encode($predictionVotes->toArray())),
                "status" => (int)$predictionVotes->Status,
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