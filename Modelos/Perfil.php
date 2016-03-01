<?php
	use Illuminate\Database\Eloquent\Model;

	class Perfil extends Model{
	    protected $table = "perfil";
		protected $primaryKey = "id";
	    public $timestamps = false;
	}