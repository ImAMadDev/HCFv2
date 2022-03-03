<?php

namespace ImAMadDev\ticks\player;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\Skin;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Filesystem;
use pocketmine\utils\TextFormat;

class CheckRanksAsyncTask extends AsyncTask
{

    private string $name;

    #[Pure] public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function onRun(): void{}

    public function onComplete(): void
    {
        $player = Server::getInstance()->getPlayerByPrefix($this->name);
        if ($player instanceof HCFPlayer){
            $player->checkRank();
        }
    }
}