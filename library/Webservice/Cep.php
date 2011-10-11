<?php
use Respect\Rest\Routable;

class Cep implements Routable {
    public function get($cep) { return 'Testes'; }
    public function version() { return '0.1'; }
    public function help() { return 'Blah blah blah'; }
}
