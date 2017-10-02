<?php

namespace juniorrosul\OpenLigaDbApi\Exception;

class ApiException extends \RuntimeException
{
    protected $response;

    public function __construct($message, $response)
    {
        $this->message = $message;
        $this->response = $response;
    }

    /**
     * @return int
     */
    public function getResponse()
    {
        return $this->response;
    }

}
