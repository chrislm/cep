<?php

/*
 * This file belongs to PHP-MG.
 * 2º PHP TALKS - Dojo
 *
 */

use Respect\Relational\Mapper;
use Respect\Relational\Finder;

set_include_path(get_include_path().PATH_SEPARATOR.__DIR__.'/../library');
spl_autoload_register(require '../library/Respect/Loader.php');

$router = new \Respect\Rest\Router();


//Routes
$router->get('/', function()
{
    header("Status: 400 Bad Request");
    return null;
});

$router->get('/address/*/*', function($cep, $formato = 'xml')
{

    if(!preg_match('/^[0-9]{8}$/', $cep)) {
        header("Status: 400 Bad Request");
        return null;
    }
    
    $pdo = new PDO('mysql:host=localhost;dbname=cidades_estados', 'development', '123456');
    $stmt = $pdo->prepare("SELECT * FROM cep WHERE cep = ? ");
    $stmt->execute(array($cep));
    $cep = $stmt->fetch(PDO::FETCH_OBJ);

    if(!$cep) {
        header("Status: 404 Not Found");
        return null;
    }


    foreach ($cep as $k => $item) {
       $cep->$k = utf8_encode($item);
    }

    if('json' == $formato) {
        header('Content-Type: application/json; charset=utf-8');
        return json_encode($cep);
    } else {
        header('Content-Type: text/xml; charset=utf-8');
        return '<endereco><estado>'.$cep->estado.'</estado><cidade>'.$cep->cidade.'</cidade><locadouro>'.$cep->locadouro.'</locadouro><bairro>'.$cep->bairro.'</bairro><tipo>'.$cep->tipo.'</tipo><observacao>'.$cep->observacao.'</observacao></endereco>';
    }


});

