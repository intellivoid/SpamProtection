<?php


    namespace SpamProtection\Objects;


    use SpamProtection\Abstracts\BlacklistFlag;
    use TelegramClientManager\Objects\TelegramClient\User;

    /**
     * Class UserStatus
     * @package SpamProtection\Objects
     */
    class UserStatus
    {
        /**
         * The user that these statuses are configured to
         *
         * @var User
         */
        public $User;

        /**
         * The generalized ID associated with this user, set it to "None" to reset.
         *
         * @var string
         */
        public $GeneralizedID;

        /**
         * The generalized ham prediction
         *
         * @var float|int
         */
        public $GeneralizedHam;

        /**
         * The generalized spam prediction
         *
         * @var float|int
         */
        public $GeneralizedSpam;

        /**
         * Indicates if this user is a operator with permissions to execute
         * administrative commands
         *
         * @var bool
         */
        public $IsOperator;

        /**
         * Indicates if this user is moderating agent that's actively 
         * reporting detected spam
         * 
         * @var bool
         */
        public $IsAgent;

        /**
         * Indicates if this user cannot be affected by automated means
         *
         * @var bool
         */
        public $IsWhitelisted;

        /**
         * Indicates if this user is blacklisted or not
         *
         * @var bool
         */
        public $IsBlacklisted;

        /**
         * If blacklisted, the the blacklist flag is provided below
         *
         * @var string|BlacklistFlag
         */
        public $BlacklistFlag;

        /**
         * If blacklisted for evade, the original private ID is shown below
         *
         * @var string
         */
        public $OriginalPrivateID;

        /**
         * A small message/note created by the operator
         *
         * @var string
         */
        public $OperatorNote;

        /**
         * The user client parameters obtained from an agent
         *
         * @var UserClientParameters
         */
        public $ClientParameters;

        /**
         * The generalized language prediction of this user
         *
         * @var string
         */
        public $GeneralizedLanguage;

        /**
         * The probability of the language prediction generalization
         *
         * @var float|int
         */
        public $GeneralizedLanguageProbability;

        /**
         * The ID of the large generalization of the language
         *
         * @var string|null
         */
        public $LargeLanguageGeneralizedID;

        /**
         * Resets the trust prediction of this user
         *
         * @return bool
         * @noinspection PhpUnused
         */
        public function resetTrustPrediction(): bool
        {
            $this->GeneralizedID = "None";
            $this->GeneralizedHam = 0;
            $this->GeneralizedSpam = 0;

            return true;
        }

        /**
         * Resets the language prediction of the user
         *
         * @return bool
         * @noinspection PhpUnused
         */
        public function resetLanguagePrediction(): bool
        {
            $this->GeneralizedLanguage = "Unknown";
            $this->GeneralizedLanguageProbability = 0;
            $this->GeneralizedID = null;

            return true;
        }

        /**
         * Returns a configuration array of the user stats
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                '0x000' => $this->GeneralizedID,
                '0x001' => (float)$this->GeneralizedHam,
                '0x002' => (float)$this->GeneralizedSpam,
                '0x003' => (int)$this->IsOperator,
                '0x004' => (int)$this->IsAgent,
                '0x005' => (int)$this->IsWhitelisted,
                '0x006' => (int)$this->IsBlacklisted,
                '0x007' => $this->BlacklistFlag,
                '0x008' => $this->OriginalPrivateID,
                '0x009' => $this->OperatorNote,
                '0x010' => $this->ClientParameters->toArray(),
                '0x011' => $this->GeneralizedLanguage,
                '0x012' => $this->GeneralizedLanguageProbability,
                '0x013' => $this->LargeLanguageGeneralizedID
            );
        }

        /**
         * Constructs a user status from a configuration array
         *
         * @param User $user
         * @param array $data
         * @return UserStatus
         */
        public static function fromArray(User $user, array $data): UserStatus
        {
            $UserStatusObject = new UserStatus();
            $UserStatusObject->User = $user;

            if(isset($data['0x000']))
            {
                $UserStatusObject->GeneralizedID = $data['0x000'];
            }
            else
            {
                $UserStatusObject->GeneralizedID = null;
            }

            if(isset($data['0x001']))
            {
                $UserStatusObject->GeneralizedHam = (float)$data['0x001'];
            }
            else
            {
                $UserStatusObject->GeneralizedHam = (float)0;
            }

            if(isset($data['0x002']))
            {
                $UserStatusObject->GeneralizedSpam = (float)$data['0x002'];
            }
            else
            {
                $UserStatusObject->GeneralizedSpam = (float)0;
            }

            if(isset($data['0x003']))
            {
                $UserStatusObject->IsOperator = (bool)$data['0x003'];
            }
            else
            {
                $UserStatusObject->IsOperator = false;
            }

            if(isset($data['0x004']))
            {
                $UserStatusObject->IsAgent = (bool)$data['0x004'];
            }
            else
            {
                $UserStatusObject->IsAgent = false;
            }

            if(isset($data['0x005']))
            {
                $UserStatusObject->IsWhitelisted = (bool)$data['0x005'];
            }
            else
            {
                $UserStatusObject->IsWhitelisted = false;
            }

            if(isset($data['0x006']))
            {
                $UserStatusObject->IsBlacklisted = (bool)$data['0x006'];
            }
            else
            {
                $UserStatusObject->IsBlacklisted = false;
            }

            if(isset($data['0x007']))
            {
                $UserStatusObject->BlacklistFlag = $data['0x007'];
            }
            else
            {
                $UserStatusObject->BlacklistFlag = BlacklistFlag::None;
            }

            if(isset($data['0x008']))
            {
                $UserStatusObject->OriginalPrivateID = $data['0x008'];
            }
            else
            {
                $UserStatusObject->OriginalPrivateID = null;
            }

            if(isset($data['0x009']))
            {
                $UserStatusObject->OperatorNote = $data['0x009'];
            }
            else
            {
                $UserStatusObject->OperatorNote = "None";
            }

            if(isset($data['0x010']))
            {
                $UserStatusObject->ClientParameters = UserClientParameters::fromArray($data['0x010']);
            }
            else
            {
                $UserStatusObject->ClientParameters = new UserClientParameters();
            }

            if(isset($data['0x011']))
            {
                $UserStatusObject->GeneralizedLanguage = $data['0x011'];
            }
            else
            {
                $UserStatusObject->GeneralizedLanguage = "Unknown";
            }

            if(isset($data['0x012']))
            {
                $UserStatusObject->GeneralizedLanguageProbability = (float)$data['0x012'];
            }
            else
            {
                $UserStatusObject->GeneralizedLanguageProbability = 0;
            }

            if(isset($data['0x013']))
            {
                $UserStatusObject->LargeLanguageGeneralizedID = $data['0x013'];
            }
            else
            {
                $UserStatusObject->LargeLanguageGeneralizedID = null;
            }

            return $UserStatusObject;
        }
    }