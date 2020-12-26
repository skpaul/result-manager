<?php
   
    class SwiftJSON { 

        public static function failure($message, $redirectUrl=null, $data=null){
            $array = array("issuccess"=>false, "message"=>$message);
            
            if(isset($redirectUrl)){
                $array['redirecturl']= $redirectUrl;
            } 

            if(isset($redirectUrl)){
                $array['data']=  $data;
            } 
            return json_encode($array,JSON_FORCE_OBJECT);

        }

        public static function success($message, $redirectUrl=null, $data=null){
            $array = array("issuccess"=>true, "message"=>$message);
            
            if(isset($redirectUrl)){
                $array['redirecturl']= $redirectUrl;
            } 

            if(isset($redirectUrl)){
                $array['data']=  $data;
            } 
            return json_encode($array,JSON_FORCE_OBJECT);
        }
      } 
?>