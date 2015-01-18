<?php

/*
 * Copyright 2015 jiaojie <jiaojie1989@gmail.com>.
 * 
 * Check your synatx if it is right.
 * Check your variables if the names are just correct.
 * Finally, run it under your command line.
 * Enjoy it!
 */

namespace common;

/**
 *
 * @author jiaojie <jiaojie1989@gmail.com>
 * @date Jan 18, 2015
 * @version 1.0.0
 * @description
 */

/**
 * Zwp_Config 保存所有全局的配置信息
 *
 * 全局配置:
 *  - tom_db 数据库连接配置，参考 {@link Zwp_Db::setOptions()}
 *  - tom_table_prefix 数据库表名前缀
 *  - tom_seller_nick 卖家 nick
 *  - tom_seller_userid 卖家 userid
 *  - tom_seller_type 卖家用户类型
 *  - tom_top_service_url TOP 回调地址
 *  - tom_seller_app_key 自用型 app key
 *  - tom_seller_secret_key 自用型 app 密钥
 *  - tom_buyer_app_key 他用型 app key
 *  - tom_buyer_secret_key 他用型 app 密钥
 *  - tom_template 使用中的模板名
 *  - tom_frontend_uri_index 前台首页 uri
 *  - tom_frontend_uri_404 前台 404 页面的 uri
 *  - tom_admin_uri_index 后台首页 uri
 *  - tom_admin_uri_404 后台 404 页面的 uri
 *  - tom_url_rewrite 是否使用服务器的 rewrite 功能
 *  - tom_clean_url 是否使用 seo 优化后的 url
 *  - tom_route_rules url seo 优化规则
 *
 * 后台使用的配置:
 *  - admin_acl_rules 后台插件 acl 规则
 *
 * 运行时重要配置:
 *  - core_bootstrap_class Context 初始化对象使用的类名
 * 
 * @package tom
 * @subpackage common
 * @author Ye Wenbin <buzhi@taobao.com>
 * @version SVN: $Id$
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
