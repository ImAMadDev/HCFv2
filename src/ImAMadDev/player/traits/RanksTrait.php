<?php

namespace ImAMadDev\player\traits;

use ImAMadDev\HCF;
use ImAMadDev\rank\RankClass;
use ImAMadDev\tags\Tag;
use pocketmine\utils\TextFormat;

trait RanksTrait
{
    private array $ranks = [];

    public ?string $tag = null;

    public function setCurrentTag(string|null $tag) : void
    {
        $this->tag = $tag;
    }

    /**
     * @return Tag|null
     */
    public function getCurrentTag(): ?Tag
    {
        return HCF::getTagManager()->getTag($this->tag) ?? null;
    }

    public function getChatFormat() : string {
        $format = $this->getFaction() === null ? "" : TextFormat::MINECOIN_GOLD . "[" . TextFormat::RED . $this->getFaction()->getName() . TextFormat::MINECOIN_GOLD . "] ";
        foreach($this->getRanks() as $rank) {
            $format .= TextFormat::colorize($rank->getFormat() . "&r") . " ";
        }
        return $format . TextFormat::GRAY;
    }

    public function getCurrentTagFormat() : string
    {
        return $this->getCurrentTag() == null ? "" : " " . $this->getCurrentTag()->getFormat();
    }

    public function setRank(RankClass $rank){
        $this->ranks[$rank->getName()] = $rank;
        $rank->givePermissions($this);
        //$this->sendMessage(TextFormat::GREEN . "Your rank {$rank->getName()} has been loaded successfully!");
    }

    public function removeRank(string $rank) {
        if(isset($this->ranks[$rank])) {
            unset($this->ranks[$rank]);
        }
    }

    public function getRanks() : array {
        return $this->ranks;
    }

    public function hasRank(string $name) : bool
    {
        return in_array($name, array_keys($this->ranks));
    }

    public function checkRank() : void {
        foreach($this->getRanks() as $rank) {
            if($rank->getName() === "User") {
                continue;
            }
            if ($this->getCache()->getCountdown('rank_' . $rank->getName()) == 0) {
                continue;
            }
            if(($this->getCache()->getCountdown('rank_' . $rank->getName()) - time()) <= 0) {
                $this->removeRank($rank->getName());
                $this->getCache()->removeInArray('ranks', $rank->getName());
                $this->getCache()->removeInData('rank_'. $rank->getName() . '_countdown', true);
                $this->sendMessage(TextFormat::RED . "Your rank {$rank->getName()} has expired!");
            }
        }
    }

}