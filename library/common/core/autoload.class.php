<?php

/*
 * Copyright 2015 jiaojie <jiaojie1989@gmail.com>.
 * 
 * Check your synatx if it is right.
 * Check your variables if the names are just correct.
 * Finally, run it under your command line.
 * Enjoy it!
 */

namespace common\core;

/**
 *
 * @author jiaojie <jiaojie1989@gmail.com>
 * @date Jan 18, 2015
 * @version 1.0.0
 * @description
 */
class autoload {

    static private $registered;

    static private $instance;

    private $include_path;

    static public function addPath($path) {
        $autoloader = self::getInstance();
        return $autoloader->addIncludePath($path);
    }

    public function addIncludePath($path) {
        if (file_exists($path)) {
            $this->include_path[rtrim($path, DIRECTORY_SEPARATOR)] = 1;
            return true;
        }
        return false;
    }

    public function getIncludePath() {
        return $this->include_path;
    }

    static public function register() {
        if (self::$registered)
            return;
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        if (false === spl_autoload_register(array(self::getInstance(), 'autoload'))) {
            throw new Exception(sprintf('Unable to register %s::autoload as an autoloading method.', get_class(self::getInstance())));
        }
        self::$registered = true;
    }

    static public function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    static public function unregister() {
        spl_autoload_unregister(array(self::getInstance(), 'autoload'));
        self::$registered = false;
    }

    public function autoload($class) {
        // class already exists
        if (class_exists($class, false) || interface_exists($class, false)) {
            return true;
        }
        if (empty($this->include_path)) {
            return false;
        }
        $class = strtolower($class);
        $pos = strpos($class, '\\');
        if ($pos !== false) {
            //$part = substr($class, 0, $pos) . DIRECTORY_SEPARATOR . 'lib' . str_replace('_', DIRECTORY_SEPARATOR, substr($class, $pos));
            //$part = substr($class, 0, $pos) . DS . str_replace('\\', DS, substr($class, $pos));
            $part = str_replace('\\', DS, substr($class, $pos));
            foreach ($this->include_path as $path => $i) {
                $file = $path . DS . $part . '.class.php';
                if (file_exists($file)) {
                    require($file);
                    return true;
                }
            }
        }
        return false;
    }

}
