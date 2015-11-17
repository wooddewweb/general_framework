<?php

namespace Main\Model;

/**
 * Classe de relação aos dados da tabela teste
 *
 * @author Bruno P. Gonçalves
 */
class Teste extends \General\Db\Model
{
	/**
	 * Schema
	 */
	public $schema = "public";
	
	/**
	 * Table
	 */
	public $table = "testes";
	
	/**
	 * Primary key
	 */
	public $primaryKey = "idteste";
	
	/**
	 * Configura o model
	 */
	public function configure()
	{
		// Configura
// 		$this->setSchema("public");
// 		$this->setTable("teste");
// 		$this->setPrimaryKey("idteste");
	}
}