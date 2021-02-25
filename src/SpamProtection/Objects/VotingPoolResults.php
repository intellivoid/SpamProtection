<?php


    namespace SpamProtection\Objects;

    /**
     * Class VotingPoolResults
     * @package SpamProtection\Objects
     */
    class VotingPoolResults
    {
        /**
         * The path for the ham dataset that was generated
         *
         * @var null
         */
        public $HamDatasetPath = null;

        /**
         * The total amount of ham results
         *
         * @var int
         */
        public $HamCount = 0;

        /**
         * The path for the spam dataset that was generated
         *
         * @var null
         */
        public $SpamDatasetPath = null;

        /**
         * The total amount of spam results
         *
         * @var int
         */
        public $SpamCount = 0;

        /**
         * The total amount of User Yay votes
         *
         * @var int
         */
        public $UserYayVotes = 0;

        /**
         * The total amount of User Nay votes
         *
         * @var int
         */
        public $UserNayVotes = 0;

        /**
         * The total amount of CPU Yay votes
         *
         * @var int
         */
        public $CpuYayVotes = 0;

        /**
         * The total amount of CPU Nay votes
         *
         * @var int
         */
        public $CpuNayVotes = 0;

        /**
         * The amount of votes that consulted in ties
         *
         * @var int
         */
        public $TieVotes = 0;

        /**
         * The total amount of voting records there are
         *
         * @var int
         */
        public $VotingRecordsCount = 0;

        /**
         * The total amount of failures when processing voting records
         *
         * @var int
         */
        public $VotingRecordsFailureCount = 0;

        /**
         * The top 10 users that contributed
         *
         * @var array
         */
        public $TopUsers = [];

    }