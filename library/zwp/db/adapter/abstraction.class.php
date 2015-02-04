<?php

/*
 * Copyright 2015 jiaojie <jiaojie1989@gmail.com>.
 * 
 * Check your synatx if it is right.
 * Check your variables if the names are just correct.
 * Finally, run it under your command line.
 * Enjoy it!
 */

namespace zwp\db\adapter;

/**
 *
 * @author jiaojie <jiaojie1989@gmail.com>
 * @date Feb 4, 2015
 * @version 1.0.0
 * @description
 */
abstract class abstraction {

    /**
     * 内部连接资源。如底层用pdo，则为PDO对象; 如底层使用mysql，则为mysql连接
     */
    protected $resource;

    /**
     * 数据库连接配置
     * @var array
     */
    protected $dbconf;

    /**
     * 出错处理模式。可为下列值：
     *  - Zwp_Db::ERRMODE_SILENT (默认)
     *  - Zwp_Db::ERRMODE_WARNING
     *  - Zwp_Db::ERRMODE_EXCEPTION
     * @var int
     */
    protected $error_mode;

    /**
     * 记录返回类型，可为下列值：
     *  - Zwp_Db::FETCH_ASSOC (默认)
     *  - Zwp_Db::FETCH_NUM
     *  - Zwp_Db::FETCH_BOTH
     * @var int
     */
    protected $fetch_mode;

    /**
     * 日志对象
     * @var Zwp_Log_Logger
     */
    //protected $logger;

    /**
     * 出错信息数组，有三个元素，分别为：
     *  - SQLSTATE 错误代码(ANSI SQL 标准定义的 5 个长度的标志符)
     *  - driver 给出的出错代码
     *  - driver 给出的出错信息
     * @var array
     */
    protected $error_info = array();

    /*     * #@- */

    /**
     * 构造函数
     *
     * 在构造连接类时已经进行连接。连接参数如下：
     *  - host 数据库主机名
     *  - username 数据库用户
     *  - password 数据库密码
     *  - dbname  数据库名
     *  - encoding 连接使用的字符集
     *  - driver_options 连接类特异参数
     * @param array $dbconf 
     */
    function __construct($dbconf) {
//        $this->logger = Zwp_Log::getLogger('Zwp_Db');
        $this->dbconf = $dbconf;
//        if ($this->logger->canDebug()) {
//            $dsn = implode(' ', array_map(create_function('$k,$v', 'return $k."=".$v;'), array_keys($dbconf), array_values($dbconf)));
//            $this->logger->debug("connect to " . $dsn);
//        }
        $this->connect();
//        $this->logger->debug('end connect');
        $this->setErrorMode(\zwp\db::ERRMODE_SILENT);
        $this->setFetchMode(\zwp\db::FETCH_ASSOC);
    }

    /**
     * 获得底层连接资源
     * 
     * @return mixed
     */
    public function getResource() {
        return $this->resource;
    }

    /**
     * 执行 SQL 查询
     * 
     * @param string $sql
     * @return Zwp_Db_Statement_Abstract
     */
    public function query($sql) {
        $stmt = $this->prepare($sql);
        if (false !== $stmt) {
            $stmt->execute();
        }
        return $stmt;
    }

    /**
     * 执行 SQL
     * 
     * @param string $sql
     * @return boolean 
     */
    public function exec($sql) {
//        $this->logger->debug($sql);
        $retval = $this->_exec($sql);
//        $this->logger->debug('end query');
        return $retval;
    }

    /**
     * 返回错误代码
     * 
     * @return int
     */
    public function errorCode() {
        return isset($this->error_info[0]) ? $this->error_info[0] : null;
    }

    /**
     * 返回出错信息
     * 
     * @return array
     */
    public function errorInfo() {
        return $this->error_info;
    }

    /**
     * 对 SQL 中的字符串作转义
     * 
     * @param mixed $value
     * @return string
     */
    public function quote($value) {
        if (is_int($value)) {
            return $value;
        } elseif (is_float($value)) {
            return sprintf('%F', $value);
        }
        return "'" . addcslashes($value, "\000\n\r\\'\"\032") . "'";
    }

    /**
     * 获得当前记录返回类型
     * 
     * @return int
     */
    public function getFetchMode() {
        return $this->fetch_mode;
    }

    /**
     * 设置当前记录返回类型
     *
     * @param int $mode
     * @return Zwp_Db_Adapter_Abstract $this
     * @throws Zwp_Db_Exception 如果返回类型未知
     */
    public function setFetchMode($mode) {
        if (in_array($mode, array(\zwp\db::FETCH_ASSOC, \zwp\db::FETCH_NUM, \zwp\db::FETCH_BOTH))) {
            $this->fetch_mode = $mode;
            return $this;
        } else {
            throw new Zwp_Db_Exception("Invalid fetch mode '$mode' specified");
        }
    }

    /**
     * 获得出错处理模式
     * 
     * @return int
     */
    public function getErrorMode() {
        return $this->error_mode;
    }

    /**
     * 设置当前处理模式
     * 
     * @param int $mode
     * @return Zwp_Db_Adapter_Abstract $this
     * @throws Zwp_Db_Exception 如果出错模式未知
     */
    public function setErrorMode($mode) {
        if (in_array($mode, array(\zwp\db::ERRMODE_SILENT, \zwp\db::ERRMODE_WARNING, \zwp\db::ERRMODE_EXCEPTION))) {
            $this->error_mode = $mode;
        } else {
            throw new Zwp_Db_Exception("Invalid error mode '$mode' specified");
        }
    }

    /*     * #@+
     * @abstract
     */

    /**
     * 使用连接资源执行 SQL
     * 
     * @access protected
     * @param string $sql
     * @return boolean
     */
    abstract protected function _exec($sql);

    /**
     * 连接数据库，设置连接资源
     * @throws Zwp_Db_Exception 如果连接出错
     */
    abstract public function connect();

    /**
     * 准备 Statement 对象用于后续执行
     * @return Zwp_Db_Statement_Abstract
     */
    abstract public function prepare($sql);

    /**
     * 事务开始
     *
     * 该函数依赖于底层支持，mysql扩展不支持事务
     */
    abstract public function beginTransaction();

    /**
     * 提交事务
     */
    abstract public function commit();

    /**
     * 事务回滚
     */
    abstract public function rollBack();

    /**
     * 获得最近一次插入的自增ID
     * @return int
     */
    abstract public function lastInsertId();

    /**
     * 断开连接
     */
    abstract public function disconnect();
}
