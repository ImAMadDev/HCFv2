<?php

namespace ImAMadDev\kit;

use ImAMadDev\HCF;
use ImAMadDev\manager\KitManager;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\InventoryUtils;
use formapi\CustomForm;
use pocketmine\player\Player;
use pocketmine\entity\effect\{
	StringToEffectParser,
	EffectInstance
};
use pocketmine\utils\{
	Limits,
	TextFormat
};
class ClassCreatorSession
{
	
	public static array $effectsNames = [
		"absorption", "blindness", 
		"fire_resistance", "haste", "health_boost", "hunger", 
		"invisibility", "jump_boost", "levitation", "mining_fatigue", 
		"nausea", "night_vision", "poison", "regeneration", 
		"resistance", "saturation", "slowness", "speed", 
		"strength", "water_breathing", "weakness", "wither"];
		
	public static array $translatedEffectsNames = [
		"potion.absorption", "potion.blindness", 
		"potion.fireResistance", "potion.digSpeed", "potion.healthBoost", "potion.hunger", 
		"potion.invisibility", "potion.jump", "potion.levitation", "potion.digSlowDown", 
		"potion.confusion", "potion.nightVision", "potion.poison", "potion.regeneration", 
		"potion.resistance", "potion.saturation", "potion.moveSlowdown", "potion.moveSpeed", 
		"potion.damageBoost", "potion.waterBreathing", "potion.weakness", "potion.wither"];

    private array $data = [];

    private HCFPlayer $player;

    public function __construct(HCFPlayer $player, string $name)
    {
        $this->player = $player;
        $this->data["name"] = $name;
    }

    /**
     * @return HCFPlayer
     */
    public function getPlayer(): HCFPlayer
    {
        return $this->player;
    }

    public function setName(string $name) : void
    {
        $this->data["name"] = $name;
    }

    public function setEnergy(string $energy) : void
    {
        $this->data["Energy"] = $energy;
    }

    public function addEffect(string $name, int $amplifier, bool $visible): void
    {
        $this->data["Effects"][] = ['name' => self::translateEffect($name), 'amplifier' => $amplifier, 'visible' => $visible];
    }
    
    private function addItem(int $id, int $meta, int $price, array $clickEffect, array $passiveEffect): void
    {
    	if(!isset($this->data['Energy'])) {
    		$this->getPlayer()->sendMessage('§aAun no haz puesto la cantidad de energía');
    		return;
    	}
    	$passive = empty($passiveEffect) ? [] : ['name' => self::translateEffect($passiveEffect[0]), 'amplifier' => $passiveEffect[1], 'duration' => 5]; 
    	$this->data["Items"][] = ['Id' => $id, 'meta' => $meta, 'energy_price' => $price, 'click_effect' => ['name' => self::translateEffect($clickEffect[0]), 'amplifier' => $clickEffect[1], 'duration' => $clickEffect[2]], 'passive_effect' => $passive];
    	$this->getPlayer()->sendMessage(TextFormat::GREEN . "You have added a new Item to the class: $id:$meta energy price: $price");
    }
    
    public static function stringToEffect(string $parser): EffectInstance | null
    {
    	$msg = explode(":", $parser);
    	$effect = StringToEffectParser::getInstance()->parse($msg[0]);
    	if($effect !== null){
    		return new EffectInstance($effect, Limits::INT32_MAX, $msg[1] ?? 1, boolval($msg[2]) ?? false);
    	}
    	return null;
    }

    public function copyInventory() : void
    {
    	$armor = $this->getPlayer()->getArmorInventory();
    	$this->data["Armor"]["helmet"] = $armor->getHelmet()->getId();
   	 $this->data["Armor"]["chestplate"] = $armor->getChestplate()->getId();
   	 $this->data["Armor"]["leggings"] = $armor->getLeggings()->getId();
   	 $this->data["Armor"]["boots"] = $armor->getBoots()->getId();
    }
    
    
    public static function translateEffect(string $effect): string
    {
    	if(!in_array($effect, self::$translatedEffectsNames)) return $effect;
    	return self::$effectsNames[array_search($effect, self::$translatedEffectsNames, true)];
    }
    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
    
    public function save() : void
    {
        KitManager::getInstance()->createCustomClass($this);
    }
    
    public function createItem()
    {
    	if(!isset($this->data['Energy'])) {
    		$this->getPlayer()->sendMessage('§aAun no haz puesto la cantidad de energía');
    		return;
    	}
    	$player = $this->getPlayer();
		$form = new CustomForm(function (Player $player, $data) {
			if($data === null){
				return;
			}
			if($data['id'] == null) {
				$player->sendMessage("§cDebes escribir la id del item");
				return;
			}
			if(self::stringToEffect($data['effect'] . ":" . $data['amplifier'] . ':' . 'true') == null) {
				$player->sendMessage("§cEste efecto {$data['effect']} no existe");
				return;
			}
			if(isset($data['pasive_effect'])) {
				if(self::stringToEffect($data['pasive_effect'] . ":" . $data['pasive_amplifier'] . ':' . 'true') == null) {
					$player->sendMessage("§cEste efecto {$data['pasive_effect']} no existe");
					return;
				}
				$this->addItem(intval($data['id'] ?? 5), intval($data['meta'] ?? 0), intval($data['price']), [$data['effect'], $data['amplifier'], $data['duration']], [$data['passive_effect'], $data['passive_amplifier']]);
			} else {
				$this->addItem(intval($data['id'] ?? 5), intval($data['meta'] ?? 0), intval($data['price']), [$data['effect'], $data['amplifier'], $data['duration']], []);
			}
		});
		$form->setTitle(TextFormat::GREEN . "Item creation");
		$form->addInput("§eItem ID", "", null, "id");
		$form->addInput("§eItem Meta", "", "0", "meta");
		$form->addSlider("§eEnergy Price", 1, intval($this->data['Energy']), 1, 1, 'price');
		$form->addInput("§eClick Effect name", "", null, "effect");
		$form->addSlider("§eClick Effect level", 1, 7, 1, 1, 'amplifier');
		$form->addSlider("§eClick Effect Duration In Secconds", 1, 15, 1, 1, 'duration');
		$form->addInput("§ePasive Effect name", "", null, "passive_effect");
		$form->addSlider("§ePasive Effect level", 1, 3, 1, 1, 'passive_amplifier');
		$form->sendToPlayer($this->getPlayer());
	}
}