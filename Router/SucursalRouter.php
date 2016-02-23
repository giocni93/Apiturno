<?php
	$app->post('/sucursal', "SucursalControl:postSucursal");
	$app->put('/sucursal/{id}',"SucursalControl:putsucursal");
    $app->get('/sucursal', "SucursalControl:Versucursales");
    $app->get('/sucursal/maxid',"SucursalControl:maxIdsucursal");
		$app->get('/empresa/{idEmpresa}/posicion/{latitud}/{longitud}/sucursal', "SucursalControl:getSucursalesByPosicion");

	$app->get('/sucursal/empresas/{id}',"SucursalControl:getsucursalxempresa");
