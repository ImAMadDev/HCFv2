<?php

namespace ImAMadDev\manager;

use pocketmine\block\{
    BlockBreakInfo,
    BlockIdentifier as BID,
    BlockLegacyIds as Ids,
    BlockLegacyMetadata as Meta,
    BlockToolType,
    BlockFactory};

use ImAMadDev\block\{Portal, IronDoor, Flower, DoublePlant, MonsterSpawner, PotionGenerator, Observer, Dirt, Grass, TNT, Obsidian, PressurePlate};
use ImAMadDev\HCF;

class BlockManager {
	
	public static HCF $main;
	
	public function __construct(HCF $main) {
		self::$main = $main;
		$this->init();
	}
	
	public function init() {
        $bf = BlockFactory::getInstance();
        $bf->register(new Dirt(new BID(Ids::DIRT, 0), "Dirt", new BlockBreakInfo(0.5, BlockToolType::SHOVEL)), true);
        $bf->register(new DoublePlant(new BID(Ids::DOUBLE_PLANT, Meta::DOUBLE_PLANT_SUNFLOWER), "Sunflower", BlockBreakInfo::instant()), true);
        $bf->register(new DoublePlant(new BID(Ids::DOUBLE_PLANT, Meta::DOUBLE_PLANT_LILAC), "Lilac", BlockBreakInfo::instant()), true);
        $bf->register(new DoublePlant(new BID(Ids::DOUBLE_PLANT, Meta::DOUBLE_PLANT_ROSE_BUSH), "Rose Bush", BlockBreakInfo::instant()), true);
        $bf->register(new DoublePlant(new BID(Ids::DOUBLE_PLANT, Meta::DOUBLE_PLANT_PEONY), "Peony", BlockBreakInfo::instant()), true);
        $bf->register(new Flower(new BID(Ids::DANDELION, 0), "Dandelion", BlockBreakInfo::instant()), true);
        $bf->register(new Flower(new BID(Ids::RED_FLOWER, Meta::FLOWER_ALLIUM), "Allium", BlockBreakInfo::instant()), true);
        $bf->register(new Flower(new BID(Ids::RED_FLOWER, Meta::FLOWER_AZURE_BLUET), "Azure Bluet", BlockBreakInfo::instant()), true);
        $bf->register(new Flower(new BID(Ids::RED_FLOWER, Meta::FLOWER_BLUE_ORCHID), "Blue Orchid", BlockBreakInfo::instant()), true);
        $bf->register(new Flower(new BID(Ids::RED_FLOWER, Meta::FLOWER_CORNFLOWER), "Cornflower", BlockBreakInfo::instant()), true);
        $bf->register(new Flower(new BID(Ids::RED_FLOWER, Meta::FLOWER_LILY_OF_THE_VALLEY), "Lily of the Valley", BlockBreakInfo::instant()), true);
        $bf->register(new Flower(new BID(Ids::RED_FLOWER, Meta::FLOWER_ORANGE_TULIP), "Orange Tulip", BlockBreakInfo::instant()), true);
        $bf->register(new Flower(new BID(Ids::RED_FLOWER, Meta::FLOWER_OXEYE_DAISY), "Oxeye Daisy", BlockBreakInfo::instant()), true);
        $bf->register(new Flower(new BID(Ids::RED_FLOWER, Meta::FLOWER_PINK_TULIP), "Pink Tulip", BlockBreakInfo::instant()), true);
        $bf->register(new Flower(new BID(Ids::RED_FLOWER, Meta::FLOWER_POPPY), "Poppy", BlockBreakInfo::instant()), true);
        $bf->register(new Flower(new BID(Ids::RED_FLOWER, Meta::FLOWER_RED_TULIP), "Red Tulip", BlockBreakInfo::instant()), true);
        $bf->register(new Flower(new BID(Ids::RED_FLOWER, Meta::FLOWER_WHITE_TULIP), "White Tulip", BlockBreakInfo::instant()), true);
        $bf->register(new Grass(new BID(Ids::GRASS, 0), "Grass", new BlockBreakInfo(0.6, BlockToolType::SHOVEL)), true);
        $bf->register(new TNT(), true);
        $bf->register(new IronDoor(), true);
        $bf->register(new Portal(), true);
        $bf->register(new Obsidian(), true);
        /*
        BlockFactory::registerBlock(new Observer(), true);
        BlockFactory::registerBlock(new Water(), true);
        BlockFactory::registerBlock(new PotionGenerator(), true);
        BlockFactory::registerBlock(new MonsterSpawner(), true);
        BlockFactory::registerBlock(new PressurePlate(Block::STONE_PRESSURE_PLATE, 0, "Stone Pressure Plate"), true);
		BlockFactory::registerBlock(new PressurePlate(Block::HEAVY_WEIGHTED_PRESSURE_PLATE, 0, "Heavy Weighted Pressure Plate"), true);
		BlockFactory::registerBlock(new PressurePlate(Block::LIGHT_WEIGHTED_PRESSURE_PLATE, 0, "Light Weighted Pressure Plate"), true);
		BlockFactory::registerBlock(new PressurePlate(Block::WOODEN_PRESSURE_PLATE, 0, "Wooden Pressure Plate"), true);*/
	}

}