<?php

namespace App\Traits;

use Carbon\Carbon;

trait ApiResponse
{
    /**
     * Format response sukses
     */
    public function successResponse($result = null, $message = 'success', $code = 200, $description = 'OK')
    {
        return response()->json([
            'timestamp'   => Carbon::now()->format('d/m/Y H:i:s'),
            'code'        => $code,
            'description' => $description,
            'message'     => $message,
            'result'      => $result // Data harusnya muncul di sini
        ], $code, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Format response error
     */
    public function errorResponse($message, $code, $description, $result = null)
    {
        return response()->json([
            'timestamp'   => Carbon::now()->format('d/m/Y H:i:s'),
            'code'        => $code,
            'description' => $description,
            'message'     => $message,
            'result'      => $result
        ], $code, [], JSON_UNESCAPED_SLASHES);
    }
}
