create table if not exists intellivoid.telegram_clients
(
    id            int auto_increment comment 'The unique internal database ID for this Telegram Client',
    public_id     varchar(255) null comment 'The unique Public ID for this Telegram Client',
    available     tinyint(1)   null comment 'Indicates if this Telegram Client is available',
    account_id    int          null comment '0 If a account is not associated with this Telegram Client',
    user          blob         null comment 'ZiProto encoded data for Telegram User Data',
    chat          blob         null comment 'ZiProto encoded data for Telegram Chat Data',
    session_data  blob         null comment 'ZiProto encoded data for Telegram Session Data',
    chat_id       varchar(255) null comment 'The chat ID associated with this Telegram Client',
    user_id       varchar(255) null comment 'The user ID associated with this Telegram Client',
    username      varchar(255) null comment 'Unique Username of the this Telegram client',
    last_activity int          null comment 'The Unix Timestamp of when this client was last active',
    created       int          null comment 'The Unix Timestamp of when this client was created and registered into the database',
    constraint telegram_clients_chat_id_user_id_uindex unique (chat_id, user_id),
    constraint telegram_clients_id_uindex unique (id),
    constraint telegram_clients_public_id_account_id_uindex unique (public_id, account_id),
    constraint telegram_clients_public_id_uindex unique (public_id),
    constraint telegram_clients_username_uindex unique (username)
) comment 'Table of Telegram Clients that were assocaited with a Telegram Bot' collate = utf8mb4_general_ci;

alter table intellivoid.telegram_clients add primary key (id);

