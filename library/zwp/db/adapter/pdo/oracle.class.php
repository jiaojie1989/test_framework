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
class oracle extends \zwp\db\adapter\pdo\abstraction {

    protected $type = 'oci';

    public function connect() {
        if (!empty($this->dbconf['encoding'])) {
            $this->dbconf['driver_options'][\PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES \'' . $this->dbconf['encoding'] . '\'';
        }
        parent::connect();
    }

}
