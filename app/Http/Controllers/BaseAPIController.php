<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BaseAPIController extends Controller
{
    public function __construct()
    {

    }

    protected function error_response($message, $code = 400)
    {
        // check if $message is object and transforms it into an array
        if (!is_array($message)) {
            $message = implode(",", [$message]);
        }

        switch ($code) {
            default:
                $code_message = 'An error occurred';
                break;
        }

        $data = array(
            'code' => $code,
            'message' => $code_message,
            'errors' => $message,
            'status' => 'failed',
            'success' => false
        );

        return $this->send_response($message, $code);
    }

    protected function success_response($data = [])
    {

        $response = array('data' => $data, 'status' => 'ok', 'success' => true);

        return $this->send_response($response, 200);
    }

    private function send_response($data, $code) : JsonResponse
    {

        return response()->json($data, $code)->header("Access-Control-Allow-Origin",  "*");
    }

    protected function validate_fields(Request $request, array $validations)
    {

        $validator = Validator::make($request->all(), $validations);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $err = [];

            foreach ($errors->all() as $error) {
                $err[] = $error;
            }

            $this->error_response($err, 422)->send();
            exit();
        }
    }
}