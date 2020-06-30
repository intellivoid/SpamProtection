<?php


    namespace SpamProtection\Objects;


    use TelegramClientManager\Objects\TelegramClient\Chat;
    use TelegramClientManager\Objects\TelegramClient\User;

    /**
     * Class ImageLog
     * @package SpamProtection\Objects
     */
    class ImageLog
    {
        /**
         * The Unique Internal Database ID of this record
         *
         * @var int
         */
        public $ID;

        /**
         * Unique message hash dependent on the content of the message
         *
         * @var string
         */
        public $MessageHash;

        /**
         * The ID of the message
         *
         * @var int
         */
        public $MessageID;

        /**
         * Identifier for this file, which can be used to download or reuse the file
         *
         * @var string
         */
        public $FileID;

        /**
         * Unique identifier for this file, which is supposed to be the same over time and for different bots.
         * Can't be used to download or reuse the file.
         *
         * @var string
         */
        public $FileUniqueID;

        /**
         * The size of the file
         *
         * @var int
         */
        public $FileSize;

        /**
         * The ID of the chat that this message was sent in
         *
         * @var int
         */
        public $ChatID;

        /**
         * The chat that this message was sent in
         *
         * @var Chat
         */
        public $Chat;

        /**
         * The ID of the user
         *
         * @var int
         */
        public $UserID;

        /**
         * The sender of this message
         *
         * @var User
         */
        public $User;

        /**
         * The original sender of this message
         *
         * @var User
         */
        public $ForwardForm;

        /**
         * The channel/chat that this message is from
         *
         * @var Chat
         */
        public $ForwardFromChat;

        /**
         * The ID of the message sent from a chat
         *
         * @var int
         */
        public $ForwardFromMessageID;

        /**
         * SHA256 Hash of the image contents
         *
         * @var string
         */
        public $ContentHash;

        /**
         * The width of the image
         *
         * @var int
         */
        public $Width;

        /**
         * The height of the image
         *
         * @var int
         */
        public $Height;

        /**
         * The spam prediction value of the image
         *
         * @var float|int
         */
        public $SpamPrediction;

        /**
         * The ham prediction of the image
         *
         * @var float|int
         */
        public $HamPrediction;

        /**
         * Unix Timestamp of when this record was created
         *
         * @var int
         */
        public $Timestamp;
    }