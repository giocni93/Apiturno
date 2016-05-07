<?php

$app->get('/empleado/{idEmpleado}/servicio/{idServicio}/confirmados/turnos', "TurnoControl:getTurnosEnColaByEmpleado");
$app->get('/empleado/{idEmpleado}/servicio/{idServicio}/solicitados/turnos', "TurnoControl:getTurnosEnEsperaByEmpleado");
$app->put('/servicio/{idServicio}/empleado/{idEmpleado}/turno/{id}', "TurnoControl:cambiarEstadoTurno");
$app->post('/turno', "TurnoControl:postTurno");
$app->get('/cliente/{idCliente}/turnos', "TurnoControl:getTurnosCliente");

$app->post('/anonimo/turno', "TurnoControl:postTurnoAnonimo");
$app->post('/anonimo/reserva', "TurnoControl:postReservaAnonimo");
$app->get('/turno/pedidos/{id}/{fechainicial}/{fechafinal}',"TurnoControl:turnosxestado");
$app->get('/turno/pedidos/servicio/{id}/{fechainicial}/{fechafinal}',"TurnoControl:turnoxservicio");
$app->get('/turno/empresa/servicio/{id}/{fechainicial}/{fechafinal}',"TurnoControl:turnosempresa");
$app->get('/turno/empresaxservicio/{id}/{fechainicial}/{fechafinal}',"TurnoControl:turnosempresaxservicios");
$app->get('/getbysucursal/{id}',"TurnoControl:getsucursalid");
$app->get('/reporte/empleado/{id}/{fechainicial}/{fechafinal}',"TurnoControl:empleadomasturnos");
$app->put('/aplazar/turno/{idTurno}/empleado/{idEmpleado}/servicio/{idServicio}',"TurnoControl:aplazarTurno");
$app->put('/aplazar/cancelar/turno/{idTurno}/empleado/{idEmpleado}/servicio/{idServicio}',"TurnoControl:aplazarCancelarTurno");

$app->post('/turno/reserva', "TurnoControl:postReserva");
$app->get('/sucursal/{idSucursal}/idServicio/{idServicio}/fecha/{fechaReserva}/reservas', 'TurnoControl:getReservaBySucursal');
$app->get('/reservas/idsucursal/{idSucursal}/mes/{mes}/ano/{ano}/reservas',"TurnoControl:getTurnoreserva");

$app->get('/cliente/{idCliente}/reservas', 'TurnoControl:getReservaByCliente');
$app->get('/ver/reserva/{idTurno}',"TurnoControl:verturnocalendario");
$app->put('/aplazar/calendario/{id}',"TurnoControl:cancelarservicio");

$app->post('/turno/recurrente', "TurnoControl:postTurnoRecurrente");
$app->get('/cliente/{idCliente}/sucursal/{idSucursal}/reservas', "TurnoControl:getClienteByReserva");
$app->get('/cliente/{idCliente}/sucursal/{idSucursal}/turnos', "TurnoControl:getClienteByTurnos");
