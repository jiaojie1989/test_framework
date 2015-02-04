<?php

/*
 * Copyright 2015 jiaojie <jiaojie1989@gmail.com>.
 * 
 * Check your synatx if it is right.
 * Check your variables if the names are just correct.
 * Finally, run it under your command line.
 * Enjoy it!
 */

/**
 *
 * @author jiaojie <jiaojie1989@gmail.com>
 * @date Jan 21, 2015
 * @version 1.0.0
 * @description
 */
\zwp\config::addConfig(
        array(
            'zwp_db_mysql_249' => array(
                array(
                    'host' => '172.16.15.249',
                    'username' => 'homelink',
                    'password' => 'homelink',
                    'dbname' => 'newziroom',
                    'encoding' => 'utf8'
                )
            ),
            'zwp_db_mysql_207' => array(
                array(
                    'host' => '172.16.4.207',
                    'username' => 'ziroom',
                    'password' => 'fljt2lFVagi2jln',
                    'dbname' => 'newziroom',
                    'encoding' => 'utf8'
                )
            ),
            'zwp_db_mysql_37' => array(//数组下标0为db写,大于1的随即取一个db读
                array(
                    'host' => '172.16.6.37',
                    'port' => '3306',
                    'username' => 'ziroom',
                    'password' => 'fljt2lFVagi2jln',
                    'dbname' => 'newziroom',
                    'encoding' => 'utf8'
                ),
            ),
            'zwp_db_mysql_20' => array(
                array(
                    'host' => '172.16.6.20',
                    'username' => 'ziroom_customer',
                    'password' => '1c051cd53095c866f10d3222d2cf964d',
                    'dbname' => 'ziroom_customer',
                    'encoding' => 'utf8'
                )
            ),
//        'zwp_db_mysql_20_mode_price' => array(
//            array(
//                'host' => '172.16.6.20',
//                'username' => 'mode_price',
//                'password' => 'tp3cSkvzr7nziGy',
//                'dbname' => 'mode_price',
//                'encoding' => 'utf8'
//            )
//        ),
            'zwp_db_mysql_20_mode_price' => array(
                array(
                    'host' => '172.16.6.20',
                    'username' => 'mode_price',
                    'password' => 'tp3cSkvzr7nziGy',
                    'dbname' => 'mode_price',
                    'encoding' => 'utf8'
                )
            ),
            'zwp_db_mysql_242_mode_price' => array(
                array(
                    'host' => '172.16.15.242',
                    'username' => 'homelink',
                    'password' => 'homelink',
                    'dbname' => 'mode_price',
                    'encoding' => 'utf8'
                )
            ),
            'zwp_db_oracle_21' => array(
                array(
                    'tns' => "oci:dbname=(DESCRIPTION =(ADDRESS_LIST =(ADDRESS = (PROTOCOL = TCP)(HOST = 172.16.6.21)(PORT = 1521)))(CONNECT_DATA =(SERVICE_NAME = svdp)));charset=UTF8", //测试服务器不支持utf8,charset=UTF8
                    'username' => 'HPASSET',
                    'password' => 'NN8rOo3L'
                )
            ),
            'zwp_db_oracle_114' => array(
                array(
                    'tns' => "oci:dbname=(DESCRIPTION =(ADDRESS_LIST =(ADDRESS = (PROTOCOL = TCP)(HOST = 172.16.5.115)(PORT = 1521)))(CONNECT_DATA =(SERVICE_NAME = svdp)));charset=UTF8", //测试服务器不支持utf8
                    'username' => 'READER',
                    'password' => 'READER'
                )
            ),
            'zwp_db_oracle_114_new' => array(
                array(
                    'tns' => "oci:dbname=(DESCRIPTION =(ADDRESS_LIST =(ADDRESS = (PROTOCOL = TCP)(HOST = 172.16.5.114)(PORT = 1521)))(CONNECT_DATA =(SERVICE_NAME = testsmsdb)));charset=UTF8", //测试服务器不支持utf8
                    'username' => 'HLASSET',
                    'password' => 'ziroom'
                )
            ),
            'zwp_db_oracle_115' => array(
                array(
                    'tns' => "oci:dbname=(DESCRIPTION =(ADDRESS_LIST =(ADDRESS = (PROTOCOL = TCP)(HOST = 172.16.5.115)(PORT = 1521)))(CONNECT_DATA =(SERVICE_NAME = svdp)));charset=UTF8", //测试服务器不支持utf8
                    'username' => 'hlasset',
                    'password' => 'oracle'
                )
            ),
            'zwp_ad_host' => '172.16.3.12',
            'zwp_ad_port' => 389,
            'zwp_ad_admin_user' => 'ldap',
            'zwp_ad_admin_password' => 'homelink',
            'zwp_ad_base_dn' => 'dc=corp,dc=homelink,dc=com,dc=cn',
            'zwp_index_action' => array(
                0 => array('text' => "积分计划", 'imgUrl' => 's.ziroom.com/images/advert/190x140jfjh.jpg', 'href' => 'http://zmall.ziroom.com/ziroomer/score/?utm_source=ziroom.com&utm_medium=referral&utm_campaign=jifen&utm_content=ad-006'),
            ),
            'commu_api_url' => array(
                'get_contract' => "http://www.baidu.com",
            ),
            'not_check_page' => array(
                'houses/insert1',
                'houses/info',
                'houses/fetchone',
                'houses/tenement'
            ),
            'not_login_page' => array(
                'houses/list',
                'houses/insert1',
                'houses/info',
                'houses/fetchone',
                'houses/tenement'
            )
        )
);
