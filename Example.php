<?php
require 'vendor/autoload.php';

use Namdevel\GojekPay;
$gopay = new GojekPay();

$access_token = 'eyJhbGciOiJSUzI1NiIsImtpZCI6IiJ9.....';
$gopay = new GojekPay($access_token);

echo $gopay->getTransactionHistory();

echo $gopay->getBalance();

echo $gopay->getProfile();

echo $gopay->goClubMembership();

echo $gopay->paylaterProfile();

echo $gopay->kycStatus();

echo $gopay->isGojek('<+628xxxxxxxxxx>'); // include +62

echo $gopay->getQrid('+628xxxxxxxxxx'); // include +62

echo $gopay->getBankList(); // get bank code

echo $gopay->isBank('<bankcode: bri/bca/mandiri/btpn>', '<nomor_rekening>');

echo $gopay->transferGopay('<+628xxxxxxxxxx>', 10000, '<pin_gopay>'); // include +62

echo $gopay->transferBank('<bri/bca/mandiri/btpn>', '<nomor_rekening>', 10000, '<pin_gopay>');

echo $gopay->transferBankDetail('<request_id>');
