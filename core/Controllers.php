<?php
    class Controllers {

        function __construct() { }


        function __destruct() { }

        protected function ResponseJSON($data) {
            header('Content-Type: application/json');
            echo(json_encode($data));
            exit;
        }

        protected function ResponseVIEW($file,$data) 
        {
            include(_VIEW_."/".$file.".php");
        }

    }
