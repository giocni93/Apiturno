<?php
	$app->get('/tipoturnosector', "TipoturnosectorControl:gettipoturnosector");
	$app->get('/tipoturnosector/{id}', "TipoturnosectorControl:tiposelecionados");
	$app->post('/tipoturnosector/{id}', "TipoturnosectorControl:posttipoturnosector");