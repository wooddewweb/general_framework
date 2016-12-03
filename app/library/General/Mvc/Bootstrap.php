<?php

namespace General\Mvc;

/**
 * Classe abstrata para carregamento de modulos
 */
class Bootstrap
{
	/**
	 * Construtor da classe
	 */
	public function __construct()
	{
		// Recupera os métodos do bootstrap
		$methodNames = get_class_methods($this);
		
		// Percorre os metodos
		foreach($methodNames as $method) {
		
			// Verifica se inicia com ini
			if(substr($method, 0, 3) == "ini") {
				$this->$method();
			}
		}
	}
}