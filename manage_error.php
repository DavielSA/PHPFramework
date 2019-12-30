<?php
    if (DEBUG) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    set_error_handler(function($errno, $errstr, $errfile, $errline, $errcontext) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    });
    
    class HttpError {

        public static function CSS($file){
            header("Content-type: text/css");
            include($file);
        }

        public static function JS($file){
            header('Content-Type: application/javascript');
            include($file);
        }
        


        private static $_e404=_VIEW_."error/404.php";
        public static function e404() {
            include_once(self::$_e404);
        }

        private static $_e500=_VIEW_."error/500.php";
        public static function e500($e=''){
            ob_start();
            self::PrettyErrors($e);
            $Error = ob_get_clean();
            include_once(self::$_e500);
        }
    
        public static function e401($e=''){
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }

        private static function PrettyErrors($e='') {
            $args = func_get_args();

            $backtrace = debug_backtrace();
            $code = file($backtrace[0]['file']);    
        
            echo "<pre style='background: #eee; border: 1px solid #aaa; clear: both; overflow: auto; padding: 10px; text-align: left; margin-bottom: 5px'>";
        
            echo "<b>".htmlspecialchars(trim($code[$backtrace[0]['line']-1]))."</b>\n";
        
            echo "\n";
        
            ob_start();

                foreach ($args as $arg)
                    var_dump($arg);

                $str = ob_get_contents();

            ob_end_clean();

            $str = preg_replace('/=>(\s+)/', ' => ', $str);
            $str = preg_replace('/ => NULL/', ' &rarr; <b style="color: #000">NULL</b>', $str);
            $str = preg_replace('/}\n(\s+)\[/', "}\n\n".'$1[', $str);
            $str = preg_replace('/ (float|int)\((\-?[\d\.]+)\)/', " <span style='color: #888'>$1</span> <b style='color: brown'>$2</b>", $str);

            $str = preg_replace('/array\((\d+)\) {\s+}\n/', "<span style='color: #888'>array&bull;$1</span> <b style='color: brown'>[]</b>", $str);
            $str = preg_replace('/ string\((\d+)\) \"(.*)\"/', " <span style='color: #888'>str&bull;$1</span> <b style='color: brown'>'$2'</b>", $str);
            $str = preg_replace('/\[\"(.+)\"\] => /', "<span style='color: purple'>'$1'</span> &rarr; ", $str);
            $str = preg_replace('/object\((\S+)\)#(\d+) \((\d+)\) {/', "<span style='color: #888'>obj&bull;$2</span> <b style='color: #0C9136'>$1[$3]</b> {", $str);
            $str = str_replace("bool(false)", "<span style='color:#888'>bool&bull;</span><span style='color: red'>false</span>", $str);
            $str = str_replace("bool(true)", "<span style='color:#888'>bool&bull;</span><span style='color: green'>true</span>", $str);

            echo $str;   
            echo "</pre>";
            echo "<div class='block tiny_text' style='margin-left: 10px'>";
            echo "Sizes: ";
            foreach ($args as $k => $arg) {
                if ($k > 0)
                    echo ",";
                echo count($arg);
            }
            echo "</div>";
        }
        
    }