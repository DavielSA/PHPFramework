<?php
    namespace phpframework\core;
    
    class Logs {

        private $File;
		private $Name;
		private $PathLogs;
		private $fp;
        function __construct($file,$name)
        {
            if (empty($file)) {
                $file =__FILE__;
            }
            $this->File = $file;
			$this->Name = empty($name) ? "log.log" : $name.".log";
			$this->PathLogs = realpath( dirname(__FILE__))."/logs";
			
			if (!file_exists($this->PathLogs)){
				mkdir($this->PathLogs, 0777, true);
			}
			$this->PathLogs = $this->PathLogs ."/".$name.".log";
			$this->fp = fopen($this->PathLogs,"wb");
			fwrite($this->fp,$this->WriteText("Info",""));
        }
		
		function __destructor() {
			fclose($this->fp);
		}

        private function WriteText($sType, $obj) {
			ob_start();
			var_dump($obj);
			$result = ob_get_clean();
			$msg = "[".date("Y-m-d H:i:s")."] [".$sType."] [".$this->File."] ". $result;
			fwrite($this->fp,$msg);
        }

        public function Info($obj){
            $this->WriteText("Info",$obj);
        }
        
        public function Warning($obj){
            $this->WriteText("Warning",$obj);
        }

        public function Error($obj){
            $this->WriteText("Error",$obj);
        }

    }
