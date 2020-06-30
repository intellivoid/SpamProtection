create table if not exists spam_protection.image_logs
(
    id                      int(255) auto_increment comment 'The unique internal database ID of this record',
    message_hash            varchar(256) null comment 'The hash of the message',
    message_id              int(255)     null comment 'The ID of the message sent in the chat',
    file_id                 varchar(256) null comment 'Identifier for this file, which can be used to download or reuse the file',
    file_unique_id          varchar(256) null comment 'Unique identifier for this file, which is supposed to be the same over time and for different bots. Can''t be used to download or reuse the file.',
    file_size               int(255)     null comment 'Optional. File size',
    chat_id                 int(255)     null comment 'The ID of the chat this image was sent in',
    chat                    blob         null comment 'ZiProto Encoded data of the chat',
    user_id                 int(255)     null comment 'The user ID of who sent this image',
    user                    blob         null comment 'ZiProto Encoded data of the user',
    forward_from            blob         null comment 'ZiProto encoded data of the original sender of this image',
    forward_from_chat       blob         null comment 'ZiProto Encoded data of the chat (channel) that this image is originally from',
    forward_from_message_id int(255)     null comment 'The original message ID',
    content_hash            varchar(256) null comment 'The SHA256 hash of the image contents',
    width                   int(255)     null comment 'Photo width',
    height                  int(255)     null comment 'Photo height',
    spam_prediction         float        null comment 'The spam prediction of the image',
    ham_prediction          float        null comment 'The ham prediction of the image',
    timestamp               int(255)     null comment 'The Unix Timestamp of when this record was created',
    constraint image_logs_file_unique_id_uindex unique (file_unique_id),
    constraint image_logs_id_uindex unique (id),
    constraint image_logs_content_hash_uindex unique (content_hash),
    constraint image_logs_message_hash_uindex unique (message_hash)
) comment 'Table of image hashes';

alter table spam_protection.image_logs add primary key (id);

