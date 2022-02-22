<?php

namespace ImAMadDev\ticks\asynctask;

use ImAMadDev\utils\HCFUtils;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;

class SaveSkinAsyncTask extends AsyncTask
{
	
	public function __construct() {}
	
	
	public function onRun(): void 
	{
		
	}
	
	public function onComplete(): void 
	{
		HCFUtils::saveSkin($player->getSkin(), $player->getName());
	}
	
}