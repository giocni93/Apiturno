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
    
    function contabilidadsucursal(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');//sum(ingresos.valor) as total,
            $id = $request->getAttribute("idSector");
            $fechainicial = $request->getAttribute("fechainicial");
            $fechafinal = $request->getAttribute("fechafinal");
            $data = DB::select(DB::raw("select sum(ingresos.valor) as total,ingresos.fecha,"
                    . "empresa.razonSocial,sucursal.nombre"
                    . " from sectorempresa "
                    . "inner join empresa on empresa.id = sectorempresa.idEmpresa "
                    . "inner join sucursal on sucursal.idEmpresa = empresa.id "
                    . "inner join empleado on empleado.idSucursal = sucursal.id "
                    . "inner join ingresos on ingresos.idEmpleado = empleado.id "
                    . "where sucursal.id = ".$id." and ingresos.fecha BETWEEN '".$fechainicial."' and "
                    . "'".$fechafinal."' "
                    . "GROUP BY empresa.razonSocial"));
            $response->getBody()->write(json_encode($data));
            return $response;
    }
    
    function contasucursales(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');//sum(ingresos.valor) as total,
        $id = $request->getAttribute("idsucursal");
        $fechainicial = $request->getAttribute("fechainicial");
        $fechafinal = $request->getAttribute("fechafinal");
        $data = DB::select(DB::raw("select sucursal.nombre,sucursal.telefono,sucursal.direccion,sucursal.estado,"
                . "sum(ingresos.valor) as total,empresa.razonSocial,sucursal.id from empresa "
                . "inner join sucursal on sucursal.idEmpresa = empresa.id "
                . "inner join empleado on empleado.idSucursal = sucursal.id "
                . "inner join ingresos on ingresos.idEmpleado = empleado.id "
                . "where empresa.id = ".$id." and ingresos.fecha BETWEEN '".$fechainicial."' and "
                . "'".$fechafinal."' "
                . "GROUP BY sucursal.nombre "));
        $response->getBody()->write(json_encode($data));
            return $response;
    }
    
    function contabilidadempleado(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $id = $request->getAttribute("idempleado");
        $fechainicial = $request->getAttribute("fechainicial");
        $fechafinal = $request->getAttribute("fechafinal");
        $data = DB::select(DB::raw("select CONCAT(empleado.nombres,' ',empleado.apellidos) as cliente,
                empleado.identificacion,empleado.telefono,sum(ingresos.valor) as total,sucursal.nombre,
                sucursal.direccion,empresa.razonSocial,empleado.logo,empleado.email,
                count(ingresos.idServicio) as termindaos
                from ingresos
                inner join empleado on empleado.id = ingresos.idEmpleado
                inner join sucursal on sucursal.id = empleado.idSucursal
                inner join empresa on empresa.id = sucursal.idEmpresa
                where ingresos.idEmpleado = ".$id." and ingresos.fecha BETWEEN '".$fechainicial."' and "
                . "'".$fechafinal."'"));
        $response->getBody()->write(json_encode($data));
        return $response;
    }
	
}