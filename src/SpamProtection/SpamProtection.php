<?php


    namespace SpamProtection;

    use acm\acm;
    use mysqli;

    if(class_exists('acm\acm') == false)
    {
        include_once(__DIR__ . DIRECTORY_SEPARATOR . 'acm' . DIRECTORY_SEPARATOR . 'acm.php');
    }

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'AutoConfig.php');


    class SpamProtection
    {
        /**
         * @return mysqli
         */
        private $database;

        /**
         * @var array
         */
        private $DatabaseConfiguration;

        /**
         * @var acm
         */
        private $acm;

        /**
         * SpamProtection constructor.
         * @throws \Exception
         */
        public function __construct()
        {
            $this->acm = new acm(__DIR__, 'SpamProtection');
            $this->DatabaseConfiguration = $this->acm->getConfiguration('Database');
            $this->database = null;
        }

        /**
         * @return mysqli
         */
        public function getDatabase()
        {
            if($this->database == null)
            {
                $this->database = new mysqli(
                    $this->DatabaseConfiguration['Host'],
                    $this->DatabaseConfiguration['Username'],
                    $this->DatabaseConfiguration['Password'],
                    $this->DatabaseConfiguration['Name'],
                    $this->DatabaseConfiguration['Port']
                );
            }

            return $this->database;
        }
    }