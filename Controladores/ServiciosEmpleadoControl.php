<?php
use Slim\Http\Request;
use Slim\Http\Response;

class ServiciosEmpleadoControl{

	function guardarserviciosempleado(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        try{
            $servicio = new ServiciosEmpleado;
            $servicio->idEmpleado   =  $data['idEmpleado'];
            $servicio->idServicio 	=  $data['idServicio'];
            $servicio->save();
            $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
            $response = $response->withStatus(200);
        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }
        $response->getBody()->write($respuesta);
        return $response;
	}

    function servicioxempleado(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $id = $request->getAttribute("id");
        $data = ServiciosEmpleado::select("*")
                        ->where("idEmpleado","=",$id)
                        ->get();
        $response->getBody()->write($data);
        return $response;
    }

}
