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
class pdo extends \zwp\db\statement\abstraction {

    static protected $FETCH_MODE_MAP = array(\zwp\db::FETCH_ASSOC => \PDO::FETCH_ASSOC, \zwp\db::FETCH_NUM => \PDO::FETCH_NUM, \zwp\db::FETCH_BOTH => \PDO::FETCH_BOTH,
    );

    public function setFetchMode($mode) {
        parent::setFetchMode($mode);
        $this->statement->setFetchMode(self::$FETCH_MODE_MAP[$mode]);
    }

    protected function prepare($sql) {
        return $this->connection_resource->prepare($sql);
    }

    protected function handleException($e) {
        if ($this->error_mode === \zwp\db::ERRMODE_EXCEPTION) {
            throw new \zwp\db\exception($e->getMessage(), $e->getCode());
        } else {
            $this->error_info = $e->errorInfo;
        }
    }

    protected function _execute(array $params = null) {
        try {
            if (null === $params) {
                return $this->statement->execute();
            } else {
                return $this->statement->execute($params);
            }
        } catch (\PDOException $e) {
            $this->handleException($e);
            return false;
        }
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

    public function fetch($fetch_mode = null, $orientation = null, $offset = null) {
        return $this->statement->fetch($this->convertFetchMode($fetch_mode), $orientation, $offset);
    }

    public function fetchAll($fetch_mode = null) {
        return $this->statement->fetchAll($this->convertFetchMode($fetch_mode));
    }

}
