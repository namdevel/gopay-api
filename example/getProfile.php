<?php
require('../src/GojekPay.php');

use Namdevel\GojekPay;

$access_token = 'eyJhbGciOiJSUzI1NiIsImtpZCI6IiJ9.....';
$gopay = new GojekPay($access_token);
/**
@ get Account Information
return @type json
*/
echo $gopay->getProfile();
