<?php
    namespace phpframework\Session;

    use phpframework\JWT\Token;
    use phpframework\models\mUsers;
    use phpframework\models\mUsersAuths;
    

    class Session
    {
        private static $Data;

        /**
         * SetSession. Method for add entity to session vars
         * @param $Data Array. Object class entity with data to add session.
         */
        public static function SetSession(array $Data=[])
        {
            if (!isset($_SESSION))
                session_start();

            $Data = COUNT($Data) < 1 
                ? self::$Data 
                : $Data;

            if (COUNT($Data)<1)
                $Data = [];
            
            foreach($Data As $key=>$val) {
                $_SESSION[$key]=$val;
            }

        }


        public static function ValidSessionWithToken(string $token) 
        {
            $result = Token::DecodeToken($token);
            var_dump($token);
            var_dump($result);
            die();
            $EntityToken = new mUsersAuths();
          
            $EntityToken->token = $token;

        }


        /**
         * StartSession. Mediante mUsers generamos los tokens 
         * @param $Data array. mUsers. Datos para generar el token
         * @return mUsersAuths. Retornamos el objeto creado en BD de los tokens
         */
        public static function StartSession(array $Data) : mUsersAuths
        {
            self::$Data= $Data;
            $UserAuths = new mUsersAuths();
            $UserAuths->userId = $Data->id;
            $UserAuths->role = $Data->role;
            $UserAuths->token = Token::Encode($Data);
            $UserAuths->token_renew = Token::EncodeRenew($Data);
            $UserAuths->exp = time()+_TOKENEXPIREDTIME_;
            $UserAuths->fexpired_renew = time()+_TOKENEXPIREDTIMERENEW_;

            if ($UserAuths->Create()) {
                self::SetSession();
            }
            return $UserAuths;
        }


        /**
         * GetSession. Obtenemos la session actual.
         * @param $Token string optional. 
         * @return mUsers. Entidad que revolvemos
         */
        public static function GetSession(string $Token="") : mUsers
        {
            $User = new mUsers();
            if (isset($_SESSION))
            {
                foreach($_SESSION As $key => $val)
                {
                    if ($User->{$key})
                    {
                        $User->{$key} = $val;
                    }
                }
            } else 
            {
                $User = null;
            }
            return $User;    
        }

        

    }