<?php

namespace Qs\request;

Class Request{
    // 配置信息
    protected $config =[];
    // 保存$_SERVER变量
    protected $server;
    // 保存头部信息变量
    protected $header;
    // 保存php://input 变量
    protected $input;
    // 初始化
    public function __construct(array $options =[]) {
        $this->init($options);
        $this->input = file_get_contents();
    }

    public function init(array $options = []) {
        $this->config = array_merge($this->config, $options);
        $this->server = $_SERVER;
        // TODO: 初始化操作
    }

    /**
     * @Author : Qs
     * @Name   : 获取配置信息
     * @Note   : 
     * @Time   : 2019/07/01 11:39
     * @param    String|Null    $name    配置键名
     * @return   Array|String|Null
     **/
    public function config($name = null) {
        if (null == $name) return $this->config;
        if (isset($this->config[$name])) return $this->config[$name];
        return null; 
    }

    /**
     * @Author : Qs
     * @Name   : 获取$_SERVER信息
     * @Note   : 
     * @Time   : 2019/07/01 11:44
     * @param    String|Null    $name    配置键名
     * @return   Array|String|Null
     **/
    public function server($name = null) {
        if (null == $name) return $this->server;
        if (isset($this->server[$name])) return $this->server[$name];
        return null;
    }

    /**
     * @Author : Qs
     * @Name   : 获取时间戳
     * @Note   : 
     * @Time   : 2019/07/01 11:44
     * @param    Boolean    $float     是否获取带小数点的时间戳
     * @return   String
     **/
    public function time($float = false) {
        return ($float) ? $this->server['REQUEST_TIME_FLOAT'] : $this->server['REQUEST_TIME'];
    }

    /**
     * @Author : Qs
     * @Name   : 获取请求方法
     * @Note   : 
     * @Time   : 2019/07/01 12:18
     * @param    Boolean    $origin    是否获取请求方法
     **/
    public function method($origin = false) {
        if ($origin) return $this->server('REQUEST_METHOD');
        if ($this->server('HTTP_X_HTTP_METHOD_OVERRIDE')) {
            return $this->server('HTTP_X_HTTP_METHOD_OVERRIDE');
        }
        return $this->server('REQUEST_METHOD');
    }

}