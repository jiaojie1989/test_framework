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
class mysqli extends \zwp\db\statement\abstraction {

    protected $meta;
    protected $keys;
    protected $values;

    protected function prepare($sql) {
        $stmt = $this->connection_resource->prepare($sql);
        if ($stmt === false || $this->connection_resource->errno) {
            $this->handleError();
        }
        return $stmt;
    }

    protected function _execute(array $params = null) {
        if (is_array($params)) {
            array_unshift($params, str_repeat('s', count($params)));
            call_user_func_array(array($this->statement, 'bind_param'), $params);
        }
        $retval = $this->statement->execute();
        if ($retval === false) {
            $this->handleError();
        }
        $this->meta = $this->statement->result_metadata();
        if ($this->statement->errno) {
            $this->handleError();
        }
        if ($this->meta !== false) {
            $this->keys = array();
            foreach ($this->meta->fetch_fields() as $col) {
                $this->keys[] = $col->name;
            }
            $this->values = array_fill(0, count($this->keys), null);
            $refs = array();
            foreach ($this->values as $i => &$f) {
                $refs[$i] = &$f;
            }
            $this->statement->store_result();
            call_user_func_array(array($this->statement, 'bind_result'), $this->values);
        }
        return $retval;
    }

    public function fetch($fetch_mode = null, $orientation = null, $offset = null) {
        $retval = $this->statement->fetch();
        if ($retval === null || $retval === false) {
            $this->statement->reset();
            return false;
        }
        if (null === $fetch_mode) {
            $fetch_mode = $this->fetch_mode;
        }
        $values = array();
        foreach ($this->values as $k => $v) {
            $values[] = $v;
        }
        $row = false;
        switch ($fetch_mode) {
            case \zwp\db::FETCH_NUM:
                $row = $values;
                break;
            case \zwp\db::FETCH_ASSOC:
                $row = array_combine($this->keys, $values);
                break;
            case \zwp\db::FETCH_BOTH:
                $row = array_combine($this->keys, $values);
                $row = array_merge($values, $row);
                break;
            default:
                throw new Zwp_Db_Exception("Fetch mode '$fetch_mode' is not support");
        }
        return $row;
    }

    protected function handleError() {
        if ($this->error_mode === Zwp_Db::ERRMODE_EXCEPTION) {
            throw new Zwp_Db_Exception($this->connection_resource->error, $this->connection_resource->errno);
        } elseif ($this->error_mode === Zwp_Db::ERRMODE_WARNING) {
            error_log($this->connection_resource->error);
        }
    }

    public function errorCode() {
        return substr($this->statement->sqlstate, 0, 5);
    }

    public function errorInfo() {
        return array(
            substr($this->statement->sqlstate, 0, 5),
            $this->statement->errno,
            $this->statement->error
        );
    }

    function __destruct() {
        if ($this->statement) {
            $this->statement->close();
        }
    }

}
