<?php


    namespace SpamProtection\Objects;

    use SpamProtection\Abstracts\BlacklistFlag;
    use SpamProtection\Exceptions\InvalidBlacklistFlagException;
    use SpamProtection\Exceptions\PropertyConflictedException;
    use TelegramClientManager\Objects\TelegramClient\Chat;

    /**
     * Class ChannelStatus
     * @package SpamProtection\Objects
     */
    class ChannelStatus
    {
        /**
         * The chat that these settings are configured for
         *
         * @var Chat
         */
        public $Chat;

        /**
         * Indicates if this channel is blacklisted or not
         *
         * @var bool
         */
        public $IsBlacklisted;

        /**
         * If the channel is blacklisted, the blacklist flag is applicable here
         *
         * @var string|BlacklistFlag
         */
        public $BlacklistFlag;

        /**
         * Indicates if this channel is whitelisted
         *
         * @var bool
         */
        public $IsWhitelisted;

        /**
         * Indicates if this channel is marked as official by Intellivoid
         *
         * @var bool
         */
        public $IsOfficial;

        /**
         * The generalized ID of the channel
         *
         * @deprecated
         * @var string|null
         */
        public $GeneralizedID;

        /**
         * The generalized ham prediction of the channel
         *
         * @deprecated
         * @var float|int
         */
        public $GeneralizedHam;

        /**
         * The generalized spam prediction of the channel
         *
         * @deprecated
         * @var float|int
         */
        public $GeneralizedSpam;

        /**
         * A note placed by the operator
         *
         * @var string
         */
        public $OperatorNote;

        /**
         * The generalized language prediction of this channel
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
         * Linked of linked chats
         *
         * @var string[]
         */
        public $LinkedChats;

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
         * Links a chat to the channel
         *
         * @param string $public_id
         * @return bool
         * @noinspection PhpUnused
         */
        public function linkChat(string $public_id): bool
        {
            if(in_array($public_id, $this->LinkedChats))
            {
                return false;
            }

            $this->LinkedChats[] = $public_id;
            return true;
        }

        /**
         * Unlinks a chat from the channel
         *
         * @param string $public_id
         * @return bool
         * @noinspection PhpUnused
         */
        public function unlinkChat(string $public_id): bool
        {
            if(in_array($public_id, $this->LinkedChats) == false)
            {
                return false;
            }

            $this->LinkedChats = array_diff($this->LinkedChats, [$public_id]);
            return true;
        }

        /**
         * Updates the blacklist flag of the user
         *
         * @param string $blacklist_flag
         * @return bool
         * @throws InvalidBlacklistFlagException
         * @throws PropertyConflictedException
         * @noinspection PhpUnused
         */
        public function updateBlacklist(string $blacklist_flag): bool
        {
            if($this->IsWhitelisted)
            {
                throw new PropertyConflictedException("This whitelisted channel cannot be blacklisted, remove the whitelist first.");
            }

            // Auto-capitalize the flag
            $blacklist_flag = strtoupper($blacklist_flag);
            $blacklist_flag = str_replace("0X", "0x", $blacklist_flag);

            switch($blacklist_flag)
            {
                case BlacklistFlag::None:
                    $this->IsBlacklisted = false;
                    $this->BlacklistFlag = $blacklist_flag;
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
                    break;

                case BlacklistFlag::BanEvade:
                    throw new PropertyConflictedException("The blacklist flag is not applicable to a channel");

                default:
                    throw new InvalidBlacklistFlagException($blacklist_flag, "The given blacklist flag is not valid");

            }

            return true;
        }


        /**
         * Returns an array which represents this object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                '0x000' => (int)$this->IsBlacklisted,
                '0x001' => $this->BlacklistFlag,
                '0x002' => (int)$this->IsWhitelisted,
                '0x003' => (int)$this->IsOfficial,
                '0x004' => $this->GeneralizedID,
                '0x005' => (float)$this->GeneralizedHam,
                '0x006' => (float)$this->GeneralizedSpam,
                '0x007' => $this->OperatorNote,
                '0x008' => $this->GeneralizedLanguage,
                '0x009' => $this->GeneralizedLanguageProbability,
                '0x010' => $this->LargeLanguageGeneralizedID,
                '0x011' => $this->LinkedChats,
                '0x012' => $this->GeneralizedSpamProbability,
                '0x013' => $this->GeneralizedHamProbability,
                '0x014' => $this->GeneralizedSpamLabel,
                '0x015' => $this->LargeSpamGeneralizedID
            );
        }

        /**
         * Constructs object from array
         *
         * @param Chat $chat
         * @param array $data
         * @return ChannelStatus
         */
        public static function fromArray(Chat $chat, array $data): ChannelStatus
        {
            $ChannelStatusObject = new ChannelStatus();
            $ChannelStatusObject->Chat = $chat;

            if(isset($data['0x000']))
            {
                $ChannelStatusObject->IsBlacklisted = (bool)$data['0x000'];
            }
            else
            {
                $ChannelStatusObject->IsBlacklisted = false;
            }

            if(isset($data['0x001']))
            {
                $ChannelStatusObject->BlacklistFlag = $data['0x001'];
            }
            else
            {
                $ChannelStatusObject->BlacklistFlag = BlacklistFlag::None;
            }

            if(isset($data['0x002']))
            {
                $ChannelStatusObject->IsWhitelisted = (bool)$data['0x002'];
            }
            else
            {
                $ChannelStatusObject->IsWhitelisted = false;
            }

            if(isset($data['0x003']))
            {
                $ChannelStatusObject->IsOfficial = (bool)$data['0x003'];
            }
            else
            {
                $ChannelStatusObject->IsOfficial = false;
            }

            if(isset($data['0x004']))
            {
                $ChannelStatusObject->GeneralizedID = $data['0x004'];
            }
            else
            {
                $ChannelStatusObject->GeneralizedID = null;
            }

            if(isset($data['0x005']))
            {
                $ChannelStatusObject->GeneralizedHam = (float)$data['0x005'];
            }
            else
            {
                $ChannelStatusObject->GeneralizedHam = (float)0;
            }

            if(isset($data['0x006']))
            {
                $ChannelStatusObject->GeneralizedSpam = (float)$data['0x006'];
            }
            else
            {
                $ChannelStatusObject->GeneralizedSpam = (float)0;
            }

            if(isset($data['0x007']))
            {
                $ChannelStatusObject->OperatorNote = $data['0x007'];
            }
            else
            {
                $ChannelStatusObject->OperatorNote = null;
            }

            if(isset($data['0x008']))
            {
                $ChannelStatusObject->GeneralizedLanguage = $data['0x008'];
            }
            else
            {
                $ChannelStatusObject->GeneralizedLanguage = "Unknown";
            }

            if(isset($data['0x009']))
            {
                $ChannelStatusObject->GeneralizedLanguageProbability = (float)$data['0x009'];
            }
            else
            {
                $ChannelStatusObject->GeneralizedLanguageProbability = 0;
            }

            if(isset($data['0x010']))
            {
                $ChannelStatusObject->LargeLanguageGeneralizedID = $data['0x010'];
            }
            else
            {
                $ChannelStatusObject->LargeLanguageGeneralizedID = null;
            }

            if(isset($data['0x011']))
            {
                $ChannelStatusObject->LinkedChats = $data['0x011'];
            }
            else
            {
                $ChannelStatusObject->LinkedChats = [];
            }

            if(isset($data['0x012']))
            {
                $ChannelStatusObject->GeneralizedSpamProbability = $data["0x012"];
            }
            else
            {
                $ChannelStatusObject->GeneralizedSpamProbability = 0;
            }

            if(isset($data['0x013']))
            {
                $ChannelStatusObject->GeneralizedHamProbability = $data["0x013"];
            }
            else
            {
                $ChannelStatusObject->GeneralizedHamProbability = 0;
            }

            if(isset($data["0x014"]))
            {
                $ChannelStatusObject->GeneralizedSpamLabel = $data["0x014"];
            }
            else
            {
                $ChannelStatusObject->GeneralizedSpamLabel = "Unknown";
            }

            if(isset($data["0x015"]))
            {
                $ChannelStatusObject->LargeSpamGeneralizedID = $data["0x015"];
            }
            else
            {
                $ChannelStatusObject->LargeSpamGeneralizedID = null;
            }

            return $ChannelStatusObject;
        }
    }