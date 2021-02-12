# naverpay-order-linked  
네이버페이의 주문서 내역을 확인한다.  
[네이버페이센터](https://admin.pay.naver.com/)에서 주문내역을 볼 때 보기가 불편하여 연동해보았음.

몇 가지 절차가 필요하다.

`1) 네이버페이 API레퍼런스 검토`  
`2) SANDBOX(개발)환경 라이센스 발급`  
`3) 가맹점 개발사 연동 개발 진행`  
`4) SANDBOX환경 검수 진행`  
`5) Production(운영)환경 라이센스 발급`    
`6) 실환경 적용(주문수집 및 주문테스트 진행)`  

## Using Extension
[PEAR Message Package](https://pear.php.net/package/Message) HMAC SHA256  

## Example
#### Configure
```php
<?php

use nhn\NhnApiSCL;

class NaverPay
{
    /**
     * License Issuance Settings
     */
    const ACCESS_KEY    = 'your-access-key';
    const SECRET_KEY    = 'your-secret-key';

    // 가맹점 아이디
    const MALLID    = 'you-mall-id';
    ...
```
#### Usage
```php
<?php
require_once 'vendor/autoload.php';

$naverpay   = new NaverPay();
```






