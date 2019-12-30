<?php
    
    class BD
    {
        private $con;
        private $HOST = "localhost";
        private $USER = "root";
        private $PASS = "proenium";
        protected $DATABASE = "";
        protected $TABLE = "";
        protected $DT = "";
        protected $Log;
        protected $TableCreate="";

        public $fcreated;
        public $fupdated;

        /**
         * Constructor de la clase de manejo de BD
         * @param $database Database a la cual haremos las peticiones.
         * @param $table tabla que usaremos para las peticiones CRUD.
         */
        function __construct($database, $table)
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
            $this->Log = new Logs(__FILE__,"BD");

        }

        private function __destructor()
        {
            $this->con=null;
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
                $this->fupdate  = new DateTime();
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
                $this->fupdate  = new DateTime();

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
                $stmt->execute($param);
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
                $stmt->execute($param);
                $RowAffected = $stmt->rowCount();
                $stmt = null;
                return $RowAffected;  
            } catch(Exception $e){
                $this->Log->Error($e);
            }

        }

        /**
         * Generamos los argumentos que PDO mapeara. en formato => [:key]=`valor`
         */
        private function MakeArguments($Campos){
            $items=[];
            try {
                array_walk($Campos,function ($u,$key) use (&$items) {
                    $items[":".$u->Key]=$u->Value;
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
                $field->Format = isset($type[1]) ? $type[1] : "text";
                return $field;
            }, $props);
            return $fields;
        }
    

    }

    class FIELDS
    {
        public $Key;
        public $Value;
        public $PK;
        public $Unique;
        public $Format;
    }

    class FIELDS_TYPES
    {
        const PK = "pk";
        const UN = "unique";
    }

    class FIELDS_FORMAT
    {
        const VARCHAR = "varchar";
        public static function ValidVarchar($txt, $length)
        {
        }
    }



