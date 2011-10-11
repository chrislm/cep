<?php

/**
* This file belongs to PHP-MG.
* 2º PHP TALKS - Dojo
*/

use Respect\Config\Container;

set_include_path(get_include_path().PATH_SEPARATOR.__DIR__.'/../library');
spl_autoload_register(require '../library/Respect/Loader.php');

$app = new Container('../config.ini');

//Routes
$app->router->get('/address/*', function($cep) use ($app) {
    return $app->mapper->cep[$cep]->fetch();
})->when(function($cep){
    return preg_match('/^[0-9]{8}$/', $cep);
})->accept(array(
    '.json' => function($cep) {
        header('Content-Type: application/json');
        return json_encode($data);
    },
    '.xml' => function($cep) {
        header('Content-Type: text/xml; charset=utf-8');
        return '<endereco><estado>'.$cep->estado.'</estado><cidade>'.$cep->cidade.'</cidade><locadouro>'.$cep->locadouro.'</locadouro><bairro>'.$cep->bairro.'</bairro><tipo>'.$cep->tipo.'</tipo><observacao>'.$cep->observacao.'</observacao></endereco>';
    },
));

//Not found route
$app->router->get('/**', function()
{
    header("HTTP/1.1 404 Not Found");
    return null;
});

