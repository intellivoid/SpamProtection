<?php


    namespace SpamProtection\Managers;


    use msqg\QueryBuilder;
    use SpamProtection\Exceptions\UnsupportedMessageException;
    use SpamProtection\Objects\ImageLog;
    use SpamProtection\Objects\TelegramObjects\Message;
    use SpamProtection\SpamProtection;
    use SpamProtection\Utilities\Hashing;
    use ZiProto\ZiProto;

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

        /**
         * Registers a message prediction into the database
         *
         * @param Message $message
         * @param string $image_url
         * @param float $spam_prediction
         * @param float $ham_prediction
         * @return ImageLog
         * @throws UnsupportedMessageException
         * @noinspection PhpUnused
         * @noinspection DuplicatedCode
         * @noinspection PhpUndefinedClassInspection
         */
        public function registerMessage(Message $message, string $image_url, float $spam_prediction, float $ham_prediction): ImageLog
        {
            if($message->Chat == null)
            {
                throw new UnsupportedMessageException("The message is missing the 'Chat' object");
            }

            if($message->From == null)
            {
                throw new UnsupportedMessageException("The message is missing the 'User' object");
            }

            if($message->Photo == null)
            {
                throw new UnsupportedMessageException("The message is missing the 'Photo' object for it to be a ImageLog");
            }

            if(count($message->Photo) == 0)
            {
                throw new UnsupportedMessageException("The message has no photos to parse");
            }

            if($message->getText() == null)
            {
                throw new UnsupportedMessageException();
            }

            $message_id = (int)$message->MessageID;
            $photo_size = $this->spamProtection->getDatabase()->real_escape_string(ZiProto::encode($message->photosToArray()));
            $chat_id = (int)$message->Chat->ID;
            $chat = $this->spamProtection->getDatabase()->real_escape_string(ZiProto::encode($message->Chat->toArray()));
            $user_id = (int)$message->From->ID;
            $user = $this->spamProtection->getDatabase()->real_escape_string(ZiProto::encode($message->From->toArray()));
            $forward_from = $this->spamProtection->getDatabase()->real_escape_string(ZiProto::encode(array()));
            $forward_from_chat = $this->spamProtection->getDatabase()->real_escape_string(ZiProto::encode(array()));
            $forward_from_message_id = (int)0;

            if($message->ForwardFrom !== null)
            {
                $forward_from = $this->spamProtection->getDatabase()->real_escape_string(ZiProto::encode(
                    $message->ForwardFrom->toArray()
                ));
            }

            if($message->ForwardFromChat !== null)
            {
                $forward_from_chat = $this->spamProtection->getDatabase()->real_escape_string(ZiProto::encode(
                    $message->ForwardFromChat->toArray()
                ));
            }

            if($message->ForwardFromMessageID !== null)
            {
                $forward_from_message_id = (int)$message->ForwardFromMessageID;
            }

            $timestamp = (int)time();

            $photos_hash = "";

            foreach($message->Photo as $photoSize)
            {
                $photos_hash .= Hashing::photoSizeHash($photoSize);
            }

            $content_hash = $this->spamProtection->getDatabase()->real_escape_string(Hashing::messageContent($message->getText()));
            $message_hash = Hashing::messageImageHash($message_id, $chat_id, $user_id, $timestamp, $content_hash, $photos_hash);
            $spam_prediction = (float)$spam_prediction;
            $ham_prediction = (float)$ham_prediction;

            $Query = QueryBuilder::insert_into('message_logs', array(
                'message_hash' => $message_hash,
                'message_id' => $message_id,
                'chat_id' => $chat_id,
                'chat' => $chat,
                'user_id' => $user_id,
                'user' => $user,
                'forward_from' => $forward_from,
                'forward_from_chat' => $forward_from_chat,
                'forward_from_message_id' => $forward_from_message_id,
                'content_hash' => $content_hash,
                'spam_prediction' => $spam_prediction,
                'ham_prediction' => $ham_prediction,
                'timestamp' => $timestamp
            ));

            $QueryResults = $this->spamProtection->getDatabase()->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->spamProtection->getDatabase()->error);
            }

            return $this->getMessage($message_hash);
        }
    }