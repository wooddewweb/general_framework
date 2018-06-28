<?php 

namespace General\Db\Adapters;

use General\Application\Exception;

/**
 * Adaptador de conexões PDO
 */
class PdoAdapter
{
	/**
	 * Instancia
	 */
	protected static $instance;
	
	/**
	 * Conexão
	 */
	protected static $connection;
	
	/**
	 * Singleton
	 */
	static public function getInstance($params=NULL)
	{
		if (! self::$instance) {
			// Cria a instancia
			self::$instance = new static();
			
			// Monta o driver
			$driverName = "\\General\\Db\\Adapters\\PdoDrivers\\" . ucfirst($params->driver);
			self::$instance->connection = new $driverName($params);
			
			// Configura o pdo
			self::$instance->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			self::$instance->connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
			
			// Verifica se tem configurações de atributos
			if(isset($params->options)) {
				foreach($params->options as $attribute => $value) {
					self::$instance->connection->setAttribute($attribute, $value);
				}
			}
		}
		
		return self::$instance;
	}
	
	/**
	 * Executa query
	 *
	 * @param string $query String da query
	 * @param array $params Vetor com os parametros para dar o bind
	 * @return PDOStatement
	 */
	public function execute($query, array $params = array())
	{
		try {
			$response = $this->connection->prepare($query);
			$response->execute($params);
		} catch (\PDOException $e) {
			// @todo Traduzir a mensagem
			throw new \Exception($e->getMessage());
		}
	
		return $response;
	}
	
	/**
	 * Recupera o ultimo id inserido
	 * 
	 * @param string $sequence_name Nome da sequencia
	 */
	public function getLastInsertId($sequence_name=NULL)
	{
		return $this->connection->lastInsertId($sequence_name);
	}
}