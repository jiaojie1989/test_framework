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
class mysqli extends \zwp\db\adapter\abstraction {

    protected $stmt;
    static $stmtClass = 'Zwp_Db_Statement_Mysqli';

    public function connect() {
        $this->resource = mysqli_init();
        if (!empty($this->dbconf['driver_options'])) {
            foreach ($this->dbconf['driver_options'] as $name => $value) {
                mysqli_options($this->resource, $option, $value);
            }
        }
        $retval = @mysqli_real_connect(
                        $this->resource, $this->dbconf['host'], $this->dbconf['username'], $this->dbconf['password'], $this->dbconf['dbname'], isset($this->dbconf['port']) ? (int) $this->dbconf['port'] : null
        );
        if ($retval === false || mysqli_connect_errno()) {
            $this->disconnect();
            throw new \zwp\db\exception(mysqli_connect_error());
        }
        if (!empty($this->dbconf['encoding'])) {
            $this->resource->set_charset($this->dbconf['encoding']);
        }
    }

    protected function _exec($sql) {
        $retval = $this->resource->real_query($sql);
        if ($retval === false) {
            $this->handleError();
        }
        return $retval;
    }

    public function prepare($sql) {
        $stmt = new self::$stmtClass($this, $sql);
        if ($stmt->getStatement() === false || $this->resource->errno) {
            return false;
        }
        return $stmt;
    }

    public function beginTransaction() {
        $this->resource->autocommit(false);
    }

    public function commit() {
        $this->resource->commit();
        $this->resource->autocommit(true);
    }

    public function rollBack() {
        $this->resource->rollback();
        $this->resource->autocommit(true);
    }

    public function lastInsertId() {
        return $this->resource->insert_id;
    }

    public function errorCode() {
        return substr($this->resource->sqlstate, 0, 5);
    }

    public function errorInfo() {
        return array(
            substr($this->resource->sqlstate, 0, 5),
            $this->resource->errno,
            $this->resource->error
        );
    }

    public function quote($value) {
        if (is_int($value) || is_float($value)) {
            return $value;
        }
        return "'" . $this->resource->real_escape_string($value) . "'";
    }

    public function disconnect() {
        $this->resource->close();
    }

    protected function handleError() {
        if ($this->error_mode === \zwp\db::ERRMODE_EXCEPTION) {
            throw new \zwp\db\exception($this->resource->error, $this->resource->errno);
        } elseif ($this->error_mode === \zwp\db::ERRMODE_WARNING) {
            error_log($this->resource->error);
        }
    }

}
