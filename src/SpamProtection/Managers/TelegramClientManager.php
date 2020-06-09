<?php


    namespace SpamProtection\Managers;


    use msqg\QueryBuilder;
    use SpamProtection\Abstracts\TelegramClientSearchMethod;
    use SpamProtection\Exceptions\DatabaseException;
    use SpamProtection\Exceptions\InvalidSearchMethod;
    use SpamProtection\Exceptions\TelegramClientNotFoundException;
    use SpamProtection\Objects\TelegramClient;
    use SpamProtection\SpamProtection;
    use ZiProto\ZiProto;

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

        /**
         * Returns an existing client from the database
         *
         * @param string $search_method
         * @param string $value
         * @return TelegramClient
         * @throws DatabaseException
         * @throws InvalidSearchMethod
         * @throws TelegramClientNotFoundException
         */
        public function getClient(string $search_method, string $value): TelegramClient
        {
            switch($search_method)
            {
                case TelegramClientSearchMethod::byId:
                    $search_method = $this->spamProtection->getDatabase("IVDatabase")->real_escape_string($search_method);
                    $value = (int)$value;
                    break;

                case TelegramClientSearchMethod::byPublicId:
                    $search_method = $this->spamProtection->getDatabase("IVDatabase")->real_escape_string($search_method);
                    $value = $this->spamProtection->getDatabase("IVDatabase")->real_escape_string($value);;
                    break;

                default:
                    throw new InvalidSearchMethod();
            }

            $Query = QueryBuilder::select('telegram_clients', [
                'id',
                'public_id',
                'available',
                'account_id',
                'user',
                'chat',
                'session_data',
                'chat_id',
                'user_id',
                'last_activity',
                'created'
            ], $search_method, $value);

            $QueryResults = $this->spamProtection->getDatabase("IVDatabase")->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->spamProtection->getDatabase("IVDatabase")->error);
            }
            else
            {
                if($QueryResults->num_rows !== 1)
                {
                    throw new TelegramClientNotFoundException();
                }

                $Row = $QueryResults->fetch_array(MYSQLI_ASSOC);
                $Row['user'] = ZiProto::decode($Row['user']);
                $Row['chat'] = ZiProto::decode($Row['chat']);
                $Row['session_data'] = ZiProto::decode($Row['session_data']);
                return TelegramClient::fromArray($Row);
            }
        }

        /**
         * Updates an existing client in the database
         *
         * @param TelegramClient $telegramClient
         * @return bool
         * @throws DatabaseException
         */
        public function updateClient(TelegramClient $telegramClient): bool
        {
            $id = (int)$telegramClient->ID;
            $available = (int)$telegramClient->Available;
            $account_id = (int)$telegramClient->AccountID;
            $user = ZiProto::encode($telegramClient->User->toArray());
            $user = $this->spamProtection->getDatabase("IVDatabase")->real_escape_string($user);
            $chat = ZiProto::encode($telegramClient->Chat->toArray());
            $chat = $this->spamProtection->getDatabase("IVDatabase")->real_escape_string($chat);
            $session_data = ZiProto::encode($telegramClient->SessionData->toArray());
            $session_data = $this->spamProtection->getDatabase("IVDatabase")->real_escape_string($session_data);
            $chat_id = $this->spamProtection->getDatabase("IVDatabase")->real_escape_string($telegramClient->Chat->ID);
            $user_id = $this->spamProtection->getDatabase("IVDatabase")->real_escape_string($telegramClient->User->ID);
            $last_activity = (int)time();

            $Query = QueryBuilder::update('telegram_clients', array(
                'available' => $available,
                'account_id' => $account_id,
                'user' => $user,
                'chat' => $chat,
                'session_data' => $session_data,
                'chat_id' => $chat_id,
                'user_id' => $user_id,
                'last_activity' => $last_activity
            ), 'id', $id);
            $QueryResults = $this->spamProtection->getDatabase("IVDatabase")->query($Query);

            if($QueryResults == true)
            {
                return true;
            }
            else
            {
                throw new DatabaseException($Query, $this->spamProtection->getDatabase("IVDatabase")->error);
            }
        }
    }