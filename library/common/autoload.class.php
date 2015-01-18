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
class autoload {

    static private $instance;
    static private $registered;

    static public function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    static public function register() {
        if (self::$registered)
            return NULL;
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        if (false === spl_autoload_register(array(self::getInstance(), 'autoload'))) {
            throw new Exception(sprintf('Unable to register %s::autoload as an autoloading method.', get_class(self::getInstance())));
        }
        self::$registered = true;
    }
    
    static public function unregister() {
        spl_autoload_unregister(array(self::getInstance(), 'autoload'));
        self::$registered = false;
    }
    
    public function autoload($class) {
        if (class_exists($class, false) || interface_exists($class, false)) {
            return true;
        }
        $class = strtolower($class);
        $file = LIB_DIR . DS . str_replace('\\', DS, $class) . '.class.php';
        if (file_exists($file)) {
            require($file);
            return true;
        }
        return false;
    }
}
