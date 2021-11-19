<?php

namespace ImAMadDev\ticks;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\CrateManager;

use pocketmine\math\Vector3;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;
use pocketmine\utils\TextFormat;

class CheckVoteTick extends AsyncTask {

    const API_KEY = "up5wXKqGSoLtsK6witDcNZ6PohKkzULX";

    const CHECK_URL = "http://minecraftpocket-servers.com/api-vrc/?object=votes&element=claim&key=" . self:: API_KEY . "&username={USERNAME}";

    const POST_URL = "http://minecraftpocket-servers.com/api-vrc/?action=post&object=votes&element=claim&key=" . self:: API_KEY . "&username={USERNAME}";

    const VOTED = "voted";

    const CLAIMED = "claimed";

    /** @var string */
    private string $player;

    /**
     * CheckVoteTask constructor.
     *
     * @param string $player
     */
    public function __construct(string $player) {
        $this->player = $player;
    }

    public function onRun() : void {
        $get = Internet::getURL(str_replace("{USERNAME}", $this->player, self::CHECK_URL));
        if($get === null) {
            return;
        }
        $get = json_decode($get->getBody(), true);
        if((!isset($get["voted"])) or (!isset($get["claimed"]))) {
            return;
        }
        $this->setResult([
            self::VOTED => $get["voted"],
            self::CLAIMED => $get["claimed"],
        ]);
        if($get["voted"] === true and $get["claimed"] === false) {
            $post = Internet::postURL(str_replace("{USERNAME}", $this->player, self::POST_URL), []);
            if($post === null) {
                $this->setResult(null);
            }
        }
    }

    public function onCompletion() : void {
        $player = Server::getInstance()->getPlayerByPrefix($this->player);
        if((!$player instanceof HCFPlayer) or $player->isClosed()) {
            return;
        }
        $result = $this->getResult();
        if(empty($result)) {
            $player->sendMessage(TextFormat::RED . "An error had occurred in this process!");
            return;
        }
        $player->setCheckingForVote(false);
        if($result[self::VOTED] === true) {
            if($result[self::CLAIMED] === true) {
                $player->setVoted();
                $player->sendMessage(TextFormat::RED . "You have already claimed your vote!");
                return;
            }
            $player->setVoted();
            Server::getInstance()->broadcastMessage(TextFormat::colorize("&a&l[VOTE] &r- &e{$player->getName()} &7has voted for our server at &6minestalia.com/vote &7and received &63 Vote Key"));
            $keys = CrateManager::getInstance()->getCrateByName('Vote')->getCrateKey(3);
            if($player->getInventory()->canAddItem($keys)) {
				$player->getInventory()->addItem($keys);
			} else {
				$player->getWorld()->dropItem($player->getPosition()->asVector3(), $keys, new Vector3(0, 0, 0));
			}
            return;
        }
        $player->sendMessage(TextFormat::RED . "You have not voted yet! Vote at " . TextFormat::GOLD . "minestalia.com/vote!");
        $player->setVoted(false);
    }
}