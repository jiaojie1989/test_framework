<?php

/*
 * Copyright 2015 jiaojie <jiaojie1989@gmail.com>.
 * 
 * Check your synatx if it is right.
 * Check your variables if the names are just correct.
 * Finally, run it under your command line.
 * Enjoy it!
 */

namespace zwp\db\adapter\pdo;

/**
 *
 * @author jiaojie <jiaojie1989@gmail.com>
 * @date Feb 4, 2015
 * @version 1.0.0
 * @description
 */
class abstraction extends \zwp\db\adapter\abstraction {

    protected $type;
    static $stmtClass = '\zwp\db\statement\pdo';
    //static $oci;
    static $conn = array();

    public function connect() {
        $dsn = $this->dbconf;
        foreach (array('username', 'password', 'encoding', 'driver_options') as $name) {
            unset($dsn[$name]);
        }
        foreach ($dsn as $key => $val) {
            $dsn[$key] = $key . '=' . $val;
        }
        try {
            $dsn_key = md5($this->type . $this->dbconf['username'] . $this->dbconf['password']);
            if (isset(self::$conn[$dsn_key]) && self::$conn[$dsn_key] instanceof \PDO) {
                $this->resource = self::$conn[$dsn_key];
            } else {
                if ('mysql' == $this->type) {
                    self::$conn[$dsn_key] = new \PDO($this->type . ':' . implode(';', $dsn), $this->dbconf['username'], $this->dbconf['password'], $this->dbconf['driver_options']);
                    $this->resource = self::$conn[$dsn_key];
                } else if ('oci' == $this->type) {
                    self::$conn[$dsn_key] = new \PDO($this->dbconf['tns'], $this->dbconf['username'], $this->dbconf['password']);
                    $this->resource = self::$conn[$dsn_key];
                } else {
                    throw new \Exception("未知数据库连接类型");
                }
            }
            $this->resource->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new \zwp\db\exception($e->getMessage(), $e->getCode());
        }
    }

    protected function handleException($e) {
        if ($this->error_mode === \zwp\db::ERRMODE_EXCEPTION) {
            throw new \zwp\db\exception($e->getMessage(), $e->getCode());
        } else {
            $this->error_info = $e->errorInfo;
        }
    }

    protected function _exec($sql) {
        try {
            return $this->resource->exec($sql);
        } catch (\PDOException $e) {
            $this->handleException($e);
            return false;
        }
    }

    public function prepare($sql) {
        try {
            $stmt = new self::$stmtClass($this, $sql);
            if ($stmt->getStatement() === false) {
                return false;
            }
            return $stmt;
        } catch (\PDOException $e) {
            $this->handleException($e);
            return false;
        }
    }

    public function beginTransaction() {
        return $this->resource->beginTransaction();
    }

    public function commit() {
        return $this->resource->commit();
    }

    public function rollBack() {
        return $this->resource->rollBack();
    }

    public function errorCode() {
        return $this->resource->errorCode();
    }

    public function errorInfo() {
        return $this->resource->errorInfo();
    }

    public function lastInsertId() {
        return $this->resource->lastInsertId();
    }

    public function quote($value, $type = null) {
        return $this->resource->quote($value);
    }

    public function disconnect() {
        $this->resource = null;
    }

}
