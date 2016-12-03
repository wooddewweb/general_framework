<?php 

namespace General\Db\Adapters\PdoDrivers;

use General\Application\Config;

/**
 * Classe de conexão com o banco de dados Postgresql
 */
class Postgresql extends \PDO
{
	/**
	 * Construtor da classe
	 */
	public function __construct(Config $params)
	{
		// Verifica se possui porta
		if(!isset($params->port)) {
			$port = 5432;
		}
		else {
			$port = $params->port;
		}
		
		// Monta to dsn e conecta
		$dsn = "pgsql:host=" . $params->host . ";port=" . $port . ";dbname=" . $params->database . ";";
		parent::__construct($dsn, $params->username, $params->password);
	}
}