<?php

namespace General\Helpers;

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\SMTP;

/**
 * Classe para manipulação de senhas
 */
final class Mail
{
	/**
	 * Construtor da classe
	 */
	public function __construct()
	{
		$email = "agencia.general.mail@gmail.com";
		$senha = "Kiksuwjjjaksisk";
		$host = "smtp.gmail.com";
		$port = 587;

		$this->mail = new PHPMailer();

		$this->mail->isSMTP();
		$this->mail->SMTPDebug = 0;
		$this->mail->Host = $host;
		$this->mail->Port = $port;

		$this->mail->SMTPSecure = "tls";

		$this->mail->SMTPAuth = true;
		$this->mail->Username = $email;
		$this->mail->Password = $senha;
	}

	public function setTo($email, $name="")
	{
		$this->mail->addAddress($email, $name);
		
		return $this;
	}

	public function setFrom($email, $name="")
	{
		$this->mail->setFrom($email, $name);
		
		return $this;
	}

	public function addBcc($email, $name="")
	{
		$this->mail->addBCC($email, $name);
		
		return $this;
	}

	public function addCc($email, $name="")
	{
		$this->mail->addCC($email, $name);
		
		return $this;
	}

	public function setReplyto($email, $name="")
	{
		$this->mail->addReplyto($email, $name);
		
		return $this;
	}

	public function setHtml($html)
	{
		$this->msgHTML($html);
		
		return $this;
	}

	public function setSubject($assunto)
	{
		$this->mail->Subject = $assunto;
		
		return $this;
	}

	public function setTemplate($path, $vars)
	{
		// Cria o view
		$view = new \General\Mvc\View();

		// Assina as variaveis
		foreach ($vars as $var => $value) {
			$view->assign($var, $value);
		}
		
		// Recupera o html
		$html = $view->fetch($path);
		$this->mail->msgHTML($html);

		return $this;
	}

	public function send()
	{
		if (!$this->mail->send()) {
			throw new \Exception($this->mail->ErrorInfo);
		}

		return TRUE;
	}
}