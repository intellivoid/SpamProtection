<?php


    namespace SpamProtection\Managers;


    use SpamProtection\Objects\TelegramClient;
    use SpamProtection\Objects\UserStatus;
    use SpamProtection\SpamProtection;

    /**
     * Class ChatSettingsManager
     * @package SpamProtection\Managers
     */
    class ChatSettingsManager
    {
        /**
         * @var SpamProtection
         */
        private $spamProtection;

        /**
         * ChatSettingsManager constructor.
         * @param SpamProtection $spamProtection
         */
        public function __construct(SpamProtection $spamProtection)
        {
            $this->spamProtection = $spamProtection;
        }

        /**
         * Returns the user status of the telegram client
         *
         * @param TelegramClient $telegramClient
         * @return UserStatus
         */
        public function getUserStatus(TelegramClient $telegramClient): UserStatus
        {
            if(isset($telegramClient->SessionData["user_status"]) == false)
            {
                $telegramClient->SessionData["user_status"] = UserStatus::fromArray($telegramClient->User, array());
            }

            return UserStatus::fromArray($telegramClient->User, $telegramClient->SessionData["user_status"]);
        }

        /**
         * Updates the user status configuration in the telegram client
         *
         * @param TelegramClient $telegramClient
         * @param UserStatus $userStatus
         * @return TelegramClient
         */
        public function updateUserStatus(TelegramClient $telegramClient, UserStatus $userStatus): TelegramClient
        {
            $telegramClient->SessionData["user_status"] = $userStatus->toArray();
            return $telegramClient;
        }
    }