<?php
define('OPENSSO_BASE_URL', 'https://opensso-pre.us.es/opensso/');
define('OPENSSO_COOKIE_NAME', 'iPlanetDirectoryPro');
define('OPENSSO_LOGIN_URL',      OPENSSO_BASE_URL . 'UI/Login');
define('OPENSSO_LOGOUT_URL',     OPENSSO_BASE_URL . 'UI/Logout');
define('OPENSSO_LOGOUT_SERVICE',     OPENSSO_BASE_URL . 'identity/logout');
define('OPENSSO_IS_TOKEN_VALID', OPENSSO_BASE_URL .
		'identity/isTokenValid');
define('OPENSSO_ATTRIBUTES',     OPENSSO_BASE_URL . 'identity/attributes');
define('OPENSSO_COOKIE_NAME_FETCH',     OPENSSO_BASE_URL .
		'identity/getCookieNameForToken');
define('OPENSSO_DOMAIN', ".us.es");


// Certificados
define('VALIDATE_CERT', TRUE);
define('SELF_SIGNED', TRUE);
define('CRT_SERIALNUMBER', '0');

