<?php

namespace Qs\request;

Class Request{
    // 配置信息
    protected $config =[];
    // 保存$_SERVER变量
    protected $server;
    // 保存头部信息变量
    protected $header;
    // 保存参数信息变量
    protected $param;
    // 保存路由参数变量
    protected $router;
    // 保存php://input 变量
    protected $input;
    // 初始化
    public function __construct(array $options =[]) {
        $this->init($options);
        $this->input = file_get_contents('php://input');
    }

    public function init(array $options = []) {
        $this->config = array_merge($this->config, $options);
        // TODO: 初始化操作
        $this->server = $_SERVER;
        // TODO: HEADER
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

    public function content_type() {
        $contentType = $this->server('CONTENT_TYPE');

        if ($contentType) {
            if (strpos($contentType, ';')) {
                list($type) = explode(';', $contentType);
            } else {
                $type = $contentType;
            }
            return trim($type);
        }

        return '';
    }

    /**
     * @Author : Qs
     * @Name   : 获取请求头信息
     * @Note   : 
     * @Time   : 2019/07/01 14:38
     * @param    String    $name    键名
     * @return   Array|String
     **/
    public function header($name = '') {
        if (empty($this->header)) {
            $header = [];
            $result = [];
            if (function_exists('apache_request_headers') && $result = apache_request_headers()) {
                $header = $result;
            } else {
                $server = $this->server;
                foreach ($server as $k => $v) {
                    if (0 == strpos($k,'HTTP_')) {
                        $k = str_replace('_', '-', strtolower(substr($k, 5)));
                        $header[$k] = $v;
                    }
                }
                if (isset($server['CONTENT_TYPE'])) $header['content-type'] =$server['CONTENT_TYPE'];
                if (isset($server['CONTENT_LENGTH'])) $header['content-length'] =$server['CONTENT_LENGTH'];
            }
            $this->header = array_change_key_case($header);
        }
        if ('' == $name) return $this->header;
        $name = str_replace('_', '-', strtolower($name));

        return (isset($this->header[$name])) ? $this->header[$name] : '';
    }

    public function set_router_param(array $data = []) {
        $this->router = $data;
    }

    /**
     * @Author : Qs
     * @Name   : 获取所有信息
     * @Note   : 
     * @Time   : 2019/07/01 15:08
     * @param    String|False    $name       当为false时为不处理数据
     * @return   Array|Object
     **/
    public function param($name = '') {
        $method = $this->method(true);
        $data = !empty($_POST) ? $_POST : $this->getInputData($this->input);
        $this->param = array_merge($this->param, $_GET, $data, $this->router);
        return $this->param;
    }

    public function getInputData($data = []) {
        if (false !== strpos($this->content_type(), 'application/json') || 0 === strpos($content, '{"')) {
            return (array) json_decode($data, true);
        } elseif (strpos($content, '=')) {
            parse_str($content, $data);
            return $data;
        }
        return [];
    }


}