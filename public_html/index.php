<?php

// Força a codificação
header("Content-Type: text/html; charset=utf-8");
header("X-UA-Compatible: IE=edge,chrome=1");

date_default_timezone_set("America/Sao_Paulo");

// Seta o tipo do erro
error_reporting(E_ALL & ~ E_WARNING & ~ E_NOTICE & ~ E_STRICT);
ini_set("display_errors", "On");

// Define aspath to application directory
defined("APPLICATION_PATH") || define("APPLICATION_PATH", dirname(__FILE__) . "/../app");

// Define application environment
defined("APPLICATION_ENV") || define("APPLICATION_ENV", (getenv("APPLICATION_ENV") ? getenv("APPLICATION_ENV") : "production"));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
APPLICATION_PATH . "/src",
	APPLICATION_PATH . "/library",
	get_include_path()
)));

// Inicial a aplicação
require_once ("General/Application/Bootstrap.php");
$application = new \General\Application\Bootstrap();