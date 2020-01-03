<?php
    namespace phpframework\Routers;
    class _Routers_ {
            
        public $Action;
        public $Verb;
        public $Controller;
        public $Method;
        public $Secure;
        public $Role;

        /**
         * Register a new route
         * 
         * @param $action string
         * @param $verb string 
         * @param $controler string Class of controller
         * @param $method string method of controller to execute
         */
        public function __construct(string $action,string $verb, string $controler, string $method,bool $secure=false, int $role=0)
        {
            $this->Action       = $action;
            $this->Verb         = strtolower($verb);
            $this->Controller   = $controler;
            $this->Method       = $method;
            $this->Secure       = $secure;
            $this->Role         = $role;
            /**
             * auth_token
             *  token - hash - token_expired - expiredTime - createdToken
             * 0 => Invitado
             * 3 => Usuario Basico
             * 4 => Jefe departamento
             * 6 => Administración
             * 7 => Root
             */
        }

        
    }