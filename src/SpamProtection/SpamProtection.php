<?php

    /** @noinspection PhpUndefinedClassInspection */


    namespace SpamProtection;

    use acm\acm;
    use Exception;
    use mysqli;
    use SpamProtection\Managers\MessageLogManager;
    use TelegramClientManager\TelegramClientManager;

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'AutoConfig.php');


    /**
     * Class SpamProtection
     * @package SpamProtection
     */
    class SpamProtection
    {
        /**
         * @return mysqli
         */
        private $database;

        /**
         * The database configuration
         *
         * @var array
         */
        private $DatabaseConfiguration;

        /**
         * @var acm
         * @noinspection PhpUndefinedClassInspection
         */
        private $acm;

        /**
         * @var MessageLogManager
         */
        private $MessageLogManager;

        /**
         * @var TelegramClientManager
         */
        private $TelegramClientManager;


        /**
         * SpamProtection constructor.
         * @throws Exception
         */
        public function __construct()
        {
            /** @noinspection PhpUndefinedClassInspection */
            $this->acm = new acm(__DIR__, 'SpamProtection');
            $this->DatabaseConfiguration = $this->acm->getConfiguration('Database');
            $this->database = null;

            $this->MessageLogManager = new MessageLogManager($this);
            $this->TelegramClientManager = null;
        }

        /**
         * @return mysqli
         */
        public function getDatabase()
        {
            if($this->database == null)
            {
                $this->connectDatabase();
            }

            return $this->database;
        }

        /**
         * @return MessageLogManager
         * @noinspection PhpUnused
         */
        public function getMessageLogManager(): MessageLogManager
        {
            return $this->MessageLogManager;
        }

        /**
         * @return TelegramClientManager
         * @noinspection PhpUnused
         */
        public function getTelegramClientManager(): TelegramClientManager
        {
            if($this->TelegramClientManager == null)
            {
                $this->TelegramClientManager = new TelegramClientManager();
            }

            return $this->TelegramClientManager;
        }

        /**
         * Closes the current database connection
         */
        public function disconnectDatabase()
        {
            $this->database->close();
            $this->database = null;
        }

        /**
         * Creates a new database connection
         */
        public function connectDatabase()
        {
            if($this->database !== null)
            {
                $this->disconnectDatabase();
            }

            $this->database = new mysqli(
                $this->DatabaseConfiguration['Host'],
                $this->DatabaseConfiguration['Username'],
                $this->DatabaseConfiguration['Password'],
                $this->DatabaseConfiguration['Database'],
                $this->DatabaseConfiguration['Port']
            );
        }

    }