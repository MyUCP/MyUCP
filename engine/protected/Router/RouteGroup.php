<?php

class RouteGroup
{
    private $url;
    private $group;
    private $link;

    private $domains = [];
    private $prefixes = [];
    private $sessions = [];

    public function __construct($group, $url, &$link)
    {
        $this->group = $group;
        $this->url = $url;
        $this->link = &$link;
        $this->callGroup();
    }

    protected function callGroup()
    {
        if(array_key_exists("domain", $this->group['rule']))
            $this->initDomainGroup($this->group);

        if(array_key_exists("prefix", $this->group['rule']))
            $this->initPrefixGroup($this->group);

        if(array_key_exists("param", $this->group['rule']))
            $this->initParamGroup($this->group);

        return $this;
    }

    protected function initPrefixGroup($group = [])
    {
        if(preg_match('/^['. $group['rule']['prefix'] .']+\/.+/', $this->url)){
            $this->link['type'] = "prefix";
            return $group['callback']();
        }

        return false;
    }

    protected function initDomainGroup($group = [])
    {

        $regex = '/' . preg_replace('/\//', '\/', $group['rule']['domain']) .  '/';
        $params = [];
        if(preg_match_all('/\{([a-z]+):(.*?)\}/', $group['rule']['domain'], $preg)) {
            for($i = 0; $i < count($preg[0]); $i++){
                $params[] = $preg[1][$i];
                $regex = str_replace($preg[0][$i], '(' . $preg[2][$i] . ')', $regex);
            }
            preg_match($regex, $_SERVER['HTTP_HOST'], $preg);

            $parameters = [];
            for ($i = 0; $i < count($params); $i++) {
                $parameters[$params[$i]] = $preg[$i + 1];
            }
            $this->link['type'] = "domain";
            $this->link['parameters'] = $parameters;
            return call_user_func_array($group['callback'], $parameters);
        } else {
            if($group['rule']['domain'] == $_SERVER['HTTP_HOST']) {
                $this->link['type'] = "domain";
                return $group['callback']();
            }
        }

        return false;
    }

    protected function initParamGroup($group = [])
    {
        $param = $group['rule']['param'];

        if(is_array($param[2])) {
            $key = array_keys($param)[2];
            $value = $param[$key];
        } else {
            $key = $param[2];
            $value = null;
        }

        switch ($param[0]) {
            case "session":
                if($this->makeComparison($param[1], session($key), $value))
                    return $group['callback']();
                break;

            case "get":
                if($this->makeComparison($param[1], Request::get($key), $value))
                    return $group['callback']();
                break;

            case "post":
                if($this->makeComparison($param[1], Request::post($key), $value))
                    return $group['callback']();
                break;

            case "cookie":
                if($this->makeComparison($param[1], cookie($key)->getValue(), $value))
                    return $group['callback']();
                break;
        }

        return false;
    }

    private function makeComparison($condition, $valueOne, $valueTwo = null)
    {
        switch ($condition) {
            case "==":
               if($valueOne == $valueTwo)
                    return true;
                break;

            case "!=":
                if($valueOne != $valueTwo) return true;

                break;

            case "empty":
                if(empty($valueOne)) return true;
                break;

            case "!empty":
                if(!empty($valueOne)) return true;

                break;

            case "isset":
                if(isset($valueOne)) return true;

                break;

            case "!isset":
                if(!isset($valueOne)) return true;

                break;
        }

        return false;
    }

    public function __destruct()
    {
        $this->link['parameters'] = null;
    }
}