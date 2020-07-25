<?php


    namespace SpamProtection\Objects;

    /**
     * Class UserClientParameters
     * @package SpamProtection\Objects
     */
    class UserClientParameters
    {
        /**
         * Indicates if this user is a contact
         *
         * @var bool
         */
        public $IsContact;

        /**
         * Indicates if this user is a mutual contact
         *
         * @var bool
         */
        public $IsMutualContact;

        /**
         * Indicates if the account was deleted
         *
         * @var bool
         */
        public $IsDeleted;

        /**
         * Indicates if this user is verified by Telegram
         *
         * @var bool
         */
        public $IsVerified;

        /**
         * Indicates
         *
         * @var bool
         */
        public $IsRestricted;

        /**
         * Indicates if the user is marked as a scammer from Telegram
         *
         * @var bool
         */
        public $IsScam;

        /**
         * Whether this is an official support user from Telegram
         *
         * @var bool
         */
        public $IsSupport;

        /**
         * The phone number of the user if available
         *
         * @var string
         */
        public $PhoneNumber;

        /**
         * The ID of the data center that this user is connected to
         *
         * @var int
         */
        public $DataCenterID;

        /**
         * The Unix Timestamp of when this was last updated
         *
         * @var int
         */
        public $LastUpdated;

        /**
         * Returns an array which represents this object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                "is_contact" => (bool)$this->IsContact,
                "is_mutual_contact" => (bool)$this->IsMutualContact,
                "is_deleted" => (bool)$this->IsDeleted,
                "is_verified" => (bool)$this->IsVerified,
                "is_restricted" => (bool)$this->IsRestricted,
                "is_scam" => (bool)$this->IsScam,
                "is_support" => (bool)$this->IsSupport,
                "phone_number" => $this->PhoneNumber,
                "data_center_id" => (int)$this->DataCenterID,
                "last_updated" => (int)$this->LastUpdated
            );
        }

        /**
         * Constructs object from array
         *
         * @param array $data
         * @return UserClientParameters
         * @noinspection DuplicatedCode
         */
        public static function fromArray(array $data): UserClientParameters
        {
            $UserClientParametersObject = new UserClientParameters();

            if(isset($data["is_contact"]))
            {
                $UserClientParametersObject->IsContact = (bool)$data["is_contact"];
            }

            if(isset($data["is_mutual_contact"]))
            {
                $UserClientParametersObject->IsMutualContact = (bool)$data["is_mutual_contact"];
            }

            if(isset($data["is_deleted"]))
            {
                $UserClientParametersObject->IsDeleted = (bool)$data["is_deleted"];
            }

            if(isset($data["is_verified"]))
            {
                $UserClientParametersObject->IsVerified = (bool)$data["is_verified"];
            }

            if(isset($data["is_restricted"]))
            {
                $UserClientParametersObject->IsRestricted = (bool)$data["is_restricted"];
            }

            if(isset($data["is_scam"]))
            {
                $UserClientParametersObject->IsScam = (bool)$data["is_scam"];
            }

            if(isset($data["is_support"]))
            {
                $UserClientParametersObject->IsSupport = (bool)$data["is_support"];
            }

            if(isset($data["phone_number"]))
            {
                $UserClientParametersObject->PhoneNumber = $data["phone_number"];
            }

            if(isset($data["data_center_id"]))
            {
                $UserClientParametersObject->DataCenterID = (int)$data["data_center_id"];
            }

            if(isset($data["last_updated"]))
            {
                $UserClientParametersObject->LastUpdated = (int)$data["last_updated"];
            }

            return $UserClientParametersObject;
        }
    }