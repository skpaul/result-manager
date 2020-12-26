
<?php 
   
    //The prefix "Swift" is used to avoid conflict with other namespaces.
    class SwiftLogger
    {

        private $rootDirectory = "";

        //sub-directory is needed to prevent guess the log file path from hacker.
        //DON'T USE UNDERSCORE BEFORE A FOLDER NAME. .gitignore does not work with underscore prefix.
        private $subDirectory = "";

        private $logFileName = "error_logs.log";
        private $logFilePath = "";

        //Constructor this class.
        //If user provides values in this, it will call connect() method.
        //Otherwise, user have to call connect() method by himself.
        public function __construct($rootDirectoryPath, $subDirectoryName = null) {

            $this->rootDirectory = $rootDirectoryPath;

            if(isset($subDirectoryName)){
                $this->subDirectory = $subDirectoryName;
                if (!file_exists($this->rootDirectory . "/" . $this->subDirectory)) {
                    mkdir($this->rootDirectory . "/" . $this->subDirectory, 0777, true);
                }
    
                $this->logFilePath = $this->rootDirectory . "/" . $this->subDirectory . "/" . $this->logFileName;
        
                if(!file_exists($this->logFilePath)){
                    $handle = fopen($this->logFilePath, 'w') or die("Can't create file");
                    fclose($handle);
                }
            }
            else{
    
                $this->logFilePath = $this->rootDirectory . "/" . $this->logFileName;
        
                if(!file_exists($this->logFilePath)){
                    $handle = fopen($this->logFilePath, 'w') or die("Can't create file");
                    fclose($handle);
                }
            }

            error_reporting( E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED); 
            //error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

            ini_set('log_errors', '1'); 
           // ini_set('display_errors', 1); //show errors in response stream.

            ini_set('error_log', $this->logFilePath);

            set_error_handler( function($errno, $errstr, $errfile, $errline){
                if (!(error_reporting() & $errno)) {
                    // This error code is not included in error_reporting, so let it fall
                    // through to the standard PHP error handler
                    return false;
                }
    
                // $errstr may need to be escaped:
                $errstr = htmlspecialchars($errstr);
    
                switch ($errno) {
                    case E_ERROR:
                    case E_WARNING:
                    case E_PARSE:
                    case E_NOTICE:
                    case E_CORE_ERROR:
                    case E_CORE_WARNING:
                    case E_COMPILE_ERROR:
                        $this->_createErrorLog($errno, $errstr, $errfile, $errline);
                        break;
    
                    default:
                        $this->_createErrorLog($errno, $errstr, $errfile, $errline);
                    //  header("location:".BASE_URL."sorry.php");
                        break;
                    }
    
                    /* Don't execute PHP internal error handler */
                    return true;
            });

            set_exception_handler(function($exp){
                $this->_createErrorLog($exp->getCode(), $exp->getMessage(), $exp->getFile(), $exp->getLine());
                 //  After header(...); you must use exit;
                //  always use 301 or 302 reference:
                //exit(header("location:".BASE_URL."sorry.php",true, 301));
                echo("<script>location.href = '".BASE_URL."sorry.php?msg=';</script>");
            });
        }

        //This function is used in set_error_handler() and set_exception_handler() to handle uncaught errors.
        private function _createErrorLog($errNo, $errDetails, $fileName, $lineNumber){
            
            $currentdatetime = new DateTime("now", new DateTimeZone('Asia/Dhaka'));
            $FormattedDateTime = $currentdatetime->format('d-m-Y h:i:s A');  //date('Y-m-d H:i:s');
            
            $final_log = "Error No.-". $errNo. ". Description-". $errDetails . "\n";
            $final_log .= "File:$fileName, Line:$lineNumber, Datetime:$FormattedDateTime  " . "\n";
            $final_log .= "------------------------------------------------------------------------------------\n";
            file_put_contents($this->logFilePath, $final_log, FILE_APPEND | LOCK_EX );
        }
        
        private function _createLog($log_text){
            $bt = debug_backtrace();
            $caller = array_shift($bt);
            $path = $caller['file'];
            $file_name = basename($path); // $file is set to "file.php"
            $line_number = $caller['line'];
            
            $currentdatetime = new DateTime("now", new DateTimeZone('Asia/Dhaka'));
            $FormattedDateTime = $currentdatetime->format('d-m-Y h:i:s A');  //date('Y-m-d H:i:s');
            
            $final_log = $log_text . "\n";
            $final_log .= "File:$file_name, Line:$line_number, Datetime:$FormattedDateTime  " . "\n";
            $final_log .= "------------------------------------------------------------------------------------\n";

            //error_log($final_log . "\n",3, $this->rootDirectory . "/site_logs.log");
            //Default path to error log is /var/log/apache2/error.log
            
            file_put_contents($this->logFilePath, $final_log, FILE_APPEND | LOCK_EX );
        }
       
        public function createLog($log_text){
            $this->_createLog($log_text);
        }

        private function _clearLogs($file){
            file_put_contents($file, "");
            // $fh = fopen( 'filelist.txt', 'w' );
            // fclose($fh);
        }

        public function clearLogs(){
            $this->_clearLogs($this->logFilePath);
        }

        public function deleteLogs(){
            $this->_clearLogs($this->logFilePath);
        }
        

        private function _readLogs($file){
            $fp = fopen($file, "r");

            if(filesize($file) > 0){
                $content = fread($fp, filesize($file));
                $lines = explode("\n", $content);
                fclose($fp);
               
                foreach($lines as $newline){
                    echo ''.$newline.'<br>';
                }
            }
            else{
                echo "Hurray!! No log found.";
            }
        }

    

        public function readLogs(){
           $this->_readLogs($this->logFilePath);
        }

        public function hasLogs(){
            if(filesize($this->logFilePath) > 0){
               return true;
            }
            else{
               return false;
            }
         }

    } //<--class

?>