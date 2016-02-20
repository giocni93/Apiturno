<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Illuminate\Database\Capsule\Manager as DB;

class EmpleadoControl{
  function getAll(Request $request, Response $response) {
    $response = $response->withHeader('Content-type', 'application/json');
    $data = Empleado::all();
    if(count($data) == 0){
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($data);
    return $response;
  }

  function getById(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $id = $request->getAttribute("id");
    $data = Empleado::select("*")
                    ->where("id","=",$id)
                    ->get();
    $response->getBody()->write($data);
    return $response;
  }

  function post(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $data = json_decode($request->getBody(),true);

    try{
        $empleado = new Empleado;
        $empleado->idSucursal       =   $data['idSucursal'];
        $empleado->identificacion   =   $data['identificacion'];
        $empleado->email            =   $data['email'];
        $empleado->nombres          =   $data['nombres'];
        $empleado->apellidos        =   $data['apellidos'];
        $empleado->telefono         =   $data['telefono'];
        $empleado->pass             =   sha1($data['pass']);
        $empleado->estado           =   "ACTIVO";
        $empleado->estadoOnline     =   "INACTIVO";
        $empleado->save();
        $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
        $response = $response->withStatus(200);
    }catch(Exception $err){
        $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
        $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;
  }

  public function delete(Request $request, Response $response)
  {
    try {
      $response = $response->withHeader('Content-type', 'application/json');
      $id = $request->getAttribute("id");
      $empleado = Empleado::select("*")
                      ->where("id","=",$id)
                      ->get();
      $empleado->estado = "INACTIVO";
      $empleado->save();
      $respuesta = json_encode(array('msg' => "Eliminado correctamente", "std" => 1));
      $response = $response->withStatus(200);
    } catch (Exception $err) {
      $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;
  }

  public function update(Request $request, Response $response)
  {
    try {
      $response = $response->withHeader('Content-type', 'application/json');
      $data = json_decode($request->getBody(),true);
      $id = $request->getAttribute("id");
      $empleado = Empleado::select("*")
                          ->where("id","=",$id)
                          ->first();
      $empleado->identificacion   =   $data['identificacion'];
      $empleado->email            =   $data['email'];
      $empleado->nombres          =   $data['nombres'];
      $empleado->apellidos        =   $data['apellidos'];
      $empleado->telefono         =   $data['telefono'];
      $empleado->save();
      $respuesta = json_encode(array('msg' => "Modificado correctamente", "std" => 1));
      $response = $response->withStatus(200);
    } catch (Exception $err) {
      $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;
  }

  function sesion(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $data = json_decode($request->getBody(),true);
    $data = Empleado::select("id","idSucursal","nombres","apellidos","email","telefono","identificacion")
                    ->where("pass","=",sha1($data['pass']))
                    ->where("identificacion","=",$data['identificacion'])
                    ->first();
    $respuesta = json_encode(array('empleado' => $data, "std" => 1));
    if($data == null){
      $respuesta = json_encode(array('empleado' => null, "std" => 0));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;
  }

  function updatePush(Request $request, Response $response){
    try {
      $response = $response->withHeader('Content-type', 'application/json');
      $data = json_decode($request->getBody(),true);
      $id = $request->getAttribute("id");
      $empleado = Empleado::select("*")
                          ->where("id","=",$id)
                          ->first();
      $empleado->idPush   =   $data['idpush'];
      $empleado->save();
      $respuesta = json_encode(array('msg' => "idpus registrado correctamente", "std" => 1));
      $response = $response->withStatus(200);
    } catch (Exception $err) {
      $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;
  }

  function getEmpleadosBySucursal(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $idSucursal = $request->getAttribute("idSucursal");
    $query = "SELECT "
              . "emp.id as idEmpleado,"
              . "ser.id as idServicio,"
              . "CONCAT(emp.nombres, ' ', emp.apellidos) AS empleado,"
              . "ser.nombre as servicio, "
              . "'' as tiempoEstimado, "
              . "'' as turnoActual "
              . "FROM empleado emp "
              . "INNER JOIN "
              . "servicio ser ON(ser.id = emp.idServicio) "
              . "WHERE emp.idSucursal = $idSucursal AND emp.estadoOnline = 'ACTIVO'";
    $data = DB::select(DB::raw($query));
    for($i = 0; $i < count($data); $i++){
      //CALCULAR TIEMPO
      $query = "SELECT "
                ."TIME_FORMAT(SEC_TO_TIME((AVG(TIMESTAMPDIFF(SECOND,fechaInicio,fechaFinal)) * turnosFaltantes.faltantes)),'%H:%i:%s') as tiempoEstimado, "
                ."COALESCE(turnoAct.turnoActual,0) as turnoActual "
                ."FROM "
                ."( "
                ."	SELECT "
                ."    count(t.id) as faltantes "
                ."    FROM "
                ."    turno as t "
                ."    WHERE "
                ."    t.idEmpleado = ".$data[$i]->idEmpleado." AND "
                ."    t.idServicio = ".$data[$i]->idServicio." AND "
                ."    t.estadoTurno <> 'TERMINADO' AND t.estadoTurno <> 'CANCELADO'"
                .") as turnosFaltantes, "
                ."(SELECT MAX(tu.turno) as turnoActual FROM turno as tu WHERE tu.idEmpleado = ".$data[$i]->idEmpleado." AND tu.estadoTurno = 'ATENDIENDO') as turnoAct,"
                ."turno "
                ."WHERE "
                ."idEmpleado = ".$data[$i]->idEmpleado." AND "
                ."idServicio = ".$data[$i]->idServicio." AND "
                ."estadoTurno = 'TERMINADO' LIMIT 1";
        $dataTiempo = DB::select(DB::raw($query));
        $data[$i]->tiempoEstimado = $dataTiempo[0]->tiempoEstimado;
        if($data[$i]->tiempoEstimado == null){
          $data[$i]->tiempoEstimado = "00:00:00";
        }
        $data[$i]->turnoActual = $dataTiempo[0]->turnoActual;
    }
    $response->getBody()->write(json_encode($data));
    return $response;
  }

}
