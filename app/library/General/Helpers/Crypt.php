<?php

namespace General\Helpers;

/**
 * Classe para manipulação de senhas
 */
final class Crypt
{
	/**
	 * Construtor da classe
	 */
	public function __construct()
	{}

	/**
	 * Faz a checagem da senha
	 */
	public static function check($string, $hash)
	{
		return (crypt($string, $hash) === $hash);
	}
	
	/**
	 * Cria a hash da senha
	 */
	public static function hash($string, $cost=7, $length=22)
	{
		// Salt
		$salt = Strings::randomString($length, TRUE);

		// Hash string
		$hashString = sprintf("\$2a\$%02d\$%s\$", $cost, $salt);
		
		return crypt($string, $hashString);
	}
}