<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 * 
 * 
 */
!defined('IN_WEB') ? exit : true;

class Database {

    public $db_prefix;
    public $charset;
    public $collate; //TODO
    public $min_search_char;
    private $search_min_char;
    private $silent;
    protected $dblink;
    protected $dbhost;
    protected $db;
    protected $dbuser;
    protected $dbpassword;
    //Logging
    private $query_stats;
    private $query_history = [];

    function __construct($dbhost, $db, $dbuser, $dbpassword) {
        $this->db_prefix = "pb_";
        $this->charset = "utf8";
        $this->collate = "utf8_general_ci";
        $this->min_search_char = 2;
        $this->dbhost = $dbhost;
        $this->db = $db;
        $this->dbuser = $dbuser;
        $this->dbpassword = $dbpassword;
        $this->silent = false;

        $this->query_stats = 0;
    }

    function __destruct() {
        $this->close();
    }

    function connect() {
        $this->dblink = new mysqli($this->dbhost, $this->dbuser, $this->dbpassword, $this->db);
        if ($this->dblink->connect_errno) {
            printf("Failed to connect to database: %s\n ", $this->dblink->connect_error);
            exit();
        }
        $this->query("SET NAMES " . $this->charset . "");
        return true;
    }

    function set_prefix($prefix) {
        $this->db_prefix = $prefix;
    }

    function set_charset($charset) {
        $this->charset = $charset;
    }

    function set_collate($collate) {
        $this->collate = $collate;
    }

    function set_minchar_search($value) {
        $this->min_search_char = $value;
    }

    function silent($value = true) {
        $this->silent = $value;
    }

    function query($query) {
        $this->query_stats++;
        $this->query_history[] = $query;

        $result = $this->dblink->query($query);
        if (!$result && !$this->silent) {
            $this->dbdie($query);
        }
        return $result;
    }

    function fetch($query) {
        return $row = $query->fetch_assoc();
    }

    function fetch_all($query) {
        $return_ary = [];
        if ($this->num_rows($query) > 0) {
            while ($row = $this->fetch($query)) {
                $return_ary[] = $row;
            }
        }
        return $return_ary;
    }

    function escape($var) {
        return $this->dblink->real_escape_string($var);
    }

    function escape_strip($var) {
        return $this->dblink->real_escape_string(strip_tags($var));
    }

    function num_rows($result) {
        return $result->num_rows;
    }

    function close() {
        !$this->dblink ? die('Could not connect: ' . $this->dblink->error) : false;
        $this->dblink->close();
    }

    private function dbdie($query) {
        printf("\n<b>Error: Unable to retrieve information.</b>");
        printf("\n<br>%s", $query);
        printf("\n<br>reported: %s", $this->dblink->error);
        $this->close();
        exit;
    }

    function insert_id() {
        if (!($id = $this->dblink->insert_id)) {
            die('Could not connect: ' . $this->dblink->error);
            $this->dblink->close();
            exit;
        }

        return $id;
    }

    function free(& $query) {
        $query->free();
    }

    function table_exist($table) {
        $query = "SHOW TABLES LIKE '$table'";
        $result = $this->query($query);
        if ($this->num_rows($result) == 1) {
            return true;
        } else {
            return false;
        }
    }

    function get_next_num($table, $field) {

        if (empty($table) || empty($field)) {
            return false;
        }
        $table = $this->db_prefix . $table;
        $query = "SELECT MAX( $field ) AS max FROM `$table`;";
        $result = $this->query($query);
        $row = $this->fetch($result);

        return ++$row['max'];
    }

    /*
     * $db->select_all("users", array('uid' => 1, 'username' => "myname"), "LIMIT 1"); 
     * Especify operator default '=';
     * $query = $db->select_all("news", array ("frontpage" => array("value"=> 1, "operator" => "="), "moderation" => 0, "disabled" => 0));
     * extra not array 
     */

    function select_all($table, $where = null, $extra = null, $logic = "AND") {

        if (empty($table)) {
            return false;
        }
        $query = "SELECT * FROM " . $this->db_prefix . $table;

        if (!empty($where)) {
            $query .= " WHERE ";
            $query .= $this->where_process($where, $logic);
        }
        !empty($extra) ? $query .= " $extra" : false;

        return $this->query($query);
    }

    function select($table, $what, $where = null, $extra = null, $logic = "AND") {
        if (empty($table) || empty($what)) {
            return false;
        }
        $query = "SELECT " . $what . " FROM " . $this->db_prefix . $table;

        if (!empty($where)) {
            $query .= " WHERE ";
            $query .= $this->where_process($where, $logic);
        }
        !empty($extra) ? $query .= " $extra" : false;

        return $this->query($query);
    }

    /* */

    function search($table, $s_fields, $searchText, $where = null, $extra = null) {

        $s_words_ary = explode(" ", $searchText);
        $fields_ary = explode(" ", $s_fields);

        $where_s_fields = "";
        $where_s_tmp = "";
        $query = "SELECT * FROM " . $this->db_prefix . $table . " WHERE ";

        if (!empty($where)) {
            $query .= $this->where_process($where, $logic = "AND");
            $query .= " AND ";
        }

        foreach ($fields_ary as $field) {
            !empty($where_s_fields) ? $where_s_fields .= " OR " : false;

            foreach ($s_words_ary as $s_word) {
                if (mb_strlen($s_word, $this->charset) > $this->search_min_char) {
                    !empty($where_s_tmp) ? $where_s_tmp .= " AND " : false;
                    $where_s_tmp .= " $field LIKE '%$s_word%' ";
                }
            }
            !empty($where_s_tmp) ? $where_s_fields .= $where_s_tmp : false;
            $where_s_tmp = "";
        }

        if (!empty($where_s_fields)) {
            $query .= "(" . $where_s_fields . ")";
        } else {
            return false;
        }
        !empty($extra) ? $query .= " $extra " : false;

        return $this->query($query);
    }

    /*  */

    function update($table, $set, $where = null, $extra = null, $logic = "AND") {

        $query = "UPDATE " . $this->db_prefix . $table . " SET ";

        if (empty($set) || empty($table)) {
            return false;
        }
        $query .= $this->set_process($set);

        if (!empty($where)) {
            $query .= " WHERE " . $this->where_process($where, $logic);
        }
        !empty($extra) ? $query .= " $extra" : false;
        return $this->query($query);
    }

    /*  */

    function insert($table, $insert_data, $extra = null) {

        if (empty($table) || empty($insert_data)) {
            return false;
        }
        $insert_ary = $this->insert_process($insert_data);
        $query = "INSERT INTO " . $this->db_prefix . $table . " ( {$insert_ary['fields']} ) VALUES ( {$insert_ary['values']} ) $extra";

        return $this->query($query);
    }

    function delete($table, $where, $extra = null, $logic = 'AND') {

        if (empty($table) || empty($where)) {
            return false;
        }
        $query = "DELETE FROM " . $this->db_prefix . $table . " WHERE ";
        $query .= $this->where_process($where, $logic);
        !empty($extra) ? $query .= " $extra" : false;

        return $this->query($query);
    }

    function upsert($table, $set_ary, $where_ary) {
        $insert_data = array_merge($where_ary, $set_ary);
        $set_data = $this->set_process($set_ary);
        $this->insert($table, $insert_data, "ON DUPLICATE KEY UPDATE $set_data");
    }

    function num_querys() {
        return $this->query_stats;
    }

    function get_query_history() {
        return $this->query_history;
    }

    private function insert_process($insert_data) {
        foreach ($insert_data as $field => $value) {
            $fields_ary[] = $field;
            $values_ary[] = "'" . $value . "'";
        }
        $insert['fields'] = implode(', ', $fields_ary);
        $insert['values'] = implode(', ', $values_ary);

        return $insert;
    }

    private function set_process($set) {
        foreach ($set as $field => $value) {
            $newset[] = "$field = " . "'" . $value . "'";
        }
        $query = implode(',', $newset);
        return $query;
    }

    private function where_process($where, $logic) {

        foreach ($where as $field => $value) {
            if (!is_array($value)) {
                $q_where_fields[] = "$field = " . "'" . $value . "'";
            } else {
                $q_where_fields[] = "$field {$value['operator']} '" . $value['value'] . "'";
                //$q_where_fields[] = "$field {$value['operator']} " . $value['value']; CHANGE 100818
            }
        }
        $query = implode(" $logic ", $q_where_fields);
        return $query;
    }

}
