<?php

/*
 * Copyright 2015 jiaojie <jiaojie1989@gmail.com>.
 * 
 * Check your synatx if it is right.
 * Check your variables if the names are just correct.
 * Finally, run it under your command line.
 * Enjoy it!
 */

namespace zwp\db\statement;

/**
 *
 * @author jiaojie <jiaojie1989@gmail.com>
 * @date Feb 4, 2015
 * @version 1.0.0
 * @description
 */
class mysql extends \zwp\db\statement\abstraction {

    protected $result;
    static protected $FETCH_MODE_MAP = array(
        \zwp\db::FETCH_ASSOC => MYSQL_ASSOC,
        \zwp\db::FETCH_NUM => MYSQL_NUM,
        \zwp\db::FETCH_BOTH => MYSQL_BOTH,
    );

    protected function prepare($sql) {
        return explode('?', $sql);
    }

    protected function _execute(array $params = null) {
        if (null === $params) {
            $params = array();
        }
        if (count($params) != count($this->statement) - 1) {
            $this->handleError('Invalid parameter number: number of bound variables does not match number of tokens');
            return false;
        }
        $sql = '';
        foreach ($params as $i => $bind) {
            $sql .= $this->statement[$i]
                    . (is_string($bind) ? $this->connection->quote($bind) : $bind);
        }
        $sql .= $this->statement[count($params)];
        $this->result = mysql_query($sql, $this->connection_resource);
        if ($this->result === false) {
            $this->handleError();
        }
        return $this->result;
    }

    public function fetch($fetch_mode = null, $orientation = null, $offset = null) {
        if (is_resource($this->result)) {
            return mysql_fetch_array($this->result, $this->convertFetchMode($fetch_mode));
        }
        return false;
    }

    protected function convertFetchMode($fetch_mode = null) {
        if (null === $fetch_mode) {
            $fetch_mode = $this->fetch_mode;
        }
        if (!isset(self::$FETCH_MODE_MAP[$fetch_mode])) {
            $fetch_mode = \zwp\db::FETCH_ASSOC;
        }
        return self::$FETCH_MODE_MAP[$fetch_mode];
    }

    protected function handleError($msg = null) {
        if (null === $msg) {
            $msg = mysql_error($this->connection_resource);
        }
        if ($this->error_mode === \zwp\db::ERRMODE_EXCEPTION) {
            throw new \zwp\db\exception($msg);
        } elseif ($this->error_mode === \zwp\db::ERRMODE_WARNING) {
            error_log($msg);
        }
    }

    public function errorCode() {
        return mysql_errno($this->connection_resource);
    }

    public function errorInfo() {
        return array(
            mysql_errno($this->connection_resource),
            mysql_errno($this->connection_resource),
            mysql_error($this->connection_resource),
        );
    }

    function __destruct() {
        if ($this->result) {
            mysql_free_result($this->result);
        }
    }

}
