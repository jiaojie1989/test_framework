<?php

/*
 * Copyright 2015 jiaojie <jiaojie1989@gmail.com>.
 * 
 * Check your synatx if it is right.
 * Check your variables if the names are just correct.
 * Finally, run it under your command line.
 * Enjoy it!
 */

namespace zwp\core;

/**
 *
 * @author jiaojie <jiaojie1989@gmail.com>
 * @date Jan 21, 2015
 * @version 1.0.0
 * @description
 */
class context {

    /**
     * context 单例对象
     * 
     * @static
     * @var Zwp_Core_Context
     * @access protected
     */
    static protected $instance;

    /*     
     * @access protected
     * @var object
     */
    protected $bootstrapper;
    protected $filter_chain;
    protected $request;
    protected $response;
    protected $view;
    protected $controller;
    protected $user;
    protected $router;

    /*     * #@- */

    /**
     * 构造函数
     *
     * 由配置中的 core_bootstrap_class 创建 bootstrapper
     */
    private function __construct() {
        $bootstrap_class = Zwp_Config::get('core_bootstrap_class', '/common/core/bootstrap');
        $this->bootstrapper = new $bootstrap_class($this);
    }

    /**
     * 创建 context 对象单例
     *
     * @static
     * @param string $class context 对象的类名，默认为 Zwp_Core_Context
     * @return Zwp_Core_Context $context
     */
    static public function createInstance($class = __CLASS__) {
        if (null === self::$instance) {
            self::$instance = new $class();
        }
        return self::$instance;
    }

    /**
     * 获得对象单例
     *
     * @static
     * @return Zwp_Core_Context $context
     */
    static public function getInstance() {
        return self::$instance;
    }

    /**
     * 处理当前请求
     *
     * 由路由解析请求 url，由控制器分发当前请求，最后由模板产生输出
     */
    public function dispatch() {
        $router = $this->getRouter();
        $request = $this->getRequest();
        $router->parse();
        $this->getFilterChain()->execute();
    }

    /**
     * 设置 bootstrapper
     *
     * @param Zwp_Core_Bootstrap $bootstrapper
     * @return Zwp_Core_Context $this
     */
    public function setBootstrapper($bootstrapper) {
        $this->bootstrapper = $bootstrapper;
        return $this;
    }

    /**
     * 获得 controller 对象
     *
     * 如果未设置过 controller，则调用 $bootstrapper->initController() 方法产生 controller 对象
     *
     * @return object $controller
     */
    public function getController() {
        if (null === $this->controller) {
            $this->controller = $this->bootstrapper->initController();
        }
        return $this->controller;
    }

    /**
     * 设置 controller 对象
     *
     * @param object $controller
     * @return Zwp_Core_Context $this
     */
    public function setController($controller) {
        $this->controller = $controller;
        return $this;
    }

    /**
     * 获得 request 对象
     *
     * 如果未设置过 request，则调用 $bootstrapper->initRequest() 方法产生 request 对象
     *
     * @return object $request
     */
    public function getRequest() {
        if (null === $this->request) {
            $this->request = $this->bootstrapper->initRequest();
        }
        return $this->request;
    }

    /**
     * 设置 request 对象
     *
     * @param object $request
     * @return Zwp_Core_Context $this
     */
    public function setRequest($request) {
        $this->request = $request;
        return $this;
    }

    /**
     * 获得 router 对象
     *
     * 如果未设置过 router，则调用 $bootstrapper->initRouter() 方法产生 router 对象
     *
     * @return object $router
     */
    public function getRouter() {
        if (null === $this->router) {
            $this->router = $this->bootstrapper->initRouter();
        }
        return $this->router;
    }

    /**
     * 设置 router 对象
     *
     * @param object $router
     * @return Zwp_Core_Context $this
     */
    public function setRouter($router) {
        $this->router = $router;
        return $this;
    }

    /**
     * 获得 response 对象
     *
     * 如果未设置过 response，则调用 $bootstrapper->initResponse() 方法产生 response 对象
     *
     * @return object $response
     */
    public function getResponse() {
        if (null === $this->response) {
            $this->response = $this->bootstrapper->initResponse();
        }
        return $this->response;
    }

    /**
     * 设置 response 对象
     *
     * @param object $response
     * @return Zwp_Core_Context $this
     */
    public function setResponse($response) {
        $this->response = $response;
        return $this;
    }

    /**
     * 获得 user 对象
     *
     * 如果未设置过 user，则调用 $bootstrapper->initUser() 方法产生 user 对象
     *
     * @return object $user
     */
    public function getUser() {
        //if ( null === $this->user ) {
        //$this->user = $this->bootstrapper->initUser();
        //}
        return $this->user;
    }

    /**
     * 设置 user 对象
     *
     * @param object $user
     * @return Zwp_Core_Context $this
     */
    public function setUser($user) {
        $this->user = $user;
        return $this;
    }

    /**
     * 获得 view 对象
     *
     * 如果未设置过 view，则调用 $bootstrapper->initView() 方法产生 view 对象
     *
     * @return object $view
     */
    public function getView() {
        if (null === $this->view) {
            $this->view = $this->bootstrapper->initView();
        }
        return $this->view;
    }

    /**
     * 设置 view 对象
     *
     * @param object $view
     * @return Zwp_Core_Context $this
     */
    public function setView($view) {
        $this->view = $view;
        return $this;
    }

    public function getFilterChain() {
        if (null === $this->filter_chain) {
            $this->filter_chain = $this->bootstrapper->initFilterChain();
        }
        return $this->filter_chain;
    }

    public function setFilterChain($chain) {
        $this->filter_chain = $chain;
        return $this;
    }

    /**
     * 将 context 中的对象及常用变量构造成数组，view 对象中将这些变量 extract 到模板文件中
     *
     * @return array
     */
    public function toArray() {
        static $arr;
        if (!$arr) {
            $arr = array(
                'context' => $this,
                'request' => $this->getRequest(),
                'router' => $this->getRouter(),
                'response' => $this->getResponse(),
                'controller' => $this->getController(),
                'user' => $this->getUser(),
                'url_root' => $this->getRouter()->getUrlRoot(),
            );
        }
        return $arr;
    }

}
