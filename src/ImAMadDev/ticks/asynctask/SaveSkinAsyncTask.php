<?php

namespace ImAMadDev\ticks\asynctask;

use ImAMadDev\HCF;
use ImAMadDev\utils\HCFUtils;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\Skin;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Filesystem;
use pocketmine\utils\TextFormat;

class SaveSkinAsyncTask extends AsyncTask
{

    private string $skinData;
    private array $data;
    private string $name;
    private string $dir;

    #[Pure] public function __construct(string $name, Skin $skin)
    {
        $this->skinData = $skin->getSkinData();
        $this->data = ["skinId" => base64_encode($skin->getSkinId()),
            "skinData" => base64_encode($skin->getSkinData()),
            "skinGeometryName" => base64_encode($skin->getGeometryName()),
            "skinGeometry" => base64_encode($skin->getGeometryData()),
            "skinCapeData" => base64_encode($skin->getCapeData())];
        $this->name = $name;
        $this->dir = HCF::getInstance()->getDataFolder() . "copied_skins";
    }
	
	
	public function onRun(): void 
	{
        if (!is_dir($this->dir)) {
            @mkdir($this->dir);
        }
	}
	
	public function onComplete(): void 
	{
        $image = HCFUtils::skinDataToImage($this->skinData);
        if ($image !== null){
            Filesystem::safeFilePutContents(HCF::getInstance()->getDataFolder() . "copied_skins/" . $this->name . '_skin.json',json_encode($this->data, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING));
            @imagepng($image, HCF::getInstance()->getDataFolder() . "copied_skins/" . $this->name . ".png");
        } else {
            HCF::getInstance()->getLogger()->error(TextFormat::RED . "No se guardo la skin");
        }
	}
}