<?php


    namespace SpamProtection\Objects\TelegramObjects;
    
    
    class ChatMember
    {
        public $User;

        public $Status;

        public $CustomTitle;

        public $UntilDate;

        public $CanBeEdited;

        public $CanPostMessages;

        public $CanEditMessages;

        public $CanDeleteMessages;

        public $CanRestrictUsers;

        public $CanPromoteMembers;

        public $CanChangeInfo;

        public $CanInviteUsers;

        public $CanPinMessages;

        public $IsMember;

        public $CanSendMessages;

        public $CanSendMediaMessages;

        public $CanSendPolls;

        /**
         * 	Optional. Restricted only. True, if the user is allowed to send animations,
         * games, stickers and use inline bots
         *
         * @var bool
         */
        public $CanSendOtherMessages;

        /**
         * Optional. Restricted only. True, if the user is allowed to add web page previews to their messages
         *
         * @var bool
         */
        public $CanAddWebPagePreviews;
    }