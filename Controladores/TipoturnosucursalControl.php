<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Illuminate\Database\Capsule\Manager as DB;

class TipoturnosucursalControl{

	function gettipoturnosector(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
	    $data = Tipoturnosucursal::all();
	    if(count($data) == 0){
	      $response = $response->withStatus(404);
	    }
	    $response->getBody()->write($data);
	    return $response;
	}

	function tiposelecionados(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
	    $id = $request->getAttribute("id");
	    $data = Tipoturnosucursal::select("*")
                    ->where("idsucursal","=",$id)
                    ->get();
	    $response->getBody()->write($data);
	    return $response;
	}

	function posttipoturno(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        try{
            
            
            $sector = new Tipoturnosucursal;
            $sector->idsucursal  	=  $data['idsucursal'];
            $sector->idtipoturno 	=  $data['idtipoturno'];
            $sector->save();

            
            $respuesta = json_encode(array('msg' => "guardado correctamente", "std" => 1));
            $response = $response->withStatus(200);
        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }
        $response->getBody()->write($respuesta);
        return $response;
	}	

	function posttipoturnosucursal(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        $id = $request->getAttribute("id");
        try{
            
            $tipo = Tipoturnosucursal::select("*")
                            ->where("idsucursal","=",$id)
                            ->delete();

            $sector = new Tipoturnosucursal;
            $sector->idsucursal  	=  $data['idsucursal'];
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