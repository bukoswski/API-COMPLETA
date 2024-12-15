<?php

namespace App\Http;

class jwt
{
    private static string $token = 'secret-key';

    public static function generate(array $data = [])
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode($data);

        $base64UrlHeader = self::base64url_encode($header);
        $base64UrlPayload = self::base64url_encode($payload);

        $signature = self::signature($base64UrlHeader, $base64UrlPayload);

        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $signature;

        return $jwt;
    }

    public static function varify(string $jwt)
    {
        $tokenParts = explode('.', $jwt);
        if (count($tokenParts) != 3) {
            error_log('Token JWT inválido: número de partes incorreto');
            return false;
        }

        [$header, $payload, $signature] = $tokenParts;

        // Verifica a assinatura
        if ($signature !== self::signature($header, $payload)) {
            error_log('Assinatura do token JWT inválida');
            return false;
        }

        // Decodifica o payload
        $decodedPayload = self::base64url_decode($payload);

        // Log do payload decodificado
        error_log('Payload decodificado: ' . print_r($decodedPayload, true));

        return $decodedPayload;
    }

    public static function signature(string $header, string $payload)
    {
        $signature = hash_hmac('sha256', $header . '.' . $payload, self::$token);
        return self::base64url_encode($signature);
    }

    public static function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64url_decode($data)
    {
        // Adiciona padding se necessário
        $padding = strlen($data) % 4;
        if ($padding > 0) {
            $data .= str_repeat('=', 4 - $padding);
        }

        // Substitui caracteres do URL-safe base64
        $data = strtr($data, '-_', '+/');

        // Decodifica
        $decoded = base64_decode($data);

        // Tenta decodificar como JSON
        $result = json_decode($decoded, true);

        // Log para depuração
        error_log('base64url_decode - Dados originais: ' . $data);
        error_log('base64url_decode - Decodificado: ' . $decoded);
        error_log('base64url_decode - JSON: ' . print_r($result, true));

        return $result;
    }
}