<?php


    namespace SpamProtection\Abstracts;


    /**
     * Class DetectionAction
     * @package SpamProtection\Abstracts
     */
    abstract class DetectionAction
    {
        const DeleteMessage = "DELETE";

        const KickOffender = "KICK_OFFENDER";

        const BanOffender = "BAN_OFFENDER";
    }