<?php

declare(strict_types=1);

namespace floatingtext;

use pocketmine\item\ItemFactory;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
use pocketmine\network\mcpe\protocol\types\entity\FloatMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\LongMetadataProperty;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use Ramsey\Uuid\Uuid;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataTypes;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class FloatingTextApi {

    /** @var array $texts */
    private static array $texts = [];

    /**
     * @param Vector3 $pos
     * @return int
     */
    public static function createText(Vector3 $pos): int {
        $eid = Entity::nextRuntimeId();
        $pk = new AddPlayerPacket();
        $pk->username = "Loading";
        $pk->uuid = Uuid::uuid4();
        $pk->actorRuntimeId = $eid;
        $pk->actorUniqueId = $eid;
        $pk->position = $pos->add(0.5, 0, 0.5);
        $pk->platformChatId = "";
        $pk->headYaw = 0;
        $pk->item = ItemStackWrapper::legacy(TypeConverter::getInstance()->coreItemStackToNet(ItemFactory::air()));
        $flags =
            1 << EntityMetadataFlags::CAN_SHOW_NAMETAG |
            1 << EntityMetadataFlags::ALWAYS_SHOW_NAMETAG |
            1 << EntityMetadataFlags::IMMOBILE;
        $pk->metadata = [
            EntityMetadataProperties::FLAGS => new LongMetadataProperty($flags),
            EntityMetadataProperties::SCALE => new FloatMetadataProperty(0.01), //zero causes problems on debug builds
            EntityMetadataProperties::BOUNDING_BOX_WIDTH => new FloatMetadataProperty(0.2),
			EntityMetadataProperties::BOUNDING_BOX_HEIGHT => new FloatMetadataProperty(0.2)
        ];
        $pk->adventureSettingsPacket = AdventureSettingsPacket::create(0, 0, 0, 0, 0, $eid);
        self::$texts[$eid] = $pk;

        return $eid;
    }

    /**
     * @param int $eid
     * @param Player $player
     * @param string $text
     */
    public static function sendText(int $eid, Player $player, string $text = "Text") {
        /** @var AddPlayerPacket $pk */
        $pk = clone self::$texts[$eid];
        $pk->username = TextFormat::colorize($text);

        $player->getNetworkSession()->sendDataPacket($pk);
    }
    
    /**
     * @param int $eid
     * @param Player $player
     */
    public static function moveText(int $eid, Player $player) {
    	$directionVector = $player->getDirectionVector()->multiply(2);
        $pk = new MoveActorAbsolutePacket();
        $pk->actorRuntimeId = $eid;
        $pk->position = $player->getPosition()->asVector3()->add($directionVector->getX(), $player->getEyeHeight(), $directionVector->getZ());
        $pk->yaw = $player->getLocation()->getYaw();
        $pk->headYaw = $player->getLocation()->getYaw();
        $pk->flags = MoveActorAbsolutePacket::FLAG_TELEPORT;
        $player->getNetworkSession()->sendDataPacket($pk);
    }
    
    

    /**
     * @param int $eid
     * @param Player $player
     */
    public static function removeText(int $eid, Player $player) {
        $pk = new RemoveActorPacket();
        $pk->actorUniqueId = $eid;
        $player->getNetworkSession()->sendDataPacket($pk);
    }
}