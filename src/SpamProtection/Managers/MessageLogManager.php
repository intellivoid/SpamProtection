<?php


    namespace SpamProtection\Managers;


    use SpamProtection\Objects\MessageLog;
    use SpamProtection\Objects\TelegramObjects\Message;
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

        public function registerMessage(Message $message, float $spam_prediction, float $ham_prediction): MessageLog
        {
            if($message)
        }
    }