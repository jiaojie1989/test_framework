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
 * @date Jan 18, 2015
 * @version 1.0.0
 * @description
 */

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/site_config.php';

\zwp\config::set('core_bootstrap_class', '\common\core\bootstrap');

\zwp\core\autoload::addPath(MODULE_DIR);
\zwp\core\autoload::addPath(PLUGIN_DIR);
\zwp\core\autoload::addPath(LIB_DIR);

$config = \zwp\config::getConfig();

\zwp\db::setOptions(\zwp\config::get('zwp_db_mysql_249'));

$db = \zwp\factory::getTable('t_cms_user');

$ret = $db->selectOne('*', array('is_del' => 0));

var_dump($ret);