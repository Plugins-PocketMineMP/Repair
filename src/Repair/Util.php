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
            /*
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
                    $price += $damage * 2;
                    break;
                case TieredTool::TIER_STONE:
                    $price += $damage * 2;
                    break;
                case TieredTool::TIER_IRON:
                    $price += $damage * 2;
                    break;
                case TieredTool::TIER_WOODEN:
                    $price += $damage * 2;
                    break;
                case TieredTool::TIER_DIAMOND:
                    $price += $damage * 2;
                    break;
                default:
                    throw new \InvalidStateException("Unknown tier {$item->getTier()}");
            }
        }else{
            $damage = $item->getDamage();
            $price += ($damage + 2)) * 2;
        }
        $price += 1000;
        return $price;
    }
}
