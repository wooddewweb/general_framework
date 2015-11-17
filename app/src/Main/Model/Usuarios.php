<?php

namespace Main\Model;

/**
 * Classe de relação aos dados da tabela usuarios
 *
 * @author Bruno P. Gonçalves
 */
class Usuarios extends \General\Db\Model
{
	/**
	 * Schema
	 */
	public $schema = "public";
	
	/**
	 * Table
	 */
	public $table = "usuarios";
	
	/**
	 * Primary key
	 */
	public $primaryKey = "idusuario";
	
	/**
	 * Configura o model
	 */
	public function configure()
	{
	}
}