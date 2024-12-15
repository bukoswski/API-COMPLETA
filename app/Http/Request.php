<?php

namespace App\Http;

class Request
{
    public static function method()
    {
        return $_SERVER["REQUEST_METHOD"];
    }

    public static function body()
    {
        $json = json_decode(file_get_contents('php://input'), true) ?? [];
        $data = match (self::method()) {
            'GET' => $_GET,
            'POST', 'DELETE', 'PUT' => $json,
        };
        return $data;
    }

    public static function authorization()
    {
        $authorization = getallheaders();

        if (!isset($authorization['Authorization'])) return ['error' => "Autorização nao foi promovida."];

        $authorizationPartials = explode(' ', $authorization['Authorization']);

        if (count($authorizationPartials) != 2) return ['error' => "Por favor forncer um header de autorização valido."];

        return $authorizationPartials[1] ?? '';
    }
}