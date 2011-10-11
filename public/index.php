<?php

use Respect\Relational\Mapper;
use Respect\Relational\Finder;


date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 1);
error_reporting(-1);
set_include_path(get_include_path().PATH_SEPARATOR.__DIR__.'/../library');
spl_autoload_register(require '../library/Respect/Loader.php');

$router = new \Respect\Rest\Router();


//Routes
$router->get('/', function() {
    return 'UHULL';
});

$router->get('/address/*/*', function($cep, $formato = 'xml') {

        if(!preg_match('/^[0-9]{8}$/', $cep))
        {
            header("Status: 404 Not Found");
            return 'Uhull nao deu certo';
        }

        $pdo = new PDO('mysql:host=localhost;dbname=cidades_estados', 'development', '123456');
        $stmt = $pdo->prepare("SELECT * FROM cep WHERE cep = ? ");
        $stmt->execute(array($cep));
        $cep = $stmt->fetch(PDO::FETCH_OBJ);

        if(!$cep){
            header("Status: 404 Not Found");
            return 'Uhull cep não encontrado ou vc mora no Acre';
        }


        foreach ($cep as $k => $item) {
            $cep->$k = utf8_encode($item);
        }

        switch ($formato)
        {
            case 'json':
                header('Content-Type: application/json; charset=utf-8');
                return json_encode($cep);
                break;
            case 'xml':
                header('Content-Type: text/xml; charset=utf-8');
                return '<endereco>
                    <estado>'.$cep->estado.'</estado>
                    <cidade>'.$cep->cidade.'</cidade>
                    <locadouro>'.$cep->locadouro.'</locadouro>
                    <bairro>'.$cep->bairro.'</bairro>
                    <tipo>'.$cep->tipo.'</tipo>
                    <observacao>'.$cep->observacao.'</observacao>
                </endereco>';
                break;
        }


});

