<?php


    namespace SpamProtection\Objects\PredictionVotes;

    use SpamProtection\Abstracts\VoteVerdict;
    use SpamProtection\Exceptions\InvalidVoteVerdictException;
    use TelegramClientManager\Objects\TelegramClient;

    /**
     * Class Votes
     * @package SpamProtection\Objects\PredictionVotes
     */
    class Votes
    {
        /**
         * List of voters and their verdict about this prediction content
         *
         * @var array
         */
        public $Votes;

        /**
         * Places a verdict on the vote, returns true if the user voted for the first time or false
         * if the user has changed their vote
         *
         * @param TelegramClient $userTelegramClient
         * @param int|VoteVerdict $verdict
         * @return bool
         * @throws InvalidVoteVerdictException
         */
        public function placeVerdict(TelegramClient $userTelegramClient, int $verdict): bool
        {
            if($verdict < 0 || $verdict > 1)
                throw new InvalidVoteVerdictException("The given verdict '$verdict' is not valid");

            $this->Votes[$userTelegramClient->ID] = $verdict;
            if(isset($this->Votes[$userTelegramClient->ID]))
            {
                return true;
            }

            return false;
        }

        /**
         * Get the total amount of nays
         *
         * @return int
         */
        public function getNays(): int
        {
            $results = 0;

            foreach($this->Votes as $voter => $verdict)
            {
                if($verdict == VoteVerdict::Nay) $results += 1;
            }

            return $results;
        }


        /**
         * Get the total amount of yays
         *
         * @return int
         */
        public function getYays(): int
        {
            $results = 0;

            foreach($this->Votes as $voter => $verdict)
            {
                if($verdict == VoteVerdict::Yay) $results += 1;
            }

            return $results;
        }

        /**
         * @return array
         */
        public function toArray(): array
        {
            return $this->Votes;
        }

        /**
         * @param array $data
         * @return Votes
         */
        public static function fromArray(array $data): Votes
        {
            $VotesObject = new Votes();
            $VotesObject->Votes = $data;

            return $VotesObject;
        }
    }