<?php

namespace Main\Controller;

use Main\Model;
use General\Db\Adapter;

/**
 * Controlador padrão
 */
class ErrorsController extends \General\Mvc\Controller
{
	/**
	 * Ação do denied
	 */
	public function errorAction()
	{
		$this->view()->disableTemplate();

		if($this->view()->exception->getCode() == 404) {
			header("HTTP/1.1 404 Not Found");
		}
		else {
			header("HTTP/1.1 500 Internal server error");
		}
	}
}
