<?php

namespace App\Models;

use App\Models\Database;


class Users extends Database
{
    public static function save(array $data)
    {
        // Conexão com o banco
        $conn = self::getConnect();

        // Preparar a consulta
        $stmt = $conn->prepare("INSERT INTO clientes (nome, idade, email, cidade, estado, senha) VALUES (?, ?, ?, ?, ?, ?)");


        // Associar os parâmetros
        $stmt->bind_param("sissss", $data['nome'], $data['idade'], $data['email'], $data['cidade'], $data['estado'], $data['senha']);

        // Executar a consulta
        if ($stmt->execute()) {
            return true; // Retorna sucesso
        } else {
            return false; // Retorna falha
        }
    }

    public static function emailExists(string $email)
    {
        $conn = Database::getConnect();

        $stmt = $conn->prepare("SELECT id_cliente FROM clientes WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    public static function Autentication(array $data)
    {
        $conn = Database::getConnect();
        $stmt = $conn->prepare("SELECT * FROM clientes WHERE email = ?");
        $stmt->bind_param("s", $data['email']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows < 1) return false; // Retorna false se não encontrar o usuário

        $user = $result->fetch_assoc();

        // Verifica a senha
        if (!password_verify($data['senha'], $user['senha'])) {
            return false; // Retorna false se a senha estiver incorreta
        }

        return [
            'id' => $user['id_cliente'],
            'nome' => $user['nome'],
            'email' => $user['email'],
            'cidade' => $user['cidade'],
            'estado' => $user['estado'],
        ];
    }


    public static function getById(int | string $id)
    {
        $conn = Database::getConnect();
        $stmt = $conn->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public static function update(int|string $id, array $data)
    {
        $conn = Database::getConnect();

        // A consulta SQL agora tem 5 parâmetros
        $stmt = $conn->prepare("UPDATE clientes SET nome = ?, idade = ?, email = ?, cidade = ?, estado = ? WHERE id_cliente = ?");

        // Agora estamos passando todos os 5 parâmetros necessários
        $stmt->bind_param("sisssi", $data['nome'], $data['idade'], $data['email'], $data['cidade'], $data['estado'], $id);

        $stmt->execute();

        return $stmt->affected_rows > 0; // Verifica se a atualização foi bem-sucedida
    }


    public static function delete(int | string $id)
    {
        $conn = Database::getConnect();
        $stmt = $conn->prepare("DELETE FROM clientes WHERE id_cliente = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->affected_rows > 0 ? true : false;
    }
}