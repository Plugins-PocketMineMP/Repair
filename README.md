# Repair

A PocketMine-MP Plugin | Repair

사용법: /수리

손에 든 아이템을 수리합니다.

수리 가능한 아이템:

`Durable` 을 상속받은 모든 __아이템__

만약 수리할 아이템의 가격을 `플러그인` 으로 얻어오고 싶으시다면,

```php
use Repair\Util;

$price = Util::getTieredToolPrice(\pocketmine\item\Durable);
```
를 이용해서 얻어오시면 됩니다.