<?php


    namespace SpamProtection\Abstracts;

    /**
     * Class MemberAction
     * @package SpamProtection\Abstracts
     */
    abstract class MemberAction
    {
        const Alert = "ALERT";

        const Mute = "MUTE";

        const Ban = "BAN";
    }