<?php


    namespace SpamProtection\Objects;


    use SpamProtection\Abstracts\BlacklistFlag;
    use SpamProtection\Exceptions\InvalidBlacklistFlagException;
    use SpamProtection\Exceptions\MissingOriginalPrivateIdException;
    use SpamProtection\Exceptions\PropertyConflictedException;
    use SpamProtection\Utilities\Validation;
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
         * @deprecated
         */
        public $GeneralizedID;

        /**
         * The generalized ham prediction
         *
         * @var float|int
         * @deprecated
         */
        public $GeneralizedHam;

        /**
         * The generalized spam prediction
         *
         * @var float|int
         * @deprecated
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
         * The current configured language of the chat
         *
         * @var string
         */
        public $ConfiguredLanguage;

        /**
         * The probability of the spam prediction generalization
         *
         * @var float|int
         */
        public $GeneralizedSpamProbability;

        /**
         * The probability of the ham prediction generalization
         *
         * @var float|int
         */
        public $GeneralizedHamProbability;

        /**
         * The top label of the spam prediction generalization
         *
         * @var float|int
         */
        public $GeneralizedSpamLabel;

        /**
         * The ID of the large generalization of the Spam Prediction
         *
         * @var string|null
         */
        public $LargeSpamGeneralizedID;

        /**
         * Indicates if this user can appeal for their blacklist
         *
         * @var bool
         */
        public $CanAppeal;

        /**
         * The total amount of messages sent as ham
         *
         * @var int
         */
        public $HamCount;

        /**
         * The total amount of messages sent as spam
         *
         * @var int
         */
        public $SpamCount;

        /**
         * Indicates if this account was created prior to the spam count update
         *
         * @var bool
         */
        public $PriorToSpamCount;

        /**
         * The data holding the messages per minute data
         *
         * @var array
         */
        public $MessagesPerMinuteData;

        /**
         * Reputation points associated with this user
         *
         * @var int
         */
        public $ReputationPoints;

        /**
         * Tracks the message speed
         *
         * @param int|null $time
         * @return bool
         */
        public function trackMessageSpeed(int $time=null): bool
        {
            if($time == null) $time = time();
            if($this->MessagesPerMinuteData == null) $this->MessagesPerMinuteData = [];

            if(isset($this->MessagesPerMinuteData[$time]))
            {
                $this->MessagesPerMinuteData[$time] += 1;
            }
            else
            {
                $this->MessagesPerMinuteData[$time] = 1;
            }

            // Remove entries older than 60 seconds
            $new_data = [];
            foreach($this->MessagesPerMinuteData as $timestamp => $value)
            {
                if((time() - $timestamp) <= 60)
                {
                    $new_data[$timestamp] = $value;
                }
            }

            $this->MessagesPerMinuteData = $new_data;

            return True;
        }

        /**
         * Calculates the average message per minute
         *
         * @return false|float|int
         */
        public function calculateAverageMessagesPerMinute()
        {
            if($this->MessagesPerMinuteData == null) return 0;
            if(count($this->MessagesPerMinuteData) == 0) return 0;

            $data = [];
            foreach($this->MessagesPerMinuteData as $timestamp => $value)
            {
                if((time() - $timestamp) <= 60)
                {
                    $data[] = $value;
                }
            }

            return array_sum($data);
        }

        /**
         * Resets the trust prediction of this user
         *
         * @return bool
         * @noinspection PhpUnused
         */
        public function resetTrustPrediction(): bool
        {
            $this->GeneralizedSpamLabel = "Unknown";
            $this->GeneralizedSpamProbability = 0;
            $this->GeneralizedHamProbability = 0;
            $this->LargeSpamGeneralizedID = null;

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
            $this->LargeLanguageGeneralizedID = null;

            return true;
        }

        /**
         * Updates the whitelist state of the user, throws an exception if there's a conflict
         *
         * @param bool $whitelisted
         * @return bool
         * @throws PropertyConflictedException
         * @noinspection PhpUnused
         */
        public function updateWhitelist(bool $whitelisted): bool
        {
            if($whitelisted)
            {
                // If the user is already blacklisted
                if($this->IsBlacklisted)
                {
                    throw new PropertyConflictedException("This blacklisted user cannot be whitelisted, remove the blacklist first.");
                }

                $this->IsWhitelisted = true;
                return true;
            }
            else
            {
                $this->IsWhitelisted = false;
                return true;
            }
        }

        /**
         * Updates the blacklist flag of the user
         *
         * @param string $blacklist_flag
         * @param string|null $original_private_id
         * @return bool
         * @throws InvalidBlacklistFlagException
         * @throws MissingOriginalPrivateIdException
         * @throws PropertyConflictedException
         * @noinspection PhpUnused
         */
        public function updateBlacklist(string $blacklist_flag, string $original_private_id=null): bool
        {
            if($this->IsWhitelisted)
            {
                throw new PropertyConflictedException("This whitelisted user cannot be blacklisted, remove the whitelist first.");
            }

            if($this->IsAgent)
            {
                throw new PropertyConflictedException("You can't blacklist an agent");
            }

            if($this->IsOperator)
            {
                throw new PropertyConflictedException("You can't blacklist an operator");
            }

            // Auto-capitalize the flag
            $blacklist_flag = strtoupper($blacklist_flag);
            $blacklist_flag = str_replace("0X", "0x", $blacklist_flag);

            switch($blacklist_flag)
            {
                case BlacklistFlag::None:
                    $this->IsBlacklisted = false;
                    $this->BlacklistFlag = $blacklist_flag;
                    $this->OriginalPrivateID = null;
                    break;

                case BlacklistFlag::Special:
                case BlacklistFlag::Spam:
                case BlacklistFlag::PornographicSpam:
                case BlacklistFlag::PrivateSpam:
                case BlacklistFlag::PiracySpam:
                case BlacklistFlag::ChildAbuse:
                case BlacklistFlag::Raid:
                case BlacklistFlag::Scam:
                case BlacklistFlag::Impersonator:
                case BlacklistFlag::MassAdding:
                case BlacklistFlag::NameSpam:
                    $this->IsBlacklisted = true;
                    $this->BlacklistFlag = $blacklist_flag;
                    $this->OriginalPrivateID = null;
                    break;

                case BlacklistFlag::BanEvade:
                    if($original_private_id == null)
                    {
                        throw new MissingOriginalPrivateIdException();
                    }

                    $this->IsBlacklisted = true;
                    $this->BlacklistFlag = $blacklist_flag;
                    $this->OriginalPrivateID = $original_private_id;
                    break;

                default:
                    throw new InvalidBlacklistFlagException($blacklist_flag, "The given blacklist flag is not valid");

            }

            return true;
        }

        /**
         * Updates the agent permissions
         *
         * @param bool $grant_permissions
         * @return bool
         * @throws PropertyConflictedException
         * @noinspection PhpUnused
         */
        public function updateAgent(bool $grant_permissions): bool
        {
            if($this->IsBlacklisted)
            {
                throw new PropertyConflictedException("You can't make a blacklisted user an agent");
            }

            if($grant_permissions)
            {
                $this->IsAgent = true;
            }
            else
            {
                $this->IsAgent = false;
            }

            return true;
        }

        /**
         * Updates the operator permissions
         *
         * @param bool $grant_permissions
         * @return bool
         * @throws PropertyConflictedException
         * @noinspection PhpUnused
         */
        public function updateOperator(bool $grant_permissions): bool
        {
            if($this->IsBlacklisted)
            {
                throw new PropertyConflictedException("You can't make a blacklisted user an operator");
            }

             if($grant_permissions)
             {
                 $this->IsOperator = true;
             }
             else
             {
                 $this->IsOperator = false;
             }

            return true;
        }

        /**
         * Returns a trust prediction score of the user
         *
         * @return float|int
         */
        public function getTrustPrediction()
        {
            if ($this->GeneralizedSpamProbability > 0 && $this->GeneralizedHamProbability > 0)
            {
                return 100 * (0.5**($this->GeneralizedSpamProbability/$this->GeneralizedHamProbability));
            }

            /** @noinspection PhpDeprecationInspection */
            if($this->GeneralizedSpam !== null && $this->GeneralizedSpam > 0 && $this->GeneralizedHam !== null && $this->GeneralizedHam > 0)
            {
                /** @noinspection PhpDeprecationInspection */
                return 100 * (0.5**($this->GeneralizedSpam/$this->GeneralizedHam));
            }

            return 0;
        }

        /**
         * Gets the generalized spam value (Including the old value)
         *
         * @return float|int
         */
        public function getGeneralizedSpamValue()
        {
            if($this->GeneralizedSpamProbability !== null)
            {
                return $this->GeneralizedSpamProbability;
            }

            /** @noinspection PhpDeprecationInspection */
            if($this->GeneralizedSpam !== null)
            {
                /** @noinspection PhpDeprecationInspection */
                return $this->GeneralizedSpam;
            }

            return 0;
        }

        /**
         * Gets the generalized ham value (Including the old value)
         *
         * @return float|int
         */
        public function getGeneralizedHamValue()
        {
            if($this->GeneralizedHamProbability !== null)
            {
                return $this->GeneralizedHamProbability;
            }

            /** @noinspection PhpDeprecationInspection */
            if($this->GeneralizedHam !== null)
            {
                /** @noinspection PhpDeprecationInspection */
                return $this->GeneralizedHam;
            }

            return 0;
        }

        /**
         * Determines if the user is a potential spammer based off their generalized values, it will also take account
         * if the user is new and the spam count
         *
         * @return bool
         */
        public function isPotentialSpammer(): bool
        {
            if($this->PriorToSpamCount)
            {
                if($this->SpamCount > 1)
                {
                    if($this->getGeneralizedSpamValue() > $this->getGeneralizedHamValue())
                    {
                        return true;
                    }
                }
            }
            else
            {
                if($this->getGeneralizedSpamValue() > $this->getGeneralizedHamValue())
                {
                    return true;
                }
            }

            return false;
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
                '0x013' => $this->LargeLanguageGeneralizedID,
                '0x014' => $this->ConfiguredLanguage,
                '0x015' => $this->GeneralizedSpamLabel,
                '0x016' => $this->GeneralizedSpamProbability,
                '0x017' => $this->GeneralizedHamProbability,
                '0x018' => $this->LargeSpamGeneralizedID,
                '0x019' => $this->CanAppeal,
                '0x020' => $this->HamCount,
                '0x021' => $this->SpamCount,
                '0x022' => $this->PriorToSpamCount,
                '0x023' => $this->MessagesPerMinuteData,
                '0x024' => $this->ReputationPoints
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

            if(isset($data['0x014']))
            {
                $UserStatusObject->ConfiguredLanguage = $data['0x014'];
            }
            else
            {
                $UserStatusObject->ConfiguredLanguage = "en";

                if($UserStatusObject->GeneralizedLanguage !== "Unknown" && $UserStatusObject->GeneralizedLanguage !== null)
                {
                    if(Validation::supportedLanguage($UserStatusObject->GeneralizedLanguage))
                    {
                        $UserStatusObject->ConfiguredLanguage = $UserStatusObject->GeneralizedLanguage;
                    }
                }
            }

            if(isset($data["0x015"]))
            {
                $UserStatusObject->GeneralizedSpamLabel = $data["0x015"];
            }
            else
            {
                $UserStatusObject->GeneralizedSpamLabel = "Unknown";
            }

            if(isset($data["0x016"]))
            {
                $UserStatusObject->GeneralizedSpamProbability = $data["0x016"];
            }
            else
            {
                $UserStatusObject->GeneralizedSpamProbability = 0;
            }

            if(isset($data["0x017"]))
            {
                $UserStatusObject->GeneralizedHamProbability = $data["0x017"];
            }
            else
            {
                $UserStatusObject->GeneralizedHamProbability = 0;
            }

            if(isset($data["0x018"]))
            {
                $UserStatusObject->LargeSpamGeneralizedID = $data["0x018"];
            }
            else
            {
                $UserStatusObject->LargeSpamGeneralizedID = null;
            }

            if(isset($data["0x019"]))
            {
                $UserStatusObject->CanAppeal = $data["0x019"];
            }
            else
            {
                if($UserStatusObject->IsBlacklisted)
                {
                    // If the user was already blacklisted before this update, they cannot appeal.
                    $UserStatusObject->CanAppeal = false;
                }
                else
                {
                    $UserStatusObject->CanAppeal = true;
                }
            }

            if(isset($data['0x020']))
            {
                $UserStatusObject->HamCount = $data['0x020'];
            }
            else
            {
                $UserStatusObject->HamCount = 0;
            }

            if(isset($data['0x021']))
            {
                $UserStatusObject->SpamCount = $data['0x021'];
            }
            else
            {
                $UserStatusObject->SpamCount = 0;
            }

            if(isset($data['0x022']))
            {
                $UserStatusObject->PriorToSpamCount = $data['0x022'];
            }
            else
            {
                $UserStatusObject->PriorToSpamCount = false;
            }

            if(isset($data['0x023']))
            {
                $UserStatusObject->MessagesPerMinuteData = $data['0x023'];
            }
            else
            {
                $UserStatusObject->MessagesPerMinuteData = [];
            }

            if(isset($data['0x024']))
            {
                $UserStatusObject->ReputationPoints = (int)$data['0x024'];
            }
            else
            {
                $UserStatusObject->ReputationPoints = 0;
            }

            return $UserStatusObject;
        }
    }