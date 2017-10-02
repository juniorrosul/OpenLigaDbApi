<?php

namespace juniorrosul\OpenLigaDbApi\Exception;

class InvalidEntityException extends ApiException
{
    public function __construct($response)
    {
        parent::__construct('mapped entity is invalid', $response);
    }
}
