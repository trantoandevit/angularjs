<?php

namespace Libs;

class Translator
{

    protected $data = array();
    protected $baseDir;
    protected $language;

    function __construct($baseDir)
    {
        $this->setBaseDir($baseDir);
    }

    function setBaseDir($dir)
    {
        $this->baseDir = $dir;
        return $this;
    }

    function load($path)
    {
        require $this->baseDir . '/' . $path;
        if (!isset($langs))
        {
            trigger_error('File must has $langs', E_USER_ERROR);
            return;
        }
        $this->data += $langs;
        return $this;
    }

    function getData()
    {
        return $this->data;
    }

    /**
     * 
     * @param type $key
     * @param type $params
     * @return str
     */
    function translate($key, $params = array())
    {
        $str = arrData($this->data, $key);
        if (!$str)
        {
            return '{{' . $key . '}}';
        }
        $ks = array_keys($params);
        $vs = array_values($params);
        foreach ($ks as &$k)
        {
            $k = '{{' . $k . '}}';
        }

        return str_replace($ks, $vs, $str);
    }

    /**
     * 
     * @param type $key
     * @param type $params
     * @return str
     */
    function __($key, $params = array())
    {
        return $this->translate($key, $params);
    }

}
