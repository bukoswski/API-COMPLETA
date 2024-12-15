<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class NotFoundController
{
    public static function index(Request $request, Response $response)
    {
        $response::json([
            'error' => true,
            'success' => false,
            'message' => 'Sorry , this route is not found.'
        ], 404);
    }
}