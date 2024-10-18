<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class Database_Model extends PDO
{
    protected $_is_transaction_level = 0;
    protected $_is_connected = false;
    protected $_affected_rows = 0;
    protected $_last_db_error = "";
    protected $_has_where_part = false;
    private $wf_query;
    private $wf_querytype;
    private $wf_fetch;
    private $wf_bind;
    private $wf_join;
    private $wf_where;
    private $wf_order;
    private $wf_group;
    private $wf_limit;
    private $wf_tables_with_modified = ["HostFact_Debtors", "HostFact_Creditors", "HostFact_Products", "HostFact_Invoice", "HostFact_CreditInvoice", "HostFact_PeriodicElements", "HostFact_PriceQuote", "HostFact_NewOrder", "HostFact_Domains", "HostFact_Handles", "HostFact_Hosting", "HostFact_NewCustomers", "HostFact_SSL_Certificates", "HostFact_VPS_Services"];
    public static $instance;
    public static function getInstance()
    {
        if(!self::$instance) {
            self::$instance = new Database_Model();
        }
        return self::$instance;
    }
    private function reset($queryType = "get")
    {
        $this->wf_query = $this->wf_join = "";
        $this->wf_querytype = $queryType;
        $this->wf_fetch = "object";
        $this->wf_bind = $this->wf_where = $this->wf_order = $this->wf_group = $this->wf_limit = [];
        $this->isUnionQuery = false;
        $this->_affected_rows = 0;
        $this->_last_db_error = "";
        $this->_has_where_part = false;
    }
    public function __construct()
    {
        $this->Error = [];
        if(!@file_exists("connect.php")) {
            fatal_error("Uw databaseverbinding is nog niet geconfigureerd", "Controleer het bestand connect.php om uw database verbinding te herstellen.");
        }
        require_once "connect.php";
        header("Content-type: text/html; charset=utf-8");
        mb_internal_encoding("UTF-8");
        if(defined("DB_CRYPT") && DB_CRYPT) {
            $db_host = defined("DB_HOST") ? $this->crypto(DB_HOST, DB_CRYPT) : "";
            $db_user = defined("DB_USERNAME") ? $this->crypto(DB_USERNAME, DB_CRYPT) : "";
            $db_password = defined("DB_PASSWORD") ? $this->crypto(DB_PASSWORD, DB_CRYPT) : "";
            $db_name = defined("DB_NAME") ? $this->crypto(DB_NAME, DB_CRYPT) : "";
        } elseif(defined("DB_HOST")) {
            $db_host = defined("DB_HOST") ? DB_HOST : "";
            $db_user = defined("DB_USERNAME") ? DB_USERNAME : "";
            $db_password = defined("DB_PASSWORD") ? DB_PASSWORD : "";
            $db_name = defined("DB_NAME") ? DB_NAME : "";
        } else {
            if(@is_dir("install")) {
                if(!defined("INSTALL_HAS_NO_DB_CONNECTION")) {
                    define("INSTALL_HAS_NO_DB_CONNECTION", true);
                }
                return true;
            }
            if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL) {
                fatal_error("No database connection configured", "Please check the connect.php file to recover your database connection.");
            } else {
                fatal_error("Uw databaseverbinding is nog niet geconfigureerd", "Controleer het bestand connect.php om uw database verbinding te herstellen.");
            }
        }
        try {
            @parent::__construct("mysql:dbname=" . $db_name . ";host=" . $db_host, $db_user, $db_password);
            $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, ["wfPDOStatement"]);
            $this->query("SET collation_connection = 'utf8_general_ci', CHARACTER SET 'utf8', NAMES 'utf8', collation_server = 'utf8_general_ci', character_set_server = 'utf8', character_set_results = 'utf8', character_set_connection = 'utf8', character_set_client = 'utf8', collation_database = 'utf8_general_ci'");
            $this->_is_connected = true;
            $strict_check = $this->query("SELECT @@SESSION.sql_mode as Modus");
            if($strict_check) {
                $strict_check = $strict_check->fetch();
                if(strpos(strtolower($strict_check->Modus), "strict") !== false || strpos(strtolower($strict_check->Modus), "traditional") !== false || strpos(strtolower($strict_check->Modus), "no_zero_date") !== false || strpos(strtolower($strict_check->Modus), "only_full_group_by") !== false) {
                    $this->query("SET SESSION sql_mode ='NO_ENGINE_SUBSTITUTION'");
                }
            }
        } catch (PDOException $e) {
            if(!in_array("pdo_mysql", get_loaded_extensions())) {
                if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL) {
                    fatal_error("PDO module not active", "For some features the PHP module PDO is required.<br />Please contact your server administrator to install this module for you.");
                } else {
                    fatal_error("PDO module is niet actief", "Voor een correcte werking van HostFact dient uw server te beschikken over de PDO module.<br />U kunt contact opnemen met uw serverbeheerder om deze module te laten installeren.");
                }
            } elseif(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL) {
                fatal_error("Cannot connect to the database", "Please check the connect.php file to recover your database connection.<br />" . $e->getMessage());
            } else {
                fatal_error("Kan geen verbinding maken met uw database", "Controleer het bestand connect.php om uw database verbinding te herstellen.<br />" . $e->getMessage());
            }
        }
    }
    public function insert($table_name, $data_array)
    {
        $this->reset("insert");
        $this->wf_query = "INSERT INTO " . $this->backTick($table_name) . " (" . $this->backTick(implode(", ", array_keys($data_array))) . ") VALUES (";
        foreach ($data_array as $column => $value) {
            if(is_array($value) && strtolower(key($value)) == "raw" && is_null($value[key($value)])) {
                $this->wf_query .= "NULL, ";
            } elseif(!is_array($value)) {
                $this->wf_query .= $this->bind("insert_" . $column, $value) . ", ";
            } elseif(is_array($value)) {
                $key = key($value);
                $val = $value[$key];
                if(strtolower($key) == "raw") {
                    $this->wf_query .= $val . ", ";
                }
            }
        }
        $main_table = is_array($table_name) ? $table_name[0] : $table_name;
        if(in_array($main_table, $this->wf_tables_with_modified)) {
            $this->wf_query = str_replace(") VALUES", ", " . $this->backTick($main_table) . ".`Created`, " . $this->backTick($main_table) . ".`Modified`) VALUES", $this->wf_query);
            $this->wf_query .= "NOW(), NOW(), ";
        }
        $this->wf_query = rtrim($this->wf_query, ", ") . ")";
        return $this;
    }
    public function insertMultiple($table_name, $multiple_data_array)
    {
        $this->reset("insert");
        $this->wf_query = "INSERT INTO " . $this->backTick($table_name) . " (" . $this->backTick(implode(", ", array_keys($multiple_data_array[0]))) . ") VALUES ";
        foreach ($multiple_data_array as $record_i => $data_array) {
            $this->wf_query .= "(";
            foreach ($data_array as $column => $value) {
                if(is_array($value) && strtolower(key($value)) == "raw" && is_null($value[key($value)])) {
                    $this->wf_query .= "NULL, ";
                } elseif(!is_array($value)) {
                    $this->wf_query .= $this->bind($record_i . "_insert_" . $column, $value) . ", ";
                } elseif(is_array($value)) {
                    $key = key($value);
                    $val = $value[$key];
                    if(strtolower($key) == "raw") {
                        $this->wf_query .= $val . ", ";
                    }
                }
            }
            $this->wf_query = rtrim($this->wf_query, ", ") . "), ";
        }
        $this->wf_query = rtrim($this->wf_query, ", ");
        return $this;
    }
    public function onDuplicate($data_array)
    {
        $this->wf_query .= "ON DUPLICATE KEY UPDATE ";
        foreach ($data_array as $column => $value) {
            if(is_array($value) && strtolower(key($value)) == "raw" && is_null($value[key($value)])) {
                $this->wf_query .= $this->backTick($column) . "=NULL, ";
            } elseif(!is_array($value)) {
                $this->wf_query .= $this->backTick($column) . "=" . $this->bind("set_" . $column, $value) . ", ";
            } elseif(is_array($value)) {
                $key = key($value);
                $val = $value[$key];
                if(strtolower($key) == "raw") {
                    $this->wf_query .= $this->backTick($column) . "=" . $val . ", ";
                }
            }
        }
        $this->wf_query = rtrim($this->wf_query, ", ");
        return $this;
    }
    public function update($table_name, $data_array)
    {
        $this->reset("update");
        $this->wf_query = "UPDATE " . (is_array($table_name) ? implode(", ", $this->backTick($table_name)) : $this->backTick($table_name)) . " SET ";
        foreach ($data_array as $column => $value) {
            if(is_array($value) && strtolower(key($value)) == "raw" && is_null($value[key($value)])) {
                $this->wf_query .= $this->backTick($column) . "=NULL, ";
            } elseif(!is_array($value)) {
                $this->wf_query .= $this->backTick($column) . "=" . $this->bind("set_" . $column, $value) . ", ";
            } elseif(is_array($value)) {
                $key = key($value);
                $val = $value[$key];
                if(strtolower($key) == "raw") {
                    $this->wf_query .= $this->backTick($column) . "=" . $val . ", ";
                }
            }
        }
        $main_table = is_array($table_name) ? $table_name[0] : $table_name;
        if(in_array($main_table, $this->wf_tables_with_modified)) {
            $this->wf_query .= $this->backTick($main_table) . ".`Modified`=NOW(), ";
        }
        $this->wf_query = rtrim($this->wf_query, ", ");
        return $this;
    }
    public function delete($table_name)
    {
        $this->reset("delete");
        $this->wf_query = "DELETE FROM " . $this->backTick($table_name) . " ";
        return $this;
    }
    public function truncate($table_name)
    {
        $this->reset("truncate");
        $this->wf_query = "TRUNCATE TABLE " . $this->backTick($table_name) . " ";
        return $this;
    }
    public function get($table_name, $columns = "*")
    {
        $this->reset("get");
        if(!$columns) {
            $columns = "*";
        }
        $columns = !is_array($columns) ? explode(",", $columns) : $columns;
        $columns = implode(", ", $this->backTick($columns, "array"));
        $this->wf_query = "SELECT " . $columns . " FROM " . (is_array($table_name) ? "(" . implode(", ", $this->backTick($table_name)) . ")" : $this->backTick($table_name)) . " ";
        return $this;
    }
    public function getUnion($table_name, $columns = "*")
    {
        $this->prepareQuery();
        $this->wf_join = "";
        $this->wf_where = $this->wf_order = $this->wf_group = $this->wf_limit = [];
        $this->isUnionQuery = $this->isUnionQuery === false ? "_union_1" : "_union_" . (str_replace("_union_", "", $this->isUnionQuery) + 1);
        if(!$columns) {
            $columns = "*";
        }
        $columns = !is_array($columns) ? explode(",", $columns) : $columns;
        $columns = implode(", ", $this->backTick($columns, "array"));
        if(substr($this->wf_query, 0, 1) != "(") {
            $this->wf_query = "(" . $this->wf_query;
        }
        $this->wf_query .= ") UNION (SELECT " . $columns . " FROM " . (is_array($table_name) ? "(" . implode(", ", $this->backTick($table_name)) . ")" : $this->backTick($table_name)) . " ";
        return $this;
    }
    public function closeUnion()
    {
        if(isset($this->isUnionQuery) && $this->isUnionQuery !== false) {
            $this->prepareQuery();
            $this->wf_query .= ")";
            $this->wf_join = "";
            $this->wf_where = $this->wf_order = $this->wf_group = $this->wf_limit = [];
            $this->isUnionQuery = false;
        }
        return $this;
    }
    public function getOne($table_name, $columns = "*")
    {
        $this->get($table_name, $columns);
        $this->wf_querytype = "getOne";
        return $this;
    }
    public function rowCount($table_name, $columns = "*")
    {
        $this->wf_querytype = "rowCount";
        $this->wf_query = "SELECT " . $this->backTick($columns) . " FROM " . (is_array($table_name) ? "(" . implode(", ", $this->backTick($table_name)) . ")" : $this->backTick($table_name)) . " ";
        $this->wf_order = [];
        $this->wf_limit = [];
        return $this->execute();
    }
    public function getGroupedData($table_name, $grouped_columns = "")
    {
        $this->wf_querytype = "getOne";
        if($grouped_columns) {
            $grouped_columns = !is_array($grouped_columns) ? explode(",", $grouped_columns) : $grouped_columns;
            $grouped_columns = implode(", ", $this->backTick($grouped_columns, "array"));
            $this->wf_query = "SELECT " . $grouped_columns . " FROM " . (is_array($table_name) ? "(" . implode(", ", $this->backTick($table_name)) . ")" : $this->backTick($table_name)) . " ";
            $this->wf_order = [];
            $this->wf_limit = [];
            return $this->execute();
        }
        return false;
    }
    public function rowCountWithIDOptimalisation($table_name, $columns = "*", $optimalisation_id = "")
    {
        $current_props = ["wf_querytype" => $this->wf_querytype, "wf_query" => $this->wf_query, "wf_order" => $this->wf_order, "wf_limit" => $this->wf_limit];
        $this->wf_querytype = "rowCountWithIDs";
        $this->wf_query = "SELECT " . $this->backTick($columns) . " FROM " . (is_array($table_name) ? "(" . implode(", ", $this->backTick($table_name)) . ")" : $this->backTick($table_name)) . " ";
        $this->wf_order = [];
        $this->wf_limit = [];
        $result = $this->execute();
        foreach ($current_props as $k => $v) {
            $this->{$k} = $v;
        }
        if(count($result) < 1000) {
            $old_wf_where = $this->wf_where;
            $this->wf_where = [];
            foreach ($this->wf_bind as $k => $v) {
                if(substr($k, 0, 6) == "where_") {
                    unset($this->wf_bind[$k]);
                }
            }
            foreach ($old_wf_where as $k => $v) {
                if(isset($v[0]) && isset($v[1]) && $v[1] === false && strpos($v[0], "=") !== false && is_array($table_name)) {
                    $this->wf_where[] = $v;
                }
            }
            $ids = [];
            foreach ($result as $_result) {
                $ids[] = $_result->id;
            }
            if(!empty($ids)) {
                $this->where($optimalisation_id, ["IN" => $ids]);
            } else {
                $this->where($optimalisation_id, 0);
            }
        }
        return count($result);
    }
    public function getAffectedRows()
    {
        return $this->_affected_rows;
    }
    public function showColumns($table_name)
    {
        $this->reset("get");
        $this->wf_query = "SHOW COLUMNS FROM " . $this->backTick($table_name) . "";
        return $this;
    }
    public function noWhere()
    {
        $this->_has_where_part = true;
        return $this;
    }
    public function where($column, $value = false)
    {
        $this->_has_where_part = true;
        $this->wf_where[] = [$column, $value];
        return $this;
    }
    public function orWhere($or_array)
    {
        $this->_has_where_part = true;
        $this->wf_where[] = ["OR" => $or_array];
        return $this;
    }
    public function join($table_name, $join_condition, $join_type = "LEFT")
    {
        $join_type = strtoupper(trim($join_type));
        $table_name = htmlspecialchars($table_name);
        $this->wf_join .= $join_type . " JOIN " . $this->backTick($table_name) . " ON (" . $this->backTick($join_condition) . ") ";
        return $this;
    }
    public function orderBy($column, $direction = "ASC")
    {
        $direction = strtoupper(trim($direction));
        $columnkey = str_replace(".", "_", $column);
        $columnkey = preg_replace("/[^a-z0-9\\_]/i", "", $columnkey);
        if(!in_array($direction, ["ASC", "DESC"])) {
            $direction = "ASC";
        }
        $this->wf_order[$columnkey] = ["column" => $column, "direction" => $direction];
        return $this;
    }
    public function groupBy($column)
    {
        if(is_array($column) && isset($column["RAW"])) {
            $column = $column["RAW"];
        } else {
            $column = preg_replace("/[^-a-z0-9\\.\\(\\),_]+/i", "", $column);
        }
        $this->wf_group[] = $column;
        return $this;
    }
    public function limit($offset, $rowcount)
    {
        $this->wf_limit = ["offset" => $offset, "rowcount" => $rowcount];
        return $this;
    }
    private function prepareQuery()
    {
        if($this->wf_join) {
            $this->wf_query .= " " . $this->wf_join;
        }
        if(!empty($this->wf_where)) {
            $this->wf_query .= " WHERE ";
            $this->wf_query .= $this->parseWhereArray($this->wf_where);
        }
        if(!empty($this->wf_group)) {
            $this->wf_query .= " GROUP BY ";
            foreach ($this->wf_group as $column) {
                $this->wf_query .= $this->backTick($column) . ", ";
            }
            $this->wf_query = rtrim($this->wf_query, ", ") . " ";
        }
        if(!empty($this->wf_order)) {
            $this->wf_query .= " ORDER BY ";
            foreach ($this->wf_order as $column_key => $column) {
                $this->wf_query .= $this->backTick($column["column"]) . " " . $column["direction"] . ", ";
            }
            $this->wf_query = rtrim($this->wf_query, ", ") . " ";
        }
        if(!empty($this->wf_limit)) {
            $this->wf_query .= " LIMIT " . $this->wf_limit["offset"] . ", " . $this->wf_limit["rowcount"];
        }
    }
    public function rawQuery($query, $bind_array = [])
    {
        $pdo_statement = self::prepare($query);
        if(is_array($bind_array)) {
            foreach ($bind_array as $binder => $value) {
                $pdo_statement->bindValue(":" . $binder, $value);
            }
        }
        $query_result = $pdo_statement->execute();
        if(!$query_result) {
            $this->_last_db_error = $pdo_statement->errorInfo();
            return false;
        }
        return $pdo_statement;
    }
    public function getLastError()
    {
        return is_array($this->_last_db_error) ? $this->_last_db_error[2] : false;
    }
    protected function stringifyResult($result)
    {
        if(is_object($result)) {
            $objectVars = get_object_vars($result);
            foreach ($objectVars as $key => $value) {
                if(is_int($value) || is_float($value)) {
                    $result->{$key} = (string) $value;
                }
            }
            return $result;
        } elseif(is_array($result)) {
            foreach ($result as $key => $value) {
                if(is_array($value) || is_object($value)) {
                    $result[$key] = $this->stringifyResult($value);
                } elseif(is_int($value) || is_float($value)) {
                    $result[$key] = (string) $value;
                }
            }
            return $result;
        } else {
            if($result === false) {
                return false;
            }
            throw new Exception("Invalid stringifyResult");
        }
    }
    public function execute()
    {
        if(defined("INSTALL_HAS_NO_DB_CONNECTION") && $this->_is_connected === false) {
            return false;
        }
        $this->prepareQuery();
        if($this->_has_where_part === false && in_array($this->wf_querytype, ["update", "delete"])) {
            fatal_error("UPDATE or DELETE needs a WHERE", "SQL: " . $this->wf_query);
        }
        $pdo_statement = self::prepare($this->wf_query);
        if(is_array($this->wf_bind)) {
            krsort($this->wf_bind);
            foreach ($this->wf_bind as $binder => $value) {
                if(strpos($this->wf_query, ":" . $binder) !== false) {
                    $pdo_statement->bindValue(":" . $binder, $value);
                }
            }
        }
        $query_result = $pdo_statement->execute();
        if(!$query_result) {
            $this->_last_db_error = $pdo_statement->errorInfo();
            return false;
        }
        if($this->wf_querytype == "insert") {
            $this->_affected_rows = $pdo_statement->rowCount();
            return $this->lastInsertId();
        }
        if($this->wf_querytype == "update") {
            $this->_affected_rows = $pdo_statement->rowCount();
            return true;
        }
        if($this->wf_querytype == "delete") {
            return true;
        }
        if($this->wf_querytype == "rowCount") {
            return $pdo_statement->rowCount();
        }
        if($this->wf_querytype == "rowCountWithIDs") {
            return $pdo_statement->fetchAll();
        }
        if($this->wf_querytype == "getOne") {
            $result = $this->wf_fetch == "array" ? $pdo_statement->fetch(PDO::FETCH_ASSOC) : $pdo_statement->fetch();
            return $this->stringifyResult($result);
        }
        if($this->wf_querytype == "get") {
            $result = $this->wf_fetch == "array" ? $pdo_statement->fetchAll(PDO::FETCH_ASSOC) : $pdo_statement->fetchAll();
            return $this->stringifyResult($result);
        }
        return true;
    }
    public function printQuery($with_values = true)
    {
        $this->prepareQuery();
        if($with_values === false) {
            return $this->wf_query . "<br /><br />" . print_r_pre($this->wf_bind) . "<br /><br />";
        }
        $return_query = $this->wf_query;
        foreach ($this->wf_bind as $key => $value) {
            $return_query = str_replace(":" . $key, "'" . $value . "'", $return_query);
        }
        return $return_query;
    }
    private function parseWhereArray($where_array, $key_prefix = "")
    {
        $wf_query = "";
        foreach ($where_array as $where_count => $value) {
            if(0 < $where_count) {
                $wf_query .= " AND ";
            }
            if(isset($value["OR"])) {
                $wf_query .= "(";
                foreach ($value["OR"] as $k => $tmp_value) {
                    if(count($tmp_value) == 1) {
                        $tmp_value[1] = false;
                    }
                    if(0 < $k) {
                        $wf_query .= " OR ";
                    }
                    $wf_query .= $this->parseWhereArray([$tmp_value], $key_prefix . $where_count . $k);
                }
                $wf_query .= ")";
            } elseif(isset($value["AND"])) {
                $wf_query .= "(";
                foreach ($value["AND"] as $k => $tmp_value) {
                    if(count($tmp_value) == 1) {
                        $tmp_value[1] = false;
                    }
                    if(0 < $k) {
                        $wf_query .= " AND ";
                    }
                    $wf_query .= $this->parseWhereArray([$tmp_value], $key_prefix . $where_count . $k);
                }
                $wf_query .= ")";
            } elseif(isset($value[1]) && is_array($value[1])) {
                $key = key($value[1]);
                $val = $value[1][$key];
                $column = $this->backTick($value[0]);
                strtolower($key);
                switch (strtolower($key)) {
                    case "in":
                    case "not in":
                        $comparison = "";
                        foreach ($val as $k => $v) {
                            if(strtolower($k) == "raw") {
                                $comparison = $v;
                                $wf_query .= $column . " " . strtoupper($key) . " (" . rtrim($comparison, ",") . ")";
                            } else {
                                $comparison .= $this->bind("where_in_" . $column . "_" . $key_prefix . $where_count . "_" . $k, $v) . " ,";
                            }
                        }
                        break;
                    case "between":
                        $wf_query .= $column . " BETWEEN " . $this->bind("where_" . $column . "_" . $key_prefix . $where_count . "_a", $val[0]) . " AND " . $this->bind("where_" . $column . "_" . $key_prefix . $where_count . "_b", $val[1]);
                        break;
                    case "raw":
                        $wf_query .= $column . "=" . $val;
                        break;
                    default:
                        if(!in_array($key, ["=", "!=", ">", "<", ">=", "<=", "<>", "LIKE", "NOT LIKE", "REGEXP", "NOT REGEXP", "IS", "IS NOT", "EXISTS", "NOT EXISTS"])) {
                            fatal_error("MySQL KEY", "Wrong MySQL parameter:" . $key);
                            $key = "=";
                        }
                        if(is_array($val) && strtolower(key($val)) == "raw") {
                            $wf_query .= $column . " " . $key . " " . $val[key($val)];
                        } else {
                            $wf_query .= $column . " " . $key . " " . $this->bind("where_" . $column . "_" . $key_prefix . $where_count, $val);
                        }
                }
            } elseif(!isset($value[1]) || $value[1] === NULL) {
                fatal_error("MySQL VALUE IS NULL", "Wrong MySQL parameter:" . $value[0] . " should not be NULL");
            } elseif($value[1] === false) {
                $column = $this->backTick($value[0]);
                $wf_query .= $column;
            } else {
                $column = $this->backTick($value[0]);
                $wf_query .= $column . "=" . $this->bind("where_" . $column . "_" . $key_prefix . $where_count, $value[1]);
            }
        }
        return $wf_query;
    }
    public function asArray()
    {
        $this->wf_fetch = "array";
        return $this;
    }
    public function bindValue($key, $value)
    {
        $key = str_replace(".", "_", $key);
        $key = preg_replace("/[^a-z0-9\\_]/i", "", $key);
        $this->wf_bind[$key] = $value;
        return $this;
    }
    private function bind($key, $value)
    {
        $key = str_replace(".", "_", $key);
        $key = preg_replace("/[^a-z0-9\\_]/i", "", $key);
        if($this->isUnionQuery !== false) {
            $key .= $this->isUnionQuery;
        }
        $this->wf_bind[$key] = $value;
        return ":" . $key;
    }
    private function backTick($string, $stringType = "string")
    {
        if(is_array($string)) {
            if($stringType == "array") {
                foreach ($string as $k => $v) {
                    $string[$k] = strpos($v, "`") !== false ? $v : $this->backTick($v);
                }
            } else {
                $string = $this->backTick($string, "array");
            }
        } elseif(strpos($string, "`") === false) {
            $string = preg_replace("/([a-z0-9\\_]+)/i", "`\\1`", $string);
            $string = str_replace(["`as`", "`AS`"], "AS", $string);
        }
        return $string;
    }
    private function crypto($str, $ky = "")
    {
        $ori_str = $str;
        if($ky == "") {
            return $str;
        }
        $ky = str_replace(chr(32), "", $ky);
        if(strlen($ky) < 8) {
            fatal_error("No database connection configured", "Please regenerate the connect.php file to recover your database connection: <a href=\"" . INTERFACE_URL . "/hosting/connect_generator.php\">" . INTERFACE_URL . "/hosting/connect_generator.php</a>");
        }
        $kl = strlen($ky) < 32 ? strlen($ky) : 32;
        $k = [];
        for ($i = 0; $i < $kl; $i++) {
            $k[$i] = ord($ky[$i]) & 31;
        }
        $j = 0;
        for ($i = 0; $i < strlen($str); $i++) {
            $e = ord($str[$i]);
            $str[$i] = $e & 224 ? chr($e ^ $k[$j]) : chr($e);
            $j++;
            $j = $j == $kl ? 0 : $j;
        }
        if(trim($str) != $str || rtrim($str) != $str) {
            $str = "AA" . $ori_str . "AA";
            $j = 0;
            for ($i = 0; $i < strlen($str); $i++) {
                $e = ord($str[$i]);
                $str[$i] = $e & 224 ? chr($e ^ $k[$j]) : chr($e);
                $j++;
                $j = $j == $kl ? 0 : $j;
            }
        }
        if(substr($str, 0, 2) == "AA" && substr($str, -2) == "AA") {
            $str = substr($str, 2, -2);
        }
        return $str;
    }
    public function beginTransaction()
    {
        if($this->_is_transaction_level <= 0) {
            parent::beginTransaction();
        }
        $this->_is_transaction_level++;
        return true;
    }
    public function rollBack()
    {
        $this->_is_transaction_level--;
        if($this->_is_transaction_level <= 0) {
            return parent::rollBack();
        }
        return true;
    }
    public function commit()
    {
        $this->_is_transaction_level--;
        if($this->_is_transaction_level <= 0) {
            return parent::commit();
        }
        return true;
    }
    public function getDatabaseName()
    {
        return defined("DB_CRYPT") && DB_CRYPT ? $this->crypto(DB_NAME, DB_CRYPT) : DB_NAME;
    }
}
class wfPDOStatement extends PDOStatement
{
    public function bindValue($parameter, $value = PDO::PARAM_STR, $data_type)
    {
        if($value === NULL) {
            $value = "";
        }
        return parent::bindValue($parameter, $value, $data_type);
    }
    public function execute($parameters)
    {
        try {
            if(!empty($parameters)) {
                return parent::execute($parameters);
            }
            return parent::execute();
        } catch (PDOException $e) {
            $_SESSION["flashMessage"]["Error"][] = "MySQL-error: " . $e->getMessage();
            return false;
        }
    }
}
$error_class = new error_class();
if(file_exists("class/settings.php")) {
    require_once "class/settings.php";
} else {
    fatal_error("A file is missing", "Can not find the \"settings.php\" file.");
}
if(!defined("INSTALL_HAS_NO_DB_CONNECTION")) {
    $settings = new settings();
    $settings->show();
}
class error_class
{
    public $Error = [];
    public $Warning = [];
    public $Success = [];
}

?>