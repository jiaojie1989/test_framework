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

\common\core\autoload::addPath(MODULE_DIR);
\common\core\autoload::addPath(PLUGIN_DIR);
\common\core\autoload::addPath(LIB_DIR);

\test\test2::see();