<?php
	use Illuminate\Database\Eloquent\Model;

	class Departamento extends Model{
	    protected $table = "departamento";
			protected $primaryKey = "id";
	    public $timestamps = false;
	}
