<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Illuminate\Database\Capsule\Manager as DB;

class ClienteControl{

  function getAll(Request $request, Response $response) {
    $response = $response->withHeader('Content-type', 'application/json');
    $data = Cliente::all();
    if(count($data) == 0){
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($data);
    return $response;
  }

  function getById(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $id = $request->getAttribute("id");
    $data = Cliente::select("*")
                    ->where("id","=",$id)
                    ->first();
    $response->getBody()->write($data);
    return $response;
  }

  function getClienteByemail(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $email = $request->getAttribute("email");
    $data = Cliente::select("nombres","apellidos","id","email")
                    ->where("email","=",$email)
                    ->first();
    if($data == null){
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($data);
    return $response;
  }

  function verificarLoginFacebook(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $id = $request->getAttribute("idFace");
    $cliente = Cliente::select("*")
                    ->where("idFace","=",$id)
                    ->where("estado","=","ACTIVO")
                    ->first();
    $respuesta = json_encode(array("std" => 1, "cliente" => $cliente, "msg" => "Ok"));
    if($cliente == null){
      $respuesta = json_encode(array('cliente' => null, "std" => 0, "msg" => "Email o contraseña no validos."));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;
  }

  function loginFacebook(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $data = json_decode($request->getBody(),true);
    $cliente = null;
    $email = null;
    $pass = "";
    if($data['email'] != ""){
        $email = $data['email'];
        $pass = sha1($email);
    }
    
    if($email != null){
        $cliente = Cliente::select("*")
                    ->where("email","=",$email)
                    ->where("estado","=","ACTIVO")
                    ->first();
    }else{
        $cliente = Cliente::select("*")
                    ->where("idFace","=",$data['idFace'])
                    ->where("estado","=","ACTIVO")
                    ->first();
    }
    
    if($cliente == null){
        try{
            $cliente = new Cliente;
            $cliente->email     =   $email;
            $cliente->nombres   =   $data['nombres'];
            $cliente->apellidos =   $data['apellidos'];
            $cliente->telefono  =   "";
            $cliente->pass      =   $pass;
            $cliente->idPush    =   "01";//$data['idPush'];
            $cliente->idFace    =   $data['idFace'];//$data['idFace'];
            $cliente->estado    =   "ACTIVO";
            $cliente->save();
            $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
            $response = $response->withStatus(200);
        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }
    }else{
      try{
          $cliente->email     =   $email;
          //$cliente->pass      =   $pass;
          $cliente->idFace    =   $data['idFace'];
          $cliente->save();
          $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
          $response = $response->withStatus(200);
      }catch(Exception $err){
          $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
          $response = $response->withStatus(404);
      }
    }
    $respuesta = json_encode(array("std" => 1, "cliente" => $cliente, "msg" => "Ok"));
    $response->getBody()->write($respuesta);
    return $response;
  }

  function post(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $data = json_decode($request->getBody(),true);
    //echo $data["email"];
    try{
        $cliente = new Cliente;
        $cliente->email     =   $data['email'];
        $cliente->nombres   =   $data['nombres'];
        $cliente->apellidos =   $data['apellidos'];
        $cliente->telefono  =   $data['telefono'];
        $cliente->pass      =   sha1($data['pass']);
        $cliente->idPush    =   "01";//$data['idPush'];
        $cliente->idFace    =   "01";//$data['idFace'];
        $cliente->estado    =   "INACTIVO";
        $cliente->save();
        $respuesta = json_encode(array('msg' => "Guardado correctamente, revise su correo para activar su cuenta", "std" => 1, 'idCliente' => $cliente->id));
        $response = $response->withStatus(200);
        
        //ENVIAR ACTIVACION AL CORREO
        $id = $cliente->id;
        $para = $cliente->email;
        $user = $cliente->nombres.' '.$cliente->apellidos;

        $titulo = utf8_encode('Activacion de la cuenta [Turnomovil]');

        $mensaje = ""
                . "<html>
                    <head>
                      <title>". utf8_encode("Activacion de la cuenta") ."</title>
                    </head>
                    <body>          
                    <img style='height:40px;' src='http://turnomovil.com/images/turnomovil.png' alt=''/>
                      <h1>Hola, $user </h1><br/>
                      <h4>Hemos Recibido una solicitud, para activar tu cuenta</h4>

                      <br/>    
                        <h4>Usuario Turnomovil: $user</h4>
                      <br/>

                      <h4> Si deseas activar tu cuenta, por favor sigue este enlace</h4>
                      <h4><a href='http://turnomovil.com/api/cliente/$id/activar/cuenta' target='_blank'>Click Aqui, para activar la cuenta turnomovil</a></h4>
                      <h4>Atentamente</h4>
                      <h4>Turnomovil.com</h4>
                    </body>
                    </html>";

        $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

        $cabeceras .= 'To: '.$user.' <'.$para.'>' . "\r\n";
        $cabeceras .= 'From: Turnomovil.com' . "\r\n";

        mail($para, $titulo, $mensaje, $cabeceras);
        
        
        
    }catch(Exception $err){
        $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
        $response = $response->withStatus(404);
        //echo $respuesta;
    }
    $response->getBody()->write($respuesta);
    return $response;
  }
  
  function activarCuenta(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $id = $request->getAttribute("id");
    $cliente = Cliente::select("*")
                          ->where("id","=",$id)
                          ->first();
    if($cliente != null){
        $cliente->estado = "ACTIVO";
        $cliente->save();
    }
    $response->getBody()->write("BIEN");
    return $response;
  }

  public function delete(Request $request, Response $response)
  {
    try {
      $response = $response->withHeader('Content-type', 'application/json');
      $id = $request->getAttribute("id");
      $cliente = Cliente::select("*")
                      ->where("id","=",$id)
                      ->first();
      $cliente->estado = "INACTIVO";
      $cliente->save();
      $respuesta = json_encode(array('msg' => "Eliminado correctamente", "std" => 1));
      $response = $response->withStatus(200);
    } catch (Exception $err) {
      $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;

  }

  public function put(Request $request, Response $response)
  {
    try {
      $response = $response->withHeader('Content-type', 'application/json');
      $data = json_decode($request->getBody(),true);
      $id = $request->getAttribute("id");
      $cliente = Cliente::select("*")
                          ->where("id","=",$id)
                          ->first();
      $cliente->email     =   $data['email'];
      $cliente->nombres   =   $data['nombres'];
      $cliente->apellidos =   $data['apellidos'];
      $cliente->telefono  =   $data['telefono'];
      $cliente->pass      =   sha1($data['pass']);
      $cliente->idPush    =   "01";//$data['idPush'];
      $cliente->idFace    =   "01";//$data['idFace'];
      $cliente->save();
      $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
      $response = $response->withStatus(200);
    } catch (Exception $err) {
      $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;
  }

  public function putIdpush(Request $request, Response $response)
  {
    try {
      $response = $response->withHeader('Content-type', 'application/json');
      $data = json_decode($request->getBody(),true);
      $id = $request->getAttribute("id");
      $cliente = Cliente::select("*")
                          ->where("id","=",$id)
                          ->first();
      $cliente->idPush    =   $data['idPush'];
      $cliente->save();
      $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
      $response = $response->withStatus(200);
    } catch (Exception $err) {
      $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;
  }

  function login(Request $request, Response $response)
  {
    $response = $response->withHeader('Content-type', 'application/json');
    $response = $response->withStatus(200);
    $data = json_decode($request->getBody(),true);
    //echo $data["email"];
    $cliente = Cliente::select("*")
                        ->where("email","=",$data["email"])
                        ->where("pass","=",sha1($data["pass"]))
                        ->first();
    
    if($cliente == null){
      $respuesta = json_encode(array('cliente' => null, "std" => 0, "msg" => "Email o contraseña no validos."));
    }else{
        if($cliente->estado == "ACTIVO"){
            $respuesta = json_encode(array("std" => 1, "cliente" => $cliente, "msg" => "Ok"));
        }else{
            if($cliente->estado == "INACTIVO"){
                $respuesta = json_encode(array('cliente' => null, "std" => 0, "msg" => "Debe activar su cuenta ingresando a la dirección enviada a su correo"));
            }else{
                $respuesta = json_encode(array('cliente' => null, "std" => 0, "msg" => "Su cuenta ha sido desactivada por infringir las normas del sitio"));
            }
        }
    }
    $response->getBody()->write($respuesta);
    return $response;

  }
  
    public function putperfilcliente(Request $request, Response $response)
    {
        try {
            $response = $response->withHeader('Content-type', 'application/json');
            $data = json_decode($request->getBody(),true);
            $id = $request->getAttribute("id");
            $cliente = Cliente::select("*")
                              ->where("id","=",$id)
                              ->first();
            if(strtolower($data['email']) == "null"){
                $data['email'] = null;
            }
            $cliente->email     =   $data['email'];
            $cliente->nombres   =   $data['nombres'];
            $cliente->apellidos =   $data['apellidos'];
            $cliente->telefono  =   $data['telefono'];
            $cliente->save();
            $respuesta = json_encode(array('msg' => "Actualizado correctamente", "std" => 1));
            $response = $response->withStatus(200);
        } catch (Exception $err) {
            $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }
            $response->getBody()->write($respuesta);
            return $response;
    }
    
    public function putContrasenaCliente(Request $request, Response $response)
    {
        try {
            $response = $response->withHeader('Content-type', 'application/json');
            $data = json_decode($request->getBody(),true);
            $id = $request->getAttribute("id");
            $cliente = Cliente::select("*")
                              ->where("id","=",$id)
                              ->first();
            $cliente->pass      =   sha1($data['pass']);
            $cliente->save();
            $respuesta = json_encode(array('msg' => "Actualizado correctamente", "std" => 1));
            $response = $response->withStatus(200);
        } catch (Exception $err) {
            $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }
            $response->getBody()->write($respuesta);
            return $response;
    }
    
    public function postcliente(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        //echo $data["email"];
        try{
            $cliente = new Cliente;
            $cliente->email     =   $data['email'];
            $cliente->nombres   =   $data['nombres'];
            $cliente->apellidos =   $data['apellidos'];
            $cliente->telefono  =   $data['telefono'];
            $cliente->pass      =   sha1($data['pass']);
            $cliente->estado    =   "ACTIVO";
            $cliente->save();
            $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1, 'idCliente' => $cliente->id));
            $response = $response->withStatus(200);
        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
            //echo $respuesta;
        }
        $response->getBody()->write($respuesta);
        return $response;
    }
    
    function maxId(Request $request,Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $data = Cliente::select("")
                        ->max('id');
        $cli = Cliente::select('*')
                ->where('id','=',$data)
                ->first();
        $response->getBody()->write($cli);
        return $response;
    }
    
    function vercliente(Request $request,Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $response = $response->withStatus(200);
        $id = $request->getAttribute("id");
        $data = Cliente::select("*")
                        ->orwhere('idFace','=',$id)
                        ->orwhere('id','=',$id)
                        ->first();
        $response->getBody()->write($data);
        return $response;
    }
    
    function enviaremail(Request $request, Response $response){
      try {
          $response = $response->withHeader('Content-type', 'application/json');
          $data = json_decode($request->getBody(),true);
          $id = $data['email'];

          $cliente = Cliente::select('*')
                      ->where('email','=',$id)
                      ->first();

          if($cliente == null){
              $respuesta = json_encode(array("msg" => "El email ingresado no esta registrado en el sistema", "std" => 0));
          }else{

              $para = $cliente->email;
              $user = $cliente->nombres.' '.$cliente->apellidos;

              $titulo = utf8_encode('Recuperacion de Clave [Turnomovil]');

              $mensaje = ""
                      . "<html>
                          <head>
                            <title>". utf8_encode("Recuperación de Usuario o Contraseña") ."</title>
                          </head>
                          <body>
                          <img style='height:40px;' src='http://turnomovil.com/images/turnomovil.png' alt=''/>
                            <h1>Hola, $user </h1><br/>
                            <h4>Hemos Recibido una solicitud, para recuperar tu Usuario o  Contraseña</h4>

                            <br/>
                              <h4>Usuario Turnomovil: $user</h4>
                            <br/>

                            <h4> Si deseas cambiar tu Contraseña, por favor sigue este enlace para ingresar una Nueva Contraseña</h4>
                            <h4><a href='http://turnomovil.com/sesion.html#/cambiarclavecliente/$id/$para' target='_blank'>Click Aqui, para cambiar la Contraseña</a></h4>
                            <h4>Atentamente</h4>
                            <h4>Turnomovil.com</h4>
                          </body>
                          </html>";

              $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
              $cabeceras .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

              $cabeceras .= 'To: '.$user.' <'.$para.'>' . "\r\n";
              $cabeceras .= 'From: Turnomovil.com' . "\r\n";

              mail($para, $titulo, $mensaje, $cabeceras);
              $respuesta = $respuesta = json_encode(array("msg" => "Se ha enviado un correo a la dirección especificada para recuperar su contraseña", "std" => 1));

          }

      } catch (Exception $exc) {
          $respuesta = $respuesta = json_encode(array("msg" => "Error del servidor, ".$exc->getTraceAsString(), "std" => 0));
      }
      $response->getBody()->write($respuesta);
      return $response;
    }


    function updateclave(Request $request, Response $response){
        try {
        $response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        $id = $request->getAttribute("id");
        //$para = $request->getAttribute("email");
        $cliente = Cliente::select("*")
                            ->where("id","=",$id)
                            ->first();
        $cliente->pass     =   sha1($data['pass']);
        $cliente->save();

        $respuesta = json_encode(array('msg' => "Clave actualizada correctamente", "std" => 1));

        $clave = $data['pass'];

        $titulo = utf8_encode('Clave actualizada [Turnomovil]');

        $mensaje = ""
                    . "<html>
                        <head>
                          <title>". utf8_encode("Nueva contraseña registrada") ."</title>
                        </head>
                        <body>
                        <img style='height:60px;' src='http://turnomovil.com/images/turnomovil.png' alt=''/>
                          <h1>Has actualizado tu contraseña </h1><br/>
                          <h4>Gracias por usar nuestros servicios</h4>

                          <br/>
                            <h4>Tu nueva contraseña es: $clave</h4>
                          <br/>


                        </body>
                        </html>";

            $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
            $cabeceras .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
            $cabeceras .= 'To: '.$cliente->nombres.' '.$cliente->apellidos.' <'.$cliente->email.'>' . "\r\n";
            $cabeceras .= 'From: Turnomovil.com' . "\r\n";

            mail($cliente->email, $titulo, $mensaje, $cabeceras);

        $response = $response->withStatus(200);
      } catch (Exception $err) {
        $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
        $response = $response->withStatus(404);
      }
      $response->getBody()->write($respuesta);
      return $response;
    }
    
    function validarcorreo(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $response = $response->withStatus(200);
        $correo = $request->getAttribute("email");
        $data = Cliente::select("email")
                        ->where('email','=',$correo)
                        ->first();
        if($data != null){
            $respuesta = json_encode(array("std" => 1, "msg" => "Este correo ya esta registrado"));
        }else{
            $respuesta = json_encode(array("std" => 0, "msg" => "Este correo esta disponible"));
        }
        $response->getBody()->write($respuesta);
        return $response;
    }
    
    public function getClienteBySucursal(Request $request, Response $response)
    {
        $response = $response->withHeader('Content-type', 'application/json');
        $response = $response->withStatus(200);
        $idSucursal = $request->getAttribute("idSucursal");
        $query = "SELECT DISTINCT c.id,c.email,c.nombres,c.apellidos,c.telefono FROM turno t inner join cliente c on t.idCliente = c.id where t.idSucursal = '$idSucursal' and c.estado <> 'INACTIVO' ORDER BY (c.nombres) ASC";
        $data = DB::select(DB::raw($query));
        $response->getBody()->write(json_encode($data));
        return $response;

    }

}
