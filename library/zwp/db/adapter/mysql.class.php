<?php

/*
 * Copyright 2015 jiaojie <jiaojie1989@gmail.com>.
 * 
 * Check your synatx if it is right.
 * Check your variables if the names are just correct.
 * Finally, run it under your command line.
 * Enjoy it!
 */

namespace zwp\db\adapter;

/**
 *
 * @author jiaojie <jiaojie1989@gmail.com>
 * @date Feb 4, 2015
 * @version 1.0.0
 * @description
 */
class mysql extends \zwp\db\adapter\abstraction {

    static $stmtClass = '\zwp\db\statement\mysql';

    public function connect() {
        if (isset($this->dbconf['port'])) {
            $host = $this->dbconf['host'] . ':' . $this->dbconf['port'];
        } else {
            $host = $this->dbconf['host'];
        }
        $this->resource = mysql_connect(
                $host, $this->dbconf['username'], $this->dbconf['password']
        );
        if (!$this->resource) {
            throw new Zwp_Db_Exception(mysql_error());
        }
        mysql_select_db($this->dbconf['dbname'], $this->resource);
        if (!empty($this->dbconf['encoding'])) {
            mysql_set_charset($this->dbconf['encoding'], $this->resource);
        }
    }

    protected function _exec($sql) {
        $retval = mysql_query($sql);
        if ($retval === false) {
            $this->handleError();
        }
        return $retval;
    }

    public function prepare($sql) {
        return new self::$stmtClass($this, $sql);
    }

    public function beginTransaction() {
        error_log("Transaction is not supported in 'mysql'");
    }

    public function commit() {
        error_log("Transaction is not supported in 'mysql'");
    }

    public function rollBack() {
        error_log("Transaction is not supported in 'mysql'");
    }

    public function lastInsertId() {
        return mysql_insert_id($this->resource);
    }

    public function disconnect() {
        mysql_close($this->resource);
    }

    protected function handleError() {
        if ($this->error_mode === Zwp_Db::ERRMODE_EXCEPTION) {
            throw new Zwp_Db_Exception(
            mysql_error($this->resource), mysql_errno($this->resource)
            );
        } elseif ($this->error_mode === Zwp_Db::ERRMODE_WARNING) {
            error_log(mysql_error($this->resource));
        }
        $this->error_info = array(
            mysql_errno($this->resource),
            mysql_errno($this->resource),
            mysql_error($this->resource),
        );
    }

    public function quote($value) {
        if (is_int($value) || is_float($value)) {
            return $value;
        }
        return "'" . mysql_real_escape_string($value, $this->resource) . "'";
    }

}
