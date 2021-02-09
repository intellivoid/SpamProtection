<?php


    namespace SpamProtection\Utilities;

    /**
     * Class Validation
     * @package SpamProtection\Utilities
     */
    class Validation
    {
        /**
         * Determines if the input is a supported language
         *
         * @param string $input
         * @return bool
         */
        public static function supportedLanguage(string $input): bool
        {
            $supported_languages = [
                "en",
                "es",
                "jp",
                "zh",
                "de",
                "pl",
                "fr",
                "nl",
                "ko",
                "it",
                "tr",
                "ru",
                "auto"
            ];

            if(in_array(strtolower($input), $supported_languages))
                return True;

            return false;
        }
    }