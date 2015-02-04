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

define('DS', DIRECTORY_SEPARATOR);
define('PRE_DIR', '..');
define('CUR_DIR', '.');

define('WEB_ROOT', __DIR__ . DS . PRE_DIR);

define('LIB_DIR', WEB_ROOT . DS . 'library');
define('CONFIG_DIR', WEB_ROOT . DS . 'config');
define('INDEX_DIR', WEB_ROOT . DS . 'htdocs');

define('MODULE_DIR', WEB_ROOT . DS . 'modules');
define('PLUGIN_DIR', WEB_ROOT . DS . 'plugins');
define('TEMPLATES_DIR', WEB_ROOT . DS . 'templates');

require LIB_DIR . DS . 'zwp/autoload.class.php';
\zwp\autoload::register();
\zwp\core\autoload::register();

