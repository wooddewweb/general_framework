<?php 

namespace General\Db\Adapters\PdoDrivers;

use General\Application\Config;

/**
 * Classe de conexão com o banco de dados Sqlite
 * 
 * @author Bruno P. Gonçalves
 */
class Sqlite extends \PDO
{
	/**
	 * Construtor da classe
	 */
	public function __construct(Config $params)
	{
		// Monta to dsn e conecta
		$dsn = "sqlite:" . $params->filename;
		parent::__construct($dsn);
	}
}