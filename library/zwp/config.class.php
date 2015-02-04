<?php

/*
 * Copyright 2015 jiaojie <jiaojie1989@gmail.com>.
 * 
 * Check your synatx if it is right.
 * Check your variables if the names are just correct.
 * Finally, run it under your command line.
 * Enjoy it!
 */

namespace zwp;

/**
 *
 * @author jiaojie <jiaojie1989@gmail.com>
 * @date Jan 18, 2015
 * @version 1.0.0
 * @description
 */
class config {

    protected static $config = array();

    /**
     * 获得配置参数
     * 
     * @param string $name 配置参数名
     * @param mixed $default 默认的配置参数值
     * @return mixed 配置参数值。如果配置参数不存在，返回提供的默认配置参数，默认为 null
     */
    static public function get($name, $default = null) {
        return isset(self::$config[$name]) ? self::$config[$name] : $default;
    }

    /**
     * 设置配置参数的值
     * 
     * @param string $name 配置参数名
     * @param mixed $value 配置参数值
     */
    static public function set($name, $value) {
        self::$config[$name] = $value;
    }

    /**
     * 检查配置参数是否存在
     * 
     * @param string $name 配置参数名
     * @return bool 如果存在返回 true，否则返回 false
     */
    static public function has($name) {
        return array_key_exists($name, self::$config);
    }

    /**
     * 获得所有的配置参数值
     * 
     * @return array 配置参数值构成的关联数组
     */
    static public function getConfig() {
        return self::$config;
    }

    /**
     * 使用数组新增配置参数值。保留旧的配置参数
     * 
     * @param array $config 新增配置参数值
     */
    static public function addConfig($config) {
        //var_dump($config);
        self::$config = array_merge(self::$config, $config);
    }

    /**
     * 使用数组设置配置参数值。旧的配置参数将清空
     * 
     * @param array $config 配置参数值
     */
    static public function setConfig($config) {
        self::$config = $config;
    }

}
