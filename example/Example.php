<?php
require('../src/GojekPay.php');

use Namdevel\GojekPay;

$access_token = 'eyJhbGciOiJSUzI1NiIsImtpZCI6IiJ9.....';
$gopay = new GojekPay($access_token);

echo $gopay->getProfile();
echo $gopay->isGojek('<phoneNumber>');
echo $gopay->getBankList();
echo $gopay->getHistory();
echo $gopay->isBank('<bankCode>', '<bank_account_number>');
echo $gopay->getBankAccountName('<bankCode>', '<bank_account_number>');
echo $gopay->getBalance();
echo $gopay->transferGopay('<phoneNumber>', '<amount>', '<pin>');
echo $gopay->getQrid('<phoneNumber>');
echo $gopay->transferBank('<bankCode>', '<bank_account_number>', '<amount>', '<pin>');
