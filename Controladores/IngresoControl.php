<?php
use Slim\Http\Request;
use Slim\Http\Response;

class IngresoControl{

    function getingreso(Request $request, Response $response) {
        $response = $response->withHeader('Content-type', 'application/json');
        $data = Ingreso::all();
        if(count($data) == 0){
          $response = $response->withStatus(404);
        }
        $response->getBody()->write($data);
        return $response;
    }
    
    function contablidadempresa(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $idempresa = $request->getAttribute("idempresa");
        $fechainicial = $request->getAttribute("fechainicial");
        $fechafinal = $request->getAttribute("fechafinal");
        
        $sucu = Sucursal::select('id','nombre')
                ->where('idEmpresa','=',$idempresa)
                ->get();
        
        for($i=0;$i<count($sucu);$i++){
            $turno = Turno::select('turno.idEmpleado','turno.idServicio','turno.fechaSolicitud','empleado.nombres'
                    ,'empleado.apellidos','servicio.nombre as servicio')
                    ->join('empleado','empleado.id','=','turno.idEmpleado')
                    ->join('servicio','servicio.id','=','turno.idServicio')
                    ->where('turno.idSucursal','=',$sucu[$i]->id)
                    ->where('turno.estadoTurno','=','TERMINADO')
                    ->whereBetween('turno.fechaSolicitud',array($fechainicial,$fechafinal))
                    ->groupBy('turno.idServicio')
                    ->groupBy('turno.idEmpleado')
                    ->get();
            
            $sucu[$i]['sucursal'] = $turno;
                for($j=0;$j<count($turno);$j++){
                    $ingreso = Ingreso::select('valor','idServicio','idEmpleado')
                            ->where('idServicio','=',$turno[$j]->idServicio)
                            ->where('idEmpleado','=',$turno[$j]->idEmpleado)
                            ->sum('valor');
                    $turno[$j]['suma'] = $ingreso;
                }
            
        }
        
        $response->getBody()->write($sucu);
        return $response;



    }
	
}