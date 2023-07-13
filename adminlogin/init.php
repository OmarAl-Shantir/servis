<?php
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/../' );
}
$constants = array("YEAR" => date("Y"));
if ( ! defined( 'HOMEPAGE' ) ) {
	define( 'HOMEPAGE', 'https://'.$_SERVER['SERVER_NAME']."/adminlogin/" );
	$constants['homepage'] = 'https://'.$_SERVER['SERVER_NAME'];
}

require_once ABSPATH.'config.php';
require_once __DIR__.'/class-autoloader-admin.php';

//phpMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'theme/vendor/phpmailer/Exception.php';
require_once 'theme/vendor/phpmailer/PHPMailer.php';
require_once 'theme/vendor/phpmailer/POP3.php';
require_once 'theme/vendor/phpmailer/SMTP.php';
//
if(!isset($configView)){
	$configView = new ConfigView();
	$data = $configView->getAllConfig();;
	foreach ($data as $name => $value) {
		if(!defined($name)){
			define(strtoupper($name), $value['value']);
			$constants[strtoupper($name)] = $value['value'];
		}
	}
}

if ( ! defined( 'DEFINED_CONSTANTS' ) ) {
	define( 'DEFINED_CONSTANTS', $constants );
}
