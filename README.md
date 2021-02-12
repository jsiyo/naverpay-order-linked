# naverpay-order-linked  
네이버페이의 주문서 내역을 확인한다.  
[네이버페이센터]()에서 주문내역을 볼 때 보기가 불편하여 만들어보았음.
### Configure
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
    const MALLID    = 'your-id';

    public $service = 'MallService41';
    public $detail  = 'Full';
    public $version = '4.1';
    
    ...
}
```
### Usage
```php
<?php
require_once 'vendor/autoload.php';

$naverpay   = new NaverPay();

/**
 * 변경 상품 주문 내역을 조회
 * @params ISO 8601 date
 */
$naverpay->getChangedProductOrderList(
    '2021-02-13T00:00:00+09:00', '2021-02-14T00:00:00+09:00'
);

/**
 * 특정 상품 주문에 대한 상세 내역을 조회
 * @params array 상품주문번호
 */
$naverpay->getProductOrderInfoList([
    2021021270617780,
    2021021045630650
]);
```

### PEAR
[Message](https://pear.php.net/package/Message) Package HAMC SHA256








