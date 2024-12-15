<?php

require_once __DIR__ . "/Vendor/autoload.php";
require_once __DIR__ . "/app/routes/Main.php";

use App\Core\Core;
use App\Http\Route;

//Define o tipo de conteúdo da resposta HTTP como JSON
header("Content-Type: application/json; charset=UTF-8");
// Necessário para a mesma máquina (localhost)
header("Access-Control-Allow-Origin: *");
//Define os métodos HTTP permitidos pela API.
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
//Controla o cache do navegador
header("Access-Control: no-cache, no-store, must-revalidate");
//Define quais headers HTTP podem ser incluídos nas requisições feitas pelo cliente.
header("Access-Control-Allow-Headers: *");
//Define por quanto tempo (em segundos) o navegador pode armazenar em cache as permissões CORS.
header("Access-Control-Max-Age: 86400");
//Define o tipo de hoarario no sistema
date_default_timezone_set("America/Sao_Paulo");


Core::dispatch(Route::routes());