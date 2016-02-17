<?php
	$app->post('/sucursal', "SucursalControl:postSucursal");
    $app->get('/sucursal', "SucursalControl:Versucursales");
		$app->get('/empresa/{idEmpresa}/posicion/{latitud}/{longitud}/sucursal', "SucursalControl:getSucursalesByPosicion");
