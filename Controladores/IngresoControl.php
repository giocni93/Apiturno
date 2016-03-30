<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Illuminate\Database\Capsule\Manager as DB;

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
    
    function contabilidadsector(Request $request, Response $response){
        $response = $response->withHeader('Content-type','application/json');
        $id = $request->getAttribute("idSector");
        $fechainicial = $request->getAttribute("fechainicial");
        $fechafinal = $request->getAttribute("fechafinal");
        $data = SectorEmpresa::select('sectorempresa.idEmpresa','empresa.razonSocial')
                ->join('empresa','empresa.id','=','sectorempresa.idEmpresa')
                ->where('idSector','=',$id)
                ->get();
        for($i=0;$i<count($data);$i++){
            $sucu = Sucursal::select('sucursal.id','sucursal.nombre')
                    ->where('sucursal.idEmpresa','=',$data[$i]->idEmpresa)
                    ->get();
            $data[$i]['sucu'] = $sucu;
            for($j=0;$j<count($sucu);$j++){
                $empl = Empleado::select('empleado.id as idempleado')
                        ->where('idSucursal','=',$sucu[$j]->id)
                        ->get();
                $sucu[$j]['idEmpleado'] = $empl;
                for($k=0;$k<count($empl);$k++){
                    $ingreso = Ingreso::select('ingresos.valor')
                                ->where('ingresos.idEmpleado','=',$empl[$k]->idempleado)
                                ->whereBetween('ingresos.fecha',array($fechainicial,$fechafinal))
                                ->sum('ingresos.valor');
                    $empl[$k]['valor'] = $ingreso;
                }
            }
        }
        $response->getBody()->write($data);
        return $response;
    }
    
    function contabilidadsectores(Request $request, Response $response){
        $response = $response->withHeader('Content-type','application/json');
        $id = $request->getAttribute("idSector");
        $data = SectorEmpresa::select('sectorempresa.idSector','empresa.razonSocial','sucursal.nombre',
                                'empleado.nombres','ingresos.idServicio','ingresos.valor')
                                ->join('empresa','empresa.id','=','sectorempresa.idEmpresa')
                                ->join('sucursal','sucursal.idEmpresa','=','empresa.id')
                                ->join('empleado','empleado.idSucursal','=','sucursal.id')
                                ->join('ingresos','ingresos.idEmpleado','=','empleado.id')
                                ->where('sectorempresa.idSector','=',$id)
                                ->sum('ingresos.valor');
        $response->getBody()->write($data);
        return $response;
    }
    
	
}