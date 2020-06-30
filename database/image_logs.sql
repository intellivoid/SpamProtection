create table if not exists spam_protection.image_logs
(
    id                      int(255) auto_increment comment 'The unique internal database ID of this record',
    message_hash            varchar(256) null comment 'The hash of the message',
    message_id              int(255)     null comment 'The ID of the message sent in the chat',
    photo_size              blob         null comment 'ZiProto Encoded data of the photo size',
    chat_id                 int(255)     null comment 'The ID of the chat this image was sent in',
    chat                    blob         null comment 'ZiProto Encoded data of the chat',
    user_id                 int(255)     null comment 'The user ID of who sent this image',
    user                    blob         null comment 'ZiProto Encoded data of the user',
    forward_from            blob         null comment 'ZiProto encoded data of the original sender of this image',
    forward_from_chat       blob         null comment 'ZiProto Encoded data of the chat (channel) that this image is originally from',
    forward_from_message_id int(255)     null comment 'The original message ID',
    content_hash            varchar(256) null comment 'The SHA256 hash of the image contents',
    spam_prediction         float        null comment 'The spam prediction of the image',
    ham_prediction          float        null comment 'The ham prediction of the image',
    timestamp               int(255)     null comment 'The Unix Timestamp of when this record was created',
    constraint image_logs_content_hash_uindex unique (content_hash),
    constraint image_logs_id_uindex unique (id),
    constraint image_logs_message_hash_uindex unique (message_hash),
    constraint image_logs_message_id_chat_id_uindex unique (message_id, chat_id),
    constraint image_logs_message_id_chat_id_user_id_uindex unique (message_id, chat_id, user_id)
) comment 'Table of image hashes';

alter table spam_protection.image_logs add primary key (id);