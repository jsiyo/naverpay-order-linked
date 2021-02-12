# naverpay-order-linked
### test

### Example
```php
<?php
require_once 'vendor/autoload.php';

$naverpay = new NaverPay([
  'mallId'    => '가맹점 아이디',
  'accessKey' => 'AccessLicense Key',
  'secretKey' => 'Secret Key'
]);

$orderList  = $naverpay->getChangedProductOrderList(
  '2021-02-08T00:00:00+09:00', '2021-02-09T00:00:00+09:00'
);
// ProductOrderID
$orderListInfo   = $naverpay->getProductOrderInfoList([2021020878289900, 2021020878223900, ...]);
```

### Available API
| category | description | additional date |
| -------- | ----------- | ---------------- |
| `getChangedProductOrderList` | 변경 상품주문내역에 대한 내역조회 | 2021-02-08 |
| `getProductOrderInfoList` | 특정 상품주문건에 대한 주문내역 상세조회 | 2021-02-08 |
1
