<?php


    namespace SpamProtection\Exceptions;


    use Exception;
    use Throwable;

    /**
     * Class InvalidVoteVerdictException
     * @package SpamProtection\Exceptions
     */
    class InvalidVoteVerdictException extends Exception
    {
        /**
         * InvalidVoteVerdictException constructor.
         * @param string $message
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct($message = "", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
    }