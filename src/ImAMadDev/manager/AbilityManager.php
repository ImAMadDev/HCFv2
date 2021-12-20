<?php

namespace ImAMadDev\manager;

use JetBrains\PhpStorm\Pure;
use pocketmine\item\Item;
use ImAMadDev\HCF;
use ImAMadDev\ability\Ability;

use ImAMadDev\ability\types\{PartnerPackages,
    StrengthPortable,
    PortableRogue,
    SummerLootbox,
    Switcher,
    PrePearl,
    AntiTrapper,
    StormBreaker,
    ResetPearl,
    EffectsDisabler,
    SpeedPortable,
    JumpPortable,
    ResistancePortable,
    AntiBuild,
    RePot,
    NinjaAbility,
    Immobilizer,
    Airdrops,
    RankSharp};

use pocketmine\utils\SingletonTrait;
use function count;

class AbilityManager {
    use SingletonTrait;

	/** @var Ability[] */
	private array $abilities = [];

	/** @var HCF */
	private static HCF $main;

	public function __construct(HCF $main) {
		self::$main = $main;
        self::setInstance($this);
		$this->loadAbilities();
	}

	public function loadAbilities() {
		$this->addAbility(new StrengthPortable());
		$this->addAbility(new AntiTrapper());
		$this->addAbility(new StormBreaker());
		$this->addAbility(new ResetPearl());
		$this->addAbility(new EffectsDisabler());
		$this->addAbility(new SpeedPortable());
		$this->addAbility(new JumpPortable());
		$this->addAbility(new ResistancePortable());
		$this->addAbility(new AntiBuild());
		$this->addAbility(new Switcher());
		$this->addAbility(new RePot());
		$this->addAbility(new PrePearl());
		$this->addAbility(new Immobilizer());
		$this->addAbility(new NinjaAbility());
		$this->addAbility(new PortableRogue());
		$this->addAbility(new Airdrops());
		$this->addAbility(new RankSharp());
        $this->addAbility(new SummerLootbox());
        $this->addAbility(new PartnerPackages());
		$this->getMain()->getLogger()->info("§aThe abilities have been loaded! Number of abilities: " . count($this->getAbilities()));
	}

	public function addAbility(Ability $ability) {
		$this->abilities[$ability->getName()] = $ability;
	}
	
	#[Pure] public function getAbilityByName(string $name): ?Ability
    {
		foreach ($this->getAbilities() as $ability) {
			if ($ability->isAbilityName($name)) {
				return $ability;
			}
		}
		return null;
	}
	
	public function getAbilityByItem(Item $item): ?Ability
    {
		foreach ($this->getAbilities() as $ability) {
			if ($ability->isAbility($item)) {
				return $ability;
			}
		}
		return null;
	}
	
	/**
	 * @return Ability[]
	 */
	public function getAbilities(): array {
		return $this->abilities;
	}
	
	public function getMain(): HCF {
		return self::$main;
	}

	/**
	 * @return Item[]
	 */
	public function getAllAbilityItems(): array {
		$items = [];
		foreach ($this->getAbilities() as $ability) {
			$items[] = $ability->get(1);
		}
		return $items;
	}

}
