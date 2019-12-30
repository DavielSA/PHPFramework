<?php
    class RouterClass
    {
        /**
         * Holds the registered routes
         * 
         * @var array $routes
         */
        private static $routes=[];
        private static $AllowedVerb=["get","post","put","patch","delete"];
        private static $Styles=[];
        private static $JavaScript=[];

        function __construct() 
        { }
        
        /**
         * Register a new route
         * 
         * @param $action string
         * @param $verb string 
         * @param $controler string Class of controller
         * @param $method string method of controller to execute
         */
        public static function Add(string $action,string $verb, string $controler,string $method)
        {
            if (!in_array(strtolower($verb),self::$AllowedVerb)){
                throw new Exception("Method not alowed");
            }

            $action=trim($action,'/');           
            self::$routes[$action] = new _Routers_($action,$verb,$controler,$method);
        }

        /**
         * Register a new route
         * 
         * @param $action string
         * @param $verb string 
         * @param $controler string Class of controller
         * @param $method string method of controller to execute
         * @param $role int optional role id
         */
        public static function AddAuth(string $action,string $verb, string $controler,string $method,int $role=0)
        {
            if (!in_array(strtolower($verb),self::$AllowedVerb)){
                throw new Exception("Method not alowed");
            }

            $action=trim($action,'/');           
            self::$routes[$action] = new _Routers_($action,$verb,$controler,$method,true,$role);
        }


        /**
         * Register CSS style
         * 
         * @param $name   string Name of view styles
         * @param $path   string Relative path to declare styles
         */
        public static function AddStyle(string $name,string $path) 
        {
            if (!array_key_exists($name,self::$Styles)) 
                self::$Styles[$name]=[];
            
            $path = '<link rel="stylesheet" href="'.$path.'" />';
            
            array_push(self::$Styles[$name],$path);
        }

        /**
         * Register JavaScript files
         * 
         * @param $name     string Name of view JavaScript
         * @param $path string Relative path to declare JavaScript files
         */
        public static function AddScript(string $name,string $path)
        {
            if (!array_key_exists($name,self::$JavaScript)) 
                self::$JavaScript[$name]=[];
           
            $path = '<script src="'.$path.'"></script>';
            
            array_push(self::$JavaScript[$name],$path);
        }

        /**
         * Include Style stored in server
         * 
         * @param $name string Name of groups Style
         */
        public static function Style($name) 
        {
            if (!array_key_exists($name,self::$Styles)) 
                self::$Styles[$name]=[];
            foreach (self::$Styles[$name] as $style) {
                echo $style;
            }
        }

        /**
         * Include javascript files stored in server
         * 
         * @param $name string Name of groups JavaScript files
         */
        public static function JS($name) 
        {
            if (!array_key_exists($name,self::$JavaScript)) 
                self::$JavaScript[$name]=[];
            foreach (self::$JavaScript[$name] as $js) {
                echo $js;
            }
        }

        /**
         * Dispatch the router
         */
        public static function Dispatch()
        {
            try {

                $action=$_SERVER['REQUEST_URI'];
                $verb = strtolower($_SERVER['REQUEST_METHOD']);

                $IsStyle = strpos($action,".css");
                $IsJS = strpos($action,".js");

                if ($IsStyle>0 ){
                    HttpError::CSS($action);
                    exit();
                }
                if ($IsJS>0){
                    HttpError::JS($action);
                    exit();
                }

                $action = trim($action,'/');

                if ($action=="/" || $action=="index.php") 
                    $action="";

                $ElementExistNonSecure = (
                    array_key_exists($action,self::$routes) 
                    && self::$routes[$action]->Verb == $verb
                    && !self::$routes[$action]->Secure
                );

                $ElementExistSecure =  (
                    array_key_exists($action,self::$routes) 
                    && self::$routes[$action]->Verb == $verb
                    && self::$routes[$action]->Secure 
                );

                if ( $ElementExistNonSecure ) {
                    self::RunMethod(self::$routes[$action]->Controller,self::$routes[$action]->Method);
                } else if ( $ElementExistSecure ) {
                    $Headers = apache_request_headers();
                    $Token = $Headers['Authorization'];
                    if (isset($Token)){
                        HttpError::e401();
                    } else {
                        self::RunMethod(self::$routes[$action]->Controller,self::$routes[$action]->Method);
                    }               
                } else {
                    HttpError::e404();   
                }
            } catch(Exception $e){
                throw new Exception($e);
            }
        }

        private static function RunMethod($class,$method) 
        {
            $Reflect=null;
            $Instance=null;
            if(version_compare(PHP_VERSION, '5.6.0', '>=')){
                $Instance = new $class();
                $Instance->$method();
            } else {
                $Reflect  = new ReflectionClass($class);
                $Instance = $Reflect->newInstanceArgs();
                $Instance->$method();
            }
        }
    }


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