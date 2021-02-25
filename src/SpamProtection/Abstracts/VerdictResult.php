<?php


    namespace SpamProtection\Abstracts;


    /**
     * Class VerdictResult
     * @package SpamProtection\Abstracts
     */
    abstract class VerdictResult
    {
        /**
         * The same amount of votes from users, CPU handles the vote.
         */
        const VotingTie = 4;

        /**
         * No votes, the final verdict is based off the computer
         */
        const CpuNay = 3;

        /**
         * No votes, the final verdict is based off the computer
         */
        const CpuYay = 2;

        /**
         * People who voted yay was correct
         */
        const Yay = 1;

        /**
         * People who voted nay was correct
         */
        const Nay = 0;

    }