<?php

namespace juniorrosul\OpenLigaDbApi\Api;

use juniorrosul\OpenLigaDbApi\Model\Checkable;

class ArrayOfGroups implements Checkable
{
    /**
     * @var Group[]
     */
    protected $Group = null;

    /**
     * @return Group[]
     */
    public function getGroups()
    {
        return $this->Group;
    }

    public function isEmpty()
    {
        return $this->Group === null;
    }

    public function isValid()
    {
        return is_array($this->Group);
    }


}
