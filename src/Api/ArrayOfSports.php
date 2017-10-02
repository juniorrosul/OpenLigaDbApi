<?php

namespace juniorrosul\OpenLigaDbApi\Api;

use juniorrosul\OpenLigaDbApi\Model\Checkable;

class ArrayOfSports implements Checkable
{
    /**
     * @var Sport[]
     */
    protected $Sport = null;

    /**
     * @return Sport[]
     */
    public function getSports()
    {
        return $this->Sport;
    }

    public function isEmpty()
    {
        return $this->Sport === null;
    }

    public function isValid()
    {
        return is_array($this->Sport);
    }
}
