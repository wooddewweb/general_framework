<?php

namespace Main\Model;

/**
 * Classe de relação aos dados da tabela sessões
 *
 * @author Bruno P. Gonçalves
 */
class Sessoes extends \General\Db\Model
{
	/**
	 * Schema
	 */
	public $schema = "public";
	
	/**
	 * Table
	 */
	public $table = "sessoes";
	
	/**
	 * Primary key
	 */
	public $primaryKey = "idsessao";
	
	/**
	 * Configura o model
	 */
	public function configure()
	{
	}
}