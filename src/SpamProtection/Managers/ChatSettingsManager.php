<?php


    namespace SpamProtection\Managers;


    use SpamProtection\SpamProtection;

    /**
     * Class ChatSettingsManager
     * @package SpamProtection\Managers
     */
    class ChatSettingsManager
    {
        /**
         * @var SpamProtection
         */
        private $spamProtection;

        /**
         * ChatSettingsManager constructor.
         * @param SpamProtection $spamProtection
         */
        public function __construct(SpamProtection $spamProtection)
        {
            $this->spamProtection = $spamProtection;
        }
    }