<?php

namespace juniorrosul\OpenLigaDbApi\Api;

use juniorrosul\OpenLigaDbApi\Model\Checkable;

class ArrayOfTeams implements Checkable
{
    /**
     * @var Team[]
     */
    protected $Team = null;

    /**
     * @return Team[]
     */
    public function getTeams()
    {
        return $this->Team;
    }

    public function isEmpty()
    {
        return $this->Team === null;
    }

    public function isValid()
    {
        return is_array($this->Team);
    }
}
