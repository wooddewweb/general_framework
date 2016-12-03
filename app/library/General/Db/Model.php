<?php

namespace General\Db;

use General\Application\Exception;
use General\Db\Sql;

/**
 * Modelo de dados referentes e armazenados no banco de dados
 */
class Model
{
	/**
	 * Armazena o adaptador
	 *
	 * @var string
	 */
	protected $adapter;
	
	/**
	 * Schema
	 *
	 * @var string
	 */
	public $schema = "public";
	
	/**
	 * Table
	 *
	 * @var string
	 */
	public $table = "";
	
	/**
	 * Primary key
	 *
	 * @var string
	 */
	public $primaryKey = "";
	
	/**
	 * Construtor
	 */
	public function __construct()
	{
		// Configura o model
		$this->configure();
		
		// Recupera a conexão
		$this->adapter = Adapter::getInstance();
	}
	
	/**
	 * Recupera o schema
	 *
	 * @return string
	 */
	public function getSchema()
	{
		return $this->schema;
	}
	
	/**
	 * Seta o schema
	 *
	 * @param string $schema Nome do esquema
	 */
	public function setSchema($schema)
	{
		$this->schema = $schema;
	}
	
	/**
	 * Recupera a tabela
	 *
	 * @return string
	 */
	public function getTable()
	{
		return $this->table;
	}
	
	/**
	 * Recupera o nome da tabela completo
	 *
	 * @return string
	 */
	public function getFullTable()
	{
		if(!$this->getSchema() == NULL) {
			return $this->table;
		}
		else {
			return $this->getSchema() . "." . $this->table;
		}
	}
	
	/**
	 * Seta a tabela
	 *
	 * @param string $table Nome da tabela
	 */
	public function setTable($table)
	{
		$this->table = $table;
	}
	
	/**
	 * Recupera a chave primaria
	 *
	 * @return string
	 */
	public function getPrimaryKey()
	{
		return $this->primaryKey;
	}
	
	/**
	 * Seta a chave primaria
	 *
	 * @param string $primaryKey Nome do campo chave primaria
	 */
	public function setPrimaryKey($primaryKey)
	{
		$this->primaryKey = $primaryKey;
	}
	
	/**
	 * Recupera o select
	 *
	 * @return Sql
	 */
	public function select()
	{
		return new Sql($this->getFullTable());
	}
	
	/**
	 * Recupera os dados
	 *
	 * @param array $where Vetor contendo os parametros para montagem do where
	 * @param array $order Vetor com os parametros para a montagem do order
	 * @param int $limit Vetor com os parametros para a montagem do limit
	 * @param int $offset Vetor com os parametros para a montagem do offset
	 * @return array
	 */
	public function fetchAll($where = array(), $order = array(), $limit = NULL, $offset = NULL)
	{
		if($where instanceof Sql) {
			$select = $where;
		}
		else {
			$select = new Sql($this->getFullTable());
			
			foreach ($where as $field => $param) {
				$select->where($field, $param);
			}
			
			foreach ($order as $ord) {
				$select->order($ord);
			}
			
			$select->limit($limit);
			
			$select->offset($offset);
		}
		
		$sql = $select->getSql();
		$params = $select->getParams();
		
		return $this->adapter->execute($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	/**
	 * Recupera um registro
	 *
	 * @param array $where Vetor contendo os parametros para montagem do where
	 * @param array $order Vetor com os parametros para a montagem do order
	 * @return array
	 */
	public function fetchRow($where = array(), $order = array())
	{
		$return = $this->fetchAll($where, $order, 1, 0);
		if (count($return) == 0) {
			return FALSE;
		}
		
		return current($return);
	}
	
	/**
	 * Insere um registro
	 *
	 * @param array $data Vetor com os dados a serem inseridos
	 * @return int
	 */
	public function insert($data)
	{
		$columns = array();
		$values = array();
		$params = array();
		foreach ($data as $field => $value) {
			$values[] = "?";
			$columns[] = $field;
			$params[] = $value;
		}
		$sql = "INSERT INTO " . $this->getFullTable() . " (" . implode(",", $columns) . ") VALUES (" . implode(",", $values) . ")";
		
		//
		$this->adapter->execute($sql, $params);
		
		// @todo Verificar se o banco é PGSQL para criar o sequence name
		try {
			$sequence_name = $this->getFullTable() . "_" . $this->getPrimarykey() . "_seq";
			$id = $this->adapter->getLastInsertId($sequence_name);
		} catch (Exception $e) {
			$id = FALSE;
		}
		
		return $id;
	}
	
	/**
	 * Atualiza registros
	 *
	 * @param array $data Vetor com os dados à serem atualizados
	 * @param array $where Vetor contendo as clausulas WHERE
	 * @return bool
	 */
	public function update($data, $where)
	{
		$sql = "UPDATE " . $this->getFullTable() . " SET";
		
		//
		$params = array();
		foreach ($data as $field => $value) {
			$sql .= " " . $field . " = ?";
			$params[] = $value;
			
			if ($value !== end($data)) {
				$sql .= ",";
			}
		}
		
		//
		if (count($where) > 0) {
			$sql .= " WHERE";
		}
		
		$count = 0;
		foreach ($where as $field => $param) {
			$count++;
			if($count != 1) {
				$sql .= " AND";
			}
			
			// Verifica se realmente existe parametro
			if(is_integer($field)) {
				$sql .= " (" . $param . ")";
			}
			else {
				$sql .= " (" . $field . ")";
				
				// Verifica se possui parametro
				if(strpos($field, "?") !== FALSE) {
					if(is_array($param)) {
						foreach($param as $p) {
							$params[] = $p;
						}
					}
					else {
						$params[] = $param;
					}
				}
			}
		}
		
		try {
			$res = $this->adapter->execute($sql, $params);
		}
		catch(Exception $e) {
			throw $e;
		}
		
		return $res;
	}
	
	/**
	 * Deleta registros
	 *
	 * @param array $where Vetor contendo as clausulas WHERE
	 * @return bool
	 */
	public function delete($where)
	{
		$sql = "DELETE FROM " . $this->getFullTable();
		
		//
		if (count($where) > 0) {
			$sql .= " WHERE";
		}
		
		foreach ($where as $field => $value) {
			if ($value !== reset($where)) {
				$sql .= " AND";
			}
			$sql .= " (" . $field . ")";
			
			$params[] = $value;
		}
		
		return $this->adapter->execute($sql, $params);
	}
	
	
	
	/**
	 * Método do hook de configuração
	 */
	public function configure()
	{}
}