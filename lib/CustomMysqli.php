<?php
/*
 * @AjiKamaludin <aji19kamaludin@gmail.com>
 * @Version 1.0
 * @Package CustomMysqli
 */

 class CustomMysqli 
 {

    private $db_host = "localhost"; // Change as required
	private $db_user = "root";      // Change as required
	private $db_pass = "";  // Change as required
    private $db_name = "pasar_db";	// Change as required
    
    private $con    = false;    // Check to see if the connection is active
    private $myconn = "";       // This will be our mysqli object
	private $result = [];       // Any results from a query will be stored here
    private $sql    = "";       // used for debugging process with SQL return
    private $numRow = "";       // used for returning the number of rows
    public $tableName = "";    // used for save name of table
    private $numResults = null; // used to number of result query

    private $limit = "";        // used for pegination
    private $page = "";         // used for pegination
    private $total = "";        // used for pegination
    
    //the constructor
    public function __construct($host = null, $username = null, $password = null, $db = null)
    {
        if($host != null){
            $this->db_host = $host;
            $this->db_user = $username;
            $this->db_pass = $password;
            $this->db_name = $db;
        }
        return $this->connect();
    }

    //dinamic property for table name
    public function __get($table){
        $this->clear();
        if($this->tableExists($table)){
            if(!empty($this->tableName)){
                $this->tableName = "";
            }
            $this->tableName = $table;

            $this->select($this->tableName);
            $this->total = $this->numResults;

            return $this;
        }else{
            return false;
        }
    }

    // Function to make connection to mysqli
    private function connect(){
        if(!$this->con){
            $this->myconn = new mysqli($this->db_host,$this->db_user,$this->db_pass,$this->db_name);  // mysqli_connect() with variables defined at the start of Database class
            if($this->myconn->connect_errno > 0){
                array_push($this->result,$this->myconn->connect_error);
                return false; // Problem selecting database return FALSE
            }else{
                $this->con = true;
                return true; // Connection has been made return TRUE
            } 
        }else{  
            return true; // Connection has already been made return TRUE 
        }  	
    }

    // Function to disconnect from the database
    private function disconnect(){
        // If there is a connection to the database
        if($this->con){
            // We have found a connection, try to close it
            if($this->myconn->close()){
                // We have successfully closed the connection, set the connection variable to false
                $this->con = false;
                // Return true tjat we have closed the connection
                return true;
            }else{
                // We could not close the connection, return false
                return false;
            }
        }
    }
    
    //Function to find record from id , must be call from object->property
    public function find($id){
        if(empty($this->tableName)) { return "function must be call from dynamic property"; }
        if($this->tableExists($this->tableName)){ // check the table is exist
            $this->clear(); //clear $this->result 
            $this->query("SELECT * FROM ".$this->tableName." WHERE id_".$this->tableName." = '$id'");  //execute query
            if($this->numResult == 0){ //check row result of query
                $this->query("SELECT * FROM ".$this->tableName." WHERE id = '$id'");
            }else if($this->numResult == 0){
                return false;
            }
            return $this->getResult(); // return $result
        }else{
            return false;
        }
    }

    //Function to find record use where clause, must be call from object->property
    public function where($arr = []){ // ['username' => 'admin'] : username = admin
        $where = "";
        $i = 1;
        if($this->tableExists($this->tableName)){
            if(count($this->result) != 0){
                $this->result = [];
            }
            foreach($arr as $field => $value){
                if($i > 1){
                    $where .= " AND $field = '$value'";
                }else{
                    $where .= " $field = '$value'";
                }
                $i++;
            }
            $this->query("SELECT * FROM ".$this->tableName." WHERE " . $where);
            return $this;
        }else{
            return false;
        }
    }

    // Function to create paginate , must be call from object->property
    public function paginate($limit = 10, $page = 1, $orderBy = [], $sql = null) {
        $this->clear(); //clear $this->result 
        if($sql != null){
            $this->myQuery = $sql;
        }else{
            $this->myQuery = "SELECT * FROM $this->tableName";
        }
        
        
        if(count($orderBy) != 0){
            $this->myQuery .= " ORDER BY ". implode('',array_keys($orderBy)) ." ".implode('', $orderBy);  //default query 
        }

        $this->total = $this->myconn->query($this->myQuery)->num_rows;
        $this->limit   = $limit;
        $this->page    = $page;
     
        if ( $this->limit == 'all' ) {
            $query      = $this->myQuery;
        } else {
            $query      = $this->myQuery . " LIMIT " . ( ( $this->page - 1 ) * $this->limit ) . ", $this->limit";
        }
        
        $result  = $this->myconn->query($query);

        if($result->num_rows == 0){
            return false;
        }else{
            while($dat = $result->fetch_array()){
                $results[] = $dat;
            }
        }

        return $results;
    }

    // Function to print pegination , must be call from object->property
    public function createLinks($links = 1) { 
        if ( $this->limit == 'all' ) {
            return '';
        }
        $last       = ceil( $this->total / $this->limit ); //total from property query / limit from paginate() function
        $start      = ( ( $this->page - $links ) > 0 ) ? $this->page - $links : 1;
        $end        = ( ( $this->page + $links ) < $last ) ? $this->page + $links : $last;
        $html       = '<ul class="pagination pagination-sm no-margin pull-right">';
        $class      = ( $this->page == 1 ) ? "disabled" : "";
        $html       .= '<li class="' . $class . '">';
        if( $this->page != 1 ){
            $html   .= '<a href="?page=' . ( $this->page - 1 ) . '">&laquo;</a></li>';
        }else{
            $html   .= '<a>&laquo;</a></li>';
        }
        if ( $start > 1 ) {
            $html   .= '<li><a href="?page=1">1</a></li>';
            $html   .= '<li class="disabled"><span>...</span></li>';
        }
        for ( $i = $start ; $i <= $end; $i++ ) {
            $class  = ( $this->page == $i ) ? "active" : "";
            $html   .= '<li class="' . $class . '"><a href="?page=' . $i . '">' . $i . '</a></li>';
        }
        if ( $end < $last ) {
            $html   .= '<li class="disabled"><span>...</span></li>';
            $html   .= '<li><a href="?page=' . $last . '">' . $last . '</a></li>';
        }
        $class      = ( $this->page == $last ) ? "disabled" : "";
        $html       .= '<li class="' . $class . '"><a href="?page=' . ( $this->page + 1 ) . '">&raquo;</a></li>';
        $html       .= '</ul>';
        return $html;
    }

    public function query($sql){
        $query = $this->myconn->query($sql);
        $this->myQuery = $sql;
        if($query){
            // If the query returns >= 1 assign the number of rows to numResults
            $this->numResults = $query->num_rows;
            // Loop through the query results by the number of rows returned
            for($i = 0; $i < $this->numResults; $i++){
                $r = $query->fetch_array();
                $key = array_keys($r);
                for($x = 0; $x < count($key); $x++){
                    // Sanitizes keys so only alphavalues are allowed
                    if(!is_int($key[$x])){
                        if($query->num_rows >= 1){
                            $this->result[$i][$key[$x]] = $r[$key[$x]];
                        }else{
                            $this->result = null;
                        }
                    }
                }
            }
            return $this; // Query was successful
        }else{
            array_push($this->result,$this->myconn->error);
            return false; // No rows where returned
        }
    }

    // Function to SELECT from the database
    public function select($table, $rows = '*', $join = null, $where = null, $order = null, $limit = null){
        $this->clear();
        // Create query from the variables passed to the function
        $q = 'SELECT '.$rows.' FROM '.$table;
        if($join != null){
            $q .= ' JOIN '.$join;
        }
        if($where != null){
            $q .= ' WHERE '.$where;
        }
        if($order != null){
            $q .= ' ORDER BY '.$order;
        }
        if($limit != null){
            $q .= ' LIMIT '.$limit;
        }
        // echo $table;
        $this->myQuery = $q; // Pass back the SQL
        // Check to see if the table exists
        if($this->tableExists($table)){
            // The table exists, run the query
            $query = $this->myconn->query($q);    
            if($query){
                // If the query returns >= 1 assign the number of rows to numResults
                $this->numResults = $query->num_rows;
                // Loop through the query results by the number of rows returned
                for($i = 0; $i < $this->numResults; $i++){
                    $r = $query->fetch_array();
                    $key = array_keys($r);
                    for($x = 0; $x < count($key); $x++){
                        // Sanitizes keys so only alphavalues are allowed
                        if(!is_int($key[$x])){
                            if($query->num_rows >= 1){
                                $this->result[$i][$key[$x]] = $r[$key[$x]];
                            }else{
                                $this->result[$i][$key[$x]] = null;
                            }
                        }
                    }
                }
                return $this; // Query was successful
            }else{
                array_push($this->result,$this->myconn->error);
                return false; // No rows where returned
            }
        }else{
            return false; // Table does not exist
        }
    }

    // Function to insert into the database
    public function insert($table,$params = []){
        // Check to see if the table exists
        if($this->tableExists($table)){
            $sql='INSERT INTO `'.$table.'` (`'.implode('`, `',array_keys($params)).'`) VALUES ("' . implode('", "', $params) . '")';
            $this->myQuery = $sql; // Pass back the SQL
            // Make the query to insert to the database
            if($ins = $this->myconn->query($sql)){
                array_push($this->result,$this->myconn->insert_id);
                return true; // The data has been inserted
            }else{
                array_push($this->result,$this->myconn->error);
                return false; // The data has not been inserted
            }
        }else{
            return false; // Table does not exist
        }
    }

    //Function to delete table or row(s) from database
    public function delete($table,$where = null){
        // Check to see if table exists
        if($this->tableExists($table)){
            // The table exists check to see if we are deleting rows or table
            if($where == null){
                $delete = 'DROP TABLE '.$table; // Create query to delete table
            }else{
                $delete = 'DELETE FROM '.$table.' WHERE '.$where; // Create query to delete rows
            }
            // Submit query to database
            if($del = $this->myconn->query($delete)){
                array_push($this->result,$this->myconn->affected_rows);
                $this->myQuery = $delete; // Pass back the SQL
                return true; // The query exectued correctly
            }else{
                array_push($this->result,$this->myconn->error);
                return false; // The query did not execute correctly
            }
        }else{
            return false; // The table does not exist
        }
    }

    // Function to update row in database
    public function update($table,$params= [] ,$where){
        // Check to see if table exists
        if($this->tableExists($table)){
            // Create Array to hold all the columns to update
            $args= [];
            foreach($params as $field=>$value){
                // Seperate each column out with it's corresponding value
                $args[]=$field.'="'.$value.'"';
            }
            // Create the query
            $sql='UPDATE '.$table.' SET '.implode(',',$args).' WHERE '.$where;
            // Make query to database
            $this->myQuery = $sql; // Pass back the SQL
            if($query = $this->myconn->query($sql)){
                array_push($this->result,$this->myconn->affected_rows);
                return true; // Update has been successful
            }else{
                array_push($this->result,$this->myconn->error);
                return false; // Update has not been successful
            }
        }else{
            return false; // The table does not exist
        }
    }

    // Private function to check if table exists for use with queries
    private function tableExists($table){
        $tablesInDb = $this->myconn->query('SHOW TABLES FROM '.$this->db_name.' LIKE "'.$table.'"');
        if($tablesInDb){
            if($tablesInDb->num_rows == 1){
                return true; // The table exists
            }else{
                array_push($this->result,$table." does not exist in this database");
                return false; // The table does not exist
            }
        }
    }
    public function clear()
    {
        $this->result = [];
    }
    // Public function to return the data to the user
    public function getResult(){
        $val = $this->result;
        $this->result = [];
        return $val;
    }

    // Public function to return the data to the user
    public function get(){
        $val = $this->result;
        $this->result = [];
        return $val;
    }

    // Public function to return the a data to the user
    public function one(){
        $val = $this->result[0];
        if(empty($val)){
            return false;
        }
        $this->result = [];
        return $val;
    }
    
    // Public function to return the a error message to the user
    public function getError(){
        $val = $this->result[0];
        $this->result = [];
        return $val;
    }
    
    //Pass the SQL back for debugging
    public function getSql(){
        $val = $this->myQuery;
        $this->myQuery = [];
        return $val;
    }

    //Pass the number of rows back
    public function numRows(){
        $val = $this->numResults;
        $this->numResults = [];
        return $val;
    }

    //Pass the number of rows back
    public function count(){
        $val = $this->numResults;
        $this->numResults = [];
        return $val;
    }

    // Escape your string
    public function escapeString($data){
        return $this->myconn->real_escape_string($data);
    }

    /**
     * Validate your input , input , required input 
     * 
     * @param mixed $arr input to validate
     * @param mixed $required to check required 
     * 
     * @return array | bool
     */
    public function validateInput($arr = [], $required = []){
        $key = array_diff($required, array_keys($arr)); 
        foreach($required as $check){
            if(in_array($check, $key)){
                array_push($this->result, $check." tidak boleh kosong");
                return $this;
            }
        }
        if(!is_array($arr)){
            return false;
        }
        foreach($arr as $key => $val){
            $val = trim($val);
            $val = stripslashes($val);
            $val = htmlspecialchars($val);
            $val = $this->escapeString($val);

            if(in_array($key, $required)){
                if(empty($val) && strlen($val) == 0){
                    array_push($this->result, $key." tidak boleh kosong");
                    return $this;
                }
            }
            if($key != 'submit'){
                $arrs[$key] = $val;
            }
        }
        return $arrs;

    }

    public function __desctruct()
    {
        $this->clear();
        return $this->disconnect();
    }
} 
