<?php

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

if ( file_exists( ABSPATH . 'config.php' ) ) {
	require_once ABSPATH . 'config.php';
}
require_once ABSPATH.'class-autoloader.php';
