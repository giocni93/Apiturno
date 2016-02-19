<?php
	use Illuminate\Database\Eloquent\Model;

	class Administrador extends Model{
	    protected $table = "administrador";
		protected $primaryKey = "id";
	    public $timestamps = false;
	}