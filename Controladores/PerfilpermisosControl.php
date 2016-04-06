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
  								->groupBy('idmodulo')
  								->get();
  		$response->getBody()->write($data);
    	return $response;
  	}

  	function getIdPermisos(Request $request, Response $response){
  		$response = $response->withHeader('Content-type', 'application/json');
  		$id = $request->getAttribute("id");
  		$data = Perfilpermisos::select('idpermiso')
  								->where('idperfil','=',$id)
  								->get();
  		$response->getBody()->write($data);
    	return $response;
  	}

  	function validarpermisos(Request $request, Response $response){
            $response = $response->withHeader('Content-type', 'application/json');
	    $idperfil = $request->getAttribute("idperfil");
	    $data = Perfilpermisos::select('idpermiso','idmodulo')
                                ->where('idperfil','=',$idperfil)
                                ->get();
                $response->getBody()->write($data);
                return $response;
  	}
        
        function modulopermisoperfil(Request $request, Response $response){
            $response = $response->withHeader('Content-type', 'application/json');
            $idperfil = $request->getAttribute("idperfil");
            $data = Perfilpermisos::select('idmodulo','idperfil')
                        ->where('idperfil','=',$idperfil)
                        ->groupBy('idmodulo')
                        ->get();
            for($i=0;$i<count($data);$i++){
                $permisos = Perfilpermisos::select('idpermiso')
                                ->where('idperfil','=',$data[$i]->idperfil)
                                ->where('idmodulo','=',$data[$i]->idmodulo)
                                ->get();
                $data[$i]['permisos'] = $permisos;
            }
            $response->getBody()->write($data);
            return $response;
        }

}