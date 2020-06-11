<?php


    namespace SpamProtection\Managers;


    use msqg\QueryBuilder;
    use SpamProtection\Abstracts\TelegramChatType;
    use SpamProtection\Abstracts\TelegramClientSearchMethod;
    use SpamProtection\Exceptions\DatabaseException;
    use SpamProtection\Exceptions\InvalidSearchMethod;
    use SpamProtection\Exceptions\TelegramClientNotFoundException;
    use SpamProtection\Objects\TelegramClient;
    use SpamProtection\Objects\TelegramClient\Chat;
    use SpamProtection\Objects\TelegramClient\User;
    use SpamProtection\SpamProtection;
    use SpamProtection\Utilities\Hashing;
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
         * Registers a client into the database
         *
         * @param Chat $chat
         * @param User $user
         * @return TelegramClient
         * @throws DatabaseException
         * @throws InvalidSearchMethod
         * @throws TelegramClientNotFoundException
         */
        public function registerClient(Chat $chat, User $user): TelegramClient
        {
            $CurrentTime = (int)time();
            $PublicID = Hashing::telegramClientPublicID($chat->ID, $user->ID);

            try
            {
                if($user->Username !== null)
                {
                    $ExistingClient = $this->getClient(TelegramClientSearchMethod::byPublicId, $PublicID);
                }

                $ExistingClient = $this->getClient(TelegramClientSearchMethod::byPublicId, $PublicID);

                $ExistingClient->LastActivityTimestamp = $CurrentTime;
                $ExistingClient->Available = true;
                $ExistingClient->User = $user;
                $ExistingClient->Chat = $chat;

                $this->updateClient($ExistingClient);

                return $ExistingClient;
            }
            catch (TelegramClientNotFoundException $e)
            {
                // Ignore this exception
                unset($e);
            }

            $PublicID = $this->spamProtection->getDatabase("IVDatabase")->real_escape_string($PublicID);
            $Available = (int)true;
            $AccountID = 0;
            $User = ZiProto::encode($user->toArray());
            $User = $this->spamProtection->getDatabase("IVDatabase")->real_escape_string($User);
            $Chat = ZiProto::encode($chat->toArray());
            $Chat = $this->spamProtection->getDatabase("IVDatabase")->real_escape_string($Chat);
            $SessionData = new TelegramClient\SessionData();
            $SessionData = ZiProto::encode($SessionData->toArray());
            $SessionData = $this->spamProtection->getDatabase("IVDatabase")->real_escape_string($SessionData);
            $ChatID = $this->spamProtection->getDatabase("IVDatabase")->real_escape_string($chat->ID);
            $UserID = $this->spamProtection->getDatabase("IVDatabase")->real_escape_string($user->ID);
            $LastActivity = $CurrentTime;
            $Created = $CurrentTime;

            $Query = QueryBuilder::insert_into('telegram_clients', array(
                    'public_id' => $PublicID,
                    'available' => $Available,
                    'account_id' => $AccountID,
                    'user' => $User,
                    'chat' => $Chat,
                    'session_data' => $SessionData,
                    'chat_id' => $ChatID,
                    'user_id' => $UserID,
                    'last_activity' => $LastActivity,
                    'created' => $Created
                )
            );

            $QueryResults = $this->spamProtection->getDatabase("IVDatabase")->query($Query);
            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->spamProtection->getDatabase("IVDatabase")->error);
            }

            return $this->getClient(TelegramClientSearchMethod::byPublicId, $PublicID);
        }

        /**
         * Registers the client as a user only (private)
         *
         * @param User $user
         * @return TelegramClient
         * @throws DatabaseException
         * @throws InvalidSearchMethod
         * @throws TelegramClientNotFoundException
         */
        public function registerUser(User $user): TelegramClient
        {
            $ChatObject = new Chat();
            $ChatObject->ID = $user->ID;
            $ChatObject->Type = TelegramChatType::Private;
            $ChatObject->Title = null;
            $ChatObject->Username = $user->Username;
            $ChatObject->FirstName = $user->FirstName;
            $ChatObject->LastName = $user->LastName;

            return $this->registerClient($ChatObject, $user);
        }

        /**
         * Registers the client as a chat only (bot based)
         *
         * @param Chat $chat
         * @return TelegramClient
         * @throws DatabaseException
         * @throws InvalidSearchMethod
         * @throws TelegramClientNotFoundException
         */
        public function registerChat(Chat $chat): TelegramClient
        {
            $UserObject = new User();
            $UserObject->ID = 991157148;
            $UserObject->FirstName = "Intellivoid";
            $UserObject->LastName = "Support";
            $UserObject->LanguageCode = null;
            $UserObject->IsBot = true;
            $UserObject->Username = "IntellivoidSupport";

            return $this->registerClient($chat, $UserObject);
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