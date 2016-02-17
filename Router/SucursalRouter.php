<?php
	$app->post('/sucursal', "SucursalControl:postSucursal");
    $app->get('/sucursal', "SucursalControl:Versucursales");    