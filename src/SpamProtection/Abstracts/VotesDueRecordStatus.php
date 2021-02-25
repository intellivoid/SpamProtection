<?php


    namespace SpamProtection\Abstracts;

    /**
     * Class VotesDueRecordStatus
     * @package SpamProtection\Abstracts
     */
    abstract class VotesDueRecordStatus
    {
        const CollectingData = 0;

        const BuildingReport = 1;

        const Completed = 2;

        const NotEnoughData = 3;
    }