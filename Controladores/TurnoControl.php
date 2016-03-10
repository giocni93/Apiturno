<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Illuminate\Database\Capsule\Manager as DB;

class TurnoControl{

  public function getTurnosEnColaByEmpleado(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $idEmpleado = $request->getAttribute("idEmpleado");
    $idServicio = $request->getAttribute("idServicio");
    $data = Turno::select("tipoturno.prioridad","turno.tipoTurno","turno.id as calificacionCliente","turno.id","cliente.nombres","cliente.apellidos","turno.turno","turno.fechaSolicitud","turno.estadoTurno","cliente.idPush","cliente.id as idCliente")
                    ->join("cliente","cliente.id","=","turno.idCliente")
                    ->join("tipoturno","tipoturno.id","=","turno.tipoTurno")
                    ->where("turno.idEmpleado","=",$idEmpleado)
                    ->where("turno.idServicio","=",$idServicio)
                    ->where(function ($query) {
                        $query->where("turno.estadoTurno","=","CONFIRMADO")
                              ->orwhere("turno.estadoTurno","=","ATENDIENDO");
                    })
                    ->where("turno.estado","=","ACTIVO")
                    ->orderBy('turno.turnoReal', 'asc')
                    ->get();
    if(count($data) == 0){
      $response = $response->withStatus(404);
    }else{
        for($i = 0; $i < count($data); $i++){
            $query = "SELECT COALESCE(AVG(calificacion),0) as promedio FROM calificacioncliente WHERE idCliente = ".$data[$i]->idCliente;
            $dataCliente = DB::select(DB::raw($query));
            $data[$i]->calificacionCliente = $dataCliente[0]->promedio;
        }
    }
    $response->getBody()->write($data);
    return $response;
  }

  public function getTurnosEnEsperaByEmpleado(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $idEmpleado = $request->getAttribute("idEmpleado");
    $idServicio = $request->getAttribute("idServicio");
    $data = Turno::select("turno.tipoTurno","turno.id as calificacionCliente","turno.id","cliente.nombres","cliente.apellidos","turno.turno","turno.fechaSolicitud","turno.estadoTurno","cliente.idPush","cliente.id as idCliente")
                    ->join("cliente","cliente.id","=","turno.idCliente")
                    ->where("turno.idEmpleado","=",$idEmpleado)
                    ->where("turno.idServicio","=",$idServicio)
                    ->where("turno.estadoTurno","=","SOLICITADO")
                    ->where("turno.estado","=","ACTIVO")
                    ->orderBy('turno.id', 'asc')
                    ->orderBy('turno.tiempo', 'asc')
                    ->get();
    if(count($data) == 0){
      $response = $response->withStatus(404);
    }else{
        for($i = 0; $i < count($data); $i++){
            $query = "SELECT COALESCE(AVG(calificacion),0) as promedio FROM calificacioncliente WHERE idCliente = ".$data[$i]->idCliente;
            $dataCliente = DB::select(DB::raw($query));
            $data[$i]->calificacionCliente = $dataCliente[0]->promedio;
        }
    }
    $response->getBody()->write($data);
    return $response;
  }

  public function cambiarEstadoTurno(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $data = json_decode($request->getBody(),true);
    $id = $request->getAttribute("id");
    $idEmpleado = $request->getAttribute("idEmpleado");
    $idServicio = $request->getAttribute("idServicio");
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
          $turnos = Turno::select("turno.*","cliente.idPush")
                    ->join("cliente","cliente.id","=","turno.idCliente")
                    ->where(function ($query) {
                          $query->where("turno.estadoTurno","=","CONFIRMADO")
                                ->orwhere("turno.estadoTurno","=","ATENDIENDO");
                      })
                    ->where("turno.idEmpleado","=",$idEmpleado)
                    ->where("turno.idServicio","=",$idServicio)   
                    ->where("turno.tipoTurno","<>",2)
                    ->orderBy('turno.fechaSolicitud', 'asc')
                    ->get();
          for($i = 0; $i < count($turnos); $i++){
              $query = "SELECT "
                        ."COALESCE((AVG(TIMESTAMPDIFF(SECOND,fechaInicio,fechaFinal)) * turnosFaltantes.faltantes),0) as tiempoEstimado "
                        ."FROM "
                        ."( "
                        ."  SELECT "
                        ."    count(t.id) as faltantes "
                        ."    FROM "
                        ."    turno as t "
                        ."    WHERE "
                        ."    t.idEmpleado = ".$idEmpleado." AND "
                        ."    t.idServicio = ".$idServicio." AND "
                        ."    t.fechaSolicitud <= '".$turnos[$i]->fechaSolicitud."' AND "
                        ."    t.estadoTurno <> 'TERMINADO' AND t.estadoTurno <> 'CANCELADO'"
                        .") as turnosFaltantes, "
                        ."turno "
                        ."WHERE "
                        ."idEmpleado = ".$idEmpleado." AND "
                        ."idServicio = ".$idServicio." AND "
                        ."idCliente = ".$turnos[$i]->idCliente." AND "
                        ."estadoTurno = 'TERMINADO' LIMIT 1";
                $dataTiempo = DB::select(DB::raw($query));
                if(count($dataTiempo) > 0){
                    //VARIFICAR SI YA PASO UN TIEMPO PARAMETRIZDO PARA AVISARLE
                    $tiempo = ($dataTiempo[0]->tiempoEstimado / 60);
                    if($tiempo < 5){
                        try {
                            $tu = Turno::select("*")
                                    ->where("id","=",$turnos[$i]->id)
                                    ->first();
                            $tu->avisado     =   1;
                            $tu->save();
                          } catch (Exception $err) {
                          }
                    }
                    //ANVIAR NOTIFICACION
                    $titulo = "Turno movil";
                    $msg = "Ya esta cerca tu turno, solo falta ".$tiempo." minutos";
                    $std = 0;
                    enviarNotificacion(array($turnos[$i]->idPush),$titulo, $msg, $std);
                    //array_push($vec, $turnos[$i]->idPush);
                }

                

          }

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

  public function postTurno(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $data = json_decode($request->getBody(),true);
    //VALIDAR SI TIENE UN TURNO EN EL SERVICIO DE LA SUCURSAL SOLICITADA
    $turnoCliente = Turno::select("idCliente")
                    ->where("idCliente","=",$data["idCliente"])
                    ->where("idServicio","=",$data["idServicio"])
                    ->where("idSucursal","=",$data["idSucursal"])
                    ->where("estadoTurno","<>","TERMINADO")
                    ->where("estadoTurno","<>","CANCELADO")
                    ->first();
    if($turnoCliente == null){
        //CALCULAR EL SIGUIENTE TURNO
        $turnoSiguiente = 1;
        $con_turno = Turno::select("turno")
                        ->where("idServicio","=",$data["idServicio"])
                        ->where("idSucursal","=",$data["idSucursal"])
                        ->where("estadoTurno","<>","TERMINADO")
                        ->where("estadoTurno","<>","CANCELADO")
                        ->orderBy('turno', 'desc')
                        ->first();
        if($con_turno != null){
          $turnoSiguiente = $con_turno['turno'] + 1;
        }

        //INSERTAR TURNO
        try{
            $turno = new Turno;
            $turno->idCliente   =   $data['idCliente'];
            $turno->idEmpleado  =   $data['idEmpleado'];
            $turno->idSucursal  =   $data['idSucursal'];
            $turno->idServicio  =   $data['idServicio'];
            $turno->tiempo      =   0; //$data['tiempo'];
            $turno->turno       =   $turnoSiguiente;
            $turno->turnoReal   =   $turnoSiguiente;
            $turno->tipoTurno   =   1;
            $turno->estadoTurno =   "SOLICITADO";
            $turno->estado      =   "ACTIVO";
            $turno->save();
            $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1, "numeroTurno" => $turnoSiguiente));
            $response = $response->withStatus(200);
            
            //ENVIAR NOTIFICACION AL EMPLEADO Y AL ADMINISTRADOR DE LA SUCURSAL
            $dataEmple = Empleado::select("idPush")
               ->where("id","=",$data['idEmpleado'])
               ->first();
            if($dataEmple != null){
                $payload = array(
                    'title'         => "Turno movil",
                    'msg'           => "Te han solicitado un turno",
                    'std'           => 1,
                    'idServicio'    => $data['idServicio']
                );
                enviarNotificacion(array($dataEmple->idPush),$payload);
            }

        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error al pedir el turno", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }

    }else{
      $respuesta = json_encode(array('msg' => "Ya tienes un turno activo en este servicio", "std" => 0));
      $response = $response->withStatus(404);
    }

    $response->getBody()->write($respuesta);
    return $response;
  }
  
  public function postTurnoAnonimo(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $data = json_decode($request->getBody(),true);
    //VALIDAR SI EL CLIENTE EXISTE
    $cli = Cliente::select("id")
            ->where("email","=",$data["email"])
            ->first();
    $idCliente = "";
    if($cli == null){
        try{
        $cliente = new Cliente;
        $cliente->email     =   $data['email'];
        $cliente->nombres   =   $data['nombres'];
        $cliente->apellidos =   $data['apellidos'];
        $cliente->telefono  =   "";
        $cliente->pass      =   sha1($data['email']);
        $cliente->estado    =   "ACTIVO";
        $cliente->save();
        $idCliente = $cliente->id;
    }catch(Exception $err){
    }
    }else{
        $idCliente = $cli->id;
    }
    
    //VALIDAR SI TIENE UN TURNO EN EL SERVICIO DE LA SUCURSAL SOLICITADA
    $turnoCliente = Turno::select("idCliente")
                    ->where("idCliente","=",$idCliente)
                    ->where("idServicio","=",$data["idServicio"])
                    ->where("idEmpleado","=",$data["idEmpleado"])
                    ->where("idSucursal","=",$data["idSucursal"])
                    ->where("estadoTurno","<>","TERMINADO")
                    ->where("estadoTurno","<>","CANCELADO")
                    ->first();
    if($turnoCliente == null){
        //CALCULAR EL SIGUIENTE TURNO
        $turnoSiguiente = 0;
        $turnoReal = 0;
        $lista = Turno::select("*")
                        ->where("idServicio","=",$data["idServicio"])
                        ->where("idEmpleado","=",$data["idEmpleado"])
                        ->where("idSucursal","=",$data["idSucursal"])
                        ->where("estadoTurno","<>","TERMINADO")
                        ->where("estadoTurno","<>","CANCELADO")
                        ->orderBy('turno', 'desc')
                        ->get();
                        //echo json_encode($lista);
        if(count($lista) > 0){
          $turnoSiguiente = $lista[0]->turno;
        }
        
        $turnoSiguiente ++;
        $turnoReal = $turnoSiguiente;
        
        //VERIFICO QUE TIPO DE TURNO ES
        if($data['tipoTurno'] == 2){
            //SI ES VIP
            $ban = false;
            $cont = 0;
            $ind = 0;
            //echo "LISTA: ".count($lista);
            for($i = count($lista) - 1; $i >= 0; $i--){
            
            //CALCULAR SI EL CLIENTE YA ESTA A PUNTO DE ATENDER
            $query = "SELECT "
                        ."COALESCE((AVG(TIMESTAMPDIFF(SECOND,fechaInicio,fechaFinal)) * turnosFaltantes.faltantes),0) as tiempoEstimado "
                        ."FROM "
                        ."( "
                        ."  SELECT "
                        ."    count(t.id) as faltantes "
                        ."    FROM "
                        ."    turno as t "
                        ."    WHERE "
                        ."    t.idEmpleado = ".$data["idEmpleado"]." AND "
                        ."    t.idServicio = ".$data["idServicio"]." AND "
                        ."    t.fechaSolicitud <= '".$lista[$i]->fechaSolicitud."' AND "
                        ."    t.estadoTurno <> 'TERMINADO' AND t.estadoTurno <> 'CANCELADO'"
                        .") as turnosFaltantes, "
                        ."turno "
                        ."WHERE "
                        ."idEmpleado = ".$data["idEmpleado"]." AND "
                        ."idServicio = ".$data["idServicio"]." AND "
                        ."idCliente = ".$lista[$i]->idCliente." AND "
                        ."estadoTurno = 'TERMINADO' LIMIT 1";
                $dataTiempo = DB::select(DB::raw($query));
                if(count($dataTiempo) > 0){
                    //VARIFICAR SI YA PASO UN TIEMPO PARAMETRIZDO PARA AVISARLE
                    $tiempo = ($dataTiempo[0]->tiempoEstimado / 60);
                    if($tiempo < 5){
                        try {
                            $tu = Turno::select("*")
                                    ->where("id","=",$lista[$i]->id)
                                    ->first();
                            $tu->avisado     =   1;
                            $tu->save();
                            $lista[$i]->avisado = 1;
                          } catch (Exception $err) {
                          }
                    }

                }
            
            
                $banCont = true;
                //echo $lista[$i]->avisado;
                if($lista[$i]->estadoTurno != "ATENDIENDO"){
                	if($lista[$i]->avisado == 0 && $ban == false){
	                    //VALIDAR SI EL ANTERIOR ES VIP
	                    $inew = $i + 1;
	                    $contVIP = 0;
	                    while($contVIP < 4){
	                        if($inew < count($lista)){
	                            if($lista[$inew]->tipoTurno == 2){
	                                $banCont = false;
	                                break;
	                            }
	                        }
	                        $inew ++;
	                        $contVIP ++;
	                    }
	                    $inew = $i;
	                    if($banCont){
	                        $ban = true;
	                        $turnoReal = $lista[$inew]->turno;
	                        $ind = $inew;
	                        break;
	                    }
	                }
                
                }
            }
            if($ban){
                //ACTUALIZAR TURNOS REALES
                for($j = $ind; $j >= 0; $j--){
                    $cont = $lista[$j]->turno + 1;
                    $turUp = Turno::select("*")
                                    ->where("id","=",$lista[$j]->id)
                                    ->first();
                    $turUp->turnoReal = $cont;
                    $turUp->save();
                }
            }
        }
        
        //INSERTAR TURNO
        try{
            $turno = new Turno;
            $turno->idCliente   =   $idCliente;
            $turno->idEmpleado  =   $data['idEmpleado'];
            $turno->idSucursal  =   $data['idSucursal'];
            $turno->idServicio  =   $data['idServicio'];
            $turno->tiempo      =   0; //$data['tiempo'];
            $turno->turno       =   $turnoSiguiente;
            $turno->turnoReal   =   $turnoReal;
            $turno->tipoTurno   =   $data['tipoTurno'];
            $turno->avisado     =   1;
            $turno->estadoTurno =   "CONFIRMADO";
            $turno->estado      =   "ACTIVO";
            $turno->save();
            $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1, "numeroTurno" => $turnoSiguiente));
            $response = $response->withStatus(200);
            
            //ENVIAR NOTIFICACION AL EMPLEADO Y AL ADMINISTRADOR DE LA SUCURSAL

        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error al pedir el turno", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }

    }else{
      $respuesta = json_encode(array('msg' => "Ya tienes un turno activo en este servicio", "std" => 0));
      $response = $response->withStatus(404);
    }

    $response->getBody()->write($respuesta);
    return $response;
  }

  function turnosxestado(Request $request, Response $response){

      $response = $response->withHeader('Content-type', 'application/json');
      $id = $request->getAttribute("id");
      $fechainicial = $request->getAttribute("fechainicial");
      $fechafinal = $request->getAttribute("fechafinal");
      $date = date("Y-m-d");
      $data = DB::select(DB::raw("Select id,COUNT(*) as contador,estadoTurno,idSucursal  from turno  where   
        turno.idSucursal = ".$id." and (estadoTurno='SOLICITADO' or estadoTurno='CANCELADO' or estadoTurno='CONFIRMADO' or estadoTurno='TERMINADO') and fechaSolicitud BETWEEN '".$fechainicial."' and '".$fechafinal."' GROUP BY estadoTurno "));
          
          foreach ($data as $row) {
            $vec2 = $this->categoria(utf8_encode($row->estadoTurno));
            $vec[] = array(
              "id" => $row->id,
              "contador" => $row->contador,
              "estadoTurno" => $row->estadoTurno,
              "color" =>  $vec2["color"] 
            );
          }

      $response->getBody()->write(json_encode($vec));
      return $response;

  }

  function turnoxservicio(Request $request, Response $response){
      $response = $response->withHeader('Content-type', 'application/json');
      $id = $request->getAttribute("id");
      $fechainicial = $request->getAttribute("fechainicial");
      $fechafinal = $request->getAttribute("fechafinal");
      $data = DB::select(DB::raw("Select turno.id,turno.estadoTurno,turno.idSucursal,COUNT(*) as contador,turno.idServicio,servicio.nombre,sucursal.nombre as sucursal from turno inner join servicio on servicio.id = turno.idServicio inner join sucursal on sucursal.id = turno.idSucursal where   
        turno.idSucursal = ".$id." and estadoTurno='TERMINADO' and fechaSolicitud BETWEEN '".$fechainicial."' and '".$fechafinal."' GROUP BY idServicio "));

      $response->getBody()->write(json_encode($data));
      return $response;
  }

  function categoria($categoria){
    $vec = array("color"=>"#ffffff");
    if($categoria == 'SOLICITADO'){
      $vec["color"] = "#1ab394";
      return $vec;
    }
    if($categoria == 'CANCELADO'){
      $vec["color"] = "#EF5350";
      return $vec;
    }
    if($categoria == 'CONFIRMADO'){
      $vec["color"] = "#BABABA";
      return $vec;
    }
    if($categoria == 'TERMINADO'){
      $vec["color"] = "#AA00FF";
      return $vec;
    }
    return $vec;
  }

  function turnosempresa(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $idempresa = $request->getAttribute("id");
    $fechainicial = $request->getAttribute("fechainicial");
    $fechafinal = $request->getAttribute("fechafinal");
    $servi = Sucursal::select('sucursal.nombre','sucursal.id')
                              ->where('sucursal.idEmpresa','=',$idempresa)
                              ->get();
        for($i=0;$i<count($servi);$i++){
          $data = DB::select(DB::raw("Select turno.id,COUNT(*) as contador,turno.estadoTurno,turno.idSucursal,sucursal.nombre  from turno inner join sucursal on sucursal.id = turno.idSucursal where   
        turno.idSucursal = ".$servi[$i]->id." and  estadoTurno='TERMINADO' and fechaSolicitud BETWEEN '".$fechainicial."' and '".$fechafinal."' GROUP BY estadoTurno"));
          $servi[$i]['turno'] = $data;
        }                


    $response->getBody()->write(json_encode($servi));
      return $response;
  }

  function turnosempresaxservicios(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $idempresa = $request->getAttribute("id");
    $fechainicial = $request->getAttribute("fechainicial");
    $fechafinal = $request->getAttribute("fechafinal");
    $servi = Sucursal::select('sucursal.nombre','sucursal.id')
                              ->where('sucursal.idEmpresa','=',$idempresa)
                              ->get();
        for($i=0;$i<count($servi);$i++){
          $data = DB::select(DB::raw("Select turno.id,COUNT(*) as contador,turno.estadoTurno,turno.idSucursal,sucursal.nombre,turno.idServicio as servicio,servicio.nombre as nombreservicio  from turno inner join sucursal on sucursal.id = turno.idSucursal inner join servicio on servicio.id = turno.idServicio where   
        turno.idSucursal = ".$servi[$i]->id." and  estadoTurno='TERMINADO' and fechaSolicitud BETWEEN '".$fechainicial."' and '".$fechafinal."' GROUP BY turno.idservicio, turno.idSucursal"));
          $servi[$i]['turno'] = $data;
        }                


    $response->getBody()->write(json_encode($servi));
    return $response;
  }

  function getsucursalid(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $id = $request->getAttribute("id");
    $sucu = Sucursal::find($id);
    $response->getBody()->write($sucu);
    return $response;
  }

  public function getTurnosCliente(Request $request, Response $response)
  {
    # 
      $idCliente= $request->getAttribute("idCliente");
      $query = "SELECT tu.*, su.nombre as sucursal, em.razonSocial as empresa, concat(emp.nombres, ' ', emp.apellidos) as empleado, '' as turnoActual FROM turno tu INNER JOIN sucursal su on su.id = tu.idSucursal INNER JOIN empresa em on em.id = su.idEmpresa INNER JOIN empleado emp on emp.id = tu.idEmpleado where idCliente = '$idCliente' and CAST(fechaSolicitud AS DATE) = curdate() and (estadoturno = 'SOLICITADO' OR estadoturno = 'ATENDIENDO' OR estadoturno = 'CONFIRMADO')";
      $data = DB::select(DB::raw($query));
      for($i = 0; $i < count($data); $i++){
        $query = "SELECT "
                ."COALESCE(turnoAct.turnoActual,0) as turnoActual "
                ."FROM "
                ."(SELECT tu.turno as turnoActual FROM turno as tu WHERE tu.idEmpleado = ".$data[$i]->idEmpleado." AND (tu.estadoTurno = 'ATENDIENDO' OR tu.estadoTurno = 'CONFIRMADO') ORDER BY tu.turnoReal ASC LIMIT 1) as turnoAct,"
                ."turno "
                ."WHERE "
                ."idEmpleado = ".$data[$i]->idEmpleado." AND "
                ."idServicio = ".$data[$i]->idServicio;
        $dataTiempo = DB::select(DB::raw($query));
        $data[$i]->turnoActual = $dataTiempo[0]->turnoActual;
      }
      $response->getBody()->write(json_encode($data));
      return $response;
  }

  function empleadomasturnos(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $id = $request->getAttribute("id");
        $fechainicial = $request->getAttribute("fechainicial");
        $fechafinal = $request->getAttribute("fechafinal");
        $data = DB::select(DB::raw("Select idEmpleado,COUNT(*) as contador,estadoTurno,idSucursal  from turno where   
                turno.idEmpleado = ".$id." and estadoTurno='TERMINADO' and fechaSolicitud BETWEEN '".$fechainicial."' "
                . "and '".$fechafinal."' GROUP BY idEmpleado "));
          
      $response->getBody()->write(json_encode($data));
      return $response;
  }

}