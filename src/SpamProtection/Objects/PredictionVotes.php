<?php


    namespace SpamProtection\Objects;

    use SpamProtection\Abstracts\PredictionVotesStatus;
    use SpamProtection\Abstracts\VerdictResult;
    use SpamProtection\Abstracts\VoteVerdict;
    use SpamProtection\Objects\PredictionVotes\Votes;

    /**
     * Class PredictionVotes
     * @package SpamProtection\Objects
     */
    class PredictionVotes
    {
        /**
         * The Unique Internal Database for this ID
         *
         * @var int
         */
        public $ID;

        /**
         * The Message Hash (in relation to the Message Log)
         *
         * @var string
         */
        public $MessageHash;

        /**
         * The content of the message
         *
         * @var string
         */
        public $Content;

        /**
         * The spam prediction value of the contents
         *
         * @var float
         */
        public $PredictionSpam;

        /**
         * The ham prediction value of the contents
         *
         * @var float
         */
        public $PredictionHam;

        /**
         * The votes data for this prediction
         *
         * @var Votes
         */
        public $Votes;

        /**
         * @var int|PredictionVotesStatus
         */
        public $Status;

        /**
         * The Unix Timestamp for when this record was created
         *
         * @var int
         */
        public $CreatedTimestamp;

        /**
         * The Unix Timestamp for when this record was last updated
         *
         * @var int
         */
        public $LastUpdatedTimestamp;

        /**
         * The Unix Timestamp for when this record is due to be closed
         *
         * @var int
         */
        public $DueTimestamp;

        /**
         * Returns True if the content is spam, false if it's ham
         * If no votes were casted, the output will be based off machine prediction
         *
         * @return int|VerdictResult
         */
        public function getFinalVerdict(): int
        {
            $IsSpam = $this->PredictionSpam > $this->PredictionHam;

            if(count($this->Votes->Votes) > 0)
            {
                $YayCount = 0;
                $NayCount = 0;

                foreach($this->Votes->Votes as $voter_id => $verdict)
                {
                    switch($verdict)
                    {
                        case VoteVerdict::Nay: $NayCount +=1; break;
                        case VoteVerdict::Yay: $YayCount +=1; break;
                    }
                }

                if($IsSpam)
                {
                    // If the bot thinks the content is spam

                    if($YayCount > $NayCount) // And people agree with it
                    {
                        return VerdictResult::Yay; // It's indeed spam!
                    }

                    return VerdictResult::Nay; // People think it's not spam
                }
                else
                {
                    // If the bot thinks the content is not spam

                    if($YayCount > $NayCount) // And people agree with it
                    {
                        return VerdictResult::Yay; // It's indeed not spam!
                    }

                    return VerdictResult::Nay; // People thinks the content is indeed spam
                }
            }

            if($IsSpam)
                return VerdictResult::CpuYay;
            return VerdictResult::CpuNay;
        }

        /**
         * Returns the list of Voter IDs that voted wrong for a punishable reputation
         *
         * @return array
         */
        public function getPunishableVoters(): array
        {
            $Results = [];

            if($this->getFinalVerdict() == VerdictResult::Yay)
            {
                foreach($this->Votes->Votes as $voter_id => $verdict)
                {
                    if($verdict == VoteVerdict::Nay)
                        $Results[] = $voter_id;
                }

                return $Results;
            }
            elseif($this->getFinalVerdict() == VerdictResult::Nay)
            {
                foreach($this->Votes->Votes as $voter_id => $verdict)
                {
                    if($verdict == VoteVerdict::Yay)
                        $Results[] = $voter_id;
                }

                return $Results;
            }
            else
            {
                return $Results;
            }
        }


        /**
         * Returns the list of Voter IDs that voted wrong for a punishable reputation
         *
         * @return array
         */
        public function getRewardedVoters(): array
        {
            $Results = [];

            if($this->getFinalVerdict() == VerdictResult::Yay)
            {
                foreach($this->Votes->Votes as $voter_id => $verdict)
                {
                    if($verdict == VoteVerdict::Yay)
                        $Results[] = $voter_id;
                }

                return $Results;
            }
            elseif($this->getFinalVerdict() == VerdictResult::Nay)
            {
                foreach($this->Votes->Votes as $voter_id => $verdict)
                {
                    if($verdict == VoteVerdict::Nay)
                        $Results[] = $voter_id;
                }

                return $Results;
            }
            else
            {
                return $Results;
            }
        }

        /**
         * Indicates if the content is spam or not based off the final verdict of voters or the CPU
         *
         * @return bool
         */
        public function isSpam(): bool
        {
            $FinalVerdict = $this->getFinalVerdict();
            $IsSpam = $this->PredictionSpam > $this->PredictionHam;

            if($IsSpam)
            {
                if($FinalVerdict == VerdictResult::CpuYay || $FinalVerdict == VerdictResult::Yay)
                {
                    return true;
                }

                return false;
            }
            else
            {
                if($FinalVerdict == VerdictResult::CpuYay || $FinalVerdict == VerdictResult::Yay)
                {
                    return false;
                }

                return true;
            }
        }

        /**
         * Returns an array representation of this object
         *
         * @return array
         */
        public function toArray(): array
        {
            return [
                "id" => $this->ID,
                "message_hash" => $this->MessageHash,
                "content" => $this->Content,
                "prediction_spam" => $this->PredictionSpam,
                "prediction_ham" => $this->PredictionHam,
                "votes" => $this->Votes->toArray(),
                "status" => $this->Status,
                "created_timestamp" => $this->CreatedTimestamp,
                "last_updated_timestamp" => $this->LastUpdatedTimestamp,
                "due_timestamp" => $this->DueTimestamp
            ];
        }

        /**
         * Constructs the object from an array representation
         *
         * @param array $data
         * @return PredictionVotes
         */
        public static function fromArray(array $data): PredictionVotes
        {
            $PredictionVotesObject = new PredictionVotes();

            if(isset($data["id"]))
                $PredictionVotesObject->ID = $data["id"];

            if(isset($data["message_hash"]))
                $PredictionVotesObject->MessageHash = $data["message_hash"];

            if(isset($data["content"]))
                $PredictionVotesObject->Content = $data["content"];

            if(isset($data["prediction_spam"]))
                $PredictionVotesObject->PredictionSpam = $data["prediction_spam"];

            if(isset($data["prediction_ham"]))
                $PredictionVotesObject->PredictionHam = $data["prediction_ham"];

            if(isset($data["votes"]))
                $PredictionVotesObject->Votes = $data["votes"];

            if(isset($data["status"]))
                $PredictionVotesObject->Status = $data["status"];

            if(isset($data["created_timestamp"]))
                $PredictionVotesObject->CreatedTimestamp = $data["created_timestamp"];

            if(isset($data["last_updated_timestamp"]))
                $PredictionVotesObject->LastUpdatedTimestamp = $data["last_updated_timestamp"];

            if(isset($data["due_timestamp"]))
                $PredictionVotesObject->DueTimestamp = $data["due_timestamp"];

            return $PredictionVotesObject;
        }
    }