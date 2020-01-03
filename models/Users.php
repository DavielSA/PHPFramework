<?php
    namespace phpframework\models;

    class mUsers extends BD
    {

        function __construct()
        {
            parent::__construct("phpframework", "users"); 
            $this->TableCreate = "
                CREATE TABLE IF NOT EXISTS `users` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `email` varchar(250) DEFAULT NULL,
                    `name` varchar(250) DEFAULT NULL,
                    `surnames` varchar(500) DEFAULT NULL,
                    `role` int(11) DEFAULT NULL,
                    `profile` text,
                    `hash` varchar(100) DEFAULT NULL,
                    `salt` varchar(100) DEFAULT NULL,
                    `conditions` tinyint(4) DEFAULT NULL,
                    `fcreated` datetime DEFAULT NULL,
                    `fupdate` datetime DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `email_UNIQUE` (`email`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
            ";
            
        }

        /**
        * pk
        * type-auto
        */
        public $id;

        /**
        * unique
        * type-string-250
        */
        public $email;

        /**
        * type-string-250
        */
        public $name;

        /**
        * type-string-500
        */
        public $surnames;

        /**
        * type-int
        */
        public $role=0;

        /**
        * type-string-800
        */
        public $profile;

        /**
        * type-string-100
        */
        public $hash;

        /**
        * type-string-100
        */
        public $salt;

        /**
        * type-boolean
        */
        public $conditions;
    }