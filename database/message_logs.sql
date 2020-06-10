create table if not exists spam_protection.message_logs
(
    id                      int(255) auto_increment comment 'Unique Internal Database ID of this message record',
    message_hash            varchar(256) null comment 'The unique message hash of the message structure',
    message_id              int(255)     null comment 'Unique message identifier inside this chat',
    chat_id                 int(255)     null comment 'The chat ID that this message was sent in',
    chat                    blob         null comment 'Zi Proto encoded chat data',
    user_id                 int(255)     null comment 'The ID of the user that sent this message',
    user                    blob         null comment 'ZiProto encoded user data',
    forward_from            blob         null comment 'Optional. For forwarded messages, sender of the original message (User ZiProto Blob)',
    forward_from_chat       blob         null comment 'Optional. For messages forwarded from channels, information about the original channel (Chat ZiProto Blob)',
    forward_from_message_id int(255)     null comment 'Optional. For messages forwarded from channels, identifier of the original message in the channel',
    content_hash            varchar(256) null comment 'The hash of the message content (either from Text or Caption)',
    spam_prediction         float        null comment 'The prediction value for the spam content',
    ham_prediction          float        null comment 'The prediction value for the ham content',
    timestamp               int(255)     null comment 'The Unix Timestamp for when this record was created',
    constraint message_logs_id_uindex unique (id),
    constraint message_logs_message_hash_uindex unique (message_hash)
) comment 'Table for storing message logs (hashes) and the prediction results';
alter table spam_protection.message_logs add primary key (id);