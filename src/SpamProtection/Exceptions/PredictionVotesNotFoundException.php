<?php


    namespace SpamProtection\Exceptions;


    use Exception;
    use Throwable;

    /**
     * Class PredictionVotesNotFoundException
     * @package SpamProtection\Exceptions
     */
    class PredictionVotesNotFoundException extends Exception
    {
        /**
         * PredictionVotesNotFoundException constructor.
         * @param string $message
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct($message = "", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
    }