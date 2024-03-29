<?php
    /** @noinspection PhpUndefinedClassInspection */


    namespace SpamProtection\Managers;

    use msqg\QueryBuilder;
    use SpamProtection\Exceptions\DatabaseException;
    use SpamProtection\Exceptions\DownloadFileException;
    use SpamProtection\Exceptions\MessageLogNotFoundException;
    use SpamProtection\Exceptions\UnsupportedMessageException;
    use SpamProtection\Objects\MessageLog;
    use SpamProtection\Objects\TelegramObjects\Message;
    use SpamProtection\Objects\TelegramObjects\PhotoSize;
    use SpamProtection\SpamProtection;
    use SpamProtection\Utilities\Hashing;
    use ZiProto\ZiProto;

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

        /**
         * Registers multiple message log types into the database and returns an array of message logs
         *
         * @param Message $message
         * @param float $spam_prediction
         * @param float $ham_prediction
         * @return MessageLog[]
         * @throws DatabaseException
         * @throws DownloadFileException
         * @throws MessageLogNotFoundException
         * @throws UnsupportedMessageException
         * @noinspection PhpUnused
         */
        public function registerMessages(Message $message, float $spam_prediction, float $ham_prediction): array
        {
            $Results = array();

            try
            {
                $Results[] = $this->registerMessage($message, $spam_prediction, $ham_prediction);
            }
            catch(UnsupportedMessageException $unsupportedMessageException)
            {
                unset($unsupportedMessageException);
            }

            if($message->Photo !== null)
            {
                if(count($message->Photo) > 0)
                {
                    foreach($message->Photo as $photoSize)
                    {
                        $Results[] = $this->registerPhotoImage($message, $photoSize);
                    }
                }
            }

            return $Results;
        }

        /**
         * Registers a message prediction into the database
         *
         * @param Message $message
         * @param float $spam_prediction
         * @param float $ham_prediction
         * @return MessageLog
         * @throws DatabaseException
         * @throws MessageLogNotFoundException
         * @throws UnsupportedMessageException
         * @noinspection PhpUnused
         * @noinspection DuplicatedCode
         */
        public function registerMessage(Message $message, float $spam_prediction, float $ham_prediction): MessageLog
        {
            if($message->Chat == null)
            {
                throw new UnsupportedMessageException("The message is missing the 'Chat' object");
            }

            if($message->From == null)
            {
                throw new UnsupportedMessageException("The message is missing the 'User' object");
            }

            if($message->getText() == null)
            {
                throw new UnsupportedMessageException();
            }

            $message_id = (int)$message->MessageID;
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
            $content_hash = $this->spamProtection->getDatabase()->real_escape_string(Hashing::messageContent($message->getText()));
            $photo_size = $this->spamProtection->getDatabase()->real_escape_string(ZiProto::encode(
                PhotoSize::fromArray(array())->toArray()
            ));
            $message_hash = Hashing::messageHash($message_id, $chat_id, $user_id, $timestamp, $content_hash);
            $spam_prediction = (float)$spam_prediction;
            $ham_prediction = (float)$ham_prediction;

            $Query = QueryBuilder::insert_into('message_logs', array(
                'message_hash' => $message_hash,
                'message_id' => $message_id,
                'photo_size' => $photo_size,
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

        /**
         * Registers a message image prediction into the database
         *
         * @param Message $message
         * @param PhotoSize $photoSize
         * @return MessageLog
         * @throws DatabaseException
         * @throws DownloadFileException
         * @throws MessageLogNotFoundException
         * @throws UnsupportedMessageException
         * @noinspection DuplicatedCode
         */
        public function registerPhotoImage(Message $message, PhotoSize $photoSize): MessageLog
        {
            if($message->Chat == null)
            {
                throw new UnsupportedMessageException("The message is missing the 'Chat' object");
            }

            if($message->From == null)
            {
                throw new UnsupportedMessageException("The message is missing the 'User' object");
            }

            $message_id = (int)$message->MessageID;
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
            $content_hash = $this->spamProtection->getDatabase()->real_escape_string(Hashing::hashRemoteFile($photoSize->URL));
            $photo_size = $this->spamProtection->getDatabase()->real_escape_string(ZiProto::encode($photoSize->toArray()));
            $message_hash = Hashing::messageImageHash($message_id, $chat_id, $user_id, $timestamp, $content_hash, $photoSize);
            $spam_prediction = (float)$photoSize->UnsafePrediction;
            $ham_prediction = (float)$photoSize->SafePrediction;

            $Query = QueryBuilder::insert_into('message_logs', array(
                'message_hash' => $message_hash,
                'message_id' => $message_id,
                'photo_size' => $photo_size,
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

        /**
         * Returns an existing message from the database
         *
         * @param string $message_hash
         * @return MessageLog
         * @throws DatabaseException
         * @throws MessageLogNotFoundException
         */
        public function getMessage(string $message_hash): MessageLog
        {
            $message_hash = $this->spamProtection->getDatabase()->real_escape_string($message_hash);
            
            $Query = QueryBuilder::select('message_logs', array(
                'id',
                'message_hash',
                'message_id',
                'photo_size',
                'chat_id',
                'chat',
                'user_id',
                'user',
                'forward_from',
                'forward_from_chat',
                'forward_from_message_id',
                'content_hash',
                'spam_prediction',
                'ham_prediction',
                'timestamp'
            ), 'message_hash', $message_hash);

            $QueryResults = $this->spamProtection->getDatabase()->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->spamProtection->getDatabase()->error);
            }
            else
            {
                if($QueryResults->num_rows !== 1)
                {
                    throw new MessageLogNotFoundException();
                }

                $Row = $QueryResults->fetch_array(MYSQLI_ASSOC);
                $Row['photo_size'] = ZiProto::decode($Row['photo_size']);
                $Row['user'] = ZiProto::decode($Row['user']);
                $Row['chat'] = ZiProto::decode($Row['chat']);
                $Row['forward_from'] = ZiProto::decode($Row['forward_from']);
                $Row['forward_from_chat'] = ZiProto::decode($Row['forward_from_chat']);

                return MessageLog::fromArray($Row);
            }
        }
    }