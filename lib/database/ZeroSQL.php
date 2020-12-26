<?php
  
    //============================
    //Version Beta
    //Last modified 05/05/2020
    //This is only for php7
    //============================

class ZeroException extends Exception
{
}

class ZeroSQL{

    #region constructor and destructor

    /**
     * Constructor.
     */
    public function __construct() {
        $get_arguments       = func_get_args();
        $number_of_arguments = func_num_args();

        if($number_of_arguments == 1){
            $this->logger = $get_arguments[0];
        }
    }


    public function __destruct(){
        if(is_resource($this->connection) && get_resource_type($this->connection)==='mysql link'){
            mysqli_close($this->connection);
        }
    }
    #endregion

    #region Database Connection
    private $server = "";
    public function server($database_server) {
        $this->server = $database_server;
        return $this;
    }

    private $user = "";
    public function user($user_name) {
        $this->user = $user_name;
        return $this;
    }

    private $password = "";
    public function password($password) {
        $this->password = $password;
        return $this;
    }

    private $database = "";
    public function database($database) {
        $this->database = $database;
        return $this;
    }

    //Connect to the server and select the database.
    public function connect() {
        $this->_debugBacktrace();
        $this->connection = mysqli_connect($this->server, $this->user, $this->password, $this->database); 

        if (!$this->connection) {
            $mysqlError = mysqli_error($this->connection);
            die('ERROR CODE: ARPOASRUWWER412547');
        } 
    }
    
    //Close the connection.
    public function close() {
        $this->_debugBacktrace();
        if(is_resource($this->connection) && get_resource_type($this->connection)==='mysql link'){
            mysqli_close($this->connection);
        }
    }

    //Returns the active database connection.
    public function getConnection() {
       return $this->connection;
    }

    public function setConnection($connection) {
         $this->connection = $connection;
     }
    

    #endregion

    #region ZeroObject
        public function new($name){
            $this->_debugBacktrace();

            $meta = new stdClass();
            $meta->type = $name;

            $objStdClass = new stdClass();
            $objStdClass->__meta = $meta;
            return $objStdClass;
        }
    #endregion

    private $queryType= "";
    private $tableName = "";

    #region transaction
    private $useTransaction = false;
    public function startTransaction(){
        $this->_debugBacktrace();
        mysqli_autocommit($this->connection,FALSE);
        $this->useTransaction = true;
        return $this;
    }

    public function stopTransaction(){
        $this->_debugBacktrace();
        mysqli_autocommit($this->connection,TRUE);
        $this->useTransaction = false;
        return $this;
    }

    public function commit(){
        $this->_debugBacktrace();
        mysqli_commit($this->connection,TRUE);
        //now stop transation.
        mysqli_autocommit($this->connection,TRUE);
        $this->useTransaction = false;
        
        return $this;
    }

    public function rollBack(){
        $this->_debugBacktrace();
        if($this->useTransaction){
            mysqli_rollback($this->connection);
        }
        
        return $this;
    }

    #endregion 

    #region SELECT
        private $selectParam;
        public function select($params = NULL){
            $this->_debugBacktrace();
            $this->selectParam = $params;
            return $this;
        }

        private $distinct="";
        public function distinct($columnName = NULL) {
            $this->_debugBacktrace();
            
            if(empty($this->selectParam)){
                $this->selectParam = $columnName;
            }
           
            $this->distinct = "DISTINCT";
            return $this;
        }

        private $skipQuantity= 0;
        public function skip($quantity){
            $this->_debugBacktrace();
            $this->skipQuantity = $quantity;
            return $this;
        }

        private $takeQuantity= 0;
        public function take($quantity){
            $this->_debugBacktrace();
            $this->takeQuantity = $quantity;
            return $this;
        }

        #region ORDER BY
            private $orderByClause = "";
            private function _orderby($table_name, $column_name, $asc_or_desc){
                $this->_debugBacktrace();
                $temp = "";
                if(isset($table_name) && !empty($table_name)){
                    $temp = "`$table_name`.`$column_name`";
                }
                else{
                    $temp = "`$column_name`";
                }
                if(empty($this->orderByClause)){
                    $this->orderByClause = "$temp $asc_or_desc";
                }
                else{
                    $this->orderByClause .= ", $temp $asc_or_desc";
                }
                // return $this;
            }

            public function orderBy($column_name, $table_or_alias_name = null){
                $this->_debugBacktrace();
                $this->_orderby($table_or_alias_name, $column_name, "ASC");
                return $this;
            }

            public function orderByDesc($column_name, $table_or_alias_name = null){
                $this->_debugBacktrace();
                $this->_orderby($table_or_alias_name,  $column_name, "DESC");
                return $this;
            }

            public function ascBy($column_name, $table_or_alias_name = null){
                $this->_debugBacktrace();
                $this->_orderby($table_or_alias_name, $column_name, "ASC");
                return $this;
            }

            public function descBy($column_name, $table_or_alias_name = null){
                $this->_debugBacktrace();
                $this->_orderby($table_or_alias_name,  $column_name, "DESC");
                return $this;
            }

            public function ascendingBy($column_name, $table_or_alias_name = null){
                $this->_debugBacktrace();
                $this->_orderby($table_or_alias_name, $column_name, "ASC");
                return $this;
            }

            public function descendingBy($column_name, $table_or_alias_name = null){
                $this->_debugBacktrace();
                $this->_orderby($table_or_alias_name,  $column_name, "DESC");
                return $this;
            }
        #endregion ORDER BY

        #region JOIN
        private $joinClause = "";
        public function innerJoin($table){
            $this->_debugBacktrace();
            if(empty($this->joinClause)){
                $this->joinClause = " INNER JOIN `$table`";  
            }
            else{
                $this->joinClause .= " INNER JOIN `$table`";  
            }
            
            return $this;
        }

        public function innerJoinAs($table, $alias){
            $this->_debugBacktrace();
            if(empty($this->joinClause)){
                $this->joinClause = " INNER JOIN `$table` AS `$alias`";  
            }
            else{
                $this->joinClause .= " INNER JOIN `$table` AS `$alias`";  
            }
            
            return $this;
        }

        public function on($leftTable, $leftColumn, $rightTable, $rightColumn){
            $this->_debugBacktrace();
            $this->joinClause .= " ON `$leftTable`.`$leftColumn` = `$rightTable`.`$rightColumn`";  
            return $this;
        }
        #endregion JOIN
            
        #region SELECTION APPROACH

            /**
             * find()
             * 
             * Find a record from database by primary key.
             * It must be the last call of the query. 
             * select() must be used in first call.
             * 
             * @param mixed $id
             * 
             * @return object stdClass
            */
            public function find($id){
                $this->_debugBacktrace();

                $parameter = $this->selectParam; //transfer to local variable.
                $this->selectParam = ""; //reset selectParam

                $columns = "";
                if(!isset($parameter) || empty($parameter)){
                    $columns = "*";
                }
                else {
                    $columns = $parameter;
                }

                $tableName = $this->tableName;
                $this->tableName = "";

                $primaryKeyColumn =  $this->_findPrimaryKeyColumnName($tableName);
                $id = $this->_real_escape_string($id);
                $sql = "select $columns from $tableName where $primaryKeyColumn = $id";

                $result = $this->_query($sql);

                $matchQuantity = $result->num_rows;

                if($matchQuantity <> 1){
                    throw new ZeroException("No data found.");
                }

                $record = $this->_prepareSingleRecord($result);

                $meta = new stdClass();
                $meta->type = $tableName;
                $meta->primaryKey = $primaryKeyColumn;
                $record->__meta = $meta;
                return $record;
            }

            public function toList(){
                $sql = $this->_checkSelectSql();
                $result = $this->_query($sql);
                
                $quantity = 0;
                $rows = []; //array();
                switch ($this->fetchType){
                    case "object":
                        //mysqli_fetch_object($result)
                        while ($row = $result->fetch_object()) {
                            if(isset($tableName)){
                                $meta = new stdClass();
                                $meta->type = $tableName;
                                $row->__meta = $meta;
                            }
                            $rows[] = $row;
                            $quantity++;
                        }
                        break;
                    
                    case "assoc": //fetch_assoc is the fastest method.   
                        while ($row = $result->fetch_assoc()) {
                            $rows[] = $row;
                            $quantity++;
                        }
                        break;
                    case "array": //fetch_array is the second fastest method.
                        while ($row = $result->fetch_array()) {
                            $rows[] = $row;
                            $quantity++;
                        }
                        break;
                    case "row":
                        while ($row = $result->fetch_row()) {
                            $rows[] = $row;
                            $quantity++;
                        }
                        break;
                    case "field":
                        while ($row = $result->fetch_field()) {
                            $rows[] = $row;
                            $quantity++;
                        }
                        break;
                }

                if($quantity>0){
                    $result->free_result();
                }

                return $rows;
            
            }

            /**
             * first() 
             * 
             * When you expect one or more items to be returned by a query but you only want to access the first item in your code (ordering could be important in the query here). This will throw an exception if the query does not return at least one item.
            */
            public function first(){
                $this->takeQuantity = 1;
                $sql = $this->_checkSelectSql();
                $result = $this->_query($sql);
                $numRows =  $result->num_rows;
                if($numRows == 0)  throw new ZeroException("The query must return at least 1 record.");
                return $this->_prepareSingleRecord($result);
            }
        
            public function firstOrNull(){
                $this->takeQuantity = 1;
                $sql = $this->_checkSelectSql();
                $result = $this->_query($sql);
                $numRows =  $result->num_rows;
                if($numRows == 0) return NULL;
                return $this->_prepareSingleRecord($result);
            }

            public function single(){
                $this->takeQuantity = 1;
                $sql = $this->_checkSelectSql();
                $result = $this->_query($sql);
                $numRows =  $result->num_rows;
                if($numRows == 0)  throw new ZeroException("The query must return exactly 1 record. But found none.");
                if($numRows > 1) throw new ZeroException("The query must return exactly 1 record. But multiple records found.");
                return $this->_prepareSingleRecord($result);
            }

            public function singleOrNull(){
                $this->takeQuantity = 1;
                $sql = $this->_checkSelectSql();
                $result = $this->_query($sql);
                $numRows =  $result->num_rows;
                if($numRows == 0)  return NULL;
                if($numRows > 1) throw new ZeroException("The query must return exactly 1 record. But multiple records found.");
                return $this->_prepareSingleRecord($result);
            }

            #region Helper method for select query

            //used in first(), firstOrNull(), single(), singleOrNull(), toList()
            private function _checkSelectSql(){
                $this->_debugBacktrace();
                $parameter = $this->selectParam; //transfer to local variable.
                $this->selectParam = ""; //reset selectParam
                
                $sql = "";
                if($this->hasRawSql){
                    $sql = $parameter;
                    $this->hasRawSql = false;
                }
                else{
                    $tableName = $this->tableName;
                    $this->tableName = ""; //reset.

                    $columns = "";
                    if(!isset($parameter) || empty($parameter)){
                        $columns = "*";
                        $sql = $this->_prepare_select_sql($columns,$tableName);
                    }
                    else{
                        //first check whether it has 'select' keyword
                        $parameter = trim($parameter);
                        $keyWord = substr($parameter,0,6);
                        if(strtoupper($keyWord)=="SELECT") {
                            $sql = $parameter;
                        }
                        else{
                            $columns = $parameter;
                            $sql = $this->_prepare_select_sql($columns,$tableName);
                        }
                    }
                }

                return $sql;
            }

            //Used in _checkSelectSql()
            private function _prepare_select_sql($columns,$table){
                $this->_debugBacktrace();
                $distinct = $this->distinct; $this->distinct = "";
                $sql = "SELECT ". $distinct . " " . $columns ." FROM " . $table;
                
                if(!empty($this->joinClause)){
                    $sql .= " " . $this->joinClause;
                    $this->joinClause = "";
                }

                if(!empty($this->whereClause)){
                    $sql .= " WHERE " . $this->whereClause;
                    $this->whereClause ="";
                }

                
                if(!empty($this->groupByClause)){
                    $sql .= " GROUP BY " . $this->groupByClause;
                    $this->groupByClause ="";
                }

                if(!empty($this->havingClause)){
                    $sql .= " HAVING " . $this->havingClause;
                    $this->havingClause = "";
                }

                if(!empty($this->orderByClause)){
                    $sql .= ' ORDER BY '. $this->orderByClause;
                    $this->orderByClause = "";
                }
                //LIMIT 10 OFFSET 10
                if($this->takeQuantity > 0){
                    $sql .= " LIMIT " . $this->takeQuantity;
                    $this->takeQuantity = 0; //Reset takeQuantity
                }
                
                if($this->skipQuantity>0){
                    $sql .= " OFFSET " . $this->skipQuantity;
                    $this->skipQuantity = 0; //Reset skipQuantity
                }

                return $sql;
            }

            //used in find(), first(), firstOrNull(), single(), singleOrNull()
            private function _prepareSingleRecord($queryObject){

                $this->_debugBacktrace();

                $fetchType = $this->fetchType;
                $this->fetchType = "object"; //reset fetchType

                switch ($fetchType){
                    case "object":
                        $record = mysqli_fetch_object($queryObject);
                        break;
                    case "assoc":
                        $record = mysqli_fetch_assoc($queryObject);
                        break;
                    case "array":
                        $record =  mysqli_fetch_array($queryObject);
                        break;
                    case "row":
                        $record =  mysqli_fetch_row($queryObject);
                        break;
                    case "field":
                        $record =  mysqli_fetch_field($queryObject);
                        break;
                }

                return $record;
            }
        #endregion
   
        #endregion  SELECTION APPROACH

        //can be used repeatedly.
        private $groupByClause = "";
        public function groupBy($column_name, $table_or_alias_name=null) {
            $this->_debugBacktrace();
            if(isset($table_or_alias_name)){
                $table_name = "`$table_or_alias_name`.";
            }
            else{
                $table_name = "";
            }

            if(empty($this->groupByClause)){
                $this->groupByClause = "$table_name`$column_name`";
            }
            else{
                $this->groupByClause .= ", $table_name`$column_name`";
            }

            return $this;
        }
    #endregion SELECT

    #region INSERT
        private $insertParam;
        /**
         * insert()
         * 
         * Insert new data into table.
         * 
         * @param mixed $param
         * 
         * @return this and then last auto increment id when executed.
         */
        public function insert($param){
            $this->_debugBacktrace();
            $this->queryType= "insert";
            $this->insertParam = $param;
            return $this;
        }

        //comes from execute()
        private function _insert(){
            $this->_debugBacktrace();
            $parameter = $this->insertParam;
            unset($this->insertParam);

            if($this->hasRawSql ){ 
                $sql = $parameter;
                $this->hasRawSql = false;
            }
            elseif($parameter instanceof stdClass){
                if(isset($parameter->__meta->type)){
                    $tableName = $parameter->__meta->type;
                }

                $PropertyValueArray = $this->_createPropertyValueArrayFromStdClass($parameter);
                
                $sql = $this->_prepareInsertSQL($tableName, $PropertyValueArray);
            }

            elseif(is_array($parameter)){
                $tableName = $this->tableName;
                unset($this->tableName);

                $keyValueArray = $parameter ;
                $PropertyValueArray = $this->_createPropertyValueArrayFromKeyValuePair($keyValueArray);
                $sql = $this->_prepareInsertSQL($tableName, $PropertyValueArray);
            }
            else{
                //first check whether it has insert keyword
                $parameter = trim($parameter);
                $keyword = substr($parameter,0,6);
                if(strtoupper($keyword)=="INSERT") {
                    $sql = $parameter;
                }
                else{
                    $tableName = $this->tableName;
                    unset($this->tableName);

                    $commaSeparatedString = $parameter ;
                    $PropertyValueArray = $this->_createPropertyValueArrayFromCommaSeparatedString($commaSeparatedString);
                    $sql = $this->_prepareInsertSQL($tableName, $PropertyValueArray);
                }
            }

            $this->_query($sql);
            return $this->connection->insert_id;
            
        }
    #endregion INSERT

    #region UPDATE
       /**
        * update()       
        *
        * No need to use table name if this data was read by find() previously.
        *
        *@return int number of rows affected.
        */
        public function update($param){
            $this->_debugBacktrace();
            $this->queryType= "update";
            $this->updateParam = $param;
            return $this;
        }

        //comes from execute()
        private function _update(){
            $this->_debugBacktrace();
            $parameter = $this->updateParam; //transfer to local variable.
            unset($this->updateParam); //reset updateParam.

            $tableName = $this->tableName;
            unset($this->tableName);

        
            $sql = "";
            if($this->hasRawSql){
                $this->_debugBacktrace();
                $sql = $parameter;
                $this->hasRawSql = false;
            }

            elseif($parameter instanceof stdClass ){
                $stdClass = $parameter ;
                if(isset($stdClass->__meta->type)){
                    $tableName = $stdClass->__meta->type;
                }
                $PropertyValueArray = $this->_createPropertyValueArrayFromStdClass($stdClass);
                
                $sql = $this->_prepareUpdateSQL($tableName, $PropertyValueArray);
            }
            elseif(is_array($parameter)){
                $PropertyValueArray = $this->_createPropertyValueArrayFromKeyValuePair($parameter);
                $sql = $this->_prepareUpdateSQL($tableName, $PropertyValueArray);
            }
            else{
                //first check whether it has update keyword
                $parameter = trim($parameter);
                $update = substr($parameter,0,6);
                if(strtoupper($update)=="UPDATE") {
                    $sql = $parameter;
                }
                else{
                    $commaSeparatedString = $parameter ;
                    $PropertyValueArray = $this->_createPropertyValueArrayFromCommaSeparatedString($commaSeparatedString);
                    $sql = $this->_prepareUpdateSQL($tableName, $PropertyValueArray);
                }
            }

            $this->_query($sql);
            return $this->connection->affected_rows;
        }
    #endregion UPDATE

    #region DELETE
        /**
         * Starts a delete operation.
         *
         * Another line of desription.
         *
         * @param mix     $deletaParam       optional. If provided, then it must be a primakry key value or an stdObject.
         *
         * @return this affected rows;
         */
        public function delete($params = NULL){
            $this->_debugBacktrace();
            $this->queryType= "delete";
            $this->deleteParam = $params;
            return $this;
        }

        //comes from execute()
        private function _delete(){
            $this->_debugBacktrace();
            $parameter = $this->deleteParam; //transfer to local variable.
            unset($this->deleteParam); //reset updateParam.

            $tableName = $this->tableName;
            unset($this->tableName); //reset.
        
            $sql = "";
            if($this->hasRawSql){
                $sql = $parameter;
                $this->hasRawSql = false;
            }
            else{
                if(isset($parameter) && !empty($parameter)){
                    if($parameter instanceof stdClass ){
                        $stdClass = $parameter ;
                        if(isset($stdClass->__meta->type)){
                            $tableName = $stdClass->__meta->type;
                        }
                        if(isset($stdClass->__meta->primaryKey)){
                            $pkColumn = $stdClass->__meta->primaryKey;
                        }
                        else{
                            $pkColumn = $this->_findPrimaryKeyColumnName($tableName);
                        }
                        $keyValueArray = (array) $stdClass;
                        
                        $id = $keyValueArray[$pkColumn];
                        $sql = "DELETE FROM $tableName WHERE $pkColumn = " . $this->_real_escape_string($id);
                    }
                    else{
                        //first check whether it has 'delete' keyword
                        $parameter = trim($parameter);
                        $update = substr($parameter,0,6);
                        if(strtoupper($update)=="DELETE") {
                            $sql = $parameter;
                        }
                        else{
                            $pkColumn = $this->_findPrimaryKeyColumnName($tableName);
                            $id = $parameter;
                            $sql = "DELETE FROM $tableName WHERE $pkColumn = " . $this->_real_escape_string($id);
                        }
                    }
                }
                else{
                    //it is assumed that there is where clause.
                    $sql = "DELETE FROM $tableName WHERE " . $this->whereClause;
                    unset($this->whereClause);
                }
            }

            $this->_query($sql);
            
            return $this->connection->affected_rows;
        }
    #endregion DELETE
    
    //Must call for insert(), update() and delete()
    public function execute(){
        $this->_debugBacktrace();
        switch ($this->queryType){
            case "insert":
                return $this->_insert();
            break;
            case "update":
                return $this->_update();
            break;
            case "delete":
                return $this->_delete();
            break;
        }
    }

    #region Common function 
        //Used in select and delete
        public function from($tableName){
            $this->_debugBacktrace();
            $this->tableName ="`" . $tableName . "`";
            return $this;
        }
        
        //Used in insert and update
        public function into($tableName){
            $this->_debugBacktrace();
            $this->tableName ="`" . $tableName . "`";
            return $this;
        }

        protected $hasRawSql = false;
        protected function _hasRawSql($bool){
            $this->_debugBacktrace();
            $this->hasRawSql = $bool;
            return $this;
        }
        public function withSQL(){
            $this->_debugBacktrace();
            return $this->_hasRawSql(true);
        }
        public function fromSQL(){
            $this->_debugBacktrace();
            return $this->_hasRawSql(true);
        }
        public function useSQL(){
            $this->_debugBacktrace();
            return $this->_hasRawSql(true);
        }
    #endregion

    #region MySQL functions
    protected $logSQL = false; //to view sql on a later time for troubleshooting purpose.
    private function _query($sql){
        $this->_debugBacktrace();
        
        if($this->isEnabledSqlLogging){
            
            $currentdatetime = new DateTime("now", new DateTimeZone('Asia/Dhaka'));
            $FormattedDateTime = $currentdatetime->format('d-m-Y h:i:s A');  //date('Y-m-d H:i:s');
            $logContent = "\n\n";
            $logContent .= "-------------------" . $FormattedDateTime . "----------------------------";
            $logContent .= "\n";
            $logContent .= $sql;

            file_put_contents("ZeroSQL_LOG.txt", $logContent, FILE_APPEND | LOCK_EX );
        }

        if($this->isEnabledSqlPrinting){
            echo "<br>" . $sql . "<br>";
        }

        $result = $this->connection->query($sql);

        /*
        For SELECT, SHOW, DESCRIBE, EXPLAIN and other statements returning resultset, mysql_query() returns a resource on success, or FALSE on error.
        For other type of SQL statements, INSERT, UPDATE, DELETE, DROP, etc, mysql_query() returns TRUE on success or FALSE on error.
        */
        if ($result === false) {
            //$error_description = "An error has occured while working with database.";
           
            $error = $this->connection->error; //mysqli_error($this->connection);
            $sqlState = $this->connection->sqlstate;
            $error_description = "Failed to execute the following SQL statement: $sql. MySQL Error: $error. SqlState: $sqlState";
            throw new ZeroException($error_description);
        }

        return $result;
    }

    public function escapeString($value){
        return $this->_real_escape_string($value);
    }


    private function _real_escape_string($value){
        $this->_debugBacktrace();
        //also valid - $this->connection->real_escape_string($value)
        $value = "'" . $this->connection->escape_string($value) . "'"; 
        return $value;
    }
     
    private function _array_escape_string($array){
        $this->_debugBacktrace();
        $escaped = array_map(function($val) {
            
            $val = trim($val);
            if($val === NULL ||  strtoupper($val) === "NULL"){
                return "NULL";
            }
            else{
                return $this->_real_escape_string($val); 
            }
            }, $array);
        return $escaped;
    }

        #region fetch type

        private $fetchType = "object";
        //Default fetch type
        //returns instance of stdClass.
        public function fetchObject(){
            $this->_debugBacktrace();
            $this->fetchType = "object";
            return $this;
        }

        //Returns an associative array of strings that corresponds to the fetched row, or FALSE if there are no more rows.
        public function fetchAssoc(){
            $this->_debugBacktrace();
            $this->fetchType = "assoc";
            return $this;
        }

        //Fetch a result row as an associative array, a numeric array, or both
        public function fetchArray(){
            $this->_debugBacktrace();
            $this->fetchType = "array";
            return $this;
        }

        //Get a result row as an enumerated array
        public function fetchRow(){
            $this->_debugBacktrace();
            $this->fetchType = "row";
            return $this;
        }

        //Get column information from a result and return as an object
        public function fetchField(){
            $this->_debugBacktrace();
            $this->fetchType = "field";
            return $this;
        }
    #endregion fetch type

    #endregion

    #region utility/helper functions for select() method

    //used in find() and _updateSql()
    private function _findPrimaryKeyColumnName($tableName){
        $this->_debugBacktrace();
        $tableName= str_replace("`","",$tableName);
        $sql = "SELECT COLUMN_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_NAME = '". $tableName ."' 
                AND CONSTRAINT_NAME = 'PRIMARY'";

        $result = $this->_query($sql);
        $primaryKeyColumn = mysqli_fetch_object($result);
        return  $primaryKeyColumn->COLUMN_NAME;
    }

    #region Change Case
        private $changeCase = false;

        // default = false will be set after execute();
        public function ignoreCase(){
            $this->changeCase = false;
        }

        // default = false will be set after execute();
        public function changeCase(){
            $this->changeCase = true;
        }

        /**
         * Converts a camel cased string to a snake cased string.
         *
         * @param string $camel camelCased string to convert to snake case
         *
         * @return string
         */
        private function camelsSnake( $camel )	{
            $this->_debugBacktrace();
            return strtolower( preg_replace( '/(?<=[a-z])([A-Z])|([A-Z])(?=[a-z])/', '_$1$2', $camel ) );
        }

        private function camelCaseToUnderScore( $property )	{
            $this->_debugBacktrace();
            static $beautifulColumns = array();

            if ( ctype_lower( $property ) ) return $property;
            
            if ( !isset( $beautifulColumns[$property] ) ) {
                $beautifulColumns[$property] = $this->camelsSnake( $property );
            }
            return $beautifulColumns[$property];
        }
        /**
         * Turns a camelcase property name into an underscored property name.
         *
         * Examples:
         *
         * - oneACLRoute -> one_acl_route
         * - camelCase -> camel_case
         *
         * Also caches the result to improve performance.
         *
         * @param string $property property to un-beautify
         *
         * @return string
         */
        protected function beau( $property ){
            $this->_debugBacktrace();
            static $beautifulColumns = array();

            if ( ctype_lower( $property ) ) return $property;
            if (
                ( strpos( $property, 'own' ) === 0 && ctype_upper( substr( $property, 3, 1 ) ) )
                || ( strpos( $property, 'xown' ) === 0 && ctype_upper( substr( $property, 4, 1 ) ) )
                || ( strpos( $property, 'shared' ) === 0 && ctype_upper( substr( $property, 6, 1 ) ) )
            ) {

                $property = preg_replace( '/List$/', '', $property );
                return $property;
            }
            if ( !isset( $beautifulColumns[$property] ) ) {
                $beautifulColumns[$property] = $this->camelsSnake( $property );
            }
            return $beautifulColumns[$property];
        }
    
    #endregion Change Case

    //PropertyValueArray
    private function _createPropertyValueArrayFromBean($bean){
        $this->_debugBacktrace();
        list( $properties, $table ) = $bean->getPropertiesAndType();
        $PropertyValueArray = array();
        $k1 = 'property';
        $k2 = 'value';

        foreach( $properties as $key => $value ) {
            $PropertyValueArray[] = array( $k1 => $key, $k2 => $value );
        }
        
        return $PropertyValueArray;
    }

    private function _createPropertyValueArrayFromStdClass($stdClass){
        $this->_debugBacktrace();
        $properties = (array) $stdClass;
        $PropertyValueArray = array();
        $k1 = 'property';
        $k2 = 'value';

        foreach( $properties as $key => $value ) {
            if($key == "__meta") continue;
            $PropertyValueArray[] = array( $k1 => $key, $k2 => $value );
        }
        
        return $PropertyValueArray;
    }

    private function _createPropertyValueArrayFromKeyValuePair($arrayOfKkeyValuePair) {
        $this->_debugBacktrace();
        /*
            $keyvalues = array();
            $keyvalues['foo'] = "bar";
            $keyvalues['pyramid'] = "power";
        */

        $properties = $arrayOfKkeyValuePair;  

        $PropertyValueArray = array();
        $k1 = 'property';
        $k2 = 'value';

       
        foreach( $properties as $key => $value ) {

            $key = trim($key);
            if ( !ctype_lower( $key ) ) {
                $key = $this->beau( $key );
            } 

            $value = trim($value);
            if ( $value === FALSE ) {
                $value = '0';
            } elseif ( $value === TRUE ) {
                $value = '1';
                /* for some reason there is some kind of bug in xdebug so that it doesnt count this line otherwise... */
            } elseif ( $value instanceof \DateTime ) { 
                $value = $value->format( 'Y-m-d H:i:s' ); 
            } elseif($value === NULL ||  strtoupper($value) === "NULL"){
                $value = 'NULL';
            }

            $PropertyValueArray[] = array( $k1 => $key, $k2 => $value );
        }
        
       
        return $PropertyValueArray;
    }

    //("name=saumitra, father=fathers name")
    public function _createPropertyValueArrayFromCommaSeparatedString( $string )  {
        $this->_debugBacktrace();

        $properties = array();
    
        $arr = explode(",", $string);
        
        foreach( $arr as $item) {
            $keyValue = explode("=",$item);
            $key = $keyValue[0];
            $key = trim($key);
        
            $exists = isset( $properties[$key] );

            if($exists) continue;

            $value = $keyValue[1];
            $properties[$key] = $value;
        }
        
        $PropertyValueArray = array();
        $k1 = 'property';
        $k2 = 'value';

        
        foreach( $properties as $key => $value ) {
            $PropertyValueArray[] = array( $k1 => $key, $k2 => $value );
        }
        
        return $PropertyValueArray;
        
    }

    

    private function _prepareInsertSQL( $table, $PropertyValueArray ) {
        $this->_debugBacktrace();
		$columnsArray = $valuesArray = array();

        foreach ( $PropertyValueArray as $pair ) {
            $column = $pair['property'];
            if($this->changeCase){
                if ( !ctype_lower( $column ) ) {
                    $column = $this->camelCaseToUnderScore( $column );
                } 
            }

            $columnsArray[] = $column; //$pair['property'];

            $value = $pair['value'];
            $value = trim($value);
            if ( $value === FALSE ) {
                $value = '0';
            } elseif ( $value === TRUE ) {
                $value = '1';
                /* for some reason there is some kind of bug in xdebug so that it doesnt count this line otherwise... */
            } elseif ( $value instanceof \DateTime ) { 
                $value = $value->format( 'Y-m-d H:i:s' ); 
            } elseif($value === NULL ||  strtoupper($value) === "NULL"){
                $value = 'NULL';
            }
            $valuesArray[]  = $value; //$pair['value'];
        }
        $this->changeCase = false;
        $column_names ="`" . implode('`,`', $columnsArray) . "`";
        $val = $this->_array_escape_string($valuesArray);
        $values = implode(", ", $val);

        return "INSERT INTO $table (" . $column_names . ") VALUES(" . $values . ")";
    }
    
    private function _prepareUpdateSQL( $table, $PropertyValueArray ) {
        $this->_debugBacktrace();
		$set= "";
        $whereClause="";
        //If where clause is empty, then updateParam might be an stdClass 
        //with Primary Key column as a property. Lets find the primary key column
        //from table.
        $pk="";
        if(empty($this->whereClause)){
            $pk = $this->_findPrimaryKeyColumnName($table);
        }
        else{
            $whereClause  = $this->whereClause;
            $this->whereClause = "";
        }
        foreach ( $PropertyValueArray as $pair ) {
            $column = $pair['property'];

            if($this->changeCase){
                if ( !ctype_lower( $column ) ) {
                    $column = $this->camelCaseToUnderScore( $column );
                } 
            }

            $value = $pair['value'];
           
            
            //$value = trim($value);
            if ( $value === FALSE ) {
                $value = '0';
            } elseif ( $value === TRUE ) {
                $value = '1';
                /* for some reason there is some kind of bug in xdebug so that it doesnt count this line otherwise... */
            } elseif ( $value instanceof \DateTime ) { 
                $value = $value->format( 'Y-m-d H:i:s' ); 
            } elseif($value === NULL ||  strtoupper($value) === "NULL"){
                $value = 'NULL';
            }
            
            $value = "'" . mysqli_real_escape_string($this->connection, $value) . "'"; 

            if($column == $pk){
                $whereClause = " $pk=$value"; continue;
            }

            if(empty($set)){
                $set = "$column=$value";
            }
            else{
                $set .= ", " . "$column=$value";
            }
        }
      
        $this->changeCase = false;

        return "UPDATE " . $table . " SET " . $set . " WHERE " . $whereClause;
    }

    #endregion

    #region Conditional (Where and Having)
        
        #region Where clause

        private $whereClause = "";

        //Option to provide directly raw where clause sql statement without 'where' keyword.
        public function whereSQL($whereSqlStatement){
            $this->_debugBacktrace();
            $this->whereClause = $whereSqlStatement;
            return $this;
        }

        //comes from where(), andWhere(), orWhere()
        //return void
        private function _where($conjunction, $column_name, $table_or_alias_name=null){
            $this->_debugBacktrace();
            $this->last_call_where_or_having = "where";
            if(isset($table_or_alias_name)){
                $table_name = "`$table_or_alias_name`.";
            }
            else{
                $table_name = "";
            }

            if(empty($this->whereClause)){
                $this->whereClause = "$table_name`$column_name`";
            }
            else{
                $this->whereClause .= " $conjunction $table_name`$column_name`";
            }
            return "";
        }

        public function where($column_name, $table_or_alias_name=null){
            $this->_debugBacktrace();
            $this->_where("AND", $column_name, $table_or_alias_name);
            return $this;
        }

        public function andWhere($column_name, $table_or_alias_name=null){
            $this->_debugBacktrace();
            $this->_where("AND", $column_name, $table_or_alias_name);

            return $this;
        }

        public function orWhere($column_name, $table_or_alias_name=null){
            $this->_debugBacktrace();
            $this->_where("OR", $column_name, $table_or_alias_name);

            return $this;
        }
        
        #endregion

        #region Having clause

        private $havingClause = "";

        private function _having($column_name, $table_or_alias_name, $andOr){
            $this->_debugBacktrace();
            $this->last_call_where_or_having = "having";
            if(isset($table_or_alias_name)){
                $table_name = "`$table_or_alias_name`.";
            }
            else{
                $table_name = "";
            }

            if(empty($this->havingClause)){
                $this->havingClause = "$table_name`$column_name`";
            }
            else{
                $this->havingClause .= " $andOr $table_name`$column_name`";
            }
            return $this;
        }

        public function having($column_name, $table_or_alias_name=null){
            $this->_debugBacktrace();
            $this->_having($column_name, $table_or_alias_name, "AND");
            return $this;
        }

        public function andHaving($column_name, $table_or_alias_name=null){
            $this->_debugBacktrace();
            $this->_having($column_name, $table_or_alias_name, "AND");
            return $this;
        }

        public function orHaving($column_name, $table_or_alias_name=null){
            $this->_debugBacktrace();
            $this->_having($column_name, $table_or_alias_name, "OR");
            return $this;
        }

        //Option to provide raw sql statement with having clause.
        public function havingSQL($havingSqlStatement){
            $this->_debugBacktrace();
            $this->havingClause = $havingSqlStatement;
        }
        #endregion 

        #region Operators for Where and Having clause (= < > etc)
        public function equalTo($value){
            $this->_debugBacktrace();
            /*
                Equals is generally used unless using a verb "is" and the phrase "equal to". 
                While reading 3 ft = 1 yd you would say "three feet equals a yard," or "three feet is equal to a yard". 
                Equals is used as a verb. 
                To use equal in mathematics (generally an adjective) you need an accompanying verb.
            */
            $value = $this->_real_escape_string($value);

            if($this->last_call_where_or_having == "where"){
                $this->whereClause .= "=$value";
            }
            else{
                $this->havingClause .= "=$value";
            }
            return $this;
        }

        public function notEqualTo($value){
            $this->_debugBacktrace();
            /*
                Equals is generally used unless using a verb "is" and the phrase "equal to". 
                While reading 3 ft = 1 yd you would say "three feet equals a yard," or "three feet is equal to a yard". 
                Equals is used as a verb. 
                To use equal in mathematics (generally an adjective) you need an accompanying verb.
            */
            $value = $this->_real_escape_string($value);

            if($this->last_call_where_or_having == "where"){
                $this->whereClause .= "<>$value";
            }
            else{
                $this->havingClause .= "<>$value";
            }
            return $this;
        }

        public function greaterThan($value){
            $this->_debugBacktrace();
            $value = $this->_real_escape_string($value);

            if($this->last_call_where_or_having == "where"){
                $this->whereClause .= ">$value";
            }
            else{
                $this->havingClause .= ">$value";
            }
            return $this;
        }

        public function greaterThanOrEqualTo($value){
            $this->_debugBacktrace();
            $value = $this->_real_escape_string($value);

            if($this->last_call_where_or_having == "where"){
                $this->whereClause .= " >= $value";
            }
            else{
                $this->havingClause .= " >= $value";
            }
            return $this;
        }

        public function lessThan($value){
            $this->_debugBacktrace();
            $value = $this->_real_escape_string($value);

            if($this->last_call_where_or_having == "where"){
                $this->whereClause .= " < $value";
            }
            else{
                $this->havingClause .= " < $value";
            }
            return $this;
        }

        public function lessThanOrEqualTo($value){
            $this->_debugBacktrace();
            $value = $this->_real_escape_string($value);

            if($this->last_call_where_or_having == "where"){
                $this->whereClause .= "<=$value";
            }
            else{
                $this->havingClause .= "<=$value";
            }
            return $this;
        }

        public function between($starting_value, $ending_value){
            $this->_debugBacktrace();
            $value_one = $this->_real_escape_string($starting_value);
            $value_two = $this->_real_escape_string($ending_value);

            if($this->last_call_where_or_having == "where"){
                $this->whereClause .= " BETWEEN $value_one AND $value_two";
            }
            else{
                $this->havingClause .= " BETWEEN $value_one AND $value_two";
            }
            return $this;
        }

        public function startWith($string){
            $this->_debugBacktrace();
            $value = $this->_real_escape_string($string . "%");

            if($this->last_call_where_or_having == "where"){
                $this->whereClause .= " LIKE $value";
            }
            else{
                $this->havingClause .= " LIKE $value";
            }
            return $this;
        }

        public function notStartWith($string){
            $this->_debugBacktrace();
            $value = $this->_real_escape_string($string. "%");

            if($this->last_call_where_or_having == "where"){
                $this->whereClause .= " NOT LIKE $value";
            }
            else{
                $this->havingClause .= " NOT LIKE $value";
            }
            return $this;
        }

        public function endWith($string){
            $this->_debugBacktrace();
            $value = $this->_real_escape_string("%" . $string);

            if($this->last_call_where_or_having == "where"){
                $this->whereClause .= " LIKE $value";
            }
            else{
                $this->havingClause .= " LIKE $value";
            }
            return $this;
        }

        public function notEndWith($string){
            $this->_debugBacktrace();
            $value = $this->_real_escape_string("%" . $string);

            if($this->last_call_where_or_having == "where"){
                $this->whereClause .= " NOT LIKE $value";
            }
            else{
                $this->havingClause .= " NOT LIKE $value";
            }
            return $this;
        }

        public function contain($string){
            $this->_debugBacktrace();
            $value = $this->_real_escape_string("%". $string. "%");

            if($this->last_call_where_or_having == "where"){
                $this->whereClause .= " LIKE $value";
            }
            else{
                $this->havingClause .= " LIKE $value";
            }
            return $this;
        }

        public function notContain($string){
            $this->_debugBacktrace();
            $value = $this->_real_escape_string("%". $string. "%");

            if($this->last_call_where_or_having == "where"){
                $this->whereClause .= " NOT LIKE $value";
            }
            else{
                $this->havingClause .= " NOT LIKE $value";
            }
            return $this;
        }

        //Enable user to write raw string with wildcard characters i.e. 'itunes%'
        public function like($stringWithWildCardCharacter){
            $this->_debugBacktrace();

            $value = $this->_real_escape_string($stringWithWildCardCharacter);

            if($this->last_call_where_or_having == "where"){
                $this->whereClause .= " LIKE $value";
            }
            else{
                $this->havingClause .= " LIKE $value";
            }
            return $this;
        }

        //Enable user to write raw string with wildcard characters i.e. 'itunes%'
        public function notLike($stringWithWildCardCharacter){
            $this->_debugBacktrace();
            $value = $this->_real_escape_string($stringWithWildCardCharacter);

            if($this->last_call_where_or_having == "where"){
                $this->whereClause .= " NOT LIKE $value";
            }
            else{
                $this->havingClause .= " NOT LIKE $value";
            }
            return $this;
        }
        #endregion
    #endregion

    #region Debug and Troubleshoot
    private $isEnabledSqlLogging = false;
    public function logSQL($bool){
        $this->isEnabledSqlLogging = $bool;
        return $this;
    }

    private $isEnabledSqlPrinting = false;
    public function printSql($bool){
        $this->isEnabledSqlPrinting = $bool;
        return $this;
    }

    private $performDebugBacktrace = false;
    public function debugBacktrace($bool){
        $this->performDebugBacktrace = $bool;
        return $this;
    }

    private $callCounter = 1;
    private function _debugBacktrace(){
        if($this->performDebugBacktrace){

            $callers = debug_backtrace();
           
            $count = count($callers);
            $caller = sprintf('%02d', $this->callCounter); 
            if($count > 1){
                for($i=$count-1; $i>0; $i=$i-1){
                    //&#8594;
                    $caller .= " " . html_entity_decode("&#8594;") . " " . $callers[$i]['function'] . "()";
                }
              //  $caller = "<br>$caller";
            }
    
            // echo "<br>$caller";

             $currentdatetime = new DateTime("now", new DateTimeZone('Asia/Dhaka'));
             $FormattedDateTime = $currentdatetime->format('d-m-Y h:i:s A');  //date('Y-m-d H:i:s');
             $logContent = "\n\n";
             $logContent .= $caller . "   " . $FormattedDateTime;
 
             file_put_contents("ZeroSQL_Debug_Backtrace.txt", $logContent, FILE_APPEND | LOCK_EX );

             $this->callCounter++;
        }
    }

    #endregion

    #region Database and Table Schema
    //No need to call execute()
    public function truncate($tableName){
        $this->_debugBacktrace();
        $sql = "TRUNCATE TABLE `$tableName`";
        $this->_query($sql);
        return $this;
    }

    //Field, Type, Null, Key, Default, Extra
    public function showColumns($tableName){
        $this->_debugBacktrace();
        $sql = "SHOW COLUMNS FROM `$tableName`";
        $result = $this->_query($sql);

        $fetchType = $this->fetchType;
        $this->fetchType = "object";

        $rows = []; //array();
        switch ($fetchType){
            case "object":
                while ($row = $result->fetch_object()) {
                    $rows[] = $row;
                }
                break;
            case "assoc":
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                }
                break;
            case "array":
                while ($row = $result->fetch_array()) {
                    $rows[] = $row;
                }
                break;
            case "row":
                while ($row = $result->fetch_row()) {
                    $rows[] = $row;
                }
                break;
            case "field":
                while ($row = $result->fetch_field()) {
                    $rows[] = $row;
                }
                break;
        }

        $result->free;
       
        return $rows;
    }

    public function showTables(){
        $this->_debugBacktrace();
        $sql = "SHOW TABLES FROM " . $this->database;
        $result = $this->_query($sql);

        $fetchType = $this->fetchType;
        $this->fetchType = "object";

        $rows = []; //array();
        switch ($fetchType){
            case "object":
                while ($row = $result->fetch_object()) {
                    $rows[] = $row;
                }
                break;
            case "assoc":
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                }
                break;
            case "array":
                while ($row = $result->fetch_array()) {
                    $rows[] = $row;
                }
                break;
            case "row":
                while ($row = $result->fetch_row()) {
                    $rows[] = $row;
                }
                break;
            case "field":
                while ($row = $result->fetch_field()) {
                    $rows[] = $row;
                }
                break;
        }

        $result->free;
        return $rows;
    }
    
    public function findPrimaryKeyColumn($tableName){
        $this->_debugBacktrace();
        return $this->_findPrimaryKeyColumnName($tableName);
    }

    #endregion

    #region CSV
    public function getCSV($sql = ""){
        $query = mysqli_query($this->connection, $sql);
        $export =  $query;
        
        //$fields = mysql_num_fields ( $export );
        $fields = mysqli_num_fields($export);

        $header ='';
        $data = '';
        for ( $i = 0; $i < $fields; $i++ )
        {
            $colObj = mysqli_fetch_field_direct($export,$i);                            
            $col = $colObj->name;

            $header .= $col . "\t";
            while( $row = mysqli_fetch_row( $export ) )
            {
                $line = '';
                foreach( $row as $value )
                {                                            
                    if ( ( !isset( $value ) ) || ( $value == "" ) )
                    {
                        $value = "\t";
                    }
                    else
                    {
                        $value = str_replace( '"' , '""' , $value );
                        $value = '"' . $value . '"' . "\t";
                    }
                    $line .= $value;
                }
                $data .= trim( $line ) . "\n";
            }
        }
       
        $data = str_replace( "\r" , "" , $data );
        
        if ( $data == "" )
        {
            $data = "\n(0) Records Found!\n";                        
        }
        
        return "$header\n$data";
        
        //USAGE--------------------
        // header("Content-type: application/octet-stream");
        // header("Content-Disposition: attachment; filename=your_desired_name.xls");
        // header("Pragma: no-cache");
        // header("Expires: 0");
        // print "$header\n$data";
    }

    public function getCSVNew($sql = ""){
     
        $query = mysqli_query($this->connection, $sql);
        $export =  $query;
       
        
        //$fields = mysql_num_fields ( $export );
        $fields = mysqli_num_fields($export);

        $header ='';
        $data = '';
        for ( $i = 0; $i < $fields; $i++ )
        {
            $colObj = mysqli_fetch_field_direct($export,$i);                            
            $col = $colObj->name;

            $header .= $col . "\t";
            while( $row = mysqli_fetch_row( $export ) )
            {
                $line = '';
                foreach( $row as $value )
                {                                            
                    if ( ( !isset( $value ) ) || ( $value == "" ) )
                    {
                        $value = "\t";
                    }
                    else
                    {
                        $value = str_replace( '"' , '""' , $value );
                        $value = '"' . $value . '"' . "\t";
                    }
                    $line .= $value;
                }
                $data .= trim( $line ) . "\n";
            }
        }
       
        $data = str_replace( "\r" , "" , $data );
        
        if ( $data == "" )
        {
            $data = "\n(0) Records Found!\n";                        
        }
        
        return "$header\n$data";
        
        //USAGE--------------------
        // header("Content-type: application/octet-stream");
        // header("Content-Disposition: attachment; filename=your_desired_name.xls");
        // header("Pragma: no-cache");
        // header("Expires: 0");
        // print "$header\n$data";
    }

    public function query_to_csv($db_conn, $query, $filename, $attachment = false, $headers = true) {
       
        if($attachment) {
            // send response headers to the browser
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$filename);

            $fp = fopen('php://output', 'w');
        } else {
            $fp = fopen($filename, 'w');
        }
       
        $result = mysqli_query($db_conn, $query) or die( mysqli_error( $db_conn ) );
       
        if($headers) {
            // output header row (if at least one row exists)
            $row = mysqli_fetch_assoc($result);
            if($row) {
                fputcsv($fp, array_keys($row));
                // reset pointer back to beginning
                mysqli_data_seek($result, 0);
            }
        }
       
        while($row = mysqli_fetch_assoc($result)) {
            fputcsv($fp, $row);
        }
       
        fclose($fp);
    }
    #endregion
}

?>