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
        $tag->setTag($type, $inventoryTag);
		return zlib_encode($nbt->write(new TreeRoot($tag)), ZLIB_ENCODING_GZIP);
	}

    public static function encodeItems(array $items): string
    {
        $writer = new BigEndianNbtSerializer();
        $serializedItems = [];
        foreach ($items as $item) {
            $serializedItems[] = $item->nbtSerialize();
        }
        $nbt = CompoundTag::create();
        $nbt->setTag("Items", new ListTag($serializedItems));
        return $writer->write(new TreeRoot($nbt));
    }

    /**
     * @param string $compression
     *
     * @return Item[]
     */
    public static function decodeItems(string $compression): array
    {
        if (empty($compression)) {
            return [];
        }
        $nbt = new BigEndianNbtSerializer();
        $tag = $nbt->read($compression)->mustGetCompoundTag();
        if (!$tag instanceof CompoundTag) {
            throw new PluginException("Expected a CompoundTag, got " . get_class($tag));
        }
        $content = [];
        /** @var CompoundTag $item */
        foreach ($tag->getListTag("Items")->getValue() as $item) {
            $content[] = Item::nbtDeserialize($item);
        }
        return $content;
    }
}