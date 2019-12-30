<?php

    class Home extends Controllers {

        function __construct() { }

        function __destruct() { }

        
        public function Get() {
            $this->ResponseVIEW("index",array());
        }

    }