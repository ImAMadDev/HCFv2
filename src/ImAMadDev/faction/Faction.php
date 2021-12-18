<?php

namespace ImAMadDev\faction;

use JetBrains\PhpStorm\Pure;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\Server;

use ImAMadDev\HCF;
use ImAMadDev\claim\Claim;
use ImAMadDev\manager\ClaimManager;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\faction\ticks\{FactionTick, InviteTask, UpdateDataAsyncTask};

define("FACTION_DIRECTORY", HCF::getInstance()->getDataFolder() . "factions" . DIRECTORY_SEPARATOR);
class Faction {

	public FactionTick $task;

	public int $regenerationTime = FactionUtils::REGENERATION_TIME;
	
	public array $invites = [];
	
	public array $allyRequests = [];

    /**
     * @var FactionRally|null
     */
	public FactionRally | null $rally = null;

    public int $freezeTime = 0;

    public function __construct(
        public HCF $main,
        public array $data) {
		$this->main->getLogger()->info(TextFormat::GREEN."Faction » {$this->getName()} was loaded successfully!");
		if($this->getDTR() < $this->getMaxDTR()) {
			if(!$this->task instanceof FactionTick) {
				$this->main->getScheduler()->scheduleRepeatingTask($this->task = new FactionTick($this), 20);
				$this->main->getLogger()->info(TextFormat::GREEN."Faction » Task loaded!");
			}
		}
	}
	
	public function getName() : string {
		return $this->data['name'];
	}
	
	public function getLeader() : string {
		return $this->data['leader'];
	}
	
	public function getColeaders() : array {
		return $this->data['coleaders'];
	}
	
	public function getMembers() : array {
		return $this->data['members'];
	}
	
	public function getAllies() : array {
		return $this->data['allys'];
	}
	
	public function isLeader(string $name) : bool {
        if(empty($name)) return false;
		return $name == $this->getLeader();
	}
	
	public function isMember(string $name) : bool {
        if(empty($name)) return false;
		return in_array($name, $this->getMembers(), true);
	}
	
	public function isColeader(string $name) : bool {
        if(empty($name)) return false;
		return in_array($name, $this->getColeaders(), true);
	}
	
	public function isAlly(string $name) : bool {
		if(empty($name)) return false;
		return in_array($name, $this->getAllies(), true);
	}
	
	public function isInFaction($name) : bool {
		if($name instanceof HCFPlayer) $name = $name->getName();
		if(empty($name)) return false;
		return in_array($name, $this->getAllMembers(), true);
	}
	
	public function isAllying($faction) : bool {
		if($faction instanceof Faction) $faction = $faction->getName();
		return in_array($faction, $this->allyRequests, true);
	}
	
	#[Pure] public function isInvited($player) : bool {
		if($player instanceof HCFPlayer) $player = $player->getName();
		return in_array($player, $this->invites, true);
	}
	
	public function addAllyRequest($faction) : void {
		if($faction instanceof Faction) $faction = $faction->getName();
		if($this->isAllying($faction)) return;
		$this->allyRequests[$faction] = $faction;
		new InviteTask($this->main, $faction, $this);
	}
	
	public function addInvite($player) : void {
		if($player instanceof HCFPlayer) $player = $player->getName();
		if($this->isInvited($player)) return;
		$this->invites[$player] = $player;
		new InviteTask($this->main, $player, $this);
	}
	
	public function removeAllyRequest($faction) : void {
		if($faction instanceof Faction) $faction = $faction->getName();
		if(!$this->isAllying($faction)) return;
		unset($this->allyRequests[$faction]);
	}
	
	public function removeInvite($player) : void {
		if($player instanceof HCFPlayer) $player = $player->getName();
		if(!$this->isInvited($player)) return;
		unset($this->invites[$player]);
	}
	
	public function getMaxDTR() : float {
		if(count($this->getAllMembers()) === FactionUtils::MAXIMUM_MEMBERS) return FactionUtils::MAXIMUM_DTR;
		if(count($this->getAllMembers()) === 1) return FactionUtils::MINIMUM_DTR;
		return (count($this->getAllMembers()) + 0.1);
	}
	
	public function addAlly(Faction $ally) : void {
		if(!$this->isAlly($ally->getName())) {
			$this->data['allys'][] = $ally->getName();
            $this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
		}
	}
	
	public function setLeader(HCFPlayer $player) : void {
		if($this->isColeader($player->getName())) {
			$this->removeMember($player->getName());
		}
		if($this->isMember($player->getName())) {
			$this->removeMember($player->getName());
		}
		if(!$this->isLeader($player->getName())) {
			$this->data['leader'] = $player->getName();
			$this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
		}
	}
	
	public function addMember(HCFPlayer $player) : void {
		if($this->isLeader($player->getName())) {
			$this->data['leader'] = array_rand($this->data['coleaders'], array_rand($this->data['coleaders']));
			$this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
		}
		if($this->isColeader($player->getName())) {
			$this->removeMember($player->getName());
		}
		if(!$this->isMember($player->getName())) {
			$this->data['members'][] = $player->getName();
            $this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
		}
		if($this->getDTR() < $this->getMaxDTR()) {
			if(!$this->task instanceof FactionTick) {
				$this->main->getScheduler()->scheduleRepeatingTask($this->task = new FactionTick($this), 20);
			}
		}
	}

    /**
     * @return FactionRally|null
     */
    public function getRally(): ?FactionRally
    {
        return $this->rally;
    }

    public function openRally(?HCFPlayer $player) : void
    {
        if($player == null){
            $this->rally = null;
            return;
        }
        $this->rally = new FactionRally($player->getPosition(), $player->getName());
    }
	
	public function addColeader(HCFPlayer $player, ? HCFPlayer $newLeader = null) : void {
		if($this->isLeader($player->getName())) {
			if($newLeader === null) {
				$this->data['leader'] = array_rand($this->data['coleaders'], array_rand($this->data['coleaders']));
                $this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
			} else {
				$this->setLeader($newLeader);
			}
		}
		if($this->isMember($player->getName())) {
			$this->removeMember($player->getName());
		}
		if(!$this->isColeader($player->getName())) {
			$this->data['coleaders'][] = $player->getName();
            $this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
		}
	}
	
	public function getHomeString(bool $withY = false) : string {
		$home = $this->data['home'] ?? "0:0:0";
		if($withY) {
			return $home;
		} else {
			if($home === "0:0:0")  return "No Home Set";
			$vec = explode(":", $home);
			return intval($vec[0]) . ":" . intval($vec[2]);
		}
	}
	
	public function getHome() : ? Position {
		$home = $this->data['home'] ?? "0:0:0";
		if($home === "0:0:0") return null;
		$vec = explode(":", $home);
		$world = Server::getInstance()->getWorldManager()->getWorldByName($this->data['level']);
		return new Position((float)$vec[0], (int)$vec[1], (float)$vec[2], $world);
	}
	
	public function setHome(?Position $pos = null): void {
		if($pos instanceof Position) {
			$this->data['home'] = $pos->x . ":" . $pos->y . ":" . $pos->z;
		} else {
			$this->data['home'] = null;
		}
        $this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
	}
	
	public function changeName(string $newName) {
		
	}
	
	public function getDTR() : float {
		return $this->data['dtr'];
	}
	
	public function getPoints() : float {
		return $this->data['points'];
	}
	
	public function getKills() : int {
		return $this->data['kills'];
	}
	
	public function getBalance() : float {
		return $this->data['balance'];
	}
	
	public function getDTRColored() : string {
		if($this->getDTR() >= $this->getMaxDTR()) {
			return TextFormat::GREEN . $this->getDTR() . TextFormat::BOLD . " DTR" . TextFormat::RESET;
		} elseif($this->getDTR() > 0 && $this->getDTR() < $this->getMaxDTR()) {
			return TextFormat::YELLOW . $this->getDTR() . TextFormat::BOLD . " DTR" . TextFormat::RESET;
		} else {
			return TextFormat::DARK_RED . "- " . $this->getDTR() . TextFormat::BOLD . " DTR" . TextFormat::RESET;
		}
	}
	
	public function addDTR(float $dtr) : void {
		$this->data['dtr'] += $dtr;
		$this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
		$this->message("+ " . FactionUtils::DTR_TO_ADD . " DTR");
	}
	
	public function removeDTR(float $dtr) : void {
		$this->data['dtr'] -= $dtr;
		$this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
		if(!$this->task instanceof FactionTick) {
			$this->main->getScheduler()->scheduleRepeatingTask($this->task = new FactionTick($this), 20);
		}
		$this->freezeTime = FactionUtils::FREEZE_TIME;
		$this->message(TextFormat::RED . "- 1 DTR");
		if($this->getDTR() == 0) {
			$this->message(TextFormat::RED . "YOUR FACTION IS NOW RAIDEABLE!");
		}
	}
	
	public function addPoints(float $points) : void {
		$this->data['points'] += $points;
        $this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
	}
	
	public function removePoints(float $points) : void {
		$this->data['points'] -= $points;
        $this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
	}
	
	public function addKill(int $kills) : void {
		$this->data['kills'] += $kills;
        $this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
	}
	
	public function removeKills(int $kills) : void {
		$this->data['kills'] -= $kills;
        $this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
	}
	
	public function addBalance(float $balance) : void {
		$this->data['balance'] += $balance;
        $this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
	}
	
	public function removeBalance(float $balance) : void {
		$this->data['balance'] -= $balance;
        $this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
	}
	
	public function removeAlly(string $ally) : void {
		if($this->isAlly($ally)) {
			$new = [];
			foreach($this->getAllies() as $allies) {
				if($allies === $ally) {
					continue;
				}
				$new[] = $allies;
			}
			$this->data['allys'] = $new;
            $this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
		}
	}
	
	public function unClaim() : void {
		foreach(['x1', 'z1', 'x2', 'z2'] as $index) {
			$this->data[$index] = null;
            $this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
		}
		$claim = ClaimManager::getInstance()->getClaim($this->getName());
		if($claim instanceof Claim) {
			ClaimManager::getInstance()->disband($claim);
		}
	}
	
	public function claim(Claim $claim) : void {
		foreach($claim->data as $key => $value) {
			$this->data[$key] = $value;
            $this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
		}
	}
	
	public function hasClaim() : bool {
		$errors = 0;
		foreach(['x1', 'z1', 'x2', 'z2'] as $index) {
			if($this->data[$index] === null) {
				$errors++;
			}
		}
		return $errors == 0;
	}
	
	public function updateData(): void {
        if (!file_exists(FACTION_DIRECTORY . $this->getName()  . '.yml')) return;
        file_put_contents(FACTION_DIRECTORY . $this->getName() . '.yml', yaml_emit($this->data, YAML_UTF8_ENCODING));
    }
	
	public function getAllMembers() : array {
		$newArray = array_merge($this->getMembers(), $this->getColeaders());
		$newArray[] = $this->getLeader();
		return $newArray;
	}
	
	public function getOnlineMembers() : array {
		$members = [];
		foreach($this->getAllMembers() as $member){
			if(($player = Server::getInstance()->getPlayerExact($member)) instanceof HCFPlayer){
				$members[] = $player;
			}
		}
		return $members;
	}
	
	public function message(string $message) : void {
		foreach($this->getOnlineMembers() as $member) {
			$member->sendMessage($message);
		}
	}
	
	public function getInformationString() : string {
        $message = TextFormat::DARK_RED . TextFormat::BOLD . $this->getName() . TextFormat::RESET . TextFormat::DARK_GRAY . " [" . TextFormat::GRAY . count($this->getAllMembers()) . "/" . FactionUtils::MAXIMUM_MEMBERS . TextFormat::DARK_GRAY . "] " . TextFormat::GRAY . $this->getHomeString() . PHP_EOL;
		$members = [];
		foreach($this->getAllMembers() as $member) {
			if(($player = $this->main->getServer()->getPlayerByPrefix($member)) instanceof HCFPlayer) {
				$members[] = TextFormat::GREEN . $player->getName() . TextFormat::DARK_GRAY . "[" . TextFormat::DARK_RED . TextFormat::BOLD . $player->getCache()->getInData('kills', true, 0) . TextFormat::RESET . TextFormat::DARK_GRAY . "]";
				continue;
			}
			$members[] = TextFormat::RED . $member . TextFormat::DARK_GRAY . "[" . TextFormat::DARK_RED . TextFormat::BOLD . HCF::getInstance()->getCache($member)?->getInData('kills', true, 0) . TextFormat::RESET . TextFormat::DARK_GRAY . "]";
		}
		$raidable = "";
		if($this->getDTR() <= 0) {
			$raidable = TextFormat::RED . TextFormat::DARK_RED . " (RAIDABLE)";
		}
		$time = $this->freezeTime <= 0 ? $this->regenerationTime : $this->freezeTime;
		$message .= TextFormat::RED . " Leader: " . TextFormat::GRAY . $this->getLeader() . PHP_EOL;
		$message .= TextFormat::RED . " Members: " . implode(TextFormat::GRAY . ", ", $members) . PHP_EOL;
		$message .= TextFormat::RED . " Allies: " . TextFormat::WHITE . implode(", ", $this->getAllies()) . PHP_EOL;
		$message .= TextFormat::RED . " DTR: " . TextFormat::WHITE . $this->getDTRColored() . $raidable . PHP_EOL;
		$message .= TextFormat::RED . " DTR Regeneration: " . TextFormat::WHITE . gmdate('i:s', $time) . PHP_EOL;
		$message .= TextFormat::RED . " Balance: " . TextFormat::WHITE . "$" . $this->getBalance() . PHP_EOL;
		return $message;
	}
	
	public function removeMember(string $player) : void {
		if($this->isColeader($player)) {
			$new = [];
			foreach($this->getColeaders() as $co) {
				if($co === $player) {
					continue;
				}
				$new[] = $co;
			}
			$this->data['coleaders'] = $new;
            $this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
		}
		if($this->isMember($player)) {
			$new = [];
			foreach($this->getMembers() as $co) {
				if($co === $player) {
					continue;
				}
				$new[] = $co;
			}
			$this->data['members'] = $new;
            $this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
		}
	}
	
	public function disband() : void {
		$this->invites = [];
		$this->allyRequests = [];
		$this->unClaim();
		foreach($this->getOnlineMembers() as $member){
			if($member->getFaction() !== null && $member->getFaction()->getName() == $this->getName()) {
				$member->setFaction(null);
			}
		}
		foreach($this->getAllMembers() as $name){
			if(HCF::getInstance()->getCache($name)?->getInData('faction') == $this->getName()) {
                HCF::getInstance()->getCache($name)?->setInData('faction', null);
			}
		}
		if(count($this->getAllies()) > 0) {
			foreach($this->getAllies() as $ally){
				if(($factionAlly = $this->main->getFactionManager()->getFaction($ally)) instanceof Faction) {
					$factionAlly->removeAlly($this->getName());
				}
			}
		}
        $this->task?->getHandler()->cancel();
		$this->main->getFactionManager()->disband($this->getName());
	}

    public function saveData() : void
    {
        if (!file_exists(FACTION_DIRECTORY . $this->getName()  . '.yml')) return;
        file_put_contents(FACTION_DIRECTORY . $this->getName() . '.yml', yaml_emit($this->data, YAML_UTF8_ENCODING));
    }

    public function __destruct()
    {
        if (!file_exists(FACTION_DIRECTORY . $this->getName()  . '.yml')) return;
        file_put_contents(FACTION_DIRECTORY . $this->getName() . '.yml', yaml_emit($this->data, YAML_UTF8_ENCODING));
    }

}