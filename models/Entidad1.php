<?php
    namespace phpframework\models;
        
    class Entidad1 extends BD
    {

        function __construct()
        {
            parent::__construct("ccshop", "demo");
        }

        /**
        * pk
        * type-int
        */
        public $id;
        public $nombre;
        public $apellido;
    }