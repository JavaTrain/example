<?php

namespace Framework\Request;

class Request{

    protected $basePath;

    protected $query = array();

    protected $request = array();

    public function getUri(){

        return $_SERVER['REQUEST_URI'];
    }

    public function __construct(){

        $this->parseUri();
        $this->parseRequest();
    }

    protected function parseUri(){

        $vars = array();

        $uri_parsed = parse_url($this->getUri());

        $this->basePath = $uri_parsed['path'];

        if(!empty($uri_parsed['query'])){
            parse_str($uri_parsed['query'], $vars);
//            $this->query = $_GET;
        }

        $this->query = $vars;
    }

    protected function parseRequest(){

//        $rawData = file_get_contents('php://input');
//
//        if(!empty($rawData)){
//            parse_str($rawData, $vars);
//        }
//
//        $this->request = $vars;

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $this->request = $_POST;
        }
    }

    /**
     * @return mixed
     */
    public function getBasePath()
    {
        return $this->basePath;
    }
    /**
     * @return array
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getRequest()
    {
        return $this->request;
    }

    function post($name){
        return htmlspecialchars($this->request[$name]);
    }


}