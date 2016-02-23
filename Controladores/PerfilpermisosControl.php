<?php
use Slim\Http\Request;
use Slim\Http\Response;

class PerfilpermisosControl{
	
	function getAll(Request $request, Response $response) {
	    $response = $response->withHeader('Content-type', 'application/json');
	    $data = Perfilpermisos::all();
		    if(count($data) == 0){
		      $response = $response->withStatus(404);
		    }
	    $response->getBody()->write($data);
	    return $response;
  	}

  	function getId(Request $request, Response $response){
  		$response = $response->withHeader('Content-type', 'application/json');
  		$id = $request->getAttribute("id");
  		$data = Perfilpermisos::select('idpermiso','idmodulo')
  								->where('idperfil','=',$id)
  								->get();
  		$response->getBody()->write($data);
    	return $response;
  	}

  	function validarpermisos(Request $request, Response $response){
  		$response = $response->withHeader('Content-type', 'application/json');
	    $data = json_decode($request->getBody(),true);
	    $idperfil = $request->getAttribute("idperfil");
	    $idpermiso = $request->getAttribute("idpermiso");
	    $data = Perfilpermisos::select('idpermiso')
	    						->where('idperfil','=',$idperfil)
	    						->where('idpermiso','=',$idpermiso)
	    						->get();
	    				$response->getBody()->write($data);
    					return $response;
  	}

}