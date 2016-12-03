<?php

namespace General\Mvc;

/**
 * Classe que carrega os view helpers 
 */
class ViewHelper
{
	/**
	 * Armazena o view
	 */
	protected $view;
	
	/**
	 * Construtor da classe
	 */
	public function __construct($view)
	{
		$this->view = $view;
	}
	
	/**
	 * Método magico para chamada de metodos
	 * 
	 * @param string $name Nome do método
	 * @param array $arguments Parametros do método
	 */
	public function __call($name, $arguments)
	{
		$request = $this->view->getRequest();
		
		// Cria o nome da calsse
		$module = $request->getParam("module");
		$className = "\\" . ucfirst($module) . "\\View\\Helpers\\" . ucfirst($name);
		
		// Cria a classe do helper
		$classObject = new $className($arguments);
		$output = $classObject->run();
		
		return $output;
	}
}