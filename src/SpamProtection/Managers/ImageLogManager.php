<?php


    namespace SpamProtection\Managers;


    use SpamProtection\SpamProtection;

    /**
     * Class ImageLogManager
     * @package SpamProtection\Managers
     */
    class ImageLogManager
    {
        /**
         * @var SpamProtection
         */
        private $spamProtection;

        /**
         * ImageLogManager constructor.
         * @param SpamProtection $spamProtection
         */
        public function __construct(SpamProtection $spamProtection)
        {
            $this->spamProtection = $spamProtection;
        }
    }