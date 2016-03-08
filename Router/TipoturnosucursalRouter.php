<?php
	$app->get('/tipoturnosucursal', "TipoturnosucursalControl:gettipoturnosector");
	$app->get('/tipoturnosucursal/{id}', "TipoturnosucursalControl:tiposelecionados");
	$app->post('/tipoturnosucursal/{id}', "TipoturnosucursalControl:posttipoturnosucursal");
	$app->post('/tipoturnosucursal', "TipoturnosucursalControl:posttipoturno");