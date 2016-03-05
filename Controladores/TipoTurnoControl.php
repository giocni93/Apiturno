<?php
use Slim\Http\Request;
use Slim\Http\Response;

class TipoTurnoControl{

	function gettipoturnosector(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
	    $data = TipoTurno::all();
	    if(count($data) == 0){
	      $response = $response->withStatus(404);
	    }
	    $response->getBody()->write($data);
	    return $response;
	}

}
