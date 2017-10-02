<?php

namespace juniorrosul\OpenLigaDbApi\Api;

use juniorrosul\OpenLigaDbApi\Model\Checkable;

class ArrayOfGoals implements Checkable
{
    /**
     * @var Goal[]
     */
    protected $Goal = null;

    /**
     * @return Goal[]
     */
    public function getGoals()
    {
        return $this->Goal;
    }

    public function isEmpty()
    {
        return $this->Goal === null;
    }

    public function isValid()
    {
        return is_array($this->Goal);
    }


}
