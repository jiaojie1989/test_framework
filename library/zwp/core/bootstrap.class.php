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
class bootstrap {
    protected $context;
    
    /**
     * 构造函数
     * 
     * 用户可以把这个构造函数作为设置用户自定义的初始化代码的入口，
     * 进行事件挂载、插件加载等等操作。
     * @param $context
     */
    public function __construct($context)
    {
        $this->context = $context;
    }
    
    /**
     * 初始化 context 中 controller 对象
     * 
     * @return Zwp_Core_Controller $controller
     */
    public function initController()
    {
        return new Zwp_Core_Controller($this->context);
    }

    /**
     * 初始化 context 中 request 对象
     * 
     * @return Zwp_Core_Request $request
     */
    public function initRequest()
    {
        return new Zwp_Core_Request();
    }

    /**
     * 初始化 context 中 response 对象
     * 
     * @return Zwp_Core_Response $response
     */
    public function initResponse()
    {
        return new Zwp_Core_Response();
    }

    /**
     * 初始化 context 中 router 对象
     * 根据是否打开 tom_rewrite_url 选项选择:
     *   - 如果打开，使用 Zwp_Core_Router_Pattern,
     *   - 如果未打开使用 Zwp_Core_Router_NoRoute
     * 
     * @return Zwp_Core_Router_NoRoute|Zwp_Core_Router_Pattern
     */
    public function initRouter()
    {
        return new Zwp_Core_Router_NoRoute($this->context);
    }

    /**
     * 初始化 context 中 user 对象
     * 
     * @return Zwp_Core_User $user
     */
    public function initUser()
    {
        return new Zwp_Core_User($this->context);
    }

    /**
     * 初始化 context 中 view 对象
     * 
     * @return Zwp_Core_View $view
     */
    public function initView()
    {
        return new Zwp_Core_View($this->context);
    }

    public function initFilterChain()
    {
        $chain = new Zwp_Core_FilterChain();
        $chain->register( new Zwp_Core_Filter_User($this->context) );
        $chain->register( new Zwp_Core_Filter_Cache($this->context) );
        $chain->register( new Zwp_Core_Filter_Rendering($this->context) );
        $chain->register( new Zwp_Core_Filter_Execution($this->context) );
        return $chain;
    }
}
