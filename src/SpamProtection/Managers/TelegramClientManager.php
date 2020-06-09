<?php


    namespace SpamProtection\Managers;


    use SpamProtection\SpamProtection;

    /**
     * Class TelegramClientManager
     * @package SpamProtection\Managers
     */
    class TelegramClientManager
    {
        /**
         * @var SpamProtection
         */
        private $spamProtection;

        /**
         * TelegramClientManager constructor.
         * @param SpamProtection $spamProtection
         */
        public function __construct(SpamProtection $spamProtection)
        {
            $this->spamProtection = $spamProtection;
        }
    }