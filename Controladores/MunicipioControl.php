<?php
use Slim\Http\Request;
use Slim\Http\Response;

class MunicipiosControl{

	function getAll(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $data = Municipio::all();
        if(count($data) == 0){
          $response = $response->withStatus(404);
        }
        $response->getBody()->write($data);
        return $response;
    }

}