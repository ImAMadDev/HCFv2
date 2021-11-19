<?php

namespace ImAMadDev\ticks\asynctask;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;

class GetCapeUrlAsyncTask extends AsyncTask
{

    public function __construct(
        public string $user){}

    /**
     * @inheritDoc
     */
    public function onRun() : void
    {
        $url = 'https://api.capes.dev/load/' . $this->user;
        $data = json_decode(Internet::getURL($url)->getBody(), true);
        var_dump($data);
        if ($data == false) {
            $this->setResult(null);
        } else {
            $this->setResult($data);
        }
    }

}