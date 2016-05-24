<?php

namespace Libs\SQL;

abstract class Entity
{

    function __construct($rawData = null)
    {
        if (is_array($rawData))
        {
            foreach ($rawData as $k => $v)
            {
                $this->{$k} = $v;
            }
        }
    }

}
