<?php

namespace juniorrosul\OpenLigaDbApi\Exception;

class EmptyEntityException extends ApiException
{
    public function __construct($response)
    {
        parent::__construct('mapped entity is empty', $response);
    }
}
