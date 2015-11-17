<?php

namespace Main\Model;

/**
 * Classe de relação aos dados da tabela usuarios_status
 *
 * @author Bruno P. Gonçalves
 */
class Usuariosstatus extends \General\Db\Model
{
	/**
	 * Schema
	 */
	public $schema = "public";
	
	/**
	 * Table
	 */
	public $table = "usuarios_status";
	
	/**
	 * Primary key
	 */
	public $primaryKey = "idusuario_status";
	
	/**
	 * Configura o model
	 */
	public function configure()
	{
	}
}