<?php
declare(strict_types=1);
namespace Repair;

use pocketmine\item\Durable;
use pocketmine\item\TieredTool;

class Util{

    public static function getTieredToolPrice(Durable $item){
        $price = 0;
        if($item instanceof TieredTool){
            switch($item->getTier()){//아이템 종류 확인
                case TieredTool::TIER_WOODEN:
                    $price += 300;
                    break;
                case TieredTool::TIER_STONE:
                    $price += 500;
                    break;
                case TieredTool::TIER_IRON:
                    $price += 700;
                    break;
                case TieredTool::TIER_GOLD:
                    $price += 900;
                    break;
                case TieredTool::TIER_DIAMOND:
                    $price += 1000;
                    break;
                default:
                    throw new \InvalidStateException("Unknown tier {$item->getTier()}");
            }
            $damage = $item->getDamage();
            /**
             * Gold: 33
             * Wooden: 60
             * Stone: 132
             * Iron: 251
             * Diamond: 1562
             *
             * Source: 나무위키
             */
            switch($item->getTier()){//아이템 내구도 확인
                case TieredTool::TIER_GOLD:

                    /**
                    echo "Before: " . $price . PHP_EOL;
                    if($damage >= 30 and $damage < 33){
                        $price += mt_rand(120, 170);
                    }elseif($damage >= 20 and $damage < 30){
                        $price += mt_rand(70, 120);
                    }elseif($damage >= 10 and $damage < 20){
                        $price += mt_rand(100, 150);
                    }else{
                        $price += mt_rand(50, 100);
                    }
                    echo "After: " . $price . PHP_EOL;
                     */
                    $price += $damage * mt_rand(1, 3);
                    break;
                case TieredTool::TIER_STONE:
                    /**
                    echo "Before: " . $price . PHP_EOL;
                    if($damage >= 130 and $damage < 132){
                        $price += mt_rand(200, 300);
                    }elseif($damage >= 100 and $damage < 130){
                        $price += mt_rand(90, 130);
                    }elseif($damage >= 70 and $damage < 100){
                        $price += mt_rand(130, 150);
                    }elseif($damage >= 40 and $damage < 70){
                        $price += mt_rand(70, 90);
                    }elseif($damage >= 10 and $damage < 40){
                        $price += mt_rand(150, 200);
                    }else{
                        $price += mt_rand(50, 70);
                    }
                    echo "After: " . $price . PHP_EOL;
                     */
                    $price += $damage * mt_rand(1, 3);
                    break;
                case TieredTool::TIER_IRON:
                    /**
                    echo "Before: " . $price . PHP_EOL;
                    if($damage >= 249 and $damage < 251){
                        $price += mt_rand(250, 300);
                    }elseif($damage >= 240 and $damage < 249){
                        $price += mt_rand(400, 500);
                    }elseif($damage >= 200 and $damage < 240){
                        $price += mt_rand(100, 150);
                    }elseif($damage >= 100 and $damage < 200){
                        $price += mt_rand(150, 200);
                    }elseif($damage >= 50 and $damage < 100){
                        $price += mt_rand(200, 250);
                    }else{
                        $price += mt_rand(50, 100);
                    }
                    echo "After: " . $price . PHP_EOL;
                     */
                    $price += $damage * mt_rand(1, 3);
                    break;
                case TieredTool::TIER_WOODEN:
                    /**
                    echo "Before: " . $price . PHP_EOL;
                    if($damage >= 58 and $damage < 60){
                        $price += 60;
                    }elseif($damage >= 50 and $damage < 58){
                        $price += mt_rand(40, 50);
                    }elseif($damage >= 30 and $damage < 50){
                        $price += mt_rand(30, 40);
                    }elseif($damage >= 10 and $damage < 30){
                        $price += mt_rand(20, 30);
                    }else{
                        $price += mt_rand(10, 20);
                    }
                    echo "After: " . $price . PHP_EOL;
                     */
                    $price += $damage * mt_rand(1, 3);
                    break;
                case TieredTool::TIER_DIAMOND:
                    /**
                    echo "Before: " . $price . PHP_EOL;
                    if($damage >= 1560 and $damage < 1562){
                        $price += 1000;
                    }elseif($damage >= 1300 and $damage < 1560){
                        $price += mt_rand(500, 600);
                    }elseif($damage >= 1000 and $damage < 1300){
                        $price += mt_rand(300, 400);
                    }elseif($damage >= 500 and $damage < 1000){
                        $price += mt_rand(200, 300);
                    }elseif($damage >= 100 and $damage < 500){
                        $price += mt_rand(100, 200);
                    }else{
                        $price += 1000;
                    }
                    echo "After: " . $price . PHP_EOL;
                     */
                    $price += $damage * mt_rand(1, 3);
                    break;
                default:
                    throw new \InvalidStateException("Unknown tier {$item->getTier()}");
            }
        }else{
            $damage = $item->getDamage();
            $price += ($damage + mt_rand(5, 10)) * mt_rand(1, 3);
        }
        $price += mt_rand(50, 100);
        return $price;
    }
}