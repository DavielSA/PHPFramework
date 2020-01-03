<?php
    namespace phpframework\Controllers;
    use phpframework\HttpError\HttpResponse;

    class Controllers {

            function __construct() { }


            function __destruct() { }


            private function _jsonresponse($message = null, int $code = 200)
            {
                HttpResponse::SetJsonHeaders($code);
                // return the encoded json
                return json_encode(array(
                    'status' => $code < 300, // success or not?
                    'message' => $message
                ));
            }

            protected function ResponseJSON($data,int $code=200) 
            {
                echo $this->_jsonresponse($data,$code);
                die();
            }

            protected function ResponseVIEW($file,$data) 
            {
                include(_VIEW_."/".$file.".php");
            }


            protected function EncryptPWD(string $pass) 
            {
                $salt= password_hash($pass,PASSWORD_DEFAULT);
                $options = [
                    'cost' => 12 // the default cost is 10
                ];
                
                $hash = password_hash($salt.$pass, PASSWORD_BCRYPT, $options);
                return array(
                    "hash" => $hash,
                    "salt" => $salt
                );
            }
            
        }
