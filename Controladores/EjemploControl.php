<?php
use Slim\Http\Request;
use Slim\Http\Response;

class EjemploControl{

  function getEjemplo(Request $request, Response $response) {
      $response = $response->withHeader('Content-type', 'application/json');
      //$data = Categoria::all();
      $data = '{"mensaje" : "hola"}';
      $response->getBody()->write($data);
      return $response;
  }
}
