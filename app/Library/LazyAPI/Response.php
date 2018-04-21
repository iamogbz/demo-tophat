<?php

namespace App\Library\LazyAPI;

/**
 * Lazy api response class
 */
class Response
{

    private $data;
    private $status;
    private $errors;

    public function __construct($data, $status = 200, $errors = [])
    {
        $this->data = $data;
        $this->status = $status;
        $this->errors = $errors;
    }

    /**
     * Build new response object
     */
    public static function new($data = null, $status = 200)
    {
        return new Response($data, $status);
    }

    /**
     * Add error to response
     *
     * @return \Response
     */
    public function addError(?Error $error)
    {
        if ($error != null) {
            array_push($this->errors, $error);
        }
        return $this;
    }

    public function getStatus()
    {
        if (count($this->errors) > 0) {
            return $this->errors[0]->getStatus();
        } else {
            return $this->status;
        }
    }

    public function build()
    {
        $response = ["data"=>$this->data, "errors"=>[]];
        foreach ($this->errors as $error) {
            array_push($response["errors"], $error->data());
        }
        return $response;
    }
}
