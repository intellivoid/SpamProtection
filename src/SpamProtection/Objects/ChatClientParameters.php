<?php


    namespace SpamProtection\Objects;


    /**
     * Class ChatClientParameters
     * @package SpamProtection\Objects
     */
    class ChatClientParameters
    {
        /**
         * Indicates if this chat is verified
         *
         * @var bool
         */
        public $IsVerified;

        /**
         * Indicates if this chat is restricted
         *
         * @var bool
         */
        public $IsRestricted;

        /**
         * Indicates if this chat is a scam
         *
         * @var bool
         */
        public $IsScam;

        /**
         * The Unix Timestamp of when this object was last updated
         *
         * @var int
         */
        public $LastUpdated;

        /**
         * Returns an array which represents this object
         *
         * @return array
         */
        public function toArray()
        {
            return array(
                "is_verified" => (bool)$this->IsVerified,
                "is_restricted" => (bool)$this->IsRestricted,
                "is_scam" => (bool)$this->IsScam,
                "last_updated" => (int)$this->LastUpdated
            );
        }

        /**
         * Constructs object from array
         *
         * @param array $data
         * @return ChatClientParameters
         * @noinspection DuplicatedCode
         */
        public static function fromArray(array $data): ChatClientParameters
        {
            $ChatClientParametersObject = new ChatClientParameters();

            if(isset($data["is_verified"]))
            {
                $ChatClientParametersObject->IsVerified = (bool)$data["is_verified"];
            }

            if(isset($data["is_restricted"]))
            {
                $ChatClientParametersObject->IsRestricted = (bool)$data["is_restricted"];
            }

            if(isset($data["is_scam"]))
            {
                $ChatClientParametersObject->IsScam = (bool)$data["is_scam"];
            }

            if(isset($data["last_updated"]))
            {
                $ChatClientParametersObject->LastUpdated = (int)$data["last_updated"];
            }

            return $ChatClientParametersObject;
        }
    }