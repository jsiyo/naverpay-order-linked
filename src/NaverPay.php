<?php

use nhn\NhnApiSCL;

class NaverPay
{
    /**
     * License Issuance Settings
     */
    const ACCESS_KEY    = '0100010000c06f3f6c31b931b372a7fb008fde7fca3db207afd3de70e296a305ba453bd3d7';
    const SECRET_KEY    = 'AQABAABKekOCXYMpFHH5ZTi/kEGxzTppMe/lZ3fDo6B4JoyHRQ==';

    // 가맹점 아이디
    const MALLID    = 'np_pdved060828';

    public $service = 'MallService41';
    public $detail  = 'Full';
    public $version = '4.1';

    /**
     * SANDBOX/PRODUCTION
     * SANDBOX: http://sandbox.api.naver.com/Checkout
     * PRODUCTION: http://ec.api.naver.com/Checkout
     */
    private $uri = 'http://ec.api.naver.com/Checkout';    

    private $operation;    
    private $timestamp;

    private $scl;

    public function __construct()
    {
        $this->scl  = new NhnApiSCL();
    }

    /**
     * Create Signature
     */
    private function setSignature()
    {
        $this->timestamp    = $this->scl->getTimestamp();
        $signature  = $this->scl->generateSign(
            $this->timestamp . $this->service . $this->operation,
            self::SECRET_KEY
        );
        return $signature;
    }

    /**
     * SOAP Common Message Structure
     * @param Signature Value, message
     */
    private function setBaseResponse($signature, array $message)
    {
        $requestMessage    = "
        <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:mall=\"http://mall.checkout.platform.nhncorp.com/\" xmlns:base=\"http://base.checkout.platform.nhncorp.com/\">
            <soapenv:Header/>
            <soapenv:Body>  
                <mall:{$this->operation}Request>
                    <base:AccessCredentials>
                        <base:AccessLicense>". self::ACCESS_KEY ."</base:AccessLicense>
                        <base:Timestamp>". $this->timestamp ."</base:Timestamp>
                        <base:Signature>". $signature ."</base:Signature>
                    </base:AccessCredentials>                
                    <base:RequestID/>
                    <base:DetailLevel>". $this->detail ."</base:DetailLevel>
                    <base:Version>". $this->version ."</base:Version>";
                    if (empty($message) == false) {
                        foreach ($message as $value) {
                            $requestMessage .= $value;
                        }
                    }
        $requestMessage .= "                    
                </mall:{$this->operation}Request>
            </soapenv:Body>
        </soapenv:Envelope>";

        return $requestMessage;
    }

    /**
     * 변경 상품 주문 내역을 조회
     * @param ISO 8601 date (2021-02-10T12:45:21+09:00) 조회 시작, 종료일
     */
    public function getChangedProductOrderList($timeFrom, $timeTo)
    {
        $this->operation    = 'GetChangedProductOrderList';
        $signature  = $this->setSignature();    
        $message    = [
            '<base:InquiryTimeFrom>'. $timeFrom .'</base:InquiryTimeFrom>',
            '<base:InquiryTimeTo>'. $timeTo .'</base:InquiryTimeTo>'
        ];
        
        return $this->sendRequest(
            $this->setBaseResponse($signature, $message)
        );
    }

    /**
     * 특정 상품 주문에 대한 상세 내역을 조회
     * @param array 상품 주문 번호
     */
    public function getProductOrderInfoList(array $ProductOrderID)
    {
        $this->operation    = 'GetProductOrderInfoList';
        $signature  = $this->setSignature();
        $message    = [];
        if (empty($ProductOrderID) == false) {
            foreach ($ProductOrderID as $pid) {                
                $message[]  = "<mall:ProductOrderIDList>{$pid}</mall:ProductOrderIDList>";
            }
        }
        return $this->sendRequest(
            $this->setBaseResponse($signature, $message)
        );        
    }

    /**
     * 데이터 암호화
     * @param string text
     */
    public function encrypt($text)
    {
        $secret = $this->scl->generateKey($this->timestamp, self::SECRET_KEY);
        return $this->scl->encrypt(
            $secret, $text
        );
    }

    /**
     * 데이터 복호화
     * @param string text
     */
    public function decrypt($text)
    {
        $secret = $this->scl->generateKey($this->timestamp, self::SECRET_KEY);
        return $this->scl->decrypt(
            $secret, $text
        );
    }

    /**
     * Request to send data
     * @param SOAP message
     */
    private function sendRequest($req)
    {
        $ch = curl_init();
        $options    = [
            CURLOPT_URL => sprintf('%s/%s', $this->uri, $this->service),
            CURLOPT_HTTPHEADER  => [
                'Content-Type: text/xml;charset=utf-8',
                "SOAPAction: {$this->service}#{$this->operation}"
            ],            
            CURLOPT_POST        => true,
            CURLOPT_POSTFIELDS  => $req,
            CURLOPT_RETURNTRANSFER  => true
        ];
        curl_setopt_array($ch, $options);
        $response   = curl_exec($ch);
		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
			return simplexml_load_string(
				preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response)
			);
		}        
        curl_close($ch);
    }
}