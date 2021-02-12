# naverpay-order-linked  
네이버페이의 주문내역을 확인함  
### Example
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
### PEAR
[Message](https://pear.php.net/package/Message) Package HAMC SHA256








