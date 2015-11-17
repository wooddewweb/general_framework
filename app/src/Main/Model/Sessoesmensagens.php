<?php

namespace Main\Model;

/**
 * Classe de relação aos dados da tabela de mensagens da sessão
 *
 * @author Bruno P. Gonçalves
 */
class Sessoesmensagens extends \General\Db\Model
{
	/**
	 * Schema
	 */
	public $schema = "public";
	
	/**
	 * Table
	 */
	public $table = "sessoes_mensagens";
	
	/**
	 * Primary key
	 */
	public $primaryKey = "idsessao_mensagem";
	
	/**
	 * Configura o model
	 */
	public function configure()
	{
		
	}
}