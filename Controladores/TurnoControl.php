<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Illuminate\Database\Capsule\Manager as DB;

class TurnoControl{

  public function getTurnosEnColaByEmpleado(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $idEmpleado = $request->getAttribute("idEmpleado");
    $idServicio = $request->getAttribute("idServicio");
    $data = Turno::select("turno.aplazado","tipoturno.prioridad","turno.tipoTurno","turno.id as calificacionCliente","turno.id","cliente.nombres","cliente.apellidos","turno.turno","turno.fechaSolicitud","turno.idServicio","turno.idEmpleado","turno.estadoTurno","cliente.idPush","cliente.id as idCliente")
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

  public function aplazarTurno(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $idTurno = $request->getAttribute("idTurno");
    $idEmpleado = $request->getAttribute("idEmpleado");
    $idServicio = $request->getAttribute("idServicio");
    $data = Turno::select("*")
                    ->where("turno.idServicio","=",$idServicio)
                    ->where("turno.idEmpleado","=",$idEmpleado)
                    ->where("turno.id","<>",$idTurno)
                    ->where("turno.avisado","=",1)
                    ->where("turno.aplazado","=",0)
                    ->where("turno.estadoTurno","=","CONFIRMADO")
                    ->where("turno.estado","=","ACTIVO")
                    ->orderBy('turno.turnoReal', 'asc')
                    ->get();
    if(count($data) > 0){
      //LE ACTUALISO EL NUEVO TURNO A LA PERSONA QUE SE LE APLAZO EL TURNO
      $nuevoTurnoReal = 0;
      $tu = Turno::select("*")
                    ->where("id","=",$idTurno)
                    ->first();
      $nuevoTurnoReal = $tu->turnoReal;
      $tu->turnoReal = $data[0]->turnoReal;
      $tu->aplazado = 1;
      $tu->save();

    //COLOCO EL NUEVO TURNO A LA PERSONA QUE ESTE EN EL SITIO Y NO HA SIDO AVISADA
      $tu = Turno::select("*")
                    ->where("id","=",$data[0]->id)
                    ->first();
      $tu->turnoReal = $nuevoTurnoReal;
      $tu->save();

      //ENVIAR NOTIFICACION PARA AVISAR QUE SU TURNO HA SIDO APLAZADO

    }else{
      $tu = Turno::select("*")
                    ->where("id","=",$idTurno)
                    ->first();
      $tu->aplazado = 1;
      $tu->save();
    }
    $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
    $response->getBody()->write($respuesta);
    return $response;
  }

  public function aplazarCancelarTurno(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $idTurno = $request->getAttribute("idTurno");
    $idEmpleado = $request->getAttribute("idEmpleado");
    $idServicio = $request->getAttribute("idServicio");
    $data = Turno::select("*")
                    ->where("turno.idServicio","=",$idServicio)
                    ->where("turno.idEmpleado","=",$idEmpleado)
                    ->where("turno.avisado","=",1)
                    ->where("turno.aplazado","=",0)
                    ->where("turno.estadoTurno","=","CONFIRMADO")
                    ->where("turno.estado","=","ACTIVO")
                    ->orderBy('turno.turnoReal', 'asc')
                    ->get();
    if(count($data) > 0){
      //LE ACTUALISO EL NUEVO TURNO A LA PERSONA QUE SE LE APLAZO EL TURNO
      $nuevoTurnoReal = 0;
      $tu = Turno::select("*")
                    ->where("id","=",$idTurno)
                    ->first();
      $nuevoTurnoReal = $tu->turnoReal;
      $tu->turnoReal = $data[0]->turnoReal;
      $tu->estadoTurno = "CANCELADO";
      $tu->aplazado = 1;
      $tu->save();

    //COLOCO EL NUEVO TURNO A LA PERSONA QUE ESTE EN EL SITIO Y NO HA SIDO AVISADA
      $tu = Turno::select("*")
                    ->where("id","=",$data[0]->id)
                    ->first();
      $tu->turnoReal = $nuevoTurnoReal;
      $tu->save();

      //ENVIAR NOTIFICACION PARA AVISAR QUE SU TURNO HA SIDO CANCELADO

    }else{
      $tu = Turno::select("*")
                    ->where("id","=",$idTurno)
                    ->first();
      $tu->aplazado = 1;
      $tu->estadoTurno = "CANCELADO";
      $tu->save();
    }
    $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
    $response->getBody()->write($respuesta);
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
      $turno = Turno::select("turno.*","cliente.idPush","servicio.nombre as servicio","empleado.nombres as empleado1","empleado.apellidos as empleado2")
                    ->join("cliente","cliente.id","=","turno.idCliente")
                    ->join("servicio","servicio.id","=","turno.idServicio")
                    ->join("empleado","empleado.id","=","turno.idEmpleado")
                      ->where("turno.id","=",$id)
                      ->first();
      $turno->estadoTurno   =   $data['estadoTurno'];
      if($turno->estadoTurno == "TERMINADO"){
        $turno->fechaFinal = fechaHoraActual();
        $banTerminado = true;
      }
      if($turno->estadoTurno == "ATENDIENDO"){
        $turno->fechaInicio = fechaHoraActual();
      }
      if($turno->estadoTurno == "CONFIRMADO"){
        $payload = array(
            'title'         => "Turno movil",
            'msg'           => "Tu turno ha sido aceptado.",
            'std'           => 0,
            'idServicio'    => "0"
        );
        $notification = array(
            'body' => "Tu turno ha sido aceptado.",
            'title' => "Aceptación de turno."
        );
        enviarNotificacion(array($turno->idPush),$payload);
        enviarNotificacionIos($turno->idPush,$notification);
      }
      if($turno->estadoTurno == "CANCELADO"){
        $payload = array(
            'title'         => "Turno movil",
            'msg'           => "Tu turno ha sido aceptado.",
            'std'           => 0,
            'idServicio'    => "0"
        );
        $notification = array(
            'body' => "Tu turno no ha sido aceptado.",
            'title' => "Cancelación de turno."
        );
        enviarNotificacion(array($turno->idPush),$payload);
        enviarNotificacionIos($turno->idPush,$notification);
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
                        ."(COALESCE(AVG(TIMESTAMPDIFF(SECOND,fechaInicio,fechaFinal)),0) * turnosFaltantes.faltantes) as tiempoEstimado "
                        ."FROM "
                        ."( "
                        ."  SELECT "
                        ."    count(t.id) as faltantes "
                        ."    FROM "
                        ."    turno as t "
                        ."    WHERE "
                        ."    t.idEmpleado = ".$idEmpleado." AND "
                        ."    t.idServicio = ".$idServicio." AND "
                        ."    t.fechaSolicitud < '".$turnos[$i]->fechaSolicitud."' AND "
                        ."    t.estadoTurno <> 'TERMINADO' AND t.estadoTurno <> 'CANCELADO'"
                        .") as turnosFaltantes, "
                        ."turno "
                        ."WHERE "
                        ."idEmpleado = ".$idEmpleado." AND "
                        ."idServicio = ".$idServicio." AND "
                        //."idCliente = ".$turnos[$i]->idCliente." AND "
                        ."estadoTurno = 'TERMINADO' LIMIT 1";
                $dataTiempo = DB::select(DB::raw($query));
                if(count($dataTiempo) > 0){
                    //VARIFICAR SI YA PASO UN TIEMPO PARAMETRIZDO PARA AVISARLE
                    $tiempo = ceil(($dataTiempo[0]->tiempoEstimado / 60));
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
                    $payload = array(
                        'title'         => "Turno movil",
                        'msg'           => "Ya esta cerca tu turno, solo falta ".$tiempo." minutos",
                        'std'           => 0,
                        'idServicio'    => "0"
                    );
                     $notification = array(
                          'body' => "Ya esta cerca tu turno, solo falta ".$tiempo." minutos",
                          'title' => "Informacion de Turno."
                      );
                    enviarNotificacion(array($turnos[$i]->idPush),$payload);
                    enviarNotificacionIos($turnos[$i]->idPush,$notification);
                    //array_push($vec, $turnos[$i]->idPush);
                }
          }
          //BUSCAR PRECIO DEL SERVICIO
          $valorServicio = ServiciosSucursal::select("*")
                  ->where("idServicio","=",$turno->idServicio)
                  ->where("idSucursal","=",$turno->idSucursal)
                  ->first();
          $valor = $valorServicio->precio;
          if($turno->tipoTurno == 2){
              $valor = $valorServicio->precioVIP;
          }
          if($valor != null && $valor != 0){
              //INSERTAR EN INGRESOS
            try{
              $ingreso = new Ingreso();  
              $ingreso->idServicio    =   $turno->idServicio;
              $ingreso->idEmpleado    =   $turno->idEmpleado;
              $ingreso->valor         =   $valor;
              $ingreso->save();
            }catch(Exception $ex){

            }
          }
          
          
          //ENVIAR NOTIFICACION PARA QUE EL CLIENTE CALIFIQUE EL SERVICIO
          $payload = array(
                'title'         => "Turno movil",
                'msg'           => "Califica el servicio",
                'std'           => 10,
                'idServicio'    => $turno->idServicio,
                'idSucursal'    => $turno->idSucursal,
                'idEmpleado'    => $turno->idEmpleado,
                'empleado'    => $turno->empleado1." ".$turno->empleado2,
                'servicio'    => $turno->servicio
            );
            enviarNotificacion(array($turno->idPush),$payload);
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

  function notificacionesIOS()
  {
    # code...
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

         for ($i=0; $i < $data["numeroTurnos"]; $i++) {
          try{
              $turno = new Turno;
              $turno->idCliente   =   $data['idCliente'];
              $turno->idEmpleado  =   $data['idEmpleado'];
              $turno->idSucursal  =   $data['idSucursal'];
              $turno->idServicio  =   $data['idServicio'];
              $turno->tiempo      =   0; //$data['tiempo'];
              $turno->turno       =   $turnoSiguiente + $i;
              $turno->turnoReal   =   $turnoSiguiente + $i;
              $turno->tipoTurno   =   1;
              $turno->estadoTurno =   "SOLICITADO";
              $turno->estado      =   "ACTIVO";
              $turno->save();
              $respuesta = json_encode(array('msg' => "Su turno ha sido asignado satisfactoriamente.", "std" => 1, "numeroTurno" => $turnoSiguiente, 'idTurno' => $turno->id));
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
  }

    }else{
      $respuesta = json_encode(array('msg' => "Ya tienes un turno activo en este servicio", "std" => 0));
      $response = $response->withStatus(404);
    }

    $response->getBody()->write($respuesta);
    return $response;
  }

  public function postReserva(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $data = json_decode($request->getBody(),true);
    $minutosServicio = ServiciosSucursal::select("minutos")
                    ->where("idServicio","=",$data["idServicio"])
                    ->where("idSucursal","=",$data["idSucursal"])
                    ->first();
    try{
        $turno = new Turno;
        $turno->idCliente   =   $data['idCliente'];
        $turno->idEmpleado  =   $data['idEmpleado'];
        $turno->idSucursal  =   $data['idSucursal'];
        $turno->idServicio  =   $data['idServicio'];
        $turno->tiempo      =   0;
        $turno->turno       =   0;
        $turno->turnoReal   =   0;
        $turno->tipoTurno   =   1;
        $turno->estadoTurno =   "SOLICITADO";
        $turno->estado      =   "ACTIVO";
        $turno->reserva     =   "A";
        $turno->fechaReserva = $data['fechaReserva'];
        $turno->horaReserva = $data['horaReserva'];
        $horaInicial = $data['horaReserva'];
        for ($i=0; $i < $data["cupos"] ; $i++) { 
          $segundos_horaInicial=strtotime($horaInicial);
          $segundos_minutoAnadir=$minutosServicio->minutos*60;
          $nuevaHora=date("H:i",$segundos_horaInicial+$segundos_minutoAnadir);
          $horaInicial = $nuevaHora;
        }        
        $turno->horaFinalReserva = $nuevaHora;
        $turno->save();
        $respuesta = json_encode(array('msg' => "Su turno ha sido asignado satisfactoriamente.", "std" => 1, 'idTurno' => $turno->id));
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
    $response->getBody()->write($respuesta);
    return $response;
    //echo $minutosServicio;
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
            $ban = true;
            $cont = 0;
            $ind = 0;
            $lista = Turno::select("*")
                        ->where("idServicio","=",$data["idServicio"])
                        ->where("idEmpleado","=",$data["idEmpleado"])
                        ->where("idSucursal","=",$data["idSucursal"])
                        ->where("estadoTurno","<>","TERMINADO")
                        ->where("estadoTurno","<>","CANCELADO")
                        ->orderBy('turnoReal', 'asc')
                        ->get();
            for($i = 0; $i < count($lista); $i++){
                if($lista[$i]->estadoTurno != "ATENDIENDO"){
                	if($lista[$i]->tipoTurno == 1){


                		$turUp = Turno::select("*")
		                                    ->where("id","=",$lista[$i]->id)
		                                    ->first();
		                    if($ban){
                			$turnoReal = $turUp->turnoReal;
                			$ban = false;
                		}
		                    $turUp->turnoReal = $lista[$i]->turnoReal + 1;
		                    $turUp->save();
                	}
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
  
  public function postReservaAnonimo(Request $request, Response $response){
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

        //INSERTAR TURNO
        try{
            $turno = new Turno;
            $turno->idCliente   =   $idCliente;
            $turno->idEmpleado  =   $data['idEmpleado'];
            $turno->idSucursal  =   $data['idSucursal'];
            $turno->idServicio  =   $data['idServicio'];
            $turno->tiempo      =   0;
            $turno->turno       =   0;
            $turno->turnoReal   =   0;
            $turno->tipoTurno   =   1;
            $turno->estadoTurno =   "SOLICITADO";
            $turno->estado      =   "ACTIVO";
            $turno->reserva     =   "A";
            $turno->fechaReserva = $data['fechaReserva'];
            $turno->horaReserva = $data['horaReserva'];
            $horaInicial = $data['horaReserva'];
            for ($i=0; $i < $data["cupos"] ; $i++) { 
              $segundos_horaInicial=strtotime($horaInicial);
              $segundos_minutoAnadir=$data["minutos"]*60;
              $nuevaHora=date("H:i",$segundos_horaInicial+$segundos_minutoAnadir);
              $horaInicial = $nuevaHora;
            }        
            $turno->horaFinalReserva = $nuevaHora;
            $turno->save();
            $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1, "numeroTurno" => $turnoSiguiente));
            $response = $response->withStatus(200);

            //ENVIAR NOTIFICACION AL EMPLEADO Y AL ADMINISTRADOR DE LA SUCURSAL

        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error al pedir el turno", "std" => 0,"err" => $err->getMessage()));
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
      $idCliente= $request->getAttribute("idCliente");
      $query = "SELECT su.id as idSucursal, 0 as tiempoEstimado, tu.*,ser.nombre as servicio,su.latitud,su.longitud, su.nombre as sucursal, em.razonSocial as empresa, concat(emp.nombres, ' ', emp.apellidos) as empleado, '' as turnoActual FROM turno tu INNER JOIN servicio ser on (ser.id = tu.idServicio) INNER JOIN sucursal su on su.id = tu.idSucursal INNER JOIN empresa em on em.id = su.idEmpresa INNER JOIN empleado emp on emp.id = tu.idEmpleado where idCliente = '$idCliente' and (estadoturno = 'SOLICITADO' OR estadoturno = 'ATENDIENDO' OR estadoturno = 'CONFIRMADO')";
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
        if (count($dataTiempo) == 0){
          $data[$i]->turnoActual = 0;
        }else{
          $data[$i]->turnoActual = $dataTiempo[0]->turnoActual;
        }

        //CALCULAR TIEMPO
        $query = "SELECT "
                        ."(COALESCE(AVG(TIMESTAMPDIFF(SECOND,fechaInicio,fechaFinal)),0) * turnosFaltantes.faltantes) as tiempoEstimado "
                        ."FROM "
                        ."( "
                        ."  SELECT "
                        ."    count(t.id) as faltantes "
                        ."    FROM "
                        ."    turno as t "
                        ."    WHERE "
                        ."    t.idEmpleado = ".$data[$i]->idEmpleado." AND "
                        ."    t.idServicio = ".$data[$i]->idServicio." AND "
                        ."    t.fechaSolicitud < '".$data[$i]->fechaSolicitud."' AND "
                        ."    t.estadoTurno <> 'TERMINADO' AND t.estadoTurno <> 'CANCELADO'"
                        .") as turnosFaltantes, "
                        ."turno "
                        ."WHERE "
                        ."idEmpleado = ".$data[$i]->idEmpleado." AND "
                        ."idServicio = ".$data[$i]->idServicio." AND "
                        //."idCliente = ".$data[$i]->idCliente." AND "
                        ."estadoTurno = 'TERMINADO' LIMIT 1";
        $dataTiempo = DB::select(DB::raw($query));
        if(count($dataTiempo) > 0){
            $val = ceil(($dataTiempo[0]->tiempoEstimado / 60));
            $str = " minuto";
            if($val != 1){
                $str .= "s";
            }
            $data[$i]->tiempoEstimado = strval($val).$str;
        }

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

  public function getReservaBySucursal(Request $request, Response $response)
  {
    $response = $response->withHeader('Content-type', 'application/json');
    $idSucursal = $request->getAttribute("idSucursal");
    $idServicio = $request->getAttribute("idServicio");
    $fechaReserva = $request->getAttribute("fechaReserva");
    $query = "SELECT t.*, c.nombres, c.apellidos FROM turno t INNER JOIN cliente c ON t.idCliente = c.id WHERE t.idServicio = '$idServicio' AND t.idSucursal = '$idSucursal' AND t.fechaReserva = '$fechaReserva'";
    $data = DB::select(DB::raw($query));
    $response->getBody()->write(json_encode($data));
    return $response;
  }
  
  public function getTurnoreserva(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $idSucursal = $request->getAttribute("idSucursal");
    $mes = $request->getAttribute("mes");
    $ano = $request->getAttribute("ano");
    $query = "SELECT CONCAT( cliente.nombres,' ', cliente.apellidos ) AS cliente, turno.fechaReserva, turno.horaReserva, turno.horaFinalReserva
            FROM turno
            INNER JOIN cliente ON cliente.id = turno.idCliente
            WHERE month(turno.fechaReserva) = '$mes' and year(turno.fechaReserva) = '$ano'  
            AND turno.idSucursal = '$idSucursal'";
      $data = DB::select(DB::raw($query));
      $response->getBody()->write(json_encode($data));
      return $response;
  }

  public function getReservaByCliente(Request $request, Response $response)
  {
      $idCliente = $request->getAttribute("idCliente");
      $fecha_actual = date("Y/m/d");
      $query = "SELECT s.nombre AS nombreSucursal, se.nombre AS nombreServicio, t . *, e.nombres as nombreEmpleado, e.apellidos as apellidoEmpleado, em.razonSocial AS nombreEmpresa
                FROM turno t
                INNER JOIN sucursal s ON s.id = t.idSucursal
                INNER JOIN servicio se ON se.id = t.idServicio
                INNER JOIN empleado e ON e.id = t.idEmpleado
                INNER JOIN empresa em ON em.id = s.idEmpresa
                WHERE t.idCliente ='$idCliente'
                AND t.fechaReserva >=  '$fecha_actual'
                AND t.reserva = 'A'
                ";
      $data = DB::select(DB::raw($query));
      $response->getBody()->write(json_encode($data));
      return $response;

  }
  
    function verturnocalendario(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $idturno = $request->getAttribute("idTurno");
        $query = "SELECT CONCAT( cliente.nombres,' ', cliente.apellidos ) AS cliente, turno.fechaReserva, turno.horaReserva, turno.horaFinalReserva,turno.id,empleado.nombres  
                FROM turno
                INNER JOIN cliente ON cliente.id = turno.idCliente
                INNER JOIN empleado ON empleado.id = turno.idEmpleado 
                WHERE  turno.id = $idturno ";
          $data = DB::select(DB::raw($query));
          $response->getBody()->write(json_encode($data));
          return $response;
    }
    
    function cancelarservicio(Request $request, Response $response){
      $response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        try{
            $id = $request->getAttribute("id");
            $turno = Turno::find($id);
            $turno->estado      =   'INACTIVO';
            $turno->save();
            $respuesta = json_encode(array('msg' => "Turno cancelado", "std" => 1));
          $response = $response->withStatus(200);
    } catch (Exception $err) {
        $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
        $response = $response->withStatus(404);
      }
        $response->getBody()->write($respuesta);
        return $response;
  }

  public function postTurnoRecurrente(Request $request, Response $response)
  {
    $response = $response->withHeader('Content-type', 'application/json');
    $data = json_decode($request->getBody(),true);
    $fechaReserva = $data['fechaReserva'];
    $rango = $data["rango"];
    $meses = $data["meses"];
    
    $fechaFinal = strtotime ( "+$meses month" , strtotime ( $fechaReserva ) ) ;
    $fechaFinal = date ( 'Y/m/d' , $fechaFinal );

    $datetime1 = new DateTime($fechaReserva);
    $datetime2 = new DateTime($fechaFinal);
    $interval = $datetime1->diff($datetime2);

    $minutosServicio = ServiciosSucursal::select("minutos")
                    ->where("idServicio","=",$data["idServicio"])
                    ->where("idSucursal","=",$data["idSucursal"])
                    ->first();

    /*
      Valores de Rangos 
      
      diario = 1
      semanal = 2
      mensual = 3
    */ 

    switch ($rango) {
      case '1':
        $tiempo =  floor(($interval->format('%a')));
        break;
      case '2':
        $tiempo =  floor(($interval->format('%a')/7));
        break;
      
      default:
        $tiempo = $meses;
        break;
    }  
    //echo $tiempo;
    
    $fecha = $fechaReserva;

    for ($i=0; $i <= $tiempo; $i++) { 
      switch ($rango) {
        case '1':
          $fechaReservaFinal = strtotime ( "+1 day" , strtotime ( $fecha ) ) ;
          $fechaReservaFinal = date ( 'Y/m/d' , $fechaReservaFinal );
          break;
        case '2':
          $fechaReservaFinal = strtotime ( "+7 day" , strtotime ( $fecha ) ) ;
          $fechaReservaFinal = date ( 'Y/m/d' , $fechaReservaFinal );
          break;
        
        default:
          $fechaReservaFinal = strtotime ( "+1 month" , strtotime ( $fecha ) ) ;
          $fechaReservaFinal = date ( 'Y/m/d' , $fechaReservaFinal );
          break;
      }
      $respuesta = null;
      try {
        $turno = new Turno;
        $turno->idCliente   =   $data['idCliente'];
        $turno->idEmpleado  =   $data['idEmpleado'];
        $turno->idSucursal  =   $data['idSucursal'];
        $turno->idServicio  =   $data['idServicio'];
        $turno->tiempo      =   0;
        $turno->turno       =   0;
        $turno->turnoReal   =   0;
        $turno->tipoTurno   =   1;
        $turno->estadoTurno =   "SOLICITADO";
        $turno->estado      =   "ACTIVO";
        $turno->reserva     =   "A";
        $turno->fechaReserva = $fecha;
        $turno->horaReserva = $data['horaReserva'];
        $horaInicial = $data['horaReserva'];
        for ($j=0; $j < $data["cupos"] ; $j++) { 
          $segundos_horaInicial=strtotime($horaInicial);
          $segundos_minutoAnadir=$minutosServicio->minutos*60;
          $nuevaHora=date("H:i",$segundos_horaInicial+$segundos_minutoAnadir);
          $horaInicial = $nuevaHora;
        }        
        $turno->horaFinalReserva = $nuevaHora;
        $turno->save();
        $fecha = $fechaReservaFinal;
        $respuesta = json_encode(array('msg' => "Su turno ha sido asignado satisfactoriamente.", "std" => 1, 'idTurno' => $turno->id));
        $response = $response->withStatus(200);
      } catch(Exception $err){
        $respuesta = json_encode(array('msg' => "error al pedir el turno", "std" => 0,"err" => $err->getMessage()));
        $response = $response->withStatus(404);
      }      
    }

    $response->getBody()->write($respuesta);
    return $response;
  }

  public function getClienteByReserva(Request $request, Response $response)
  {
      $response = $response->withHeader('Content-type', 'application/json');
      $response = $response->withStatus(200);
      $idSucursal = $request->getAttribute("idSucursal");
      $idCliente = $request->getAttribute("idCliente");
      $query = "SELECT t.id,t.fechaReserva, t.horaReserva,s.nombre as servicio, su.nombre as sucursal, CONCAT( e.nombres ,' ',e.apellidos) as empleado 
        FROM turno t 
        INNER JOIN servicio s ON t.idServicio = s.id
        INNER JOIN sucursal su ON t.idSucursal = su.id 
        INNER JOIN empleado e ON t.idEmpleado = e.id
        WHERE t.idCliente = '$idCliente' AND t.idSucursal = '$idSucursal' AND t.estado <> 'INACTIVO'";
      $data = DB::select(DB::raw($query));
      $response->getBody()->write(json_encode($data));
      return $response;
  }

  public function getClienteByTurnos(Request $request, Response $response)
  {
      $response = $response->withHeader('Content-type', 'application/json');
      $response = $response->withStatus(200);
      $idSucursal = $request->getAttribute("idSucursal");
      $idCliente = $request->getAttribute("idCliente");
      $query = "SELECT t.id,s.nombre as servicio, su.nombre as sucursal, CONCAT( e.nombres ,' ',e.apellidos) as empleado 
        FROM turno t 
        INNER JOIN servicio s ON t.idServicio = s.id
        INNER JOIN sucursal su ON t.idSucursal = su.id 
        INNER JOIN empleado e ON t.idEmpleado = e.id
        WHERE t.idCliente = '$idCliente' AND t.idSucursal = '$idSucursal' AND t.estado <> 'INACTIVO'";
      $data = DB::select(DB::raw($query));
      $response->getBody()->write(json_encode($data));
      return $response;
  }

}
