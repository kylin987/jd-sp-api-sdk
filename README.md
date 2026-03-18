# JD SP-API SDK PHP

京东商家开放平台 SP-API PHP SDK

## 安装

```bash
composer require kylin987/jd-sp-api-sdk
```

## 目录结构

```
lib/
├── ApiException.php          # API 异常处理
├── Configuration.php         # 配置类
├── HeaderSelector.php        # 请求头选择器
├── ObjectSerializer.php      # 对象序列化
├── SignatureCalculator.php   # 签名计算器
├── open/                     # 开放平台安全 API
│   └── security/v0/
└── sp/                       # 业务 SP-API
    ├── address/v0/          # 地址服务
    ├── aftercare/v0/        # 售后服务
    ├── c2m/v0/              # 定制化服务
    ├── finance/v0/          # 财务
    ├── order/v0/            # 订单
    ├── product/v0/          # 商品
    ├── seller/v0/           # 店铺
    └── support/v0/          # 客服
```

## 使用示例

```php
<?php
require_once 'vendor/autoload.php';

use JdSpApi\Sp\Order\V0\Api\OrdersApi;
use JdSpApi\Configuration;

$config = Configuration::getDefaultConfiguration()
    ->setAccessToken('YOUR_ACCESS_TOKEN')
    ->setAppKey('YOUR_APP_KEY')
    ->setAppSecret('YOUR_APP_SECRET');

$ordersApi = new OrdersApi(null, $config);
$response = $ordersApi->listOrders(...);
```

## License

MIT
