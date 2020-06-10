<?php


    namespace SpamProtection\Objects;

    use SpamProtection\Objects\TelegramClient\Chat;

    /**
     * Class ChatSettings
     * @package SpamProtection\Objects
     */
    class ChatSettings
    {
        /**
         * The chat that these settings are configured for
         *
         * @var Chat
         */
        public $Chat;

        /**
         * Indicates if the spam predictions are logged publicly
         *
         * @var bool
         */
        public $LogSpamPredictions;

        /**
         * Indicates if spam prediction detections does not affect
         * users who forward content, rather the original author
         * of the content is affected
         *
         * @var bool
         */
        public $ForwardsOnly;

        public $
    }