<?php


    namespace SpamProtection\Managers;


    use SpamProtection\SpamProtection;

    /**
     * Class MessageLogManager
     * @package SpamProtection\Managers]
     */
    class MessageLogManager
    {
        /**
         * @var SpamProtection
         */
        private $spamProtection;

        /**
         * MessageLogManager constructor.
         * @param SpamProtection $spamProtection
         */
        public function __construct(SpamProtection $spamProtection)
        {
            $this->spamProtection = $spamProtection;
        }
    }