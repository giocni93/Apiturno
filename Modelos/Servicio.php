<?php
	use Illuminate\Database\Eloquent\Model;

	class Servicio extends Model{
	    protected $table = "servicio";
			protected $primaryKey = "id";
	    public $timestamps = false;
	}
