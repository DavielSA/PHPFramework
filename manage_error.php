<?php
    namespace phpframework\HttpError;
    use ErrorException;
        
    if (DEBUG) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    set_error_handler(function($errno, $errstr, $errfile, $errline, $errcontext) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    });
    
    class HttpResponse 
    {
        private static $status=array(
            200 => '200 OK',
            400 => '400 Bad Request',
            401 => '401 Unauthorized',
            404 => '404 Not Found',
            422 => 'Unprocessable Entity',
            500 => '500 Internal Server Error'
        );

        /**
         * Headers. method generics for add headers.
         * 
         * @param $code int. Number of code error http
         * @param $pragma string. ContentType of headers
         */
        private static function Headers(int $code,string $pragma) : void
        {
            // clear the old headers
            header_remove();
            // set the actual code
            http_response_code($code);
            // set the header to make sure cache is forced
            header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
            
            if (!empty($pragma))
                header($pragma);

            // ok, validation error, or failure
            header('Status: '.self::$status[$code]);
        }

        /**
         * IsAjax.  Detect origin request. If is ajax returna true or false
         * @return bool
         */
        public static function IsAjax() : bool
        {
            if( 
                !empty($_SERVER['HTTP_CONTENT_TYPE']) 
                && strpos(strtolower($_SERVER['HTTP_CONTENT_TYPE']),'json')
            ) 
            {
                return true;
            }
            return false;
        }

        /**
         * setJsonHeaders. method for add headers.
         * 
         * @param $code int. Number of code error http
         */
        public static function SetJsonHeaders(int $code) :void
        {
            self::Headers($code,'Content-Type: application/json');
        }

        /**
         * SetHTMLHeaders. method for add headers.
         * 
         * @param $code int. Number of code error http
         */
        public static function SetHTMLHeaders(int $code) :void
        {
            self::Headers($code,'Content-Type: text/html; charset=utf-8');
        }

        /**
         * SetAnyHeaders. method for add headers.
         * 
         * @param $code int. Number of code error http
         */
        public static function SetAnyHeaders(int $code):void
        {
            self::Headers($code,'');
        }

        /**
         * SetJsHeaders. method for add headers.
         * 
         * @param $code int. Number of code error http
         */
        public static function SetJsHeaders(int $code):void
        {
            self::Headers($code,'Content-Type: application/javascript');
        }

        /**
         * SetCSsHeaders. method for add headers.
         * 
         * @param $code int. Number of code error http
         */
        public static function SetCSsHeaders(int $code):void
        {
            self::Headers($code,"Content-type: text/css");
        }

    }

    class HttpError {
        
        private static $_e404=_VIEW_."error/404.php";
        
        /**
        * e404. This method set header for not found url or file.
        * 
        */
        public static function e404():void 
        {
            if (HttpResponse::IsAjax()){
                HttpResponse::SetJsonHeaders(404);
                echo json_encode(array(
                    'status' => false, // success or not?
                    'message' => "404 Not Found"
                ));
            } else {
                HttpResponse::SetHTMLHeaders(404);
                include_once(self::$_e404);
            }
        }

        private static $_e500=_VIEW_."error/500.php";

        /**
        * e500. This method set header of error 500.
        * @param $e any optional.
        */
        public static function e500($e='') : void
        {
            ob_start();
            self::PrettyErrors($e);
            $Error = ob_get_clean();
            if (HttpResponse::IsAjax()) {
                $Error=str_replace("\n","<br>",$Error);
                HttpResponse::SetJsonHeaders(500);
                echo json_encode(array(
                    'status' => false, // success or not?
                    'message' => $Error
                ));
            } else {
                HttpResponse::SetHTMLHeaders(500);
                include_once(self::$_e500);
            }            
        }
    
        /**
         * e401. This method set header of unautorized user.
         * @param $e any optional.
         */
        public static function e401($e='') : void
        {
            if ( HttpResponse::IsAjax())
                HttpResponse::SetJsonHeaders(401);
            else 
                HttpResponse::SetHTMLHeaders(401);
            exit;
        }

        /**
         * Method for print pretty errors.
         * @param $e any optional. Exception to parser
         */
        private static function PrettyErrors($e='') : void
        {
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
                    
                if (is_array($arg)) 
                    echo count($arg);
            }
            echo "</div>";
        }
        
    }