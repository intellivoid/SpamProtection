<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace SpamProtection\Objects;

    use TelegramClientManager\Objects\TelegramClient\Chat;
    use TelegramClientManager\Objects\TelegramClient\User;

    class DirectClientStatus
    {
        /**
         * The chat that these settings are configured for
         *
         * @var Chat
         */
        public $Chat;

        /**
         * The user that these settings are configured for
         *
         * @var User
         */
        public $User;

        /**
         * The Unix Timestamp for when this user was first seen in the chat
         *
         * @var int|null
         */
        public $FirstSeenTimestamp;

        /**
         * Returns an array which represents this object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                '0x000' => ($this->FirstSeenTimestamp == null ? null : (int)$this->FirstSeenTimestamp),
            );
        }

        /**
         * Constructs object from array
         *
         * @param Chat $chat
         * @param User $user
         * @param array $data
         * @return DirectClientStatus
         */
        public static function fromArray(Chat $chat, User $user, array $data): DirectClientStatus
        {
            $DirectClientStatus = new DirectClientStatus();
            $DirectClientStatus->Chat = $chat;
            $DirectClientStatus->User = $user;


            if(isset($data['0x000']))
            {
                $DirectClientStatus->FirstSeenTimestamp = (int)$data['0x000'];
            }
            else
            {
                $DirectClientStatus->FirstSeenTimestamp = null;
            }

            return $DirectClientStatus;
        }
    }