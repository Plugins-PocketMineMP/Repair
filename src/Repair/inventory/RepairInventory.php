<?php
declare(strict_types=1);
namespace Repair\inventory;

use pocketmine\block\BlockIds;
use pocketmine\inventory\BaseInventory;
use pocketmine\inventory\ContainerInventory;
use pocketmine\math\Vector3;
use pocketmine\nbt\LittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\types\ContainerIds;
use pocketmine\network\mcpe\protocol\types\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\Player;

class RepairInventory extends ContainerInventory{

    /** @var Vector3|null */
    protected $vector;

    protected $yes = false;

    protected $price;

    public function __construct(){
        parent::__construct(new Vector3(), [], 27);
    }

    public function getDefaultSize() : int{
        return 27;
    }

    public function getNetworkType() : int{
        return WindowTypes::CONTAINER;
    }

    public function getName() : string{
        return "RepairInventory";
    }

    public function onOpen(Player $who) : void{
        BaseInventory::onOpen($who);
        $this->setVector3($who->add(0, 3, 0)->floor());

        $pos = $this->getVector3();

        $x = $pos->getX();
        $y = $pos->getY();
        $z = $pos->getZ();

        $pk = new UpdateBlockPacket();
        $pk->x = $x;
        $pk->y = $y;
        $pk->z = $z;
        $pk->blockRuntimeId = RuntimeBlockMapping::toStaticRuntimeId(BlockIds::CHEST);
        $pk->flags = UpdateBlockPacket::FLAG_ALL;
        $who->sendDataPacket($pk);

        $nbt = new CompoundTag("", [
            new StringTag("id", "Chest"),
            new IntTag("x", $x),
            new IntTag("y", $y),
            new IntTag("z", $z),
            new StringTag("CustomName", "수리 인벤토리")
        ]);

        $pk = new BlockActorDataPacket();
        $pk->x = $x;
        $pk->y = $y;
        $pk->z = $z;
        $pk->namedtag = (new LittleEndianNBTStream())->write($nbt);
        $who->sendDataPacket($pk);

        $pk = new ContainerOpenPacket();
        $pk->windowId = $who->getWindowId($this);
        $pk->x = $x;
        $pk->y = $y;
        $pk->z = $z;
        $pk->type = ContainerIds::INVENTORY;
        $who->sendDataPacket($pk);

        $this->sendContents($who);
    }

    public function onClose(Player $who) : void{
        BaseInventory::onClose($who);
        $pos = $this->getVector3();

        $x = $pos->getX();
        $y = $pos->getY();
        $z = $pos->getZ();

        $block = $who->getLevel()->getBlock(new Vector3($x, $y, $z));

        $pk = new UpdateBlockPacket();
        $pk->x = $x;
        $pk->y = $y;
        $pk->z = $z;
        $pk->blockRuntimeId = RuntimeBlockMapping::toStaticRuntimeId($block->getId(), $block->getDamage());
        $pk->flags = UpdateBlockPacket::FLAG_ALL;
        $who->sendDataPacket($pk);

        $pk = new ContainerClosePacket();
        $pk->windowId = $who->getWindowId($this);
        $who->sendDataPacket($pk);
    }

    public function setVector3(?Vector3 $pos) : void{
        $this->vector = $pos;
    }

    public function getVector3() : ?Vector3{
        return $this->vector;
    }

    public function isYes() : bool{
        return $this->yes;
    }

    public function setYes(bool $v) : void{
        $this->yes = $v;
    }

    public function setPrice(int $price) : void{
        $this->price = $price;
    }

    public function getPrice() : int{
        return $this->price;
    }
}