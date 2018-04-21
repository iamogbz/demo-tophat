<?php

namespace App\Library\LazyAPI;

/**
 *Lazy api error class
 */
class Error
{
    private $code;
    private $status;
    private $message;

    public function __construct($code, $status, $message)
    {
        $this->code = $code;
        $this->status = $status;
        $this->message = $message;
    }

    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Build json style array
     *
     * @return \Array
     */
    public function data()
    {
        return ["code"=>$this->code, "message"=>$this->message];
    }
}
