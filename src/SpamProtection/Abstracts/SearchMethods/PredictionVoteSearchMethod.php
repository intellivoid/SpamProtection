<?php


    namespace SpamProtection\Abstracts\SearchMethods;

    /**
     * Class PredictionVoteSearchMethod
     * @package SpamProtection\Abstracts\SearchMethods
     */
    abstract class PredictionVoteSearchMethod
    {
        const ById = "id";

        const ByMessageHash = "message_hash";
    }