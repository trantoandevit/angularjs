<?php

namespace Libs;

class Menu
{

    public $id;
    public $label;
    public $url;
    public $selected = false;

    /** @var static */
    public $children = array();

    /** @var static */
    public $parent;

    /**
     * 
     * @param type $id
     * @param type $label
     * @param type $url
     * @param type $children
     */
    function __construct($id, $label, $url, $children = array())
    {
        $this->id = $id;
        $this->label = $label;
        $this->url = $url ? $url : 'javascript:;';
        $this->children = $children;
    }

    function addChild(Menu $menu)
    {
        $this->children[] = $menu;
        return $this;
    }

    function get($id)
    {
        if ($this->id == $id)
        {
            return $this;
        }
        else
        {
            foreach ($this->children as $child)
            {
                $found = $child->get($id);
                if ($found)
                {
                    return $found;
                }
            }
        }
    }

    function hasChildren()
    {
        return count($this->children) ? true : false;
    }

}
