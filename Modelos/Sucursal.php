<?php
	use Illuminate\Database\Eloquent\Model;

	class Sucursal extends Model{
	    protected $table = "sucursal";
			protected $primaryKey = "id";
	    public $timestamps = false;
	}
