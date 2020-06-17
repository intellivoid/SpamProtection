<?php

    include_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'SpamProtection' . DIRECTORY_SEPARATOR . 'SpamProtection.php');

    $SpamProtection = new \SpamProtection\SpamProtection();

    $Results = $SpamProtection->getDatabase("IVDatabase")->query("SELECT public_id, chat_id, user_id, user, chat, username FROM `telegram_clients`");
    $ResultsArray = [];

    while($Row = $Results->fetch_assoc())
    {
        $Row['chat'] = \ZiProto\ZiProto::decode($Row['chat']);
        $Row['user'] = \ZiProto\ZiProto::decode($Row['user']);
        $ResultsArray[] = \SpamProtection\Objects\TelegramClient::fromArray($Row);
    }

    /** @var \SpamProtection\Objects\TelegramClient $value */
    foreach($ResultsArray as $value)
    {
        if($value->Chat->ID == $value->User->ID)
        {
            if($value->Chat->Type == \SpamProtection\Abstracts\TelegramChatType::SuperGroup)
            {
                print($value->PublicID . PHP_EOL);
                file_get_contents("https://api.telegram.org/bot869979136:AAEi_uxDobRLwhC0wF0TMfkqAoy8IC0fA-0/sendmessage?chat_id=" . $value->Chat->ID . "&text=Hi%20There%21%20This%20is%20a%20rare%20broadcast%21%0A%0A%40Intellivoid%20recently%20created%20a%20new%20bot%20called%20%40SpamProtectionBot%20which%20uses%20CoffeeHouse%20to%20detect%20and%20remove%20spam%21%20feel%20free%20to%20check%20it%20out%2C%20more%20information%20about%20this%20can%20be%20found%20in%20the%20pinned%20message%20at%20%40SpamProtectionLogs%20");
            }

            if($value->Chat->Type == \SpamProtection\Abstracts\TelegramChatType::Group)
            {
                print($value->PublicID . PHP_EOL);
                file_get_contents("https://api.telegram.org/bot869979136:AAEi_uxDobRLwhC0wF0TMfkqAoy8IC0fA-0/sendmessage?chat_id=" . $value->Chat->ID . "&text=Hi%20There%21%20This%20is%20a%20rare%20broadcast%21%0A%0A%40Intellivoid%20recently%20created%20a%20new%20bot%20called%20%40SpamProtectionBot%20which%20uses%20CoffeeHouse%20to%20detect%20and%20remove%20spam%21%20feel%20free%20to%20check%20it%20out%2C%20more%20information%20about%20this%20can%20be%20found%20in%20the%20pinned%20message%20at%20%40SpamProtectionLogs%20");
            }
        }
    }