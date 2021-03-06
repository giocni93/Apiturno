<?php
	$app->post('/sucursal', "SucursalControl:postSucursal");
	$app->put('/sucursal/{id}/{idEmpleado}',"SucursalControl:putsucursal");
    $app->get('/sucursal', "SucursalControl:Versucursales");
    $app->get('/sucursal/maxid',"SucursalControl:maxIdsucursal");
		$app->get('/empresa/{idEmpresa}/posicion/{latitud}/{longitud}/sucursal', "SucursalControl:getSucursalesByPosicion");

	$app->get('/sucursal/empresas/{id}',"SucursalControl:getsucursalxempresa");

	$app->put('/sucursal/estado/{id}',"SucursalControl:updateestado");

	$app->put('/sucursal/estado/desactivar/{id}',"SucursalControl:updateestadodesactivar");

	$app->get('/sucursal/idempresa/{id}', "SucursalControl:getAllsucursalesId");

	$app->get('/servicio/{idServicio}/posicion/{latitud}/{longitud}/sucursal', "SucursalControl:getSucursalesByPosicion");

	$app->get('/sucursalxempresa',"SucursalControl:getAllsucursalesxempresa");

	$app->get('/empleado/empresa/{id}',"SucursalControl:empleadosporempresa");

	$app->get('/servicio/{idServicio}/ciudad/{ciudad}/posicion/{latitud}/{longitud}/sucursal', "SucursalControl:getSucursalesByCiudad");

	$app->get('/sucursal/activas',"SucursalControl:sucursalesactivas");
        
        $app->get('/sucursalbyempresa/{id}', "SucursalControl:sucursalebyempresaactivas");
        
        $app->get('/sucursal/{id}',"SucursalControl:getId");