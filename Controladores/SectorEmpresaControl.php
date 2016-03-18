<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Illuminate\Database\Capsule\Manager as DB;

class SectorEmpresaControl{

	function serviciosempresa(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
                $id = $request->getAttribute("id");
        	$servi = SectorEmpresa::select('idSector')
        		->where('idEmpresa','=',$id)
        		->get();
        		
        		for($i=0;$i<count($servi);$i++){
        			$serviciosector = ServiciosSector::select('serviciossector.idServicio','servicio.nombre','serviciossector.idSector')
        						->join('servicio','servicio.id','=','serviciossector.idServicio')
								->where('serviciossector.idSector','=',$servi[$i]->idSector)
                                                                ->where('servicio.estado','=','ACTIVO')
								->get();
        			$servi[$i]['servicio'] = $serviciosector;	
        		}
        	$response->getBody()->write(json_encode($servi));
		    return $response;
	}

        function sectorxempresa(Request $request, Response $response){
                $response = $response->withHeader('Content-type', 'application/json');
                $id = $request->getAttribute("id");
                $sector = SectorEmpresa::select('sectorempresa.idSector','sector.nombre')
                                        ->join('sector','sector.id','=','sectorempresa.idSector')
                                        ->where('idEmpresa','=',$id)
                                        ->where('sector.estado','=','ACTIVO')
                                        ->get();
                $response->getBody()->write(json_encode($sector));
                return $response;
        }
        
        
        public function reportesector(Request $request, Response $response){
            $response = $response->withHeader('Content-type', 'application/json');
            $id = $request->getAttribute("idsector");
            $fechainicial = $request->getAttribute("fechainicial");
            $fechafinal = $request->getAttribute("fechafinal");
            $data = SectorEmpresa::select('sectorempresa.idEmpresa','empresa.razonSocial')
                        ->join('empresa','empresa.id','=','sectorempresa.idEmpresa')
                        ->where('sectorempresa.idSector','=',$id)
                        ->where('empresa.estado','=','ACTIVO')
                        ->get();
            
            for($i=0;$i<count($data);$i++){
                $sucu = Sucursal::select('sucursal.id','sucursal.nombre','turno.estadoTurno')
                            ->join('turno','turno.idSucursal','=','sucursal.id')
                            ->where('turno.estadoTurno','=','TERMINADO')
                            ->whereBetween('turno.fechaSolicitud',array($fechainicial,$fechafinal))
                            ->where('sucursal.idEmpresa','=',$data[$i]->idEmpresa)
                            ->count();
                
                $data[$i]['sucursales'] = $sucu;
            }
            
            $response->getBody()->write(json_encode($data));
            return $response;
        }
	
}