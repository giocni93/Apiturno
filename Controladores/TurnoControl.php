<?php
use Slim\Http\Request;
use Slim\Http\Response;

class TurnoControl{

  public function getTurnosEnColaByEmpleado(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $idEmpleado = $request->getAttribute("idEmpleado");
    $data = Turno::select("turno.id","cliente.nombres","cliente.apellidos","turno.turno","turno.fechaSolicitud","turno.estadoTurno","cliente.idPush")
                    ->join("cliente","cliente.id","=","turno.idCliente")
                    ->where("turno.idEmpleado","=",$idEmpleado)
                    ->where("turno.estadoTurno","=","CONFIRMADO")
                    ->orwhere("turno.estadoTurno","=","ATENDIENDO")
                    ->where("turno.estado","=","ACTIVO")
                    ->whereRaw('ABS(TIMESTAMPDIFF(MINUTE,turno.fechaSolicitud,NOW())) >= turno.tiempo', [])
                    ->orderBy('turno.id', 'asc')
                    ->orderBy('turno.tiempo', 'asc')
                    ->get();
    if(count($data) == 0){
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($data);
    return $response;
  }

  public function getTurnosEnEsperaByEmpleado(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $idEmpleado = $request->getAttribute("idEmpleado");
    $data = Turno::select("turno.id","cliente.nombres","cliente.apellidos","turno.turno","turno.fechaSolicitud","turno.estadoTurno","cliente.idPush")
                    ->join("cliente","cliente.id","=","turno.idCliente")
                    ->where("turno.idEmpleado","=",$idEmpleado)
                    ->where("turno.estadoTurno","=","SOLICITADO")
                    ->where("turno.estado","=","ACTIVO")
                    ->orderBy('turno.id', 'asc')
                    ->orderBy('turno.tiempo', 'asc')
                    ->get();
    if(count($data) == 0){
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($data);
    return $response;
  }

  public function cambiarEstadoTurno(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $data = json_decode($request->getBody(),true);
    $id = $request->getAttribute("id");
    $banTerminado = false;

    try {
      $turno = Turno::select("*")
                      ->where("id","=",$id)
                      ->first();
      $turno->estadoTurno   =   $data['estadoTurno'];
      if($turno->estadoTurno == "TERMINADO"){
        $turno->fechaFinal = fechaHoraActual();
        $banTerminado = true;
      }
      if($turno->estadoTurno == "ATENDIENDO"){
        $turno->fechaInicio = fechaHoraActual();
      }
      $turno->save();
      if($banTerminado){
        //ENVIAR NOTIFICACIONES A LOS CLIENTES

      }
      $respuesta = json_encode(array('msg' => "Modificado correctamente", "std" => 1));
      $response = $response->withStatus(200);
    } catch (Exception $err) {
      $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;
  }

}
