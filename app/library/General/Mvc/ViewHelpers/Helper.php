<?php

namespace General\Mvc\ViewHelpers;

/**
 * Classe abstrata para os viewhelpers
 */
class Helper
{

	/**
	 * Armazena os argumentos
	 */
	protected $arguments = array();

	/**
	 * Construtor da classe
	 *
	 * @param array $arguments Parametros que foi passado ao helper
	 */
	public function __construct($arguments)
	{
		$this->arguments = $arguments;
		
		// Inicia o hook
		$this->configure();
	}

	/**
	 * Hook para configurar o helper
	 */
	public function configure()
	{}

	/**
	 * Executa o helper
	 * 
	 * @return string
	 */
	public function run()
	{
		// Faz a chamada no método pai
		$output = call_user_func_array(array($this,"call"), $this->arguments);
		
		// Retorna o output
		return $output;
	}
}