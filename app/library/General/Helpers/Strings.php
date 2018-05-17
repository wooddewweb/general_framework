<?php

namespace General\Helpers;

/**
 * Funções com strings
 */
class Strings
{
	/**
	 * Cria o hash para senhas
	 */
	static public function hashPassword($password)
	{ 
		// Quantidade do salt
		$salt = 2;
		
		// Monta os caracteres permitidos
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		
		// Cria a semente random
		mt_srand(10000000 * (double) microtime());
		
		// Cria o hash unico
		for ($i = 0, $str = "", $lc = strlen($chars) - 1; $i < $salt; $i ++) {
			$str .= $chars[mt_rand(0, $lc)];
		}
		
		// Cria o hash da senha com BOOM
		$password_hash = md5($str . $password) . ":" . $str;
		
		// Retorna o hash
		return $password_hash;
	}

	/**
	 * Cria strings aleatórias
	 */
	static public function randomString($size, $hasNumber=TRUE)
	{
		// Monta os caracteres permitidos
		$letters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$numbers = "0123456789";
		
		// Verifica se possui numeros
		if ($hasNumber) {
			$chars = $letters . $numbers;
		}
		
		// Cria a semente random
		mt_srand(10000000 * (double) microtime());
		
		// Cria a string aleatoria
		$str = "";
		for ($i = 0, $lc = strlen($chars) - 1; $i < $size; $i ++) {
			$str .= $chars[mt_rand(0, $lc)];
		}
		
		// Retorna a string
		return $str;
	}
	
	/**
	 * Converte strings para slugs utilizados em links
	 */
	static public function slug($string)
	{
	
		// Verifica se tem texto
		if (empty($string)) {
			return;
		}
	
		// Remove os espaços das bordas
		$string = rtrim(ltrim($string));
	
		// Decodifica o html entities
		$string = html_entity_decode($string, ENT_QUOTES, "UTF-8");
	
		// Diminui o tamanho da letra
		$string = mb_strtolower($string, "UTF-8");
	
		// Troca os caracteres especiais
		$trans = array(
			'ç' => "c",
			'á' => "a",
			'â' => "a",
			'à' => "a",
			'ã' => "a",
			'é' => "e",
			'ê' => "e",
			'è' => "e",
			'ẽ' => "e",
			'í' => "i",
			'î' => "i",
			'ì' => "i",
			'ĩ' => "i",
			'ó' => "o",
			'ô' => "o",
			'ò' => "o",
			'õ' => "o",
			'ú' => "u",
			'û' => "u",
			'ù' => "u",
			'ũ' => "u"
		);
		$string = strtr($string, $trans);
	
		// Trocar o que não é especial
		$string = preg_replace("@[^a-zA-Z0-9\_\.]@", "-", $string);
	
		// Troca varios espacos por 1 só
		$string = preg_replace("/__+/", "-", $string);
	
		// Troca varios espacos por 1 só
		$string = str_replace("--", "-", str_replace("--", "-", str_replace("--", "-", $string)));
	
		// Remove os "-" da direita
		$string = rtrim($string, "-");
	
		// Retorna o texto
		return $string;
	}
}
