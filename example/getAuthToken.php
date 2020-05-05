<?php 
require('../src/GojekPay.php');

use Namdevel\GojekPay;

$gopay = new GojekPay();
/** 
@ step 1
return @type json contain <otpToken> 
*/
echo $gopay->loginRequest('<phoneNumber>'); 
/** 
@ step 2
return @type json contain <access_token> 
*/
echo $gopay->getAuthToken('<otpToken>', '<otpCode>');