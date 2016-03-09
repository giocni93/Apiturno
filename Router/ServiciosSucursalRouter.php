<?php
$app->post('/serviciosucursal',"ServiciosSucursalControl:postserviciossucursal");
$app->post('/servisucursal',"ServiciosSucursalControl:guardarserviciossucursal");
$app->get('/serviciosucursal/{id}',"ServiciosSucursalControl:getallid");
$app->delete('/serviciosucursal/{id}', "ServiciosSucursalControl:deleteserviciosucursal");
$app->put('/putserviciosucursal/{id}',"ServiciosSucursalControl:putserviciosucursalprecios");
