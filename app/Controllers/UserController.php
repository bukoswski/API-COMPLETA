<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\UserServices;

class UserController
{
    public function store(Request $request, Response $response)
    {
        $body = $request::body();

        $userServices = UserServices::create($body);

        if (isset($userServices['error'])) {
            return $response::json([
                'error' => true,
                'success' => false,
                'message' => $userServices['error']
            ], 400);
        }

        $response::json([
            'error' => false,
            'success' => true,
            'data' => $userServices
        ], 201);
    }

    public function login(Request $request, Response $response)
    {
        $body = $request::body();
        $userServices = UserServices::auth($body);

        if (isset($userServices['error'])) {
            return $response::json([
                'error' => true,
                'success' => false,
                'message' => $userServices['error']
            ], 400);
        }

        $response::json([
            'error' => false,
            'success' => true,
            'JWT' => $userServices
        ], 200);
    }

    public function fetch(Request $resquest, Response $response)
    {
        $authorization = $resquest::authorization();

        $userServices = UserServices::fetch($authorization);

        if (isset($userServices['error'])) {
            return $response::json([
                'error' => true,
                'success' => false,
                'message' => $userServices['error']
            ], 400);
        }

        $response::json([
            'error' => false,
            'success' => true,
            'JWT' => $userServices
        ], 200);
        return;
    }
    public function update(Request $request, Response $response)
    {

        $authorization = $request::authorization();
        $body = $request::body();

        $userServices = UserServices::update($authorization, $body);



        if (isset($userServices['error'])) {
            return $response::json([
                'error' => true,
                'success' => false,
                'message' => $userServices['error']
            ], 400);
        }

        $response::json([
            'error' => false,
            'success' => true,
            'message' => $userServices
        ], 200);
    }

    public function delete(Request $request, Response $response, array $id)
    {

        $authorization = $request::authorization();

        $userServices = UserServices::delete($authorization, $id[0]);



        if (isset($userServices['error'])) {
            return $response::json([
                'error' => true,
                'success' => false,
                'message' => $userServices['error']
            ], 400);
        }

        $response::json([
            'error' => false,
            'success' => true,
            'message' => $userServices
        ], 200);
    }
}