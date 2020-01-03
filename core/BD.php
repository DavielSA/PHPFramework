<?php
    namespace phpframework\models;

    use ReflectionProperty;
    use ReflectionClass;
    use PDO;
    use PDO_PARAM_TYPE;
    use Exception;
    use DateTime;

    use phpframework\core\Logs;

    class BD
    {
        private $con;
        private $HOST = "localhost";
        private $USER = "phpframework";
        private $PASS = "km25Ds29o1";
        protected $DATABASE = "";
        protected $TABLE = "";
        protected $DT = "";
        protected $Log;
        protected $TableCreate="";

        public $fcreated;
        public $fupdated;

        /**
         * Constructor de la clase de manejo de BD
         * 
         * @param $database string Base de datos a la cual haremos las peticiones.
         * @param $table    string Tabla que usaremos para las peticiones CRUD
         */
        function __construct(string $database, string $table)
        {
            $this->initDataBase($database,$table);
            $this->Log = new Logs(__FILE__,"BD");

        }

        /**
         * initDataBase -> Inicializamos la configuración de la BD
         * 
         * @param $database string Base de datos a la cual haremos las peticiones.
         * @param $table    string Tabla que usaremos para las peticiones CRUD
         */
        private function initDataBase(string $database,string $table)
        {
            $this->DATABASE = $database;
            $this->TABLE = $table;
            $this->DT = $database . "." . $table;
            $dsn = "mysql:dbname={$this->DATABASE};host={$this->HOST}";
            $options = [
                PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
                PDO::MYSQL_ATTR_FOUND_ROWS   => true, //Show total rows affected 
            ];
            
            $this->con = new PDO($dsn, $this->USER, $this->PASS,$options);
            $this->con->exec("set names utf8");
        }

        private function __destructor()
        {
            $this->con=null;
        }

        /**
         * Map. Mapeamos a la entidad los datos obtenidos por cualquier verbo http
         */
        public function Map(){
            $Data = [];
            $method = $_SERVER['REQUEST_METHOD'];
            switch(strtolower($method)){
                case "get":
                    if (empty($_GET)) 
                        $_GET = json_decode(file_get_contents("php://input"), true) ? : [];
                    $Data=$_GET;
                break;
                case "post":
                    if (empty($_POST)) 
                        $_POST = json_decode(file_get_contents("php://input"), true) ? : [];
                    
                    $Data = $_POST;
                break;
                default:
                    $Data=file_get_contents('php://input');
                break;
            }

            if (COUNT($Data)<1)
                exit;
            
            $reflect = new ReflectionClass($this);
            $props =  array_map(
                function($u) { return $u->name; }
                ,$reflect->getProperties(ReflectionProperty::IS_PUBLIC)
            );
            
            foreach($Data As $key=>$val) {
                $dec = in_array($key,$props,true);
                if ($dec) {
                    $this->{$key}=$val;
                }
            }
        }

        /**
         * Metodo para traer los registros de BD. 
         * @param $entity Entidad que vamos a buscar. Si esta está vacía busca todo en BD.
         */
        public function Find()
        {
            $ValueReturn = [];
            try {
                //Mapeamos la entidad
                $Campos = $this->GetFields();

                //Filtramos los datos por aquellos que tengan valor
                $Campos = array_filter($Campos, function ($v) {
                    return isset($v->Value) && !empty($v->Value);
                });

                //Obtenemos los campos
                $Where = implode("AND", array_map(function ($u) {
                    return $u->Key . "=:".$u->Key;
                }, $Campos));

                //Obtenemos los valores
                $items=$this->MakeArguments($Campos);

                $query = "SELECT * FROM " . $this->DT;
                if (count($Campos) > 0) {
                    //Si tenemos información añadimos el where.
                    $query .=  " WHERE " . $Where;
                }

                //Asignamos los argumentos
                $ValueReturn = $this->Select($query, $items);
                $ValueReturn = $this->MapResults($ValueReturn);
            } catch (Exception $e) {
                $this->Log->Error($e);
                //Logs
            }
            return $ValueReturn;
        }

        /**
         * Metodo para traer los registros de BD mediante los campos primarios definidos. 
         * @param $entity Entidad que vamos a buscar. Si esta está vacía busca todo en BD.
         */
        public function FindByPK()
        {
            $ValueReturn = [];
            try {

                //Mapeamos la entidad
                $Campos = $this->GetFields();

                //Filtramos los datos por aquellos que tengan valor
                $Campos = array_filter($Campos, function ($v) {
                    return $v->PK && isset($v->Value) && !empty($v->Value);
                });

                //Obtenemos los campos
                $Where = implode("AND", array_map(function ($u) {
                    return $u->Key . "=:" . $u->Key;
                }, $Campos));

                //Obtenemos los valores
                $items=$this->MakeArguments($Campos);

                $query = "SELECT * FROM " . $this->DT;
                if (count($Campos) > 0) {
                    //Si tenemos información añadimos el where.
                    $query .=  " WHERE " . $Where;
                } else {
                    return  $ValueReturn;
                }

                //Asignamos los argumentos
                $ValueReturn = $this->Select($query, $items);
                $ValueReturn = $this->MapResults($ValueReturn);
            } catch (Exception $e) {
                $this->Log->Error($e);
            }
            return $ValueReturn;
        }

        /**
         * Metodo para hacer la actualización de los registros
         */
        public function Save()
        {
            $ValueReturn = false;
            try {
                $this->fupdated  = new DateTime();
                //Mapeamos la entidad
                $Campos = $this->GetFields();

                //Filtramos los datos por los primarykey
                $FieldPK = array_filter($Campos, function ($v) {
                    return $v->PK;
                });
                //Quitamos los que no son primarykey
                $FieldLast = array_filter($Campos, function ($v) {
                    return !$v->PK;
                });

                //Generamos el WHERE
                $Where = implode("AND", array_map(function ($u) {
                    return $u->Key . "=:" .$u->Key;
                }, $FieldPK));

                //Generamos los setters
                $Setters = " set " . implode(",", array_map(function ($u) {
                    return $u->Key . "=:" . $u->Key;
                }, $FieldLast));

                //Obtenemos los valores
                $items = $this->MakeArguments(array_merge($FieldLast,$FieldPK));

                $query = "UPDATE " . $this->DT . " " . $Setters;
                if (count($FieldPK) > 0) {
                    $query .=  " WHERE " . $Where;
                }

                //Mapeamos los argumentos
                
                try {
                    $ValueReturn = $this->InsertUpdateDelete($query, $items) > 0 ? true : false;
                } catch (Exception $e) {
                    $this->Log->Error($e);
                    $ValueReturn = false;
                }
            } catch (Exception $e) {
                //Logs
            }
            return $ValueReturn;
        }

        /**
         * Metodo para insertar en BD
         * @param $entity Entidad que vamos a insertar en BD
         */
        public function Create()
        {
            $ValueReturn = NULL;
            try {
                
                $this->fcreated = new DateTime();
                $this->fupdated = new DateTime();

                //Mapeamos la entidad
                $Campos = $this->GetFields();

                //Obtenemos los campos            
                $fields = implode(",", array_map(function($u) {
                    return $u->Key;
                }, $Campos));
            
                //Obtenemos la cantidad de ?
                $values = implode(",", array_map(function($u) {
                    return ":".$u->Key;
                }, $Campos));

                //Obtenemos los values
                $items=$this->MakeArguments($Campos);


                //Hacemos la consulta SQL
                $query = " INSERT INTO " . $this->DT . "(" . $fields . ") VALUES (" . $values . ");";
                $ValueReturn = $this->InsertUpdateDelete($query, $items) > 0 ? true : false;
            } catch (Exception $e) {
                $this->Log->Error($e);
                $ValueReturn = false;
            }

            return $ValueReturn;
        }

        /**
         * Crear o Actualizar un elemento
         */
        public function CreateOrUpdate() {
            $element = $this->FindByPK();
            if ( count($element) > 0 ) {
                return $this->Save();
            } else {
                return $this->Create();
            }
        }

        /**
         * Metodo para ejecutar consultas CRUD
         * @param $query Consulta SQL a ejecutar.
         * @param $params Array de valores a buscar (where, inner, left...)
         */
        public function SelectCrud($query, $params) {
            $ValueReturn = [];
            try {
                //Asignamos los argumentos
                $ValueReturn= $this->Select($query, $params);
                
            } catch (Exception $e){
                $this->Log->Error($e);
            }
            return $ValueReturn;
        }

        /**
         * Preparamos de forma dinámica los datos que irán a la consulta sql
         * @param $query Consulta sql
         * @param $param Array con los datos a bindear
         */
        private function Select($query, $param)
        {
            $data=[];
            try {
                $stmt = $this->con->prepare($query);
                foreach ($param as $key=>$item) 
                    $stmt->bindParam($key,$item["val"],$item["type"]);
                $stmt->execute();
                $data = $stmt->fetchAll();
                $stmt=null;
            } catch (Exception $e) {
                $this->Log->Error($e);
            }
            return $data;
        }
    
        /**
         * Metodo para insertar
         */
        private function InsertUpdateDelete($query, $param) {
            try {
                $stmt = $this->con->prepare($query);
                foreach ($param as $key=>$item) 
                    $stmt->bindParam($key,$item["val"],$item["type"]);
                
                $stmt->execute();
                $RowAffected = $stmt->rowCount();
                $stmt = null;
                return $RowAffected;  
            } catch(Exception $e){
                $this->Log->Error($param);
                $this->Log->Error($e->getMessage());
            }

        }

        /**
         * Generamos los argumentos que PDO mapeara. en formato => [:key]=`valor`
         */
        private function MakeArguments($Campos){
            $items=[];
            try {
                array_walk($Campos,function ($u,$key) use (&$items) {
                    $items[":".$u->Key]=array(
                        "val"   => FIELDS_FORMAT::Format($u->Format,$u->Value),
                        "type"  => FIELDS_FORMAT::getType($u->Format)
                    );
                });
            } catch (Exception $e) {
                $this->Log->Error($e);
            }

            return $items;
        }
        
        /**
         * Mapeamos la entidad 
         */
        private function GetFields()
        {
            $reflect = new ReflectionClass($this);
            $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
            
            $fields = array_map(function ($v) {
                $dec = new ReflectionProperty(get_class($this), $v->name);
                $decorator = $dec->getDocComment();
                $type = explode("-", strstr($decorator, "type"));
                $field = new FIELDS();
                $field->Key = $v->name;
                $field->Value = $this->{$v->name};
                $field->PK = strpos($decorator, FIELDS_TYPES::PK) !== false ? true : false;
                $field->Unique = strpos($decorator, FIELDS_TYPES::UN) !== false ? true : false;
                $field->Format = array_key_exists(1,$type) ? $type[1] : "string";
                return $field;
            }, $props);
            return $fields;
        }
           
        
        /**
         * Convert result query in array of this entity
         * @param $Data array. Rows returned on query
         * @return Array of this entity
         */
        private function MapResults($Data)
        {
            $ValueReturn = array();

            foreach($Data As $item) 
            {
                $class=get_class($this);
                $Instance = new $class();
                foreach($item As $key => $val)
                {
                    $dec = new ReflectionProperty(get_class($this), $key);
                    if ($dec) {
                        $Instance->{$key}=$val;
                    }
                }               
                array_push($ValueReturn,$Instance);
            }
            return $ValueReturn;
        }


    }

    class FIELDS
    {
        public $Key;
        public $Value;
        public $PK;
        public $Unique;
        public $Format;
        public $IsValid;
    }

    class FIELDS_TYPES
    {
        const PK = "pk";
        const UN = "unique";
    }

    class FIELDS_FORMAT 
    {
        private const Types=array(
            "boolean"	=> 	PDO::PARAM_BOOL,
            "byte"		=> 	PDO::PARAM_INT,
            "short"		=>	PDO::PARAM_INT,
            "int"       =>	PDO::PARAM_INT,
            "long"      =>  PDO::PARAM_STR,
            "float"     =>	PDO::PARAM_STR,
            "double"    =>  PDO::PARAM_STR,
            "string"    =>  PDO::PARAM_STR,
            "ip"        =>  PDO::PARAM_STR,
            "timestamp" =>  PDO::PARAM_STR,
            "datetime"  =>  PDO::PARAM_STR,
            "date"      =>  PDO::PARAM_STR,
            "time"      =>  PDO::PARAM_STR,
            "auto"      =>  PDO::PARAM_NULL
        );

        static private function verifyDate($date)
        {
            return (DateTime::createFromFormat('m/d/Y', $date) !== false);
        }

        public static function getType($type) 
        {
            if ( is_string($type) && strpos($type,"type-") )
                $type=explode("-", strstr($type, "type"));
            if (is_array($type) && isset($type[1])) 
                $type=$type[1];

            return  array_key_exists($type,self::Types)
                    ? self::Types[$type] 
                    : PDO::PARAM_STR;
        }

        public static function Format($val,$format) 
        {
            if ( $format=="timestamp" )
                return strtotime($val);
            if ($format=="datetime")
                return self::DateTime($val);
            if ($format=="date") 
                return self::Date($val);
            if ($format=="time")
                return self::Time($val);
            if ($format=="int")
                return self::Ints($val);
            if ($format=="auto")
                return null;
                
            return $val;
        }

        public static function Ints($val)
        {
            return !empty($val) && is_int($val) 
                   ? (int)$val 
                   : 0;
        }

        public static function Time($val)
        {
            return self::verifyDate($val) 
                    ? $val->format('H:i:s') 
                    : (new DateTime($val))->format('H:i:s'); 
        }

        public static function Date($val)
        {
            return self::verifyDate($val) 
                    ? $val->format('Y-m-d') 
                    : (new DateTime($val))->format('Y-m-d');
        }
        public static function DateTime($val)
        {
            return self::verifyDate($val) 
                    ? $val->format('Y-m-d H:i:s') 
                    : (new DateTime($val))->format('Y-m-d H:i:s');
        }

        public static function TimeStamp($val)
        {
            return self::verifyDate($val) 
                    ? strtotime($val) 
                    : strtotime(new DateTime($val));
        }
        
        
    }



