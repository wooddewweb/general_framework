<?php
namespace General\Application;

/**
 * Trata os exceptions
 *
 * @author Bruno P. Gonçalves
 */
class Exception extends \Exception
{

	/**
	 * Construtor da classe
	 */
	public function __construct($message, $code, $previous)
	{
		parent::__construct($message, $code, $previous);
		
		// @todo Verifica se deve armazenar os erros para gravar o log do trace
	}
}