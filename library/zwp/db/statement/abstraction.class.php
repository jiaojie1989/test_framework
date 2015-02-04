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
abstract class abstraction {

    /**
     * 连接类
     * @var Zwp_Db_Adapter_Abstract
     */
    protected $connection;

    /**
     * 连接类中的连接资源
     */
    protected $connection_resource;

    /**
     * 当前执行的 SQL
     * @var string
     */
    protected $sql;

    /**
     * 内部使用的 $statement，如 pdo 中即为 PDOStatement 对象
     */
    protected $statement;

    /**
     * 日志记录类
     * @var Zwp_Log_Logger
     */
//    protected $logger;

    /**
     * 记录返回类型，可为下列值：
     *  - Zwp_Db::FETCH_ASSOC (默认)
     *  - Zwp_Db::FETCH_NUM
     *  - Zwp_Db::FETCH_BOTH
     * @var int
     */
    protected $fetch_mode;

    /**
     * 出错处理模式。可为下列值：
     *  - Zwp_Db::ERRMODE_SILENT (默认)
     *  - Zwp_Db::ERRMODE_WARNING
     *  - Zwp_Db::ERRMODE_EXCEPTION
     * @var int
     */
    protected $error_mode;

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
     * @param Zwp_Db_Adapter_Abstract $connection
     * @param string $sql
     */
    function __construct($connection, $sql) {
//        $this->logger = Zwp_Log::getLogger('Zwp_Db');
        $this->connection = $connection;
        $this->connection_resource = $connection->getResource();
        $this->sql = $sql;
        $this->statement = $this->prepare($sql);
        $this->setFetchMode($connection->getFetchMode());
        $this->setErrorMode($connection->getErrorMode());
    }

    /**
     * 获得内部statement
     * 
     * @return mixed
     */
    public function getStatement() {
        return $this->statement;
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
        if (in_array($mode, array(\zwp\db::FETCH_ASSOC,\zwp\db::FETCH_NUM,\zwp\db::FETCH_BOTH))) {
            $this->fetch_mode = $mode;
        } else {
            throw new \zwp\db\exception("Invalid fetch mode '$mode' specified");
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
            throw new \zwp\db\exception("Invalid error mode '$mode' specified");
        }
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
     * 执行 prepare 的 statement
     * 
     * @param array $params prepare SQL 中的参数
     * @return boolean
     */
    public function execute(array $params = null) {
//        if ($this->logger->canDebug()) {
//            if (null === $params) {
//                $sql = $this->sql;
//            } else {
//                $statement = explode('?', $this->sql);
//                if (count($params) != count($statement) - 1) {
//                    $sql = $this->sql . ' with bind parameters: [' . implode(', ', $params) . ']';
//                } else {
//                    $sql = '';
//                    foreach ($params as $i => $bind) {
//                        $sql .= $statement[$i]
//                                . (is_string($bind) ? $this->connection_resource->quote($bind) : $bind);
//                    }
//                    $sql .= $statement[count($params)];
//                }
//            }
//            $this->logger->debug($sql);
//        }
        $retval = $this->_execute($params);
//        $this->logger->debug(__CLASS__ . ' end query');
        return $retval;
    }

    /**
     * 获得所有记录
     * 
     * @param $fetch_mode=null
     * @return void
     */
    public function fetchAll($fetch_mode = null) {
        $data = array();
        while ($row = $this->fetch($fetch_mode)) {
            $data[] = $row;
        }
        return $data;
    }

    public function getRowCount() {
        return $this->statement->rowCount();
    }

    abstract protected function prepare($sql);

    abstract protected function _execute(array $params = null);

    abstract public function fetch($fetch_style = null, $orientation = null, $offset = null);
}
