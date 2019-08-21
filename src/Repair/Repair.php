<?php
declare(strict_types=1);
namespace Repair;

use onebone\economyapi\EconomyAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Durable;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\level\sound\AnvilUseSound;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use Repair\inventory\RepairInventory;

class Repair extends PluginBase implements Listener{

    private static $instance = null;

    public function onLoad(){
        self::$instance = $this;
    }

    public function getInstance() : Repair{
        return self::$instance;
    }

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        if(!class_exists(EconomyAPI::class)){
            throw new \RuntimeException("Could not find " . EconomyAPI::class);
        }
    }

    public function getEconomyAPI() : EconomyAPI{
        return EconomyAPI::getInstance();
    }

    public function sendInventory(Player $who, Durable $repairItem) : void{
        $inv = new RepairInventory();
        $item = ItemFactory::get(ItemIds::END_CRYSTAL, 0, 1);
        $item->setCustomName("§f");
        for($i = 0; $i < 9; $i++){
            $inv->setItem($i, $item);
        }

        for($i = 18; $i < 27; $i++){
            $inv->setItem($i, $item);
        }
        $inv->setItem(9, $item);
        $inv->setItem(17, $item);

        $inv->setItem(4, $repairItem);
        $yes = ItemFactory::get(ItemIds::WOOL, 5, 1);
        $no = ItemFactory::get(ItemIds::WOOL, 14, 1);
        $yes->setNamedTagEntry(new StringTag("yes", "YES OR YES"));
        $no->setNamedTagEntry(new StringTag("no", "NONONONONONONO"));
        $yes->setCustomName("수리 하기");
        $yes->setLore(["수리 비용: " . $price = Util::getTieredToolPrice($repairItem)]);
        $no->setCustomName("취소 하기");
        $inv->setItem(20, $no);
        $inv->setItem(24, $yes);
        $who->getInventory()->removeItem($repairItem);
        $inv->setPrice($price);
        $who->addWindow($inv);
    }

    public function handleTransaction(InventoryTransactionEvent $event){
        $player = $event->getTransaction()->getSource();
        foreach($event->getTransaction()->getActions() as $action){
            if($action instanceof SlotChangeAction){
                $inv = $action->getInventory();
                if($inv instanceof RepairInventory){
                    $item = $action->getSourceItem();
                    $event->setCancelled();
                    if($item->getNamedTagEntry("yes") !== null){
                        $inv->setYes(true);
                        $inv->onClose($player);
                        return;
                    }
                    if($item->getNamedTagEntry("no") !== null){
                        $inv->setYes(false);
                        $inv->onClose($player);
                        return;
                    }
                }
            }
        }
    }

    public function onClose(InventoryCloseEvent $event){
        $player = $event->getPlayer();
        $inv = $event->getInventory();
        if($inv instanceof RepairInventory){
            if($inv->isYes()){
                $item = $inv->getItem(4);
                if($item instanceof Durable){
                    $eco = $this->getEconomyAPI();
                    $price = $inv->getPrice();
                    if($eco->reduceMoney($player, $price) !== $eco::RET_SUCCESS){
                        $player->sendMessage(TextFormat::RED . "돈이 부족합니다.");
                        $player->getInventory()->addItem($item);
                        return;
                    }
                    $item->setDamage(0);
                }
                $player->getLevel()->addSound(new AnvilUseSound($player));
                $player->getInventory()->addItem($item);
            }else{
                $item = $inv->getItem(4);
                $player->getInventory()->addItem($item);
            }
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(!$sender instanceof Player){
            return true;
        }
        $item = $sender->getInventory()->getItemInHand();
        if($item->isNull()){
            $sender->sendMessage(TextFormat::RED . "수리할 아이템은 '도구' 여야 합니다.");
            return true;
        }
        if(!$item instanceof Durable){
            $sender->sendMessage(TextFormat::RED . "수리할 아이템은 '도구' 여야 합니다.");
            return true;
        }
        $this->sendInventory($sender, $item);
        return true;
    }
}