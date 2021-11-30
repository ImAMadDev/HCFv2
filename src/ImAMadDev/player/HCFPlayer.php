<?php

namespace ImAMadDev\player;

use pocketmine\entity\{effect\EffectInstance, effect\VanillaEffects};
use ImAMadDev\HCF;
use ImAMadDev\tags\Tag;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\data\bedrock\EffectIds;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\utils\Limits;
use pocketmine\world\Position;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\{
    types\BlockPosition,
    types\BoolGameRule,
    types\entity\EntityMetadataProperties,
    types\entity\EntityMetadataTypes,
    UpdateBlockPacket,
    ChangeDimensionPacket,
    GameRulesChangedPacket};
use pocketmine\math\{Vector3, AxisAlignedBB};

use ImAMadDev\faction\Faction;
use ImAMadDev\rank\RankClass;
use ImAMadDev\customenchants\CustomEnchantment;
use ImAMadDev\manager\{AbilityManager, CrateManager, ClaimManager};
use ImAMadDev\utils\InventoryUtils;
use ImAMadDev\crate\Crate;
use ImAMadDev\ticks\player\{ParticleTick, BardTick};
use ImAMadDev\ability\Ability;

class HCFPlayer extends Player {
	
	private array $rank = [];
	
	private ? Faction $faction = null;
	
	private bool $claiming = false;
	
	private string $region = "Unknown";
	
	private ? Position $claimingFirstPosition = null;
	
	private ? Position $claimingSecondPosition = null;
	
	private int $chatMode = PlayerUtils::PUBLIC;
	
	private ?int $invincibilityTime = null;
	
	private bool $opClaim = false;
	
	private ?string $opClaimName = null;
	
	private string $device = 'Unknown';
	
	private string $inputMode = 'Unknown';
	
	private string $uiMode = 'Classic';
	
	private float $energy = 0.0;
	
	public bool $archerMark = false;
	
	private ?Cooldowns $cooldown = null;
	
	private bool $effectsActivate = true;
	
	private bool $movement = false;
	
	private ? Position $lastPosition = null;
	
	public bool $portalQueue = false;
	
	public array $abilityHits = [];
	
	public array $abilityLastHit = [];
	
	public ? Focus $focus = null;
	
	private bool $checkingForVote = false;
	
	private bool $vote = false;
	
	private int $replaceableBlock = 0;
	
	private int $counterHits = 0;
	
	private bool $leave = false;
	
	private bool $particle = false;
	
	public ?ParticleTick $particleTick = null;

    /**
     * @var bool $joined
     */
    private bool $joined = false;

    public string|null $tag = null;


    public function setCanLogout(bool $can = false) : void {
		$this->leave = $can;
	}
	
	public function canLogout() : bool {
		return $this->leave;
	}
	
	public function setParticle(bool $can = false) : void {
		$this->particle = $can;
	}
	
	public function hasParticle() : bool {
		return $this->particle;
	}

    public function setCurrentTag(string|null $tag) : void
    {
        $this->tag = $tag;
    }
	
	public function canActivateAbility(Item $item) : bool {
		if(($ability = AbilityManager::getInstance()->getAbilityByItem($item)) instanceof Ability) {
			if($ability->getHits() === 0) return true;
			$currentHits = isset($this->abilityHits[$ability->getName()]) ? $this->abilityHits[$ability->getName()] : 0;
			if($currentHits >= $ability->getHits()) {
				return true;
			}
		}
		return false;
	}
	
	public function addAbilityHits(Item $item) : void {
		if(($ability = AbilityManager::getInstance()->getAbilityByItem($item)) !== null) {
			if(isset($this->abilityHits[$ability->getName()]) === true) {
				$this->abilityHits[$ability->getName()] += 1;
			} else {
				$this->abilityHits[$ability->getName()] = 1;
			}
			$this->abilityLastHit[$ability->getName()] = (time() + 1);
		}
	}
	
	public function checkAbilityLastHit() : void {
		foreach(array_keys($this->abilityHits) as $abi) {
			$remaining = (1 - (time() - $this->abilityLastHit[$abi]));
			if($remaining <= 0) {
				$this->abilityHits[$abi] = 0;
				$this->abilityLastHit[$abi] = 0;
			}
		}
	}
	
	public function cancelMovement(bool $cancel = false) : void {
		$this->movement = $cancel;
		if($cancel === true) {
			if($this->lastPosition === null) {
				$this->lastPosition = $this->getPosition()->asPosition();
			}
		} else {
			$this->lastPosition = null;
		}
	}
	
	public function checkRank() : void {
		foreach($this->getRanks() as $rank) {
			if($rank->getName() === "User") {
                continue;
            }
            if ($this->getCache()->getCountdown('rank_' . $rank->getName()) == 0) {
                continue;
            }
            if(($this->getCache()->getCountdown('rank_' . $rank->getName()) - time()) <= 0) {
                $this->removeRank($rank->getName());
                $this->getCache()->removeInArray('ranks', $rank->getName());
                $this->getCache()->removeInData('rank_'. $rank->getName() . '_countdown', true);
                $this->sendMessage(TextFormat::GOLD . "Your rank {$rank->getName()} has expired!");
			}
		}
	}
	
	public function correctMovement() : void {
		if($this->lastPosition === null) return;
		$this->teleport($this->lastPosition);
	}
	
	public function hasCancelledMovement() : bool {
		return $this->movement;
	}
	
	public function setDeviceString(string $device) : void {
		$this->device = $device;
	}
	
	public function setInputString(string $inputMode) : void {
		$this->inputMode = $inputMode;
	}
	
	public function getDeviceString() : string {
		return $this->device;
	}
	
	public function getInputString() : string {
		return $this->inputMode;
	}
	
	public function setUIString(string $ui) : void {
		$this->uiMode = $ui;
	}
	
	public function getUIString() : string {
		return $this->uiMode;
	}
	
	public function load() : void {
		$this->cooldown = new Cooldowns();
		new BardTick($this);
	}
	
	#[Pure] public function getCooldowns() : array {
		return $this->cooldown->getAll();
	}
	
	public function getCooldown() : Cooldowns {
		if($this->cooldown === null) $this->load();
		return $this->cooldown;
	}
	
	public function getFocus() : ?Focus {
		if($this->focus == null) return null;
		return $this->focus;
	}
	
	public function setFocus(?Faction $faction = null) : void {
		if($faction === null) {
			$this->focus = null;
			return;
		}
		$this->focus = new Focus($faction);
	}
	
	public function setOpClaim(bool $val = true) : void {
		$this->opClaim = $val;
	}
	
	public function setOpClaimName(string $name = "Spawn") : void {
		$this->opClaimName = $name;
	}
	
	public function hasOpClaim() : bool {
		return $this->opClaim;
	}
	
	public function getOpClaimName() : string {
		return $this->opClaimName;
	}
	
	public function setRank(RankClass $rank){
		$this->rank[$rank->getName()] = $rank;
		$rank->givePermissions($this);
		//$this->sendMessage(TextFormat::GREEN . "Your rank {$rank->getName()} has been loaded successfully!");
	}
	
	public function removeRank(string $rank) {
		if(isset($this->rank[$rank])) {
			unset($this->rank[$rank]);
		}
	}
	
	public function getRanks() : array {
		return $this->rank;
	}

    /**
     * @return Tag|null
     */
    public function getCurrentTag(): ?Tag
    {
        return HCF::getTagManager()->getTag($this->tag) ?? null;
    }

    public function hasRank(string $name) : bool
    {
        return in_array($name, array_keys($this->rank));
	}
	
	public function getChatFormat() : string {
		$format = $this->getFaction() === null ? "" : TextFormat::YELLOW . "[" . TextFormat::RED . $this->getFaction()->getName() . TextFormat::YELLOW . "] ";
		foreach($this->getRanks() as $rank) {
			$format .= TextFormat::colorize($rank->getFormat() . "&r") . " ";
		}
		return $format . TextFormat::YELLOW;
	}

    public function getCurrentTagFormat() : string
    {
        return $this->getCurrentTag() == null ? "" : " " . $this->getCurrentTag()->getFormat();
    }
	
	public function isClaiming(): bool {
		return $this->claiming;
	}
	
	public function setClaiming(bool $value = true): void {
		$this->claiming = $value;
	}
	
	public function getFirstClaimPosition(): ?Position {
		return $this->claimingFirstPosition;
	}
	public function getSecondClaimPosition(): ?Position {
		return $this->claimingSecondPosition;
	}
	
	public function setFirstClaimingPosition(?Position $position = null): void {
		$this->claimingFirstPosition = $position;
		if($position instanceof Position) {
			$this->sendFakeBlock($position);
		}
	}
	
	public function setSecondClaimingPosition(?Position $position = null): void {
		$this->claimingSecondPosition = $position;
		if($position instanceof Position) {
			$this->sendFakeBlock($position);
		}
	}
	
	public function sendFakeBlock(?Position $position = null): void {
		$blocks = [BlockFactory::getInstance()->get(BlockLegacyIds::GLASS, 0), BlockFactory::getInstance()->get(BlockLegacyIds::DIAMOND_BLOCK, 0)];
		for($i = $position->getFloorY(); $i < $position->getFloorY() + 40; $i++){
            $pos = new BlockPosition($position->getFloorX(), $i, $position->getFloorZ());
            $block = RuntimeBlockMapping::getInstance()->toRuntimeId($blocks[array_rand($blocks)]->getFullId());
			$pk = UpdateBlockPacket::create($pos, $block, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
			$this->getNetworkSession()->sendDataPacket($pk);
		}
	}
	
	public function showCoordinates() : void {
        $c = new BoolGameRule(true, true);
		$packet = GameRulesChangedPacket::create(["showcoordinates" => $c]);
		$this->getNetworkSession()->sendDataPacket($packet);
		$this->sendMessage(TextFormat::GREEN . "Your coords settings changed.");
	}
	
	public function getRegion() : string {
		return $this->region;
	}
	
	public function setRegion(string $region = "Wilderness") : void {
		$this->region = $region;
		$this->sendMessage(TextFormat::RED . "Now entering " . TextFormat::RESET . TextFormat::GRAY . "(" . $region . ")");
	}
	
	public function setChatMode(int $chatMode) : void {
		$this->chatMode = $chatMode;
	}
	
	public function getChatMode() : int {
		return $this->chatMode;
	}
	
	public function getFaction(): ?Faction {
		return $this->faction;
	}
	
	public function setFaction(?Faction $faction): void {
		$this->faction = $faction;
	}
	
	public function setInvincible(?int $time = null): void {
		$this->invincibilityTime = $time !== null ? $time : 3600;
        $this->getCache()->setInData('invincibility_time', $time !== null ? $time : 3600);
	}
	
	public function isInvincible(): bool {
		return $this->invincibilityTime > 0;
	}
	
	#[Pure] public function canDeductInvincibilityTime(): bool {
		if(!$this->isAlive() || $this->getRegion() === "Unknown" || $this->getRegion() === "Spawn") return false;
		return $this->invincibilityTime > 0;
	}
	
	public function subtractInvincibilityTime(): void {
		$this->invincibilityTime -= 1;
        $this->getCache()->setInData('invincibility_time', $this->invincibilityTime);
	}
	
	public function getInvincibilityTime(): int {
		return $this->invincibilityTime;
	}
	
	public function playXpLevelUpSound(): void {
		$this->getXpManager()->addXp(1000);
		$this->getXpManager()->subtractXp(1000);
	}
	
	public function checkInvisibility() : void {
		if($this->isInvincible()) {
			if($this->canDeductInvincibilityTime()) {
				$this->subtractInvincibilityTime();
				if($this->getInvincibilityTime() == 0) {
					$this->setInvincible();
				}
			}
		}
	}
	
	public function setBardEnergy(int $energy) : void {
		$this->energy = $energy;
	}
	
	public function getBardEnergy() : int {
		return $this->energy;
	}
	
	public function sendCustomTagTo(string $customTag, array $players = []){
		$this->sendData($players, [EntityMetadataProperties::NAMETAG => [EntityMetadataTypes::STRING, $customTag]]);
    }

	
	public function updateNameTag() : void {
		if($this->getFaction() === null) {
			if($this->isInvincible()) {
				$nametag = TextFormat::YELLOW . $this->getName();
			} else {
				$nametag = TextFormat::RED . $this->getName();
			}
		} else {
			if($this->isInvincible()) {
				$nametag = TextFormat::YELLOW . "[ " . $this->getFaction()->getName() . " | ". $this->getFaction()->getDTRColored() . TextFormat::YELLOW . " ] " . TextFormat::EOL . TextFormat::YELLOW . $this->getName();
			} else {
				$nametag = TextFormat::RED . "[ " . $this->getFaction()->getName() . " | ". $this->getFaction()->getDTRColored() . TextFormat::RED . " ] " . TextFormat::EOL . TextFormat::RED . $this->getName();
			}
		}
		$this->sendCustomTagTo($nametag, $this->getViewers());
	}
	
	public function obtainKill(string $name) : void {
		$actualKills = $this->getCache()->getInData('kills', true, 0);
        $this->getCache()->setInData('kills', ($actualKills + 1));
		$this->addBalance(PlayerUtils::KILL_PRICE);
		if($this->getInventory()->getItemInHand()->getId() !== 0) {
			$item = $this->getInventory()->getItemInHand();
			$lore = $item->getLore();
			$message = TextFormat::colorize("&9{$this->getName()}&e killed &f$name");
			if(count($lore) >= 10){
				array_shift($lore);
			}
			$lore[] = $message;
			$item->setLore($lore);
			$this->getInventory()->setItemInHand($item);
		}
	}
	
	public function onUpdate(int $currentTick) : bool {
	    if ($this->isJoined() == true) {
            $armor = $this->getArmorInventory()->getContents();
            foreach ($armor as $slot => $item) {
                foreach ($item->getEnchantments() as $enchantment) {
                    if ($enchantment->getType() instanceof CustomEnchantment) {
                        if ($enchantment->getType()->canBeActivate()) {
                            if ((time() % 120) === 0) {
                                $enchantment->getType()->activate($item, $slot, $this);
                            }
                        } else {
                            if ($this->hasEffectsActivate()) {
                                $this->applyPotionEffect($enchantment->getType()->getEffectsByEnchantment($enchantment->getLevel()));
                            }
                        }
                    }
                }
            }
            $this->checkSets();
            $this->checkAbilityLastHit();
            $this->updateNameTag();
            $this->checkRank();
        }
        return parent::onUpdate($currentTick);
    }
	
	public function applyPotionEffect(EffectInstance $effect) {
		if(count($this->getEffects()->all()) > 0) {
			foreach($this->getEffects()->all() as $playerEffect){
				if($playerEffect->getType()->getName() == $effect->getType()->getName()) {
					if($playerEffect->getAmplifier() > $effect->getAmplifier()) return;
				}
			}
		}
		if($this->hasArcherMark() && $effect->getType()->getName() === "invisibility") {
			return;
		}
		if($this->hasEffectsActivate()) {
			$this->getEffects()->add($effect);
		}
	}
	
	public function activateEnchantment(? Event $event = null) : void {
		$item = $this->getInventory()->getItemInHand();
		foreach($item->getEnchantments() as $enchantment){
			if($enchantment->getType() instanceof CustomEnchantment){
				if($enchantment->getType()->canBeActivate()) {
					$enchantment->getType()->activate($item, 0, $this, $enchantment->getLevel(), $event);
				}
			}
		}
	}
	
	public function getClass():? string {
		if ($this->isBard()) return PlayerUtils::BARD;
		if ($this->isMiner()) return PlayerUtils::MINER;
		if ($this->isArcher()) return PlayerUtils::ARCHER;
		if ($this->isRogue()) return PlayerUtils::ROGUE;
		if ($this->isMage()) return PlayerUtils::MAGE;
		return PlayerUtils::NONE;
	}
	
	public function isBard() : bool {
		$helmet = $this->getArmorInventory()->getHelmet()->getId();
		$chestplate = $this->getArmorInventory()->getChestplate()->getId();
		$leggigns = $this->getArmorInventory()->getLeggings()->getId();
		$boots = $this->getArmorInventory()->getBoots()->getId();
		return ($helmet == 314 and $chestplate == 315 and $leggigns == 316 and $boots == 317);
	}
	
	public function isMage() : bool {
		$helmet = $this->getArmorInventory()->getHelmet()->getId();
		$chestplate = $this->getArmorInventory()->getChestplate()->getId();
		$leggigns = $this->getArmorInventory()->getLeggings()->getId();
		$boots = $this->getArmorInventory()->getBoots()->getId();
		return ($helmet == 314 and $chestplate == 303 and $leggigns == 304 and $boots == 317);
	}
	
	public function isMiner() : bool {
		$helmet = $this->getArmorInventory()->getHelmet()->getId();
		$chestplate = $this->getArmorInventory()->getChestplate()->getId();
		$leggigns = $this->getArmorInventory()->getLeggings()->getId();
		$boots = $this->getArmorInventory()->getBoots()->getId();
		return ($helmet == 306 and $chestplate == 307 and $leggigns == 308 and $boots == 309);
	}
	
	public function isArcher() : bool {
		$helmet = $this->getArmorInventory()->getHelmet()->getId();
		$chestplate = $this->getArmorInventory()->getChestplate()->getId();
		$leggigns = $this->getArmorInventory()->getLeggings()->getId();
		$boots = $this->getArmorInventory()->getBoots()->getId();
		return ($helmet == 298 and $chestplate == 299 and $leggigns == 300 and $boots == 301);
	}
	
	public function isRogue() : bool {
		$helmet = $this->getArmorInventory()->getHelmet()->getId();
		$chestplate = $this->getArmorInventory()->getChestplate()->getId();
		$leggigns = $this->getArmorInventory()->getLeggings()->getId();
		$boots = $this->getArmorInventory()->getBoots()->getId();
		return ($helmet == 302 and $chestplate == 303 and $leggigns == 304 and $boots == 305);
	}
	
	public function checkSets() {
		if($this->isBard() && $this->hasEffectsActivate()) {
			if(!$this->getEffects()->has(EffectIdMap::getInstance()->fromId(EffectIds::SPEED))) {
				$speed = new EffectInstance(EffectIdMap::getInstance()->fromId(EffectIds::SPEED), Limits::INT32_MAX, 1, false);
				$this->applyPotionEffect($speed);
			}
			if(!$this->getEffects()->has(EffectIdMap::getInstance()->fromId(EffectIds::REGENERATION))) {
                $reg =new EffectInstance(EffectIdMap::getInstance()->fromId(EffectIds::REGENERATION), Limits::INT32_MAX, 1, false);
				$this->applyPotionEffect($reg);
			}
			if(!$this->getEffects()->has(EffectIdMap::getInstance()->fromId(EffectIds::RESISTANCE))) {
                $res = new EffectInstance(EffectIdMap::getInstance()->fromId(EffectIds::RESISTANCE), Limits::INT32_MAX, 1, false);
				$this->applyPotionEffect($res);
			}
		} elseif($this->isMage() && $this->hasEffectsActivate()) {
			if(!$this->getEffects()->has(EffectIdMap::getInstance()->fromId(EffectIds::SPEED))) {
                $speed = new EffectInstance(EffectIdMap::getInstance()->fromId(EffectIds::SPEED), Limits::INT32_MAX, 1, false);
				$this->applyPotionEffect($speed);
			}
			if(!$this->getEffects()->has(EffectIdMap::getInstance()->fromId(EffectIds::REGENERATION))) {
                $reg = new EffectInstance(EffectIdMap::getInstance()->fromId(EffectIds::REGENERATION), Limits::INT32_MAX, 0, false);
				$this->applyPotionEffect($reg);
			}
			if(!$this->getEffects()->has(VanillaEffects::RESISTANCE())) {
				$res = new EffectInstance(VanillaEffects::RESISTANCE(), Limits::INT32_MAX, 1, false);
				$this->applyPotionEffect($res);
			}
		} elseif ($this->isMiner() && $this->hasEffectsActivate()) {
			if(!$this->getEffects()->has(VanillaEffects::NIGHT_VISION())) {
				$nv = new EffectInstance(VanillaEffects::NIGHT_VISION(), Limits::INT32_MAX, 0, false);
				$this->applyPotionEffect($nv);
			}
			if(!$this->getEffects()->has(VanillaEffects::HASTE())) {
				$haste = new EffectInstance(VanillaEffects::HASTE(), Limits::INT32_MAX, 1, false);
				$this->applyPotionEffect($haste);
			}
			if(!$this->getEffects()->has(VanillaEffects::FIRE_RESISTANCE())) {
				$fres = new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), Limits::INT32_MAX, 0, false);
				$this->applyPotionEffect($fres);
			}
			if($this->getPosition()->getFloorY() < 20) {
				if(!$this->getEffects()->has(VanillaEffects::INVISIBILITY()) && !$this->hasArcherMark()) {
					$inv = new EffectInstance(VanillaEffects::INVISIBILITY(), Limits::INT32_MAX, 0, false);
					$this->applyPotionEffect($inv);
				}
			} else {
				if($this->getEffects()->has(VanillaEffects::INVISIBILITY())) {
					$this->getEffects()->remove(VanillaEffects::INVISIBILITY());
				}
			}
		} elseif ($this->isArcher() && $this->hasEffectsActivate()) {
			if(!$this->getEffects()->has(VanillaEffects::SPEED())) {
				$speed = new EffectInstance(VanillaEffects::SPEED(), Limits::INT32_MAX, 2, false);
				$this->applyPotionEffect($speed);
			}
			if(!$this->getEffects()->has(VanillaEffects::REGENERATION())) {
				$reg = new EffectInstance(VanillaEffects::REGENERATION(), Limits::INT32_MAX, 0, false);
				$this->applyPotionEffect($reg);
			}
			if(!$this->getEffects()->has(VanillaEffects::RESISTANCE())) {
				$res = new EffectInstance(VanillaEffects::RESISTANCE(), Limits::INT32_MAX, 1, false);
				$this->applyPotionEffect($res);
			}
		} elseif ($this->isRogue() && $this->hasEffectsActivate()) {
			if(!$this->getEffects()->has(VanillaEffects::SPEED())) {
				$speed = new EffectInstance(VanillaEffects::SPEED(), Limits::INT32_MAX, 1, false);
				$this->applyPotionEffect($speed);
			}
			if(!$this->getEffects()->has(VanillaEffects::JUMP_BOOST())) {
				$reg = new EffectInstance(VanillaEffects::JUMP_BOOST(), Limits::INT32_MAX, 1, false);
				$this->applyPotionEffect($reg);
			}
			if(!$this->getEffects()->has(VanillaEffects::RESISTANCE())) {
				$res = new EffectInstance(VanillaEffects::RESISTANCE(), Limits::INT32_MAX, 1, false);
				$this->applyPotionEffect($res);
			}
		} else {
			if($this->getClass() == PlayerUtils::NONE) {
				if(count($this->getEffects()->all()) > 0) {
					foreach ($this->getEffects()->all() as $effect) {
						if($effect->getDuration() >= 2000*20) {
							$this->getEffects()->remove($effect->getType());
						}
					}
				}
			}
		}
	}
	
	public function getInventoryStatus(int $val = 1): string{
		$empty = 0;
		if($this->getInventory()->canAddItem(ItemFactory::getInstance()->get(BlockLegacyIds::TALL_GRASS))) {
			$empty++;
		}
		if($this->getInventory()->canAddItem(ItemFactory::getInstance()->get(BlockLegacyIds::COLORED_TORCH_RG))) {
			$empty++;
		}
		return ($empty >= $val) ? "EMPTY" : "FULL";
	}
	
	public function sendBack(Vector3 $vector, float $back = 0.350) : void {
		$deltaX=$this->getPosition()->getFloorX() - $vector->getFloorX();
		$deltaZ=$this->getPosition()->getFloorZ() - $vector->getFloorZ();
		$this->knockBack($deltaX, $deltaZ, $back);
	}
	
	public function getItemCount(?Item $item = null) : int {
		$item = $item ?? ItemFactory::getInstance()->get(ItemIds::SPLASH_POTION, 22);
		$inv = $this->getInventory();
		if(!$inv->contains($item)) {
			return 0;
		}
		return $inv->getItem($inv->first($item))->getCount();
	}
	
	public function getPotionsCount(): int{
		return count($this->getInventory()->all(ItemFactory::getInstance()->get(ItemIds::SPLASH_POTION, 22)));
	}
	
	public function claimBought() : void {
		$all = PlayerData::getSavedItems($this->getName());
		foreach($all as $item => $count) {
			if(($crate = CrateManager::getInstance()->getCrateByName($item)) instanceof Crate) {
				$keys = $crate->getCrateKey(intval($count));
				if($this->getInventory()->canAddItem($keys)) {
					$this->getInventory()->addItem($keys);
				} else {
					$this->getWorld()->dropItem($this->getPosition()->asVector3(), $keys, new Vector3(0, 0, 0));
				}
				PlayerData::removeSavedItem($this->getName(), $crate->getName());
				$this->sendMessage(TextFormat::GREEN . "You've received x$count {$crate->getName()}!");
				continue;
			}
			if(($ability = AbilityManager::getInstance()->getAbilityByName($item)) instanceof Ability) {
				$ability->obtain($this, intval($count));
				PlayerData::removeSavedItem($this->getName(), $ability->getName());
			}
		}
	}
	
	public function getBalance() : int {
        return $this->getCache()->getInData('balance', 1000);
	}
	
	public function setBalance(int $balance) : void {
        $this->getCache()->setInData('balance', $balance);
	}
	
	public function addBalance(int $balance) : void {
		$bal = $this->getBalance();
		$this->setBalance(($bal + $balance));
	}
	
	public function reduceBalance(int $balance) : void {
		$bal = $this->getBalance();
		$this->setBalance(($bal - $balance));
	}
	
	public function getNearbyPlayers(int $dictance, int $up) : array {
		$players = [];
		foreach($this->getWorld()->getNearbyEntities(new AxisAlignedBB($this->getPosition()->getFloorX() - $dictance, $this->getPosition()->getFloorY() - $up, $this->getPosition()->getFloorZ() - $dictance, $this->getPosition()->getFloorX() + $dictance, $this->getPosition()->getFloorY() + $up, $this->getPosition()->getFloorZ() + $dictance)) as $e){
            if(!$e instanceof Player){
                continue;
            }
            if($e->getName() === $this->getName()) {
                continue;
            }
            if(stripos(ClaimManager::getInstance()->getClaimNameByPosition($e->getPosition()), "Spawn") !== false && $e->isInvincible()) {
                continue;
            }
            $players[] = $e;
        }
		return $players;
	}
	
	public function setArcherMark(bool $mark) : void {
		$this->archerMark = $mark;
	}
	
	public function hasArcherMark() : bool {
		return $this->archerMark;
	}
	
	public function activateEffects(bool $activate) : void {
		$this->effectsActivate = $activate;
	}
	
	public function hasEffectsActivate() : bool {
		return $this->effectsActivate;
	}
	
	public function getMageEnergyCost(int $itemId = null) : int {
        return match ($itemId) {
            ItemIds::DYE => 45,
            ItemIds::ROTTEN_FLESH => 25,
            ItemIds::GOLDEN_NUGGET => 35,
            ItemIds::COAL, ItemIds::SPIDER_EYE => 30,
            ItemIds::SEEDS => 40,
            default => null,
        };
	}
	
	public function getBardEnergyCost(int $itemId = null) : int {
        return match ($itemId) {
            ItemIds::SUGAR => 20,
            ItemIds::DYE, ItemIds::FEATHER, ItemIds::IRON_INGOT => 30,
            ItemIds::SPIDER_EYE, ItemIds::BLAZE_POWDER => 40,
            ItemIds::GHAST_TEAR => 35,
            ItemIds::MAGMA_CREAM => 25,
            default => null,
        };
	}
	
	public function saveInventory(): void{
		$this->getSaveData()->setString("SavedInventory", InventoryUtils::encode($this->getInventory()));
		$this->getSaveData()->setString("SavedArmor", InventoryUtils::encode($this->getArmorInventory(), "Armor"));
	}

    public function restoreInventory(): void{
		$this->getInventory()->setContents(InventoryUtils::decode($this->getSaveData()->getString("SavedInventory", "")));
		$this->getArmorInventory()->setContents(InventoryUtils::decode($this->getSaveData()->getString("SavedArmor", ""), "Armor"));
	}
	
	public function sendDimension(int $dimension, bool $respawn = false): void {
        $pk = ChangeDimensionPacket::create($dimension, $this->getPosition()->asVector3(), $respawn);
		$this->getNetworkSession()->sendDataPacket($pk);
	}
	
	public function getHandName() : string {
		if($this->getInventory()->getItemInHand()->getId() === 0) return TextFormat::YELLOW . "his hand";
        return $this->getInventory()->getItemInHand()->hasCustomName() ? TextFormat::YELLOW . explode("\n", $this->getInventory()->getItemInHand()->getCustomName())[0] : TextFormat::YELLOW . $this->getInventory()->getItemInHand()->getName();
    }
    
    public function getDirectionFacing() : string {
		$yaw = $this->getLocation()->getYaw();
		$direction = ($yaw - 180) % 360;
		if ($direction < 0) $direction += 360;
		if (0 <= $direction && $direction < 22.5) return TextFormat::RED . "N";
		elseif (22.5 <= $direction && $direction < 67.5) return TextFormat::RED . "NE";
		elseif (67.5 <= $direction && $direction < 112.5) return TextFormat::RED . "E";
		elseif (112.5 <= $direction && $direction < 157.5) return TextFormat::RED . "SE";
		elseif (157.5 <= $direction && $direction < 202.5) return TextFormat::RED . "S";
		elseif (202.5 <= $direction && $direction < 247.5) return TextFormat::RED . "SW";
		elseif (247.5 <= $direction && $direction < 292.5) return TextFormat::RED . "W";
		elseif (292.5 <= $direction && $direction < 337.5) return TextFormat::RED . "NW";
		elseif (337.5 <= $direction && $direction < 360.0) return TextFormat::RED . "N";
		else return TextFormat::RED . "?";
	}
	
	public function isCheckingForVote() : bool {
		return $this->checkingForVote;
	}
	
	public function hasVoted() : bool {
		return $this->vote;
	}
	
	public function setCheckingForVote(bool $checking = true) : void {
		$this->checkingForVote = $checking;
	}
	
	public function setVoted(bool $vote = true) : void {
		$this->vote = $vote;
	}
	
	public function setReplaceableBlock(int $runtimeId) : void {
		$this->replaceableBlock = $runtimeId;
	}
	
	public function getReplaceableBlock() : int {
		return $this->replaceableBlock;
	}

    /**
     * @return bool
     */
    public function isJoined(): bool
    {
        return $this->joined;
    }

    /**
     * @param bool $joined
     */
    public function setJoined(bool $joined): void
    {
        $this->joined = $joined;
    }

    #[Pure] public function getCache() : PlayerCache
    {
        return HCF::getInstance()->getCache($this->getName());
    }
}