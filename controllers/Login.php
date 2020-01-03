<?php
    namespace phpframework\controllers;

    use phpframework\Session\Session;
    use phpframework\models\mUsers;
    use phpframework\models\mUsersAuths;
    
    
    class Login extends Controllers 
    {

        function __construct()
        { }

        function __destruct()
        { }

        /**
         * Register. Method for create a new user
         */
        public function Register()
        {
            $entity = new mUsers();
            $entity->Map();
            $entity->id=0;

            if (empty($entity->email))
            {
                $this->ResponseJSON(null,400);
            }
            if (empty($_POST["pass"]))
            {
                $this->ResponseJSON(null,400);
            }

            $pwd =$this->EncryptPWD($_POST["pass"]);

            $entity->hash=$pwd["hash"];
            $entity->salt=$pwd["salt"];
            if (!$entity->Create()){
                $this->ResponseJSON(null,400);
            }

            $this->ResponseJSON($_POST);
        }


        /**
         * Login. Method for authenticate user and return tokens
         */
        public function Login()
        {
            $entity = new mUsers();
            $entity->Map();
            
            if (empty($entity->email))
            {
                $this->ResponseJSON(null,400);
            }

            if (empty($_POST["pass"]))
            {
                $this->ResponseJSON(null,400);
            }
            //Encriptamos la contraseña para obtener el hash y el salt
            $pwd =$this->EncryptPWD($_POST["pass"]);
            $entity->hash=$pwd["hash"];
            
            //Buscamos en BD mediante el hash y el email
            $result = $entity->Find();

            if (count($result)>0){
                $UserAuths=Session::StartSession($result[0]);
                $this->ResponseJSON(array(
                    "token"         => $UserAuths->token,
                    "token_renew" => $UserAuths->token_renew,
                    ""
                ));
            } else {
                $this->ResponseJSON(null,401);
            }
        }


    }