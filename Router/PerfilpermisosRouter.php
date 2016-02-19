<?php
$app->get('/perfilpermisos', "PerfilpermisosControl:getAll");
$app->get('/perfilpermisos/{id}',"PerfilpermisosControl:getId");
$app->get('/perfilpermisosturno/{idperfil}/{idpermiso}',"PerfilpermisosControl:validarpermisos");