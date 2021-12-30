<?php

namespace ImAMadDev\utils;

use Closure;
use DateTime;
use GdImage;
use ImAMadDev\HCF;
use pocketmine\block\tile\Sign;
use pocketmine\entity\Skin;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\WrittenBook;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

final class HCFUtils {
	
	public const DEFAULT_MAP = 'MS';
	
	public const NETHER_MAP = 'Nether';

    public const END_MAP = 'End';
	
	public const PAID_SHARPNESS = 2;
	
	public const PAID_PROTECTION = 2;
	
	public const FREE_SHARPNESS = 1;
	
	public const FREE_PROTECTION = 1;
	
	public const KOTH_SHARPNESS = 3;
	
	public const KOTH_PROTECTION = 3;
	
	public const KOTH_COOLDOWN = 14400;
	
	public const CLEARLAG_TIME = 480;
	
	public const MINUTES = "m", HOURS = "h", DAYS = "d";

    public static array $acceptedSkinSize = [
        64 * 32 * 4,
        64 * 64 * 4,
        128 * 128 * 4
    ];
    public static array $skin_wight_map = [
        64 * 32 * 4 => 64,
        64 * 64 * 4 => 64,
        128 * 128 * 4 => 128
    ];

    public static array $skin_height_map = [
        64 * 32 * 4 => 32,
        64 * 64 * 4 => 64,
        128 * 128 * 4 => 128
    ];

    public static function strToSeconds(string $string) : ?int {
        //if(!preg_match("/(^[1-9][0-9]{0,2}[mhd])(,[1-9][0-9]{0,2}[mhd]){0,2}$/", $string)) return null;
        $time = 0;
        $parts = explode(",", $string);
        foreach($parts as $part) {
            $chars = str_split($part, (strlen($part) - 1));
            if(!$chars) return null;
            $time += match ($chars[1]) {
                self::MINUTES => ((int)$chars[0] * 60),
                self::HOURS => ((int)$chars[0] * 3600),
                self::DAYS => ((int)$chars[0] * 86400),
            };
        }
        return $time;
    }

	public static function getTimeString(int $time = 0): string{
		$now = time();
        $countdown = $time - $now;
		$days = floor($countdown / 86400);
		$hours = floor(($countdown / 3600) % 24);
		$minutes = floor(($countdown / 60) % 60);
		$seconds = $countdown % 60;
		$remaining = "";
		if($days >= 1) $remaining .= $days . " day(s), ";
		if($hours >= 1) $remaining .= $hours . " hour(s), ";
		if($minutes >= 1) $remaining .= $minutes . " minute(s), ";
		if($seconds >= 1) $remaining .= $seconds . " seconds";
		if($time === 0 or $time < $now) return "§aNo countdown";
		return $remaining;
	}

    public static function getGreekFormat(int $integer) : string {
		$table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1);
		$return = '';
		while($integer > 0) {
			foreach($table as $rom=>$arb) {
				if($integer >= $arb) {
					$integer -= $arb;
					$return .= $rom;
					break;
				}
			}
		}
		return $return;
	}

    public static function saveSkin(Skin $skin, string $name) : void
    {
        if (!is_dir(HCF::getInstance()->getDataFolder() . "copied_skins")) {
            @mkdir(HCF::getInstance()->getDataFolder() . "copied_skins", );
        }
        $img = self::skinDataToImage($skin->getSkinData());
        if ($img == null) {
            return;
        }
        $config = new Config(HCF::getInstance()->getDataFolder() . "copied_skins/" . $name . '_skin.json', Config::JSON);
        $data = ["skinId" => base64_encode($skin->getSkinId()),
            "skinData" => base64_encode($skin->getSkinData()),
            "skinGeometryName" => base64_encode($skin->getGeometryName()),
            "skinGeometry" => base64_encode($skin->getGeometryData()),
            "skinCapeData" => base64_encode($skin->getCapeData())];
        $config->save();
        $file = fopen(HCF::getInstance()->getDataFolder() . "copied_skins/" . $name . '_skin.json', "w+");
        $CODED = json_encode($data, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING);
        file_put_contents(HCF::getInstance()->getDataFolder() . "copied_skins/" . $name . '_skin.json', $CODED);
        @imagepng($img, HCF::getInstance()->getDataFolder() . "copied_skins/" . $name . ".png");
        fclose($file);
    }

    public static function skinDataToImage($skinData): ?GdImage
    {
        $size = strlen($skinData);
        if (!self::validateSize($size)) {
            return null;
        }
        $width = self::$skin_wight_map[$size];
        $height = self::$skin_height_map[$size];
        $skinPos = 0;
        $image = imagecreatetruecolor($width, $height);
        if ($image === false) {
            return null;
        }

        imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $r = ord($skinData[$skinPos]);
                $skinPos++;
                $g = ord($skinData[$skinPos]);
                $skinPos++;
                $b = ord($skinData[$skinPos]);
                $skinPos++;
                $a = 127 - intdiv(ord($skinData[$skinPos]), 2);
                $skinPos++;
                $col = imagecolorallocatealpha($image, $r, $g, $b, $a);
                imagesetpixel($image, $x, $y, $col);
            }
        }
        imagesavealpha($image, true);
        return $image;
    }

    public static function validateSize(int $size): bool
    {
        if (!in_array($size, self::$acceptedSkinSize)) {
            return false;
        }
        return true;
    }

    /**
     * @param string $name
     * @return Skin
     */
    public static function getSkin(string $name): Skin
    {
        $path = HCF::getInstance()->getDataFolder() . "copied_skins/" . $name . ".png";
        if(!file_exists($path)){
            $path = HCF::getInstance()->getDataFolder()."steve.png";
        }
        $img = @imagecreatefrompng($path);
        $size = getimagesize($path);
        $skin_bytes = "";
        for ($y = 0; $y < $size[1]; $y++) {
            for ($x = 0; $x < $size[0]; $x++) {
                $colorant = @imagecolorat($img, $x, $y);
                $a = ((~($colorant >> 24)) << 1) & 0xff;
                $r = ($colorant >> 16) & 0xff;
                $g = ($colorant >> 8) & 0xff;
                $b = $colorant & 0xff;
                $skin_bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        $config = new Config(HCF::getInstance()->getDataFolder() . "copied_skins/" . $name . '_skin.json');
        return new Skin(base64_decode($config->get("skinId")), $skin_bytes, base64_decode($config->get("skinCapeData")), base64_decode($config->get("skinGeometryName")), base64_decode($config->get("skinGeometryData")));
    }

    public static function firstJoin(Player $player) : void
    {
        $book = ItemFactory::getInstance()->get(ItemIds::WRITTEN_BOOK);
        if ($book instanceof WrittenBook) {
            $book->setAuthor("MineStalia");
            $book->setTitle(TextFormat::YELLOW . "Welcome to MineStalia");
            $book->setPageText(0, "Page #1");
            $book->setPageText(1, "Page #2");
            $book->setPageText(2, "Page #3");
        }
        $player->getInventory()->addItem($book);
    }

    public static function firstOrDefault(array $array, Closure $condition, mixed $fallback = null) : mixed{
        foreach($array as $index => $element){
            if($condition($index, $element)){
                return $element;
            }
        }

        return $fallback;
    }

    public static function createDeathSign(string $name, string $killer) : Item
    {
        $sing = ItemFactory::getInstance()->get(ItemIds::SIGN, 0);
        $date = new DateTime();
        $time = $date->format('D H:i:s');
        $text = [TextFormat::GREEN . $name, TextFormat::GRAY . "slain by", TextFormat::GREEN . $killer, TextFormat::GRAY . $time];
        $nbt = CompoundTag::create()->setTag(Sign::TAG_TEXT_BLOB, new StringTag(join(TextFormat::EOL, $text)));
        $sing->setCustomBlockData($nbt);
        $sing->setCustomName(TextFormat::DARK_PURPLE . "Death Sing " . $name);
        return $sing;
    }

    public static function addKillsLore(Item &$stack, string $killer, string $player) : void
    {
        $lore = $stack->getLore();
        $next_lore = $lore;
        if (empty($lore) || !str_starts_with(TextFormat::clean($lore[0]), "Kills: ")) {
            array_unshift($next_lore, TextFormat::DARK_RED . "Kills: " . TextFormat::YELLOW . "1");
        } else {
            $killsString = str_replace(TextFormat::DARK_RED . "Kills: " . TextFormat::YELLOW, "", $lore[0]);
            $kills = intval($killsString);
            $next_lore[0] = TextFormat::DARK_RED . "Kills: " . TextFormat::YELLOW . ($kills += 1);
            $next_lore[1] = "";
            $next_lore = array_slice($next_lore, 0, 6);
        }
        $next_lore[] = $killer . TextFormat::WHITE . " killed " . TextFormat::RED . $player;
        $stack->setLore($next_lore);
    }
}
