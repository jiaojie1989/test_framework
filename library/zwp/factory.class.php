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
class factory {
    
    static private $tables = array();

    public static function getTable($table_name, $connection_name = null) {
        
        if (!isset(self::$tables[$table_name])) {
            $sqlbuilder = new \zwp\db\sqlbuilder(
                    array(
                'prefix' => \zwp\config::get('tom_table_prefix'),
                'auto_quote' => false
                    )
            );
            $connection = null;
            if (!empty($connection_name)) {
                //$connection = Zwp_Db::getConnection($connection_name);
                $connection = new \zwp\db\connections($connection_name); //oracle连接继承Zwp_Db_Connections类
            }
            self::$tables[$table_name] = new \zwp\db\table($table_name, $sqlbuilder, $connection);
        }
        return self::$tables[$table_name];
    }

}
