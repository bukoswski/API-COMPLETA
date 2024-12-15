<?php

namespace App\Services;

use App\Utils\Validator;
use App\Models\Users;
use App\Http\jwt;

class UserServices
{
    public static function create(array $data)
    {
        try {
            // Validação dos campos
            $fields = Validator::validate([
                'nome' => $data['nome'] ?? '',
                'idade' => $data['idade'] ?? '',
                'email' => $data['email'] ?? '',
                'cidade' => $data['cidade'] ?? '',
                'estado' => $data['estado'] ?? '',
                'senha' => $data['senha'] ?? ''
            ]);

            if (Users::emailExists($fields['email'])) {
                return ['error' => 'Email já existe'];
            }

            // Hash da senha
            $fields['senha'] = password_hash($fields['senha'], PASSWORD_DEFAULT);

            // Salvar no banco de dados
            $users = Users::save($fields);

            if (!$users) {
                return ['error' => 'Erro ao criar usuário'];
            }

            return 'Usuário criado com sucesso';
        } catch (\mysqli_sql_exception $e) {
            // Verifica erros específicos do MySQL
            if ($e->getCode() === 1062) { // Código 1062: Duplicação de chave única
                return ['error' => 'Esse email já existe'];
            }

            return ['error' => 'Erro no banco de dados: ' . $e->getMessage()];
        } catch (\Exception $e) {
            // Tratamento genérico de exceções
            return ['error' => 'Erro inesperado: ' . $e->getMessage()];
        }
    }

    public static function auth(array $data)
    {
        try {
            $fields = Validator::validate([
                'email' => $data['email'] ?? '',
                'senha' => $data['senha'] ?? ''
            ]);

            $user = Users::Autentication($fields);

            if (!$user) {
                return ['error' => 'Desculpe, não podemos autentica-lo. Verifique suas credenciais.'];
            }

            // Gera o JWT se a autenticação for bem-sucedida
            return jwt::generate($user);
        } catch (\mysqli_sql_exception $e) {
            if ($e->getCode() === 1049) {
                return ['error' => 'Erro no banco de dados.'];
            }
            return ['error' => 'Erro inesperado: ' . $e->getMessage()];
        }
    }


    public static function fetch(mixed $authorization)
    {
        // Log do token recebido
        error_log('Token recebido no UserServices: ' . print_r($authorization, true));

        // Verifica se a autorização está vazia
        if (empty($authorization)) {
            error_log('Token de autorização está vazio');
            return ['error' => 'Token de autorização não fornecido'];
        }

        try {
            $userFromJWT = jwt::varify($authorization);

            // Verifica se a decodificação foi bem-sucedida
            if ($userFromJWT === false) {
                error_log('Falha na verificação do JWT');
                return ['error' => 'Token JWT inválido'];
            }

            // Verifica se o ID está presente no payload
            if (!isset($userFromJWT['id'])) {
                error_log('ID não encontrado no payload');
                return ['error' => 'Token JWT não contém ID de usuário'];
            }

            // Busca o usuário
            $user = Users::getById($userFromJWT['id']);

            // Verifica se o usuário foi encontrado
            if (!$user) {
                return ['error' => 'Usuário não encontrado'];
            }

            // Retorna os dados do usuário
            return $user;
        } catch (\Exception $e) {
            error_log('Erro ao verificar JWT: ' . $e->getMessage());
            return ['error' => 'Erro ao processar token: ' . $e->getMessage()];
        }
    }
    public static function update(mixed $authorization, array $data)
    {
        try {
            if (isset($authorization['error'])) return ['error' => $authorization['error']];
            $userFromJWT = jwt::varify($authorization);
            if (!$userFromJWT) return ['error' => "Por favor realize o login."];

            $fields = Validator::validate([
                'nome' => $data['nome'] ?? '',
                'idade' => $data['idade'] ?? '',
                'email' => $data['email'] ?? '',
                'cidade' => $data['cidade'] ?? '',
                'estado' => $data['estado'] ?? '',
            ]);

            $user = Users::update($userFromJWT['id'], $fields);

            if (!$user) return ['error' => 'Não conseguimos atualizar.'];
            return 'Atualizado com sucesso.';
        } catch (\mysqli_sql_exception $e) {
            if ($e->getCode() === 1049) {
                return ['error' => 'Erro no banco'];
            }
        }
    }

    public static function delete(mixed $authorization, int|string $id)
    {
        try {
            if (isset($authorization['error'])) return ['error' => $authorization['error']];
            $userFromJWT = jwt::varify($authorization);
            if (!$userFromJWT) return ['error' => "Por favor realize o login."];
            $user = Users::delete($id);
            if (!$user) return ['error' => 'Não conseguimos deletar.'];
            return 'Deletado com sucesso.';
        } catch (\mysqli_sql_exception $e) {
            if ($e->getCode() === 1049) {
                return ['error' => 'Erro no banco'];
            }
        }
    }
}