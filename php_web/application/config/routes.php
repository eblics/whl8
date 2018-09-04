<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'index';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['api/scan'] = 'Scan_api/scan';
$route['api/auth/get'] = 'Scan_api/get_auth';
