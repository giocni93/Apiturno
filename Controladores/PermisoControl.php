<?php
use Slim\Http\Request;
use Slim\Http\Response;

class PermisoControl{
	function getAll(Request $request, Response $response) {
	    $response = $response->withHeader('Content-type', 'application/json');
	    $data = Permiso::all();
		    if(count($data) == 0){
		      $response = $response->withStatus(404);
		    }
	    $response->getBody()->write($data);
	    return $response;
  	}
}