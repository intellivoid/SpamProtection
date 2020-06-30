<?php


    include_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'SpamProtection' . DIRECTORY_SEPARATOR . 'SpamProtection.php');

    var_dump(\SpamProtection\Utilities\Hashing::hashRemoteFile(
        "https://images.unsplash.com/photo-1506744038136-46273834b3fb?ixlib=rb-1.2.1&w=1000&q=80"));