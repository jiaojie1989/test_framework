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
class sqlbuilder {

    /**
     * 拼装 SQL 配置
     * 配置包括：
     *  - prefix_placeholder 表名前缀占位符，默认为 '#__'
     *  - prefix 表名前缀，默认为空
     *  - quote_char 对字段名或表名的引用字符。默认使用 mysql 的 '`' 字符
     *  - auto_quote 是否自动对字段名或表名加引用字符，默认不做自动加引用符
     * @var array
     * @access protected
     */
    protected $config = array(
        'prefix_placeholder' => '#__',
        'prefix' => '',
        'quote_char' => '`', //oracle得去掉
        'auto_quote' => false
    );

    /**
     * 构造函数
     * 
     * @param array $config 配置
     */
    public function __construct($config = null) {
        $this->setConfig($config);
    }

    /**
     * 设置配置选项
     * 
     * @param mixed $nameOrConfig 使用数组设置或字符串设置单个选项
     * @param mixed $value 选项值
     * @return Zwp_Db_SQLBuilder $this
     */
    public function setConfig($nameOrConfig, $value = null) {
        if (is_string($nameOrConfig)) {
            $config = array($nameOrConfig => $value);
        } else {
            $config = $nameOrConfig;
        }
        if (is_array($config)) {
            foreach ($config as $name => $value) {
                if (array_key_exists($name, $this->config)) {
                    $this->config[$name] = $value;
                } else {
                    throw new \zwp\db\exception("Unknown config '{$name}'");
                }
            }
        }
        return $this;
    }

    /**
     * 获得配置值
     * 
     * @param string $name 配置名
     * @return mixed 配置值
     */
    public function getConfig($name) {
        return isset($this->config[$name]) ? $this->config[$name] : null;
    }

    /**
     * 获得加上前缀的表名，如果设置 auto_quote 选项，会加上引用符
     * 
     * @param string $table
     * @return string
     */
    public function getTable($table) {
        $scheme = null;
        if (($pos = strpos($table, '.')) !== false) {
            $scheme = substr($table, 0, $pos);
            $table = substr($table, $pos + 1);
        }
        if ($this->config['prefix']) {
            $table = $this->config['prefix'] . $table;
        }
        if ($this->config['auto_quote']) {
            if ($scheme) {
                $scheme = $this->config['quote_char'] . $scheme . $this->config['quote_char'];
            }
            $table = $this->config['quote_char'] . $table . $this->config['quote_char'];
        }
        if ($scheme) {
            return $scheme . '.' . $table;
        }
        return $table;
    }

    /**
     * 替换 SQL 语句中的表名前缀占位符
     * 
     * @param string $sql
     * @return string
     */
    public function getSQL($sql) {
        return str_replace($this->config['prefix_placeholder'], $this->config['prefix'], $sql);
    }

    /**
     * 拼装单表的查询语句
     * 
     * @param string $table 表名
     * @param mixed $fields 选择字段。可有以下几种值
     *   - null，表示选择全部字段，转换在 '*'
     *   - 字符串，直接作为 select 语句的选择字段
     *   - 数组，每个值作为一个字段，如果 auto_quote 为 true 时，会自动加引用符
     * @param mixed $where 查询条件，参考 {@link where()}
     * @param mixed $order 排序字段。可有两种值：
     *   - 字符串，直接作为 ORDER BY 语句
     *   - 数组，使用逗号连接作为 ORDER BY 语句，如果 auto_quote 打开，将加引用符
     * @param mixed $limit LIMIT 语句。可有两种值：
     *   - 字符串或数字，直接拼做 LIMIT $limit
     *   - 数组，拼成 LIMIT $limit[0] OFFSET $limit[1]
     * @return array 返回 array($sql, $binds)
     */
    public function select($table, $fields = null, $where = null, $order = null, $limit = null) {
        if (empty($fields)) {
            $fields = '*';
        } elseif (is_array($fields)) {
            if ($this->config['auto_quote']) {
                $fields = array_map(array($this, 'quoteIdentifier'), $fields);
            }
            $fields = implode(',', $fields);
        }
        $whereClause = '';
        $limitClause = '';
        $orderClause = '';
        $binds = array();
        list($whereClause, $binds) = $this->where($where);
        if (!empty($whereClause))
            $whereClause = ' WHERE ' . $whereClause;
        if (!empty($order)) {
            if (is_array($order)) {
                if ($this->config['auto_quote']) {
                    $order = array_map(array($this, 'quoteIdentifier'), $order);
                }
                $orderClause = ' ORDER BY ' . implode(',', $order);
            } else {
                $orderClause = ' ORDER BY ' . $order;
            }
        }
        if (!empty($limit)) {
            if (is_array($limit)) {
                $limitClause = ' LIMIT ' . (int) $limit[0];
                if (isset($limit[1]))
                    $limitClause .= ' , ' . (int) $limit[1];
            }
            else {
                $limitClause = ' LIMIT ' . (int) $limit;
            }
        }
        $sql = 'SELECT ' . $fields . ' FROM ' . $this->getTable($table) . $whereClause . $orderClause . $limitClause;

        return array($sql, $binds);
    }

    /**
     * 拼装插入语句
     * 
     * @param string $table 表名
     * @param array $value 一行记录，key 为列字段名
     * @return array 返回 array($sql, $binds)
     *   如果 $value 为空，返回 array(null, null)
     */
    public function insert($table, $value) {
        if (empty($value))
            return array(null, null);
        foreach ($value as $k => $v) { /* convert array() => '' */
            if (is_array($v) && empty($v)) {
                $value[$k] = '';
            }
        }
        $fields = array_keys($value);
        if ($this->config['auto_quote']) {
            $fields = array_map(array($this, 'quoteIdentifier'), $fields);
        }
        $sql = 'INSERT INTO ' . $this->getTable($table) . ' (' . implode(',', $fields)
                . ') VALUES (' . substr(str_repeat('?,', count($value)), 0, -1) . ')';
        return array($sql, array_values($value));
    }

    /**
     * 拼装 update 语句
     * 
     * @param string $table 表名
     * @param mixed $set 更新字段语句。可有三种值
     *  - 字段串，直接作 SET 语句
     *  - 列字段名为 key 的数组，拼写成 field1=?,field2=? 形式
     *  - array($set, $binds) 形式的数组，第一个值为字符串，直接作 SET 语句，第二个值为参数
     * @param mixed $where 查询条件，参考 {@link where()}
     * @return array 返回 array($sql, $binds)
     */
    public function update($table, $set, $where) {
        if (empty($set)) {
            return array(null, null);
        }
        $setClause = '';
        $binds = array();
        if (is_array($set)) {
            if (isset($set[0])) {
                $setClause = $set[0];
                if (isset($set[1])) {
                    if (is_array($set[1])) {
                        $binds = $set[1];
                    } else {
                        $binds = array($set[1]);
                    }
                }
            } else {
                $sep = '';
                foreach ($set as $k => $v) {
                    if ($this->config['auto_quote']) {
                        $k = $this->quoteIdentifier($k);
                    }
                    $setClause .= $sep . $k . '=?';
                    $sep = ', ';
                    $binds[] = $v;
                }
            }
        } else {
            $setClause = $set;
        }
        list($whereClause, $whereBinds) = $this->where($where);
        if (!empty($whereClause)) {
            $whereClause = ' WHERE ' . $whereClause;
        }
        $binds = array_merge($binds, $whereBinds);
        $sql = 'UPDATE ' . $this->getTable($table) . ' SET ' . $setClause . $whereClause;
        return array($sql, $binds);
    }

    /**
     * 拼装删除语句
     * 
     * @param string $table 表名
     * @param mixed $where 查询条件，参考 {@link where()}
     * @return array 返回 array($sql, $binds)
     */
    public function delete($table, $where) {
        list($whereClause, $binds) = $this->where($where);
        if (!empty($whereClause)) {
            $whereClause = ' WHERE ' . $whereClause;
        }
        $sql = 'DELETE FROM ' . $this->getTable($table) . $whereClause;
        return array($sql, $binds);
    }

    /**
     * 获得查询条件
     *
     * @param mixed $where 查询条件。可有三种类型的值：
     *  - 字符串 直接作 WHERE 语句
     *  - 列字段名为 key 的数组，拼写成 field1=? AND field2=? 形式
     *  - array($whereClause, $binds) 形式的数组，第一个值为字符串，直接作 WHERE 语句，第二个值为参数
     * @return array 返回 array( $clause, $binds) 形式的数组, $clause 不包括 WHERE 关键字
     */
    public function where($where) {
        $query = '';
        $binds = array();
        if (!empty($where)) {
            if (is_array($where)) {
                if (isset($where[0])) {
                    $query = $where[0];
                    if (isset($where[1])) {
                        if (is_array($where[1])) {
                            $binds = $where[1];
                        } else {
                            $binds = array($where[1]);
                        }
                    }
                } else {
                    $op = '';
                    foreach ($where as $k => $v) {
                        if ($this->config['auto_quote']) {
                            $k = $this->quoteIdentifier($k);
                        }
                        $query .= $op . $k . '=?';
                        $op = ' AND ';
                        $binds[] = $v;
                    }
                }
            } else {
                $query = $where;
            }
        }
        return array($query, $binds);
    }

    /**
     * 对字段名加引用符
     * 
     * @param string $n 字段名
     * @return string
     */
    public function quoteIdentifier($n) {
        if (($pos = strpos($n, '.')) !== false) {
            return $this->config['quote_char'] . substr($n, 0, $pos) . $this->config['quote_char']
                    . '.' . $this->config['quote_char'] . substr($n, $pos + 1) . $this->config['quote_char'];
        } else {
            return $this->config['quote_char'] . $n . $this->config['quote_char'];
        }
    }

}
