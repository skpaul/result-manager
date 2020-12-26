<?php

    class UniqueCode{
        /** @var ZeroSQL */
        protected $db;
        
        /**
         * @param string $alphabet
         */
        public function __construct($zeroSql) {
            $this->db = $zeroSql;
        }

        public function generate($length, $column, $table){
             //from opu
            $code ='';
            for ($i=1; $i <= $length; $i++){ 
                $random_number = rand(65,90); 
                $code .= chr($random_number);
            }

            /*
                //A more reliable answer is geven by scott 
                //in StackOverflow question - "PHP: How to generate a random, unique, //alphanumeric string for use in a secret link?"

                $bytes = random_bytes(6); 
                $code3 = bin2hex($bytes); create code with 12 digits/characters
            */

            $isFound = $this->db->select($column)->from($table)->where($column)->equalTo($code)->firstOrNull()->execute();
            if($isFound != null){
                $this->generate($length, $column,$table);
            }

            return $code;
        }
    }
?>