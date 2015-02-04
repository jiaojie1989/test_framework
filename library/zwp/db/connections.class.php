<?php

/*
 * Copyright 2015 jiaojie <jiaojie1989@gmail.com>.
 * 
 * Check your synatx if it is right.
 * Check your variables if the names are just correct.
 * Finally, run it under your command line.
 * Enjoy it!
 */

namespace zwp\db;

/**
 *
 * @author jiaojie <jiaojie1989@gmail.com>
 * @date Feb 4, 2015
 * @version 1.0.0
 * @description
 */
class connections {

    /**
     * 数据库连接配置名
     * @var string
     * @access protected
     */
    protected $name;

    /**
     * 构造函数
     * 
     * @param string $name 数据库连接配置名，默认为 default
     */
    public function __construct($name = 'default') {
        $this->name = $name;
    }

    /**
     * 设置数据库连接配置名
     * 
     * @param string $name 数据库连接配置名
     * @return \zwp\db\connections $this
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * 获得数据库连接配置名
     * 
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * 获得数据库读连接
     * 
     * @return \zwp\db\adapter\abstraction
     */
    public function getConnection() {
        return \zwp\db::getConnection($this->name);
    }

    /**
     * 获得数据库读连接
     * 
     * @return \zwp\db\adapter\abstraction
     */
    public function getReadConnection() {
        return \zwp\db::getReadConnection($this->name);
    }

    /**
     * 获得数据库写连接
     * 
     * @return \zwp\db\adapter\abstraction
     */
    public function getWriteConnection() {
        return \zwp\db::getWriteConnection($this->name);
    }

}
