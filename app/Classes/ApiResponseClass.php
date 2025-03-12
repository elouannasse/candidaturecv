<?php

namespace App\Classes;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiResponseClass
{
    /**
     * Create a new class instance.
     */
    public static function rollback($e, $message ="Something went wrong! Process not completed"){
        DB::rollBack();
        self::throw($e, $message);
    }

    public static function throw($e, $message ="Something went wrong! Process not completed"){
        Log::error($e->getMessage(), ['exception' => $e]);

        throw new HttpResponseException(response()->json([
            "success" => false,
            "message" => $message,
            "error"   => $e->getMessage()        
        ], 500));
    }

    public static function sendResponse($result, $message = "", $code = 200)
    {
        return response()->json([
            'success' => $code < 400, 
            'data'    => $result,
            'message' => $message
        ], $code);
    }
}
