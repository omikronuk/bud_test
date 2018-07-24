<?php
/**
 * Created by PhpStorm.
 * User: kwaku
 * Date: 23/07/2018
 * Time: 16:10
 */
session_start();
$app_dir = __DIR__;
$_ENV['config'] = parse_ini_file("$app_dir/../config.ini", true);

// Some Global Definitions

define('DS_BASE_URL', $_ENV['config']['death_star']['base_url']);
define('DS_CLIENT_SECRET', $_ENV['config']['death_star']['client_secret']);
define('DS_CLIENT_ID', $_ENV['config']['death_star']['client_id']);
define('DS_TOKEN_URL', $_ENV['config']['death_star']['token_url']);
define('DS_REACTOR_URL', $_ENV['config']['death_star']['reactor_url']);
define('DS_PRISONER_URL', $_ENV['config']['death_star']['prisoner_url']);
define('SSL_CERT', "$app_dir'/../".$_ENV['config']['death_star']['cert']);
define('CERT_KEY', "$app_dir'/../".$_ENV['config']['death_star']['cert_key']);
define('TIMEOUT', $_ENV['config']['death_star']['timeout']);
