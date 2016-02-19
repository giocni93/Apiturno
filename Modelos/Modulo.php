<?php
	use Illuminate\Database\Eloquent\Model;

	class Modulo extends Model{
	    protected $table = "modulos";
		protected $primaryKey = "id";
	    public $timestamps = false;
	}