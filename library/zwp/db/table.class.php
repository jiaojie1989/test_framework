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
class table {

    /**
     * 数据库表名
     * @var string
     */
    protected $table;

    /**
     * 数据库连接类
     * @var \zwp\db\connections
     */
    protected $connections;

    /**
     * SQL 拼装类
     * @var \zwp\db\sqlbuilder
     */
    protected $sqlbuilder;

    /**
     * 数据库连接缓存, 读连接和写连接
     * @var array
     */
    static protected $dbhs;

    /**
     * 构造函数
     *
     * @param string $table
     * @param \zwp\db\sqlbuilder $sqlbuilder
     * @param \zwp\db\connections $connections
     */
    public function __construct($table = '', $sqlbuilder = null, $connections = null) {
        $this->table = $table;
        $this->setConnections($connections);
        $this->setSQLBuilder($sqlbuilder);
    }

    /**
     * 设置数据库表名
     *
     * @param $table
     * @return \zwp\db\table $this
     */
    public function setTable($table) {
        $this->table = $table;
        return $this;
    }

    /**
     * 获得表名
     *
     * @return string
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * 设置数据库连接
     *
     * @param \zwp\db\connections $connections
     * @return \zwp\db\table $this
     */
    public function setConnections(\zwp\db\connections $connections = null) {
        if (null === $connections) {
            $connections = new \zwp\db\connections();
        }
        $this->connections = $connections;
        self::$dbhs = array();
        return $this;
    }

    /**
     * 获得数据库读连接
     *
     * @return \zwp\db\adapter\abstraction
     */
    public function getConnection() {
        return $this->getReadConnection();
    }

    /**
     * 获得数据库读连接
     *
     * @return \zwp\db\adapter\abstraction
     */
    public function getReadConnection() {
        if (!isset(self::$dbhs[$this->connections->getName()][0])) {
            self::$dbhs[$this->connections->getName()][0] = $this->connections->getReadConnection();
            self::$dbhs[$this->connections->getName()][0]->setErrorMode(\zwp\db::ERRMODE_EXCEPTION);
        }
        return self::$dbhs[$this->connections->getName()][0];
    }

    /**
     * 获得数据库写连接
     *
     * @return \zwp\db\adapter\abstraction
     */
    public function getWriteConnection() {
        if (!isset(self::$dbhs[$this->connections->getName()][1])) {
            self::$dbhs[$this->connections->getName()][1] = $this->connections->getWriteConnection();
            self::$dbhs[$this->connections->getName()][1]->setErrorMode(\zwp\db::ERRMODE_EXCEPTION);
        }
        return self::$dbhs[$this->connections->getName()][1];
    }

    /**
     * 设置 SQL 拼装类
     *
     * @param \zwp\db\sqlbuilder $sqlbuilder
     * @return \zwp\db\table $this
     */
    public function setSQLBuilder($sqlbuilder = null) {
        if (null === $sqlbuilder) {
            $sqlbuilder = new \zwp\db\sqlbuilder();
        }
        $this->sqlbuilder = $sqlbuilder;
        return $this;
    }

    /**
     * 获得 SQL 拼装类
     *
     * @return \zwp\db\sqlbuilder
     */
    public function getSQLBuilder() {
        return $this->sqlbuilder;
    }

    /**
     * 执行查询操作
     *
     * @see \zwp\db\sqlbuilder::select()
     * @param mixed $fields 查询字段
     * @param mixed $where 查询条件
     * @param mixed $order 排序字段
     * @param mixed $limit limit
     * @return \zwp\db\statement\abstraction 执行后的查询句柄
     * @throws \zwp\db\exception
     */
    public function select($fields = null, $where = null, $order = null, $limit = null) {
        list($sql, $binds) = $this->sqlbuilder->select($this->table, $fields, $where, $order, $limit);
        $sth = $this->getReadConnection()->prepare($sql);
        $sth->execute($binds);
        return $sth;
    }

    /**
     * 返回查询的一条记录
     *
     * @param mixed $fields 查询字段
     * @param mixed $where 查询条件
     * @param mixed $order 排序字段
     * @return array 如果查询成功，返回一条记录; 否则返回 false
     * @throws \zwp\db\exception
     */
    public function selectOne($fields = null, $where = null, $order = null) {
        list($sql, $binds) = $this->sqlbuilder->select($this->table, $fields, $where, $order, 1);
        $sth = $this->getReadConnection()->prepare($sql);
        $sth->execute($binds);
        return $sth->fetch();
    }

    /**
     * 返回查询的一条记录
     *
     * @param mixed $fields 查询字段
     * @param mixed $where 查询条件
     * @param mixed $order 排序字段
     * @return array 如果查询成功，返回一条记录; 否则返回 false
     * @throws \zwp\db\exception
     */
    public function selectOneFromMaster($fields = null, $where = null, $order = null) {
        list($sql, $binds) = $this->sqlbuilder->select($this->table, $fields, $where, $order, 1);
        $sth = $this->getWriteConnection()->prepare($sql);
        $sth->execute($binds);
        return $sth->fetch();
    }

    /**
     * 返回所有查询记录
     *
     * @param mixed $fields 查询字段
     * @param mixed $where 查询条件
     * @param mixed $order 排序字段
     * @param mixed $limit limit
     * @return array 如果查询成功，返回所有记录; 否则返回 false
     * @throws \zwp\db\exception
     */
    public function selectAll($fields = null, $where = null, $order = null, $limit = null) {
        list($sql, $binds) = $this->sqlbuilder->select($this->table, $fields, $where, $order, $limit);
        //   echo $sql;
        $sth = $this->getReadConnection()->prepare($sql);
        $sth->execute($binds);
        return $sth->fetchAll();
    }

    /**
     * 返回所有查询记录
     *
     * @param mixed $fields 查询字段
     * @param mixed $where 查询条件
     * @param mixed $order 排序字段
     * @param mixed $limit limit
     * @return array 如果查询成功，返回所有记录; 否则返回 false
     * @throws \zwp\db\exception
     */
    public function selectAllFromMaster($fields = null, $where = null, $order = null, $limit = null) {
        //print_r($this->getReadConnection());
        list($sql, $binds) = $this->sqlbuilder->select($this->table, $fields, $where, $order, $limit);
        $sth = $this->getWriteConnection()->prepare($sql);

        $sth->execute($binds);
        return $sth->fetchAll();
    }

    /**
     * 插入1行或多行记录
     *
     * @param array $rows 可以是1行记录，也可以是多行记录
     * @throws \zwp\db\exception
     * @return boolean 如果成功插入所有记录，返回 true; 如果记录格式不正确，返回 false
     */
    public function insert($rows) {
        if (empty($rows) || !is_array($rows)) {
            return false;
        }
        // 输入是一行记录，转换成数组的数组
        if (!(isset($rows[0]) && is_array($rows[0]))) {
            $rows = array($rows);
        }
        $row = $rows[0];
        list($sql, $binds) = $this->sqlbuilder->insert($this->table, $row);
        if (empty($sql)) {
            return false;
        }
        $fields = array_keys($row);
        $sth = $this->getWriteConnection()->prepare($sql);
        foreach ($rows as $row) {
            $value = array();
            foreach ($fields as $key) {
                $value[] = isset($row[$key]) ? $row[$key] : null;
            }
            $sth->execute($value);
        }
        return $this->getWriteConnection()->lastInsertId();
    }

    /**
     * 更新记录
     *
     * @see \zwp\db\sqlbuilder::update()
     * @param mixed $set 更新字段
     * @param mixed $where 更新条件
     * @throws \zwp\db\exception
     * @return boolean
     */
    public function update($set, $where) {
        list($sql, $binds) = $this->sqlbuilder->update($this->table, $set, $where);
        $sth = $this->getWriteConnection()->prepare($sql);
        $ret = $sth->execute($binds);
        if ($ret) {
            $ret = $sth->getRowCount();
        }
        return $ret;
    }

    /**
     * 删除记录
     *
     * @see \zwp\db\sqlbuilder::delete()
     * @param mixed $where 删除条件
     * @throws \zwp\db\exception
     * @return boolean
     */
    public function delete($where) {
        list($sql, $binds) = $this->sqlbuilder->delete($this->table, $where);
        $sth = $this->getWriteConnection()->prepare($sql);
        $ret = $sth->execute($binds);
        if ($ret) {
            $ret = $sth->getRowCount();
        }
        return $ret;
    }

    /**
     * 是否只读 SQL 语句（查询语句）
     *
     * @access protected
     * @static
     * @param string $sql
     * @return boolean 如果是只读SQL，返回 true
     */
    protected static function isReadonlyStatement($sql) {
        $part = explode(' ', trim($sql), 2);
        return 'SELECT' == strtoupper($part[0]);
    }

    /**
     * 执行 SQL
     *
     * sql 中可含有表名前缀占位符，将由 sqlbuilder 替换成真正的表名前缀
     * @see \zwp\db\sqlbuilder::getSQL()
     * @param string $sql
     * @return boolean
     * @throws \zwp\db\exception
     */
    public function exec($sql) {
        $dbh = self::isReadonlyStatement($sql) ? $this->getReadConnection() : $this->getWriteConnection();
        $sql = $this->sqlbuilder->getSQL($sql);
        return $dbh->exec($sql);
    }

    /**
     * 执行查询 SQL
     *
     * sql 中可含有表名前缀占位符，将由 sqlbuilder 替换成真正的表名前缀
     * @see \zwp\db\sqlbuilder::getSQL()
     * @param string $sql
     * @param array $binds 绑定参数
     * @return \zwp\db\statement\abstraction 执行后的查询句柄
     * @throws Z\zwp\db\exception
     */
    public function query($sql, $binds = null) {
        $dbh = self::isReadonlyStatement($sql) ? $this->getReadConnection() : $this->getWriteConnection();
        $sql = $this->sqlbuilder->getSQL($sql);
        $sth = $dbh->prepare($sql);
        $sth->execute($binds);
        return $sth;
    }

}
