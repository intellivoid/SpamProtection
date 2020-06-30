<?php


    namespace SpamProtection\Objects;

    use SpamProtection\Objects\TelegramObjects\PhotoSize;
    use TelegramClientManager\Objects\TelegramClient\Chat;
    use TelegramClientManager\Objects\TelegramClient\User;

    /**
     * Class ImageLog
     * @package SpamProtection\Objects
     */
    class ImageLog
    {
        /**
         * The Unique Internal Database ID of this record
         *
         * @var int
         */
        public $ID;

        /**
         * Unique message hash dependent on the content of the message
         *
         * @var string
         */
        public $MessageHash;

        /**
         * The ID of the message
         *
         * @var int
         */
        public $MessageID;

        /**
         * PhotoSize (File Information)
         *
         * @var PhotoSize
         */
        public $PhotoSize;

        /**
         * The ID of the chat that this message was sent in
         *
         * @var int
         */
        public $ChatID;

        /**
         * The chat that this message was sent in
         *
         * @var Chat
         */
        public $Chat;

        /**
         * The ID of the user
         *
         * @var int
         */
        public $UserID;

        /**
         * The sender of this message
         *
         * @var User
         */
        public $User;

        /**
         * The original sender of this message
         *
         * @var User
         */
        public $ForwardForm;

        /**
         * The channel/chat that this message is from
         *
         * @var Chat
         */
        public $ForwardFromChat;

        /**
         * The ID of the message sent from a chat
         *
         * @var int
         */
        public $ForwardFromMessageID;

        /**
         * SHA256 Hash of the image contents
         *
         * @var string
         */
        public $ContentHash;

        /**
         * The spam prediction value of the image
         *
         * @var float|int
         */
        public $SpamPrediction;

        /**
         * The ham prediction of the image
         *
         * @var float|int
         */
        public $HamPrediction;

        /**
         * Unix Timestamp of when this record was created
         *
         * @var int
         */
        public $Timestamp;

        /**
         * Returns an array which represents this object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'id' => (int)$this->ID,
                'message_hash' => $this->MessageHash,
                'message_id' => (int)$this->MessageID,
                'photo_size' => $this->PhotoSize->toArray(),
                'chat_id' => (int)$this->ChatID,
                'chat' => $this->Chat->toArray(),
                'user_id' => (int)$this->UserID,
                'user' => $this->User->toArray(),
                'forward_from' => $this->ForwardForm->toArray(),
                'forward_from_chat' => $this->ForwardFromChat->toArray(),
                'forward_from_message_id' => $this->ForwardFromMessageID,
                'content_hash' => $this->ContentHash,
                'spam_prediction' => (float)$this->SpamPrediction,
                'ham_prediction' => (float)$this->HamPrediction,
                'timestamp' => (int)$this->Timestamp
            );
        }

        /**
         * Constructs object from array
         *
         * @param array $data
         * @return ImageLog
         * @noinspection DuplicatedCode
         */
        public static function fromArray(array $data): ImageLog
        {
            $ImageLogObject = new ImageLog();

            if(isset($data['id']))
            {
                $ImageLogObject->ID = (int)$data['id'];
            }

            if(isset($data['message_hash']))
            {
                $ImageLogObject->MessageHash = $data['message_hash'];
            }

            if(isset($data['message_id']))
            {
                $ImageLogObject->MessageID = (int)$data['message_id'];
            }

            if(isset($data['photo_size']))
            {
                $ImageLogObject->PhotoSize = PhotoSize::fromArray($data['photo_size']);
            }

            if(isset($data['chat_id']))
            {
                $ImageLogObject->ChatID = (int)$data['chat_id'];
            }

            if(isset($data['chat']))
            {
                $ImageLogObject->Chat = Chat::fromArray($data['chat']);
            }

            if(isset($data['user_id']))
            {
                $ImageLogObject->UserID = (int)$data['user_id'];
            }

            if(isset($data['user']))
            {
                $ImageLogObject->User = User::fromArray($data['user']);
            }

            if(isset($data['forward_from']))
            {
                $ImageLogObject->ForwardForm = User::fromArray($data['forward_from']);
            }

            if(isset($data['forward_from_chat']))
            {
                $ImageLogObject->ForwardFromChat = Chat::fromArray($data['forward_from_chat']);
            }

            if(isset($data['forward_from_message_id']))
            {
                $ImageLogObject->ForwardFromMessageID = (int)$data['forward_from_message_id'];
            }

            if(isset($data['content_hash']))
            {
                $ImageLogObject->ContentHash = $data['content_hash'];
            }

            if(isset($data['spam_prediction']))
            {
                $ImageLogObject->SpamPrediction = (float)$data['spam_prediction'];
            }

            if(isset($data['ham_prediction']))
            {
                $ImageLogObject->HamPrediction = (float)$data['ham_prediction'];
            }

            if(isset($data['timestamp']))
            {
                $ImageLogObject->Timestamp = (int)$data['timestamp'];
            }

            return $ImageLogObject;
        }
    }