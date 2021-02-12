# naverpay-order-linked  
네이버페이의 주문내역을 확인합니다.  

## Example
```php
<?php

use nhn\NhnApiSCL;

class NaverPay
{
    /**
     * License Issuance Settings
     */
    const ACCESS_KEY    = 'YOUR ACCESS KEY';
    const SECRET_KEY    = 'YOUR SECRET KEY';

    // 가맹점 아이디
    const MALLID    = 'YOUR ID';

    public $service = 'MallService41';
    public $detail  = 'Full';
    public $version = '4.1';
    
    ...
}
```

## PEAR
[Message](https://pear.php.net/package/Message) Package HAMC SHA256








