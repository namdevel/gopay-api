<?php
namespace Namdevel;
/**
 * [Gojek] Gopay Api PHP Class (Un-Official)
 * Author : namdevel <https://github.com/namdevel>
 * Created at 22-04-2020 14:26
 * Last Modified at 05-05-2020 18:26
 */
class GojekPay
{
    const ApiUrl = 'https://goid.gojekapi.com';
    const Api2Url = 'https://api.gojekapi.com';
    const appId = 'com.go-jek.ios';
    const phoneModel = 'Apple, iPhone11,6';
    const phoneMake = 'Apple';
    const osDevice = 'iOS, 13.3.1';
    const xPlatform = 'iOS';
    const appVersion = '3.51';
    const clientId = 'gojek:consumer:app';
    const clientSecret = 'pGwQ7oi8bKqqwvid09UrjqpkMEHklb';
    const userAgent = 'Gojek/3.51 (com.go-jek.ios; build:6890866; iOS 13.3.1) Alamofire/3.51';

    private $authToken;
    private $pin;
    private $sessionId;
    private $uniqueId;
    
    public function __construct($token = false)
    {
        $this->sessionId = '78EB815C-6AE5-4969-A6B1-BE5EC893F7AA'; // generated from self::uuidv4();
        $this->uniqueId = '5C816FEA-D93E-4910-B672-978FCFA992F2'; // generated from self::uuidv4();
        
        if ($token) {
            $this->authToken = $token;
        }
    }
    
    public function uuidv4()
    {
        $data    = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return strtoupper(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)));
    }
    
    public function transferBank($bankCode, $bankNumber, $amount, $pin)
    {
        $bankAccountName = self::getBankAccountName($bankCode, $bankNumber);
        $payload = array(
            'bank_code' => $bankCode,
            'bank_account_number' => $bankNumber,
            'amount' => $amount,
            'bank_account_name' => $bankAccountName
        );
        self::setPin($pin);
        return self::Request(self::Api2Url . '/v3/wallet/withdrawal/request', $payload, self::buildHeaders());
    }

    public function transferGopay($phoneNumber, $amount, $pin)
    {
        self::setPin($pin);
        $payload = array(
            'qr_id' => self::getQrid($phoneNumber) ,
            'amount' => $amount,
            'description' => '💰'
        );

        return self::Request(self::Api2Url . '/v2/fund/transfer', $payload, self::buildHeaders());
    }

    public function getQrid($phoneNumber)
    {
        return self::getResponse(self::isGojek($phoneNumber), 'qr_id');
    }

    public function getBankAccountName($bankCode, $bankNumber)
    {
        return self::getResponse(self::isBank($bankCode, $bankNumber), 'bank_account_name');
    }

    protected function setPin($pin)
    {
        $this->pin = $pin;
    }

    public function getRealAmount($amount)
    {
        return self::getResponse(self::Request(self::Api2Url . '/wallet/withdrawal/request?amount=' . $amount, false, self::buildHeaders()), 'total_amount');
    }

    public function getBankList()
    {
        return self::Request(self::Api2Url . '/v1/withdrawal/banks', false, self::buildHeaders());
    }

    public function getHistory($page = 1, $limit = 20)
    {
        return self::Request(self::Api2Url . "/wallet/history?page={$page}&limit={$limit}", false, self::buildHeaders());
    }

    public function getProfile()
    {
        return self::Request(self::Api2Url . '/gojek/v2/customer', false, self::buildHeaders());
    }

    public function getBalance()
    {
        return self::Request(self::Api2Url . '/wallet/profile', false, self::buildHeaders());
    }

    public function isGojek($phoneNumber)
    {
        return self::Request(self::Api2Url . '/wallet/qr-code?phone_number=' . urlencode($phoneNumber), false, self::buildHeaders());
    }

    public function getAuthToken($otpToken, $otpCode)
    {
        $payload = array(
            'data' => array(
                'otp_token' => $otpToken,
                'otp' => $otpCode
            ) ,
            'client_id' => self::clientId,
            'grant_type' => 'otp',
            'client_secret' => self::clientSecret
        );

        return self::Request(self::ApiUrl . '/goid/token', $payload, self::buildHeaders());
    }

    public function isBank($bankCode, $bankNumber)
    {
        $payload = array(
            'bank_code' => $bankCode,
            'bank_account_number' => $bankNumber
        );

        return self::Request(self::Api2Url . '/v1/withdrawal/account/validate', $payload, self::buildHeaders());
    }

    public function loginRequest($phoneNumber)
    {
        $payload = array(
            'client_id' => self::clientId,
            'client_secret' => self::clientSecret,
            'country_code' => '+62',
            'phone_number' => $phoneNumber
        );

        return self::Request(self::ApiUrl . '/goid/login/request', $payload, self::buildHeaders());
    }

    protected function buildHeaders()
    {
        $headers = array(
            'x-appid: ' . self::appId,
            'x-phonemodel: ' . self::phoneModel,
            'user-agent: ' . self::userAgent,
            'x-session-id: ' . $this->sessionId,
            'x-phonemake: ' . self::phoneMake,
            'x-uniqueid: ' . $this->uniqueId,
            'x-deviceos: ' . self::osDevice,
            'x-platform: ' . self::xPlatform,
            'x-appversion: ' . self::appVersion,
            'accept: */*',
            'content-type: application/json',
            'x-user-type: customer'
        );

        if (!empty($this->authToken)) {
            array_push($headers, 'Authorization: Bearer ' . $this->authToken);
        }
        if (!empty($this->pin)) {
            array_push($headers, 'pin: ' . $this->pin);
        }
        return $headers;
    }

    protected function Request($url, $post = false, $headers = false)
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
        ));

        if ($post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        }

        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function getResponse($response, $key)
    {
        $json = json_decode($response, true);
        return $json['data'][$key];
    }
}
