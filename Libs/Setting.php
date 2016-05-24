<?php

namespace Libs;

class Setting
{

    public $appId;

    /** @var \SimpleXMLElement */
    public $xml;
    protected $settings = array();

    static function getAllApp()
    {
        $arr = array();
        foreach (scandir(BASE_DIR . '/Config/Settings') as $item)
        {
            if (strpos($item, '.xml') === false)
            {
                continue;
            }

            $arr[] = str_replace('.xml', '', $item);
        }
        return $arr;
    }

    function __construct($appId)
    {
        $this->appId = $appId;
        $this->xml = simplexml_load_file(BASE_DIR . '/Config/Settings/' . $appId . '.xml', 'SimpleXmlElement', LIBXML_NOCDATA);

        //load settings
        if (count($this->xml->settings))
        {
            foreach ($this->xml->settings->group as $group)
            {
                foreach ($group->field as $field)
                {
                    $this->settings[strval($field->id)] = strval($field->value);
                }
            }
        }
    }

    function getSetting($key)
    {
        return arrData($this->settings, $key);
    }

}
