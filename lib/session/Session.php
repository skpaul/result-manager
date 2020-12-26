
<?php 

    /*
        Last modified - 16-04-2020
    */
    
    class Session{
      
        /**
         * @var int $defaultSessionTimeoutValue Stores the default timeout value in seconds.
         * 
         * Default value is 3600 seconds (60 minutes).
         */
       // private $defaultSessionTimeoutValue = 3600; //
        
        /**
         * constructor()
         * 
         * @param int $defaultDuration Sets the defualt duration in seconds. Default value is 7200 seconds (120 minutes).
         */
        public function __construct($defaultDuration = 7200) {
            $this->defaultSessionTimeoutValue = $defaultDuration;
             if(ENVIRONMENT=="DEVELOPMENT"){
                $sessionDirectory = "D:\php\bar-council\SESSION_STORE";
                if (!file_exists($sessionDirectory)) {
                    mkdir($sessionDirectory, 0777, true);
                }
                 ini_set('session.save_path',$sessionDirectory);
            }

            //Tell server to keep session data for AT LEAST 3600 seconds (the defaultSessionTimeoutValue)
            ini_set('session.gc_maxlifetime', $this->defaultSessionTimeoutValue);
            
            //Each client should remember their session id for EXACTLY 1 hour
            session_set_cookie_params($this->defaultSessionTimeoutValue);
        }

        public function __destruct(){ }

        /**
         * start()
         * 
         * Start a brand new session.
         * Sets default session lifetime value.
         * Updates last activity time.
         */
        public function start(){
            session_start();
            if(!isset($_SESSION['LAST_ACTIVITY'])) 
                $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
        }

        /**
         * set()
         * 
         * Sets value in session variable.
         * Also update the last activity time.
         * 
         * @param string $identifier The name of the variable i.e. "user_id".
         * @param mixed $value The value to set.
         * 
         * @throws Exception of type SessionException if session is not initialized.
         */
        public function set($identifier, $value){
            if($this->isExpired()) throw new SessionException("Session expired.");
            $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
            $_SESSION[$identifier] = $value;
        }

        public function unset($identifier){
            if($this->isExpired()) throw new SessionException("Session expired.");
            $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
            unset($_SESSION[$identifier]);
        }

        /**
         * get()
         * 
         * Get value from the specified session variable.
         * 
         * @param string $identifier The name of the variable
         * 
         * @return mixed Value
         * 
         * @throws Exception of type SessionException if session is not initialized.
         */
        public function get($identifier){
            if($this->isExpired()) throw new SessionException("Session expired.");
            if(!isset($_SESSION[$identifier])) throw new SessionException("$identifier not found in current session.");
            $_SESSION['LAST_ACTIVITY'] = time();  //update last activity time stamp
            return $_SESSION[$identifier];
        }

        /**
         * has()
         * 
         * Checks whether current session has this value.
         * 
         * @param string $identifier The name of the session variable.
         * 
         * @return boolean
         */
        public function has($identifier){
            if(isset($_SESSION[$identifier])) { 
                //update last activity time stamp
                $_SESSION['LAST_ACTIVITY'] = time(); 
                return true;
                // header('Location:' . $this->session_expired_url, true, 303);
            } 

            return false;
        }

        /**
         * isExpired()
         * 
         * Checks whether current session has been expired or not.
         * 
         * @return bool Returns 'true' if expired, otherwise 'false'
         */
        public function isExpired(){
            if(isset($_SESSION['LAST_ACTIVITY'])) {
                 $diff = time() - $_SESSION['LAST_ACTIVITY'] ;
                if( $diff > $this->defaultSessionTimeoutValue) {                    
                    session_unset();     // unset $_SESSION variable for the run-time 
                    session_destroy();   // destroy session data in storage
                    return true;
                }
                else{
                    $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
                    return false;
                }
            }
            else{
                //'LAST_ACTIVITY' variable not found.
                session_unset();     // unset $_SESSION variable for the run-time 
                session_destroy();   // destroy session data in storage
                return true;
            }  
        }

        /**
         * close()
         * 
         * Unset and destroy the curren session.
         */
        public function close(){
             /*PHP_SESSION_ACTIVE*/ /*PHP_SESSION_NONE*/
             if (session_status() == PHP_SESSION_ACTIVE) {
                 //unset $_SESSION variable for the run-time 
                session_unset();    
                    
                //destroy session data in storage
                session_destroy();   
             }
        }
    } //<--class

    class SessionException extends Exception{
        
    }
?>