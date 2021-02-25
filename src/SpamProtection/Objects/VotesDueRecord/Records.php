<?php


    namespace SpamProtection\Objects\VotesDueRecord;

    use SpamProtection\Objects\PredictionVotes;

    /**
     * Class Records
     * @package SpamProtection\Objects\VotesDueRecord
     */
    class Records
    {
        /**
         * @var array
         */
        public $Records;

        /**
         * Adds a record into the Votes Due record pool
         *
         * @param PredictionVotes $predictionVotes
         * @return bool
         */
        public function addRecord(PredictionVotes $predictionVotes): bool
        {
            if(in_array($predictionVotes->ID, $this->Records))
                return false;

            $this->Records[] = $predictionVotes->ID;
            return true;
        }

        /**
         * Returns an array representation of the object
         *
         * @return array
         */
        public function toArray(): array
        {
            return $this->Records;
        }

        /**
         * Constructs the object from an array representation
         *
         * @param array $data
         * @return Records
         */
        public static function fromArray(array $data): Records
        {
            $RecordsObject = new Records();
            $RecordsObject->Records = $data;

            return $RecordsObject;
        }
    }