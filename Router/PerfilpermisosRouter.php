<?php
$app->get('/perfilpermisos', "PerfilpermisosControl:getAll");
$app->get('/perfilpermisos/{id}',"PerfilpermisosControl:getId");
$app->get('/perfilpermisos/permisos/{id}',"PerfilpermisosControl:getIdPermisos");
$app->get('/perfilpermisosturno/{idperfil}',"PerfilpermisosControl:validarpermisos");
$app->get('/modulospermisos/{idperfil}',"PerfilpermisosControl:modulopermisoperfil");