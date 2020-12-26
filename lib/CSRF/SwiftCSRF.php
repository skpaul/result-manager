<?php
  
    //Prevention of CSRF (Cross-site Request Forgery)

    class SwiftCSRF{

        public function __construct() { 
            if(!function_exists('hash_equals')) {
                function hash_equals($str1, $str2) {
                    if(strlen($str1) != strlen($str2)) {
                        return false;
                    } else {
                        $res = $str1 ^ $str2;
                        $ret = 0;
                        for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
                        return !$ret;
                    }
                }
            }
        }
        public function __destruct() { }

        private function _random_token($length = 32){
            if(!isset($length) || intval($length) <= 8 ){
              $length = 32;
            }
            if (function_exists('random_bytes')) {
                return bin2hex(random_bytes($length));
            }
            if (function_exists('mcrypt_create_iv')) {
                return bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
            } 
            if (function_exists('openssl_random_pseudo_bytes')) {
                return bin2hex(openssl_random_pseudo_bytes($length));
            }
        }
        
        private function Salt(){
            return substr(strtr(base64_encode(hex2bin($this->_random_token(32))), '+', '.'), 0, 44);
        }
    
        public function createCsrfInput($message_to_be_hashed = "my_secret_message"){
    
            $antiforgery_token = $this->_random_token();
            $_SESSION['antiforgery_token'] = $antiforgery_token;
           
            $name_of_the_hashing_algorithm = 'sha256';
            $shared_secret_key = $antiforgery_token;
            $hash_hmac = hash_hmac($name_of_the_hashing_algorithm, $message_to_be_hashed, $shared_secret_key);
    
            echo '<input type="hidden" name="'. $antiforgery_token .'" value="'. $hash_hmac .'">';
        }
    

        public function isCsrfValid($message_to_be_hashed = "my_secret_message"){
            if(!isset($_SESSION["antiforgery_token"])) { 
                return false;
            } 

            $antiforgery_token = $_SESSION["antiforgery_token"];
    
            if(!isset($_POST[$antiforgery_token])){
               return false;
            }
            
            $input_value = $_POST[$antiforgery_token] ;
            
            $name_of_the_hashing_algorithm = 'sha256';
            $shared_secret_key = $antiforgery_token;
            $hash_hmac = hash_hmac($name_of_the_hashing_algorithm, $message_to_be_hashed, $shared_secret_key);
            
            if(!hash_equals($input_value,$hash_hmac )) {
               return false; 
            }  
            else{
                return true;
            }
        }


    } //class
  
?>



