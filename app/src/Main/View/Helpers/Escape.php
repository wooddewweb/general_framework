<?php

namespace Main\View\Helpers;

use General\Mvc\ViewHelpers\Helper;
use General\Application\Services;

/**
 * Classe abstrata para os viewhelpers
 *
 * @author Bruno P. Gonçalves
 */
class Escape extends Helper
{
	/**
	 * Executa o helper
	 * 
	 * @return string
	 */
	public function call($string)
	{
		$output = htmlspecialchars($string);
		
		return $output;
	}
}