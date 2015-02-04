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
 * @date Jan 30, 2015
 * @version 1.0.0
 * @description
 */
class db {

    /**
     * 对 fetch 方法指定返回结果数组使用列字段名作 key，默认返回结果形式
     */
    const FETCH_ASSOC = 2;

    /**
     * 对 fetch 方法指定返回结果数组使用列字段位置作 key
     */
    const FETCH_NUM = 3;

    /**
     * 对 fetch 方法指定返回结果数组同时有列字段名和字段位置
     */
    const FETCH_BOTH = 4;

    /**
     * 在出错时不抛出异常，默认模式
     */
    const ERRMODE_SILENT = 0;

    /**
     * 在出错时引发 php E_WARNING 错误
     */
    const ERRMODE_WARNING = 1;

    /**
     * 在出错时抛出异常 Zwp_Db_Exception
     */
    const ERRMODE_EXCEPTION = 2;

    /**
     * 数据库连接配置信息。key 为连接配置名，value 为连接配置
     */
    static protected $connect_options;

    /**
     * 数据库写连接。key 为连接配置名，value 为连接对象
     */
    static protected $write_connections;

    /**
     * 数据库读连接。key 为连接配置名，value 为连接对象
     */
    static protected $read_connections;

    /**
     * 设置数据库连接配置
     *
     * 数据库连接配置指数据库连接类 \zwp\db\adapter\abstraction 子类构造函数的参数，通常包括
     * 以下设置：
     *  - host 数据库主机名
     *  - username 数据库用户名
     *  - password 数据库密码
     *  - dbname 数据库名
     *  - encoding 连接使用的字符集
     *
     * 在 $options 参数中可以设置多个连接配置，表示此连接设置了主从，需要作读写分离。
     *
     * @static
     * @param array $options 数据库连接配置
     * @param string $name 连接配置名。默认为 default
     * @param string $connClass 数据库连接类名，默认根据 mysql 扩展加载，
     *     按 pdo_mysql, mysqli, mysql 优先顺序设置相应的连接类
     * @param array $driver_options 连接类的特殊配置
     */
    public static function setOptions($options, $name = 'default', $connClass = null, $driver_options = null) {
        if (null == $connClass || 'mysql' == $connClass) {
            if (extension_loaded('pdo_mysql')) {
                $connClass = '\zwp\db\adapter\pdo\mysql';
            } elseif (extension_loaded('mysqli')) {
                $connClass = '\zwp\db\adapter\mysqli';
            } elseif (extension_loaded('mysql')) {
                $connClass = '\zwp\db\adapter\mysql';
            } else {
                throw new \Exception("不支持 mysql 连接");
            }
        } else if ('oracle' == $connClass) {
            if (extension_loaded('PDO_OCI')) {
                $connClass = '\zwp\db\adapter\pdo\oracle';
            } else {
                throw new \Exception("不支持 oracle 连接");
            }
        } else {
            throw new \Exception("未知数据库连接类型");
        }
        $conn_option = array();
        if (!isset($options[0])) {
            $options = $options;
        }
        $conn_option['dbconf'] = $options;
        $conn_option['class'] = $connClass;
        $conn_option['driver_options'] = $driver_options;
        $conn_option['name'] = $name;
        self::$connect_options[$name] = $conn_option;
    }

    /**
     * 获得数据库连接
     *
     * 同 getReadConnection，在无主从数据库配置情况下，使用这个方法获得的连接可同时
     * 进行读写操作。但是在设置主从数据库配置时，只能进行读操作。建议只使用
     * getReadConnection 和 getWriteConnection 获得数据库连接。
     *
     * @static
     * @param string $name 连接配置名，默认为 default
     * @return \zwp\db\adapter\abstraction
     */
    public static function getConnection($name = 'default') {

        return self::getReadConnection($name);
    }

    /**
     * 获得数据库读连接
     *
     * 在设置主从数据库配置时，随机从配置中选择一个配置进行连接
     *
     * @static
     * @param string $name 连接配置名，默认为 default
     * @return \zwp\db\adapter\abstraction
     */
    public static function getReadConnection($name = 'default') {
        if (isset(self::$read_connections[$name])) {
            return self::$read_connections[$name];
        } elseif (isset(self::$connect_options[$name])) {
            $options = self::$connect_options[$name];
            $index = 0;

            if (count($options['dbconf']) > 1) {
                $index = rand(1, count($options['dbconf']) - 1);
            }
            return self::$read_connections[$name] = self::connect($options['class'], $options['dbconf'][$index], $options['driver_options']);
        } else {
            throw new \zwp\db\exception("set connection options first");
        }
    }

    /**
     * 获得数据库写连接
     *
     * 在设置无论是否设置主从数据库配置，都只选择第一个连接配置进行数据库连接
     *
     * @static
     * @param string $name 连接配置名，默认为 default
     * @return \zwp\db\adapter\abstraction
     */
    public static function getWriteConnection($name = 'default') {
        if (isset(self::$write_connections[$name])) {
            return self::$write_connections[$name];
        } elseif (isset(self::$connect_options[$name])) {
            $options = self::$connect_options[$name];
            return self::$write_connections[$name] = self::connect($options['class'], $options['dbconf'][0], $options['driver_options']);
        } else {
            throw new \zwp\db\exception("set connection options first");
        }
    }

    protected static function connect($class, $dbconf, $driver_options) {
        $dbconf['driver_options'] = $driver_options;
        return new $class($dbconf);
    }

}
