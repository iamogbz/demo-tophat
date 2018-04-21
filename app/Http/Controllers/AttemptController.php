<?php

namespace App\Http\Controllers;

use App\Attempt;
use Illuminate\Http\Request;
use App\Library\LazyAPI\Error;
use App\Library\LazyAPI\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AttemptController extends Controller
{

    /**
     * Get all attempts
     *
     * @return \JsonResponse of all attempts
     */
    public function getAttempts()
    {
        return self::jsonResponse(Response::new(Attempt::all()));
    }

    /**
     * Get single attempt
     *
     * @param \Int $id the attempt id
     *
     * @return \JsonResponse the attempt as json
     */
    public function getAttempt($id)
    {
        $attempt = null;
        $error = null;
        try {
            $attempt = Attempt::findOrFail($id);
        } catch (Exception $e) {
            $error = $this->handleException($e);
        }
        return self::jsonResponse(Response::new($attempt)->addError($error));
    }

    /**
     * Create attempt from request data
     *
     * @param \Request the request json
     *
     * @return \JsonResponse of newly created attempt or failure
     */
    public function create(Request $request)
    {
        try {
            $this->validate($request, [
                "note" => "required",
            ]);
            $attempt = Attempt::create($request->all());
            return self::jsonResponse(Response::new($attempt));
        } catch (Exception $e) {
            $error = $this->handleException($e);
            return self::jsonResponse(Response::new()->addError($error));
        }
    }

    /**
     * Create attempt from request data
     *
     * @param \Int $id the attempt id
     * @param \Request the request json
     *
     * @return \JsonResponse or updated resource
     */
    public function update($id, Request $request)
    {
        $attempt = null;
        $error = null;
        try {
            $this->validate($request, [
                "note" => "required",
            ]);
            $attempt = Attempt::findOrFail($id);
            $attempt->update($request->all());
        } catch (Exception $e) {
            $error = self::handleException($e);
        }

        return self::jsonResponse(Response::new($attempt)->addError($error));
    }

    /**
     * Delete single attempt
     *
     * @param \Int $id the attempt id
     *
     * @return \JsonResponse the attempt as json
     */
    public function delete($id)
    {
        $attempt = null;
        $error = null;
        try {
            $attempt = Attempt::findOrFail($id);
            $attempt->delete();
            return response("Delete successful");
        } catch (Exception $e) {
            $error = $this->handleException($e);
            return self::jsonResponse(Response::new($attempt)->addError($error));
        }
    }

    /**
     * Handle controller exceptions
     *
     * @param \Exception $e
     */
    private function handleException(Exception $e)
    {
        $code = "ACU";
        $status = 500;
        $message = $e->getMessage();
        $exc = get_class($e);
        switch ($exc) {
            case ValidationException:
                $code = "VEX";
                $status = 422;
                $message = current($e->errors())[0];
                break;
            case NotFoundHttpException:
            case ModuleNotFoundException:
                $code = "RNF";
                $status = 404;
                $message = "Resource not found";
                break;
            default:
                if (empty($message)) {
                    $message = "Server Error";
                }
        }
        return new Error($code, $status, $message);
    }

    /**
     * Helper function for json response
     *
     * @param \Response
     *
     * @return \JsonResponse data
     */
    private static function jsonResponse(Response $response)
    {
        return response()->json($response->build(), $response->getStatus());
    }
}
