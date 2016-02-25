<?php
	use Illuminate\Database\Eloquent\Model;

	class Permiso extends Model{
	    protected $table = "permisos";
		protected $primaryKey = "id";
	    public $timestamps = false;
	}