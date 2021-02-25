<?php


    namespace SpamProtection\Objects;

    use SpamProtection\Abstracts\VotesDueRecordStatus;
    use SpamProtection\Objects\VotesDueRecord\Records;

    /**
     * Class VotesDueRecord
     * @package SpamProtection\Objects
     */
    class VotesDueRecord
    {
        /**
         * The ID of the record
         *
         * @var int
         */
        public $ID;

        /**
         * @var Records
         */
        public $Records;

        /**
         * @var int|VotesDueRecordStatus
         */
        public $Status;

        /**
         * The Unix Timestamp for when this record is due
         *
         * @var int
         */
        public $DueTimestamp;

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
         * Returns an array representation of this object
         *
         * @return array
         */
        public function toArray(): array
        {
            return [
                'id' => $this->ID,
                'records' => $this->Records->toArray(),
                'due_timestamp' => $this->DueTimestamp,
                'created_timestamp' => $this->CreatedTimestamp,
                'last_updated_timestamp' => $this->LastUpdatedTimestamp
            ];
        }

        /**
         * Constructs the object from an array representation
         *
         * @param array $data
         * @return VotesDueRecord
         */
        public static function fromArray(array $data): VotesDueRecord
        {
            $VotesDueRecordObject = new VotesDueRecord();

            if(isset($data["id"]))
            {
                $VotesDueRecordObject->ID = $data["id"];
            }

            if(isset($data["records"]))
            {
                $VotesDueRecordObject->Records = Records::fromArray($data["records"]);
            }

            if(isset($data["due_timestamp"]))
            {
                $VotesDueRecordObject->DueTimestamp = $data["due_timestamp"];
            }

            if(isset($data["created_timestamp"]))
            {
                $VotesDueRecordObject->CreatedTimestamp = $data["created_timestamp"];
            }

            if(isset($data["last_updated_timestamp"]))
            {
                $VotesDueRecordObject->LastUpdatedTimestamp = $data["last_updated_timestamp"];
            }

            return $VotesDueRecordObject;
        }
    }