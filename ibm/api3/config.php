<?php

$config = array(
    // SugarCRM settings
    'sugar_user' => 'admin',
    'sugar_password' => '111111',
    'grant_type' => 'password',
    'client_id' => 'sc_web',
    'sugar_encryption' => '', // set to PLAIN or 3DES for LDAP auth
    'sugar_encryption_key' => '', // LDAP encryption key for 3DES
    'sugar_url' => 'http://jus/sugarcrm',
    'sugar_api_version' => 'v10',

    // XDebug settings
    'xdebug' => false,
    'xdebug_idekey' => 'phpstorm-key',

    // runtime config overrides
    'php_ini' => array(
        'default_socket_timeout' => 1200,
    ),
);
