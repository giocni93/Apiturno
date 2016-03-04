<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Illuminate\Database\Capsule\Manager as DB;

class TipoturnosectorControl{

	function gettipoturnosector(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
	    $data = Tipoturnosector::all();
	    if(count($data) == 0){
	      $response = $response->withStatus(404);
	    }
	    $response->getBody()->write($data);
	    return $response;
	}

	function tiposelecionados(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
	    $id = $request->getAttribute("id");
	    $data = Tipoturnosector::select("*")
                    ->where("idSector","=",$id)
                    ->get();
	    $response->getBody()->write($data);
	    return $response;
	}

	function posttipoturnosector(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        $id = $request->getAttribute("id");
        try{
            
            $tipo = Tipoturnosector::select("*")
                            ->where("idsector","=",$id)
                            ->delete();

            $sector = new Tipoturnosector;
            $sector->idsector  		=  $data['idsector'];
            $sector->idtipoturno 	=  $data['idtipoturno'];
            $sector->save();

            
            $respuesta = json_encode(array('msg' => "modificado correctamente", "std" => 1));
            $response = $response->withStatus(200);
        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }
        $response->getBody()->write($respuesta);
        return $response;
	}
	
}