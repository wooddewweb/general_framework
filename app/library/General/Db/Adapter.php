<?php

namespace General\Db;

use General\Application\Exception;
use General\Application\Services;

/**
 * Conexão com o banco de dados
 * 
 * @todo Implementar os adapters para cada banco
 */
abstract class Adapter
{
	/**
	 * Instancia
	 */
	protected static $instance;
	
	/**
	 * Construtor
	 */
	private function __construct()
	{
	}
	
	/**
	 * Singleton
	 */
	static public function getInstance()
	{
		if (! self::$instance) {
			// Recupera os dados do banco de dados
			$config = Services::getInstance()->getService("config");
			$params = $config->database;
			
			// Cria a instancia
			self::$instance = self::connect($params);
		}
		
		return self::$instance;
	}
	
	/**
	 * Efetua a conexão
	 * 
	 * @return mixed
	 */
	static protected function connect($params)
	{
		try {
			// Monta o tipo do adapter
			$adapterName = "\\General\\Db\\Adapters\\" . ucfirst($params->type) . "Adapter";
			$adapter = $adapterName::getInstance($params);
		} catch (\PDOException $e) {
			// @todo Adicionar tradução
			throw new Exception($e->getMessage());
		}
		
		return $adapter;
	}
}
