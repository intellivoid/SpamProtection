<?php


    namespace SpamProtection\Exceptions;


    use Exception;
    use SpamProtection\Abstracts\BlacklistFlag;
    use Throwable;

    /**
     * Class InvalidBlacklistFlagException
     * @package SpamProtection\Exceptions
     */
    class InvalidBlacklistFlagException extends Exception
    {
        /**
         * @var string
         */
        private $input;

        /**
         * InvalidBlacklistFlagException constructor.
         * @param $input
         * @param string $message
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct($input, $message = "", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
            $this->input = $input;
        }

        /**
         * Gets the best match of the input, eg; Did you mean ...?
         *
         * @return string
         * @noinspection PhpUnused
         */
        public function getBestMatch(): string
        {
            if($this->input == null)
            {
                return "0xSPAM";
            }

            $best = null;
            $min = (strlen($this->input) / 4 + 1) * 10 + .1;
            foreach (array_unique(BlacklistFlag::All) as $item)
            {
                if ($item !== $this->input && ($len = levenshtein($item, $this->input, 10, 11, 10)) < $min)
                {
                    $min = $len;
                    $best = $item;
                }
            }

            if($best == null)
            {
                return "0xSPAM";
            }

            return $best;
        }
    }