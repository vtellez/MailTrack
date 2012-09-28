<?php
define('OPENSSO_BASE_URL', 'https://ssopre.us.es/opensso/');
define('OPENSSO_COOKIE_NAME', 'iPlanetDirectoryPro');
define('OPENSSO_LOGIN_URL',      OPENSSO_BASE_URL . 'UI/Login');
define('OPENSSO_LOGOUT_URL',     OPENSSO_BASE_URL . 'UI/Logout');

define('OPENSSO_INTERNA_URL', 'http://192.168.1.128/OPENSSO/index.php/');

define('OPENSSO_LOGOUT_SERVICE',     OPENSSO_INTERNA_URL . 'identity/logout');
define('OPENSSO_IS_TOKEN_VALID', OPENSSO_INTERNA_URL .
		'identity/isTokenValid');
define('OPENSSO_ATTRIBUTES',     OPENSSO_INTERNA_URL . 'identity/attributes');
define('OPENSSO_COOKIE_NAME_FETCH',     OPENSSO_INTERNA_URL .
		'identity/getCookieNameForToken');
define('OPENSSO_DOMAIN', ".us.es");


// Certificados
define('VALIDATE_CERT', FALSE);

