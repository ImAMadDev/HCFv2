<?php

namespace ImAMadDev\utils;

use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\NBT;
use pocketmine\nbt\TreeRoot;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\nbt\tag\{ListTag, CompoundTag};

final class InventoryUtils {
	
	public const CUSTOM_ENCHANTMENT = "custom_enchantment";

	public static function decode(string $data, string $type = "Inventory"): array{
		if(empty($data)) return [];
		$contents = [];
        $nbt = new BigEndianNbtSerializer();
		$inventoryTag = $nbt->read(zlib_decode($data))->mustGetCompoundTag();
		/** @var CompoundTag $tag */
		foreach ($inventoryTag as $tag) {
			$contents[$tag->getByte("Slot")] = Item::nbtDeserialize($tag);
		}
		return $contents;
	}
	
	public static function encode(Inventory $inventory, string $type = "Inventory"): string{
        $nbt = new BigEndianNbtSerializer();
        $inventoryTag = new ListTag([], NBT::TAG_Compound);
		foreach ($inventory->getContents() as $slot => $item) {
            $inventoryTag->push($item->nbtSerialize($slot));
		}
        $tag = new CompoundTag();
        $tag->setTag("Inventory", $inventoryTag);
		return zlib_encode($nbt->write(new TreeRoot($tag)), ZLIB_ENCODING_GZIP);
	}
}