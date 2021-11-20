<?php

namespace ImAMadDev\ability\ticks;

use ImAMadDev\customenchants\CustomEnchantment;
use ImAMadDev\item\Fireworks;
use ImAMadDev\entity\projectile\FireworksRocket;
use ImAMadDev\manager\{CrateManager, AbilityManager};
use ImAMadDev\customenchants\CustomEnchantments;

use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\entity\Location;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\scheduler\Task;
use pocketmine\world\Position;
use pocketmine\block\tile\Chest;
use pocketmine\utils\TextFormat;

class AirDropTick extends Task {

    /**
     * @var Position|null $pos
     */
    protected ? Position $pos = null;

    /**
     * @var int $time
     */
    public int $time = 6;

    /**
     * @param Position $pos
     */
    public function __construct(Position $pos) {
		$this->pos = $pos;
	}

    public function onRun() : void {
		$position = $this->pos;
		if($this->time-- <= 0) {
            $block = BlockFactory::getInstance()->get(BlockLegacyIds::CHEST, 0);
            $position->getWorld()->setBlock($position->add(0, 1, 0), $block);
            $chest = $position->getWorld()->getTile($position->add(0, 1, 0));
            if ($chest instanceof Chest) {
                $chest->setName(TextFormat::colorize("&5&k!!&r&l&3Air Drops&5&k!!&r"));
            } else {
                $chest = new Chest($position->getWorld(), $position->add(0, 1, 0));
                $chest->setName(TextFormat::colorize("&5&k!!&r&l&3Air Drops&5&k!!&r"));
                $position->getWorld()->addTile($chest);
            }
            foreach ($this->getContents() as $item) {
                $chest->getInventory()->addItem($item);
            }
            $this->getHandler()->cancel();
		} else {
			$this->spawnFirework($this->pos);
		}
	}

    /**
     * @param int $count
     * @return array
     */
    public function getContents(int $count = 11): array {
		$items = [];
		$list = [];
		foreach(AbilityManager::getInstance()->getAbilities() as $ability) {
			if($ability->getName() === "Airdrops" or $ability->getName() === "RankSharp" or $ability->getName() === "PartnerPackages" or $ability->getName() === "SummerLootbox") {
				continue;
			}
			$list[] = $ability->get(rand(2, 5));
		}
		foreach(CrateManager::getInstance()->getCrates() as $crate) {
			$list[] = $crate->getCrateKey(rand(1, 3));
		}
		foreach(array_values(CustomEnchantments::getEnchantments()) as $enchant) {
			if($enchant->getName() === CustomEnchantment::STRENGTH || $enchant->getName() === CustomEnchantment::OVERLOAD) {
				continue;
			}
			$list[] = CustomEnchantments::getEnchantedBook($enchant->getName(), 2);
		}
		shuffle($list);
		for($i = 0; $i < $count; $i++){
			$items[] = $list[array_rand($list)];
		}
		return $items;
	}

    /**
     * @param Position $pos
     */
    public function spawnFirework(Position $pos) {
		$data = new Fireworks(new ItemIdentifier(ItemIds::FIREWORKS, 0));
		$data->addExplosion($data->getRandomType(), $data->getRandomColor(), "", 1, 1);
        $entity = new FireworksRocket(Location::fromObject($pos->add(0.5, 1, 0.5), $pos->getWorld(), lcg_value() * 360, 90), $data);
        $entity->spawnToAll();
		$entity->setLifeTime(1);
	}
	
}
