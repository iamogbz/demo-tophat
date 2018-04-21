<?php

use PHPUnit\Framework\TestCase;
use App\Library\LazyAPI\Error;
use App\Library\LazyAPI\Response;

class ErrorTest extends TestCase
{

    /**
     * Test error correctly initialised
     * @return void
     */
    public function testErrorInitStatus()
    {
        $status = 420;
        $error = new Error("TCA", $status, "testing lazy api response error");
        $this->assertEquals($status, $error->getStatus());
    }

    /**
     * Test error get data returns correct structure
     * @return void
     */
    public function testErrorGetData()
    {
        $code = "TCB";
        $message = "another test this is";
        $error = new Error($code, 400, $message);
        $this->assertEquals(
            ["code" => $code, "message" => $message],
            $error->data()
        );
    }

    /**
     * Test response correctly initialises
     * @return void
     */
    public function testResponseInit()
    {
        $status = 411;

        $resp = new Response(null, $status);
        $this->assertEquals($status, $resp->getStatus());

        $resp = Response::new([], $status);
        $this->assertEquals($status, $resp->getStatus());

        $resp = Response::new();
        $this->assertEquals(200, $resp->getStatus());
    }

    /**
     * Test adding error to response overwrites getstatus
     */
    public function testResponseAddError()
    {
        $errCode = "ECA";
        $resp = Response::new()
            ->addError(new Error($errCode, 400, ""));
        $this->assertNotEquals(200, $resp->getStatus());
    }

    /**
     * Test response get status returns the first error added
     */
    public function testResponseGetStatus()
    {
        $errCode = "ECB";
        $errStatus = 400;
        $resp = Response::new()
            ->addError(new Error($errCode, $errStatus, ""))
            ->addError(new Error($errCode, 450, ""));
        $this->assertEquals($errStatus, $resp->getStatus());
    }

    /**
     * Test response build returns correct structure
     */
    public function testResponseBuild()
    {
        $resp = Response::new();
        $this->assertEquals(["data" => null, "errors" => []], $resp->build());
        $resp = Response::new([]);
        $this->assertEquals(["data" => [], "errors" => []], $resp->build());

        $errCode = "ECB";
        $errStatus = 400;
        $errMsg = "";
        $resp = Response::new()
            ->addError(new Error($errCode, $errStatus, $errMsg));
        $this->assertEquals([
            "data" => null,
            "errors" => [["code" => $errCode, "message" => $errMsg]]
        ], $resp->build());
    }

}

?>