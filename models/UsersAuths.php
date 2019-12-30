<?php
   class mUsers extends BD
   {

       function __construct()
       {
           parent::__construct("phpframework", "users"); 
           $this->TableCreate = "
                CREATE TABLE IF NOT EXISTS `users_auth` (
                    `userid` int(11) NOT NULL,
                    `token_hash` varchar(200) NOT NULL,
                    `token` varchar(200) DEFAULT NULL,
                    `token_renew` varchar(200) DEFAULT NULL,
                    `fexpired` datetime DEFAULT NULL,
                    `fexpired_renew` datetime DEFAULT NULL,
                    `fcreated` datetime DEFAULT NULL,
                    `fupdate` datetime DEFAULT NULL,
                    PRIMARY KEY (`userid`,`token_hash`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
           ";
           
       }

       /**
        * pk
        * type-int
        */
       public $id;

        /**
        * pk
        * type-string-200
        */
       public $token_hash;

        /**
        * type-string-200
        */
       public $token;

        /**
        * type-string-200
        */
       public $token_renew;

        /**
        * type-datetime
        */
       public $fexpired;

        /**
        * type-datetime
        */
       public $fexpired_renew;
       
   }