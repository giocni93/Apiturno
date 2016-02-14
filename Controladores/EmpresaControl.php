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
            $empresa->estado        =   "ESPERA";
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

    function postfoto(Request $request,Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        try{
            $id = $data['id'];
            $empresa = Empresa::find($id);
            $empresa->logo = "http://".$_SERVER['HTTP_HOST'].'/Apiturno/img/logos/'.$id.".jpg";
            $empresa->save();
            if ($request->hasFile('imagen')) {
                $request->file('imagen')->move("../img/perfil/", $id.".jpg");
                $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
            }
            /*if(move_uploaded_file($_FILE['files']['tmp_name'],"img/logos",$_FILE['files']['id'])){
                $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
            }*/
            $respuesta = json_encode(array('msg' => "Error al guardar la foto", "std" => 1));
        }catch(Exception $ex){
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

    

}