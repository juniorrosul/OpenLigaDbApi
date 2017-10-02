<?php

namespace juniorrosul\OpenLigaDbApi\Api;

use juniorrosul\OpenLigaDbApi\Model\Checkable;

abstract class AbstractApiEntity implements Checkable
{
    public function isValid()
    {
        return !$this->isEmpty();
    }
}
