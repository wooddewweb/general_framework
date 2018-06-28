<?php
namespace General\Application;

/**
 * Trata os exceptions
 */
class Exception extends \Exception
{

	/**
	 * Construtor da classe
	 */
	public function __construct($message, $code=NULL, $previous=NULL)
	{
		parent::__construct($message, $code, $previous);
		
		// @todo Verifica se deve armazenar os erros para gravar o log do trace
	}
}