<?php

namespace Main\Model;

/**
 * Classe de relação aos dados da tabela dominios
 *
 * @author Bruno P. Gonçalves
 */
class Dominios extends \General\Db\Model
{
	/**
	 * Schema
	 */
	public $schema = "public";
	
	/**
	 * Table
	 */
	public $table = "dominios";
	
	/**
	 * Primary key
	 */
	public $primaryKey = "iddominio";
	
	/**
	 * Configura o model
	 */
	public function configure()
	{
	}
}