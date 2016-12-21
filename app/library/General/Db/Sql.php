<?php

namespace General\Db;

/**
 * Cria os selects
 * 
 * @author Bruno P. Gonçalves
 * 
 * @todo Adicionar método para os joins
 */
class Sql
{
	/**
	 * Parametros
	 * 
	 * @var array
	 */
	protected $params = array();
	
	/**
	 * Tabela
	 * 
	 * @var string
	 */
	protected $table = NULL;

	/**
	 * Colunas
	 * 
	 * @var array
	 */
	protected $columns = NULL;

	/**
	 * Joins
	 * 
	 * @var array
	 */
	protected $joins = array();

	/**
	 * Wheres
	 * 
	 * @var string
	 */
	protected $where = NULL;

	/**
	 * Orders
	 * 
	 * @var array
	 */
	protected $order = array();

	/**
	 * Groups
	 * 
	 * @var array
	 */
	protected $group = NULL;

	/**
	 * Havings
	 * 
	 * @var array
	 */
	protected $having = NULL;

	/**
	 * Limit
	 * 
	 * @todo
	 * @var int
	 */
	protected $limit = NULL;

	/**
	 * Offset
	 * 
	 * @var int
	 */
	protected $offset = NULL;
	
	/**
	 * Constructor
	 *
	 * @param string $table
	 */
	public function __construct($table=NULL)
	{
		if($table) {
			$this->from($table);
		}
	}
	
	/**
	 * Magical quote
	 */
	public function __toString()
	{
		return $this->parse();
	}
	
	/**
	 * Recupera os parametros
	 * 
	 * @return array
	 */
	public function getParams()
	{
		return $this->params;
	}
	
	/**
	 * Recupera o sql
	 * 
	 * @return string
	 */
	public function getSql()
	{
		return $this->parse();
	}
	
	/**
	 * Seta o from
	 *
	 * @param string $table
	 */
	public function from($table)
	{
		$this->table = $table;
		return $this;
	}
	
	/**
	 * Adiciona o join
	 * 
	 * @param string $table Tabela do join
	 * @param string $condition Condição da união
	 * @param string $side Lado do join
	 */
	public function join($table, $condition, $side="")
	{
		$this->joins[] = array($table, $condition, $side);
		
		return $this;
	}
	
	/**
	 * Adiciona o join left
	 * 
	 * @param string $table Tabela do join
	 * @param string $condition Condição da união
	 */
	public function joinLeft($table, $condition)
	{
		$this->join($table, $condition, "left");
		
		return $this;
	}
	
	/**
	 * Seta as colunas
	 *
	 * @param string|array $table
	 */
	public function columns($columns)
	{
		$this->columns = $columns;
		return $this;
	}
	
	/**
	 * Seta o order
	 *
	 * @param string $where
	 * @param mixed $value
	 */
	public function where($where, $value)
	{
		
		$this->where[$where] = $value;
		return $this;
	}
	
	/**
	 * Seta o limit
	 *
	 * @param int $limit
	 */
	public function limit($limit)
	{
		$this->limit = $limit;
		return $this;
	}
	
	/**
	 * Seta o offset
	 *
	 * @param int $offset
	 */
	public function offset($offset)
	{
		$this->offset = $offset;
		return $this;
	}
	
	/**
	 * Seta o order
	 *
	 * @param string $order
	 */
	public function order($order)
	{
		$this->order[] = $order;
		return $this;
	}
	
	/**
	 * Seta o grupo
	 *
	 * @param string $group
	 */
	public function group($group)
	{
		$this->group[] = $group;
		return $this;
	}
	
	/**
	 * Parse todo o sql
	 * 
	 * @return string
	 */
	private function parse()
	{
		$sql = "SELECT";
		
		//
		$sql = $this->parse_columns($sql);
		
		//
		$sql .= " FROM " . $this->table;
		
		// Da o parse do join
		$sql = $this->parse_join($sql);
		
		// 
		$sql = $this->parse_where($sql);
		
		// 
		$sql = $this->parse_order($sql);
		
		// 
		$sql = $this->parse_group($sql);
		
		// Adiciona o limit
		if($this->limit > 0) {
			$sql .= " LIMIT " . (int)$this->limit;
		}
		
		// Adiciona o offset
		if($this->offset > 0) {
			$sql .= " OFFSET " . (int)$this->offset;
		}
		
		// 
		return $sql;
	}
	
	/**
	 * Parse as columnas
	 * 
	 * @param string $sql
	 * @return string
	 */
	private function parse_columns($sql)
	{
		if(!$this->columns) {
			$sql .= " *";
		}
		elseif(is_array($this->columns)) {
			foreach($this->columns as $column_alias => $column_name) {
				
				$sql .= " " . $column_name;
				
				if(is_string($column_alias)) {
					$sql .= " as \"" . $column_alias . "\"";
				}
				
				if($column_name !== end($this->columns)) {
					$sql .= ",";
				}
			}
		}
		
		// 
		return $sql;
	}
	
	/**
	 * Parse os wheres
	 * 
	 * @param string $sql
	 * @return string
	 */
	private function parse_where($sql)
	{
		if(count($this->where) > 0) {
			$sql .= " WHERE";
		}
		
		$count = 0;
		foreach($this->where as $field => $param) {
			$count++;
			if($count != 1) {
				$sql .= " AND";
			}
			
			// Verifica se realmente existe parametro
			if(!$field) {
				$sql .= " (" . $param . ")";
			}
			else {
				$sql .= " (" . $field . ")";
				
				// Verifica se possui parametro
				if(strpos($field, "?") !== FALSE) {
					if(is_array($param)) {
						foreach($param as $p) {
							$this->params[] = $p;
						}
					}
					else {
						$this->params[] = $param;
					}
				}
			}
		}
		
		// 
		return $sql;
	}
	
	/**
	 * Parse os joins
	 *
	 * @param string $sql
	 * @return string
	 */
	private function parse_join($sql)
	{
		foreach($this->joins as $join) {
			$left = "";
			if($join[2] == "left") {
				$left = " LEFT";
			}
			
			$sql .= $left . " JOIN " . $join[0] . " ON " . $join[1];
		}
	
		//
		return $sql;
	}
	
	/**
	 * Parse os orders
	 * 
	 * @param string $sql
	 * @return string
	 */
	private function parse_order($sql)
	{
		if(count($this->order) > 0) {
			$sql .= " ORDER BY";
		}
		
		foreach($this->order as $order) {
			$sql .= " " . $order;
			if($order !== end($this->order)) {
				$sql .= ",";
			}
		}
		
		// 
		return $sql;
	}
	
	/**
	 * Parse os groups
	 * 
	 * @param string $sql
	 * @return string
	 */
	private function parse_group($sql)
	{
		if(count($this->group) > 0) {
			$sql .= " GROUP BY";
		}
		
		foreach($this->group as $group) {
			$sql .= " " . $group;
			if($group !== end($this->group)) {
				$sql .= ",";
			}
		}
		
		// 
		return $sql;
	}
}
