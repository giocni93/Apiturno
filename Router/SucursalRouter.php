<?php
	$app->post('/sucursal', "SucursalControl:postSucursal");
	$app->put('/sucursal/{id}',"SucursalControl:putsucursal");
    $app->get('/sucursal', "SucursalControl:Versucursales");
    $app->get('/sucursal/maxid',"SucursalControl:maxIdsucursal");
		$app->get('/empresa/{idEmpresa}/posicion/{latitud}/{longitud}/sucursal', "SucursalControl:getSucursalesByPosicion");

	$app->get('/sucursal/empresas/{id}',"SucursalControl:getsucursalxempresa");

	$app->put('/sucursal/estado/{id}',"SucursalControl:updateestado");

	$app->put('/sucursal/estado/desactivar/{id}',"SucursalControl:updateestadodesactivar");

	$app->get('/sucursal/idempresa/{id}', "SucursalControl:getAllsucursalesId");

	$app->get('/servicio/{idServicio}/posicion/{latitud}/{longitud}/sucursal', "SucursalControl:getSucursalesByPosicion");

	