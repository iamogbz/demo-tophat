<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;
use App\Library\LazyAPI\Error;
use App\Library\LazyAPI\Response;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{

    /**
     * Get all commments
     *
     * @return \JsonResponse of all comments
     */
    public function getComments()
    {
        return self::jsonResponse(Response::new(Comment::all()));
    }

    /**
     * Create comment from request data
     *
     * @param \Request the request json
     *
     * @return \JsonResponse of new comment or failure
     */
    public function create(Request $request)
    {
        try {
            $this->validate($request, [
                "text" => "required",
            ]);
            $comment = Comment::create($request->all());
            return self::jsonResponse(Response::new($comment));
        } catch (ValidationException $e) {
            $error = new Error("VEX", 422, current($e->errors())[0]);
            return self::jsonResponse(Response::new()->addError($error));
        } catch (QueryException $e) {
            $status = 400;
            $message = "Malformed request";
            if ($e->getCode() == "23000") {
                $status = 422;
                $message = "Parent comment does not exist";
            }
            $error = new Error("DEX", $status, $message);
            return self::jsonResponse(Response::new()->addError($error));
        }
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
