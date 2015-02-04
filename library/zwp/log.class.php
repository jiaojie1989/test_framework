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
class log {

    protected static $DEFAULT = array(
        'appender' => 'console',
        'appender_options' => null,
        'level' => 'NONE',
        'filter' => null,
        'format' => null,
    );
    public static $LOGGER_CLASS = 'Zwp_Log_Logger';
    static protected $config = array();
    static protected $loggers = array();
    static protected $timer;
    static protected $previous_timer;

    const CALLER = 1;

    /**
     * 默认配置选项
     * 
     * @param array $conf
     */
    public static function setDefault($conf) {
        self::$DEFAULT = array_merge(self::$DEFAULT, $conf);
    }

    /**
     * 初始化特定 Logger 的配置
     * 
     * @param string $name Logger 名字
     * @param array $conf 配置
     */
    public static function setConfig($nameOrConfig, $conf = null) {
        if (is_string($nameOrConfig)) {
            self::$config[$nameOrConfig] = $conf;
        } else {
            foreach ($nameOrConfig as $name => $conf) {
                self::$config[$name] = $conf;
            }
        }
    }

    /**
     * 获得特定 Logger 的配置
     * 
     * @param string $name Logger 名字
     * @param array $conf 配置
     */
    public static function getConfig($name) {
        return isset(self::$config[$name]) ? array_merge(self::$DEFAULT, self::$config[$name]) : self::$DEFAULT;
    }

    /**
     * 获得 Logger
     *
     * 如果调用此函数的位置位于某个类中，$name 默认值是类的名字。如果不在类中，使用 'main' 作名字
     * 
     * @param String $name Logger 名
     * @return Zwp_Log 
     */
    public static function getLogger($name = null) {
        if (null === $name) {
            $frames = debug_backtrace();
            $name = isset($frames[self::CALLER]['class']) ? $frames[self::CALLER]['class'] : 'main';
        }
        if (!isset(self::$loggers[$name])) {
            $conf = self::getConfig($name);
            self::$loggers[$name] = new self::$LOGGER_CLASS($name, $conf);
        }
        return self::$loggers[$name];
    }

    public static function getLoggers() {
        return self::$loggers;
    }

    public static function debug($msg) {
        Zwp_Log::getLogger('main')->log($msg, 'DEBUG');
    }

    public static function info($msg) {
        Zwp_Log::getLogger('main')->log($msg, 'INFO');
    }

    public static function error($msg) {
        Zwp_Log::getLogger('main')->log($msg, 'ERROR');
    }

    public static function fatal($msg) {
        Zwp_Log::getLogger('main')->log($msg, 'FATAL');
    }

    public static function warn($msg) {
        Zwp_Log::getLogger('main')->log($msg, 'WARN');
    }

    public static function startTimer() {
        self::$previous_timer = self::$timer = microtime(1);
    }

    public static function getTimer() {
        if (!self::$timer) {
            self::startTimer();
        }
        $time = microtime(1);
        $elapsed = $time - self::$previous_timer;
        self::$previous_timer = $time;
        return array(($time - self::$timer), $elapsed);
    }

}
