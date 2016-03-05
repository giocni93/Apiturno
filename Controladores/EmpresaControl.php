<?php
use Slim\Http\Request;
use Slim\Http\Response;

//use Illuminate\Http\Request;
//use Illuminate\Http\JsonResponse;


class EmpresaControl{


    function post(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        try{
            $empresa = new Empresa;
            $empresa->nit           =   $data['nit'];
            $empresa->razonSocial   =   $data['razonSocial'];
            $empresa->email         =   $data['email'];
            $empresa->telefono      =   $data['telefono'];
            $empresa->contacto      =   $data['contacto'];
            $empresa->promedio      =   $data['promedio'];
            $empresa->pass          =   sha1($data['pass']);
            $empresa->estado        =   "INACTIVO";
            $empresa->save();

            $administrador = new Empleado;
            $administrador->nombres         =   $data['nombres'];
            $administrador->apellidos       =   $data['apellidos'];
            $administrador->identificacion  =   $data['identificacion'];
            $administrador->pass            =   sha1($data['pass']);
            $administrador->estado          =   "INACTIVO";
            $administrador->telefono        =   $data['telefonoadmin'];
            $administrador->idperfil        =   '3';
            $administrador->email           =   $data['emailadmin'];
            $administrador->idEmpresa       =   $empresa->id;
            $administrador->save();

            for($i=0; $i<count($data['sectores']);$i++){
                $sector = new SectorEmpresa;
                $sector->idSector   =   $data['sectores'][$i]['id'];
                $sector->idEmpresa  =   $empresa->id;
                $sector->save();
            }

            $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
            $response = $response->withStatus(200);
        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }
        $response->getBody()->write($respuesta);
        return $response;
    }

    function putfotoservidor(Request $request,Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        try{
            $id = $request->getAttribute("id");
            $empresa = Empresa::find($id);
            $empresa->logo  =  $data['logo'];
            $empresa->save();
            $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
            $response = $response->withStatus(200);
        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }
        $response->getBody()->write($respuesta);
        return $response;
    }

    
    function getId(Request $request,Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $id = $request->getAttribute("id");
        $data = Empresa::find($id);
        $response->getBody()->write($data);
        return $response;
    }

    function getAll(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $data = Empresa::all();
        if(count($data) == 0){
          $response = $response->withStatus(404);
        }
        $response->getBody()->write($data);
        return $response;
    }

    function maxId(Request $request,Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $data = Empresa::select("")
                        ->max('id');
        $response->getBody()->write($data);
        return $response;
    }


    function updateempresa(Request $request,Response $response){
        try{
            $response = $response->withHeader('Content-type', 'application/json');
            $data = json_decode($request->getBody(),true);
            $id = $request->getAttribute("id");
            $empresa = Empresa::find($id);
            $empresa->nit           =   $data['nit'];
            $empresa->razonSocial   =   $data['razonSocial'];
            $empresa->email         =   $data['email'];
            $empresa->telefono      =   $data['telefono'];
            $empresa->contacto      =   $data['contacto'];
            $empresa->promedio      =   $data['promedio'];
            $empresa->save();
            $respuesta = json_encode(array('msg' => "Actualizado correctamente", "std" => 1));
            $response = $response->withStatus(200);
        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }
        $response->getBody()->write($respuesta);
        return $response;
    }


    function updateempresaestado(Request $request,Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        try{
            $id = $request->getAttribute("id");
            $empresa = Empresa::find($id);
            $empresa->estado   =   $data['estado'];
            $empresa->save();
            $respuesta = json_encode(array('msg' => "Actualizado correctamente", "std" => 1));
            $response = $response->withStatus(200);
        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }
        $response->getBody()->write($respuesta);
        return $response;
    }

    function updateempresaestadodesactivar(Request $request,Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        try{
            $id = $request->getAttribute("id");
            $empresa = Empresa::find($id);
            $empresa->estado   =   $data['estado'];
            $empresa->save();
            $respuesta = json_encode(array('msg' => "Actualizado correctamente", "std" => 1));
            $response = $response->withStatus(200);
        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }
        $response->getBody()->write($respuesta);
        return $response;
    }

    


}