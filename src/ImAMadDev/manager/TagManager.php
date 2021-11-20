<?php

namespace ImAMadDev\manager;

use ImAMadDev\HCF;
use ImAMadDev\tags\Tag;
use function Sodium\add;

class TagManager
{
    public static HCF $main;

    public array $tags = [];

    public function __construct(HCF $main)
    {
        self::$main = $main;
        $this->init();
    }

    public function init() : void
    {
        $this->addTag(new Tag("GOD", "&7[&e&lGOD&r&7]"));
        $this->addTag(new Tag("RAGE", "&7[&4&lRAGE&r&7]"));
        $this->addTag(new Tag("DNS", "&7[&a&lDNS&r&7]"));
        $this->addTag(new Tag("UwU", "&7[&d&lUwU&r&7]"));
        $this->addTag(new Tag("KILLER", "&7[&c&lK&4I&cL&4L&cE&4R&r&7]"));
        $this->addTag(new Tag("GANG", "&7[&6&l&oGANG&r&7]"));
        $this->addTag(new Tag("VAPE", "&7[&f&lVAPE&r&7]"));
        $this->addTag(new Tag("OP", "&7[&1&lOP&r&7]"));
        $this->addTag(new Tag("COVID-19", "&7[&a&lCoVid&f-19&r&7]"));
        $this->addTag(new Tag("OG", "&7[&4&lOG&r&7]"));
        $this->addTag(new Tag("#HOT", "&7[&f&l#&dHOT&r&7]"));
        $this->addTag(new Tag("E-GIRL", "&7[&f&lE-&dGIRL&r&7]"));
        $this->addTag(new Tag("Hacker", "&7[&2&lH&aA&2C&aK&2E&aR&r&7]"));
        $this->addTag(new Tag('$$$', "&7[&2&l$$$&r&7]"));
        $this->addTag(new Tag("Cheater", "&7[&4&lCHEATER&r&7]"));
        $this->addTag(new Tag("AntiTrap", "&7[&f&lAnti&6Trap&r&7]"));
        $this->addTag(new Tag("AntiRaids", "&7[&f&lAnti&cRaids&r&7]"));
        $this->addTag(new Tag("LAGGER", "&7[&c&lLAGGER&r&7]"));
        $this->addTag(new Tag("AUTOCLICKER", "&7[&3&lAUTOCLICKER&r&7]"));
        $this->addTag(new Tag("EZ", "&7[&a&lEZ&r&7]"));
        $this->addTag(new Tag("HORION", "&7[&b&lHORION&r&7]"));
        $this->addTag(new Tag("DEMON", "&7[&4&l&oDEMON&r&7]"));
        $this->addTag(new Tag("ONTOP", "&7[&a&lOn&eTOP&r&7]"));
        $this->addTag(new Tag("SAKURA", "&7[&d&l&oSAKURA&r&7]"));
        $this->addTag(new Tag("ZZZ", "&7[&3&l&oz&bZ&3z&r&7]"));
        $this->addTag(new Tag("TRAPKING", "&7[&6&lTRAP&8KING&r&7]"));
        $this->addTag(new Tag("OnixClient", "&7[&b&lOnix&fClient&r&7]"));
        $this->addTag(new Tag("USA", "&7[&1&lU&fS&4A&7]"));
        $this->addTag(new Tag("URU", "&7[&e&lU&1R&fU&7]"));
        $this->addTag(new Tag("ARG", "&7[&b&lA&fR&bG&7]"));
        $this->addTag(new Tag("MEX", "&7[&2&lM&fE&4X&7]"));
        $this->addTag(new Tag("VNZ", "&7[&e&lV&1N&cZ&7]"));
        $this->addTag(new Tag("BOL", "&7[&4&lB&eO&2L&7]"));
        $this->addTag(new Tag("CHL", "&7[&1&lC&4H&fL&7]"));
        $this->addTag(new Tag("COL", "&7[&e&lC&9O&4L&7]"));
        $this->addTag(new Tag("ECU", "&7[&e&lE&1C&4U&7]"));
        $this->addTag(new Tag("ENG", "&7[&f&lE&4N&fG&7]"));
        $this->addTag(new Tag("ESP", "&7[&4&lE&eS&4P&7]"));
        $this->addTag(new Tag("CRC", "&7[&1&lC&4R&fC&7]"));
        $this->addTag(new Tag("PAN", "&7[&1&lP&fA&4N&r&7]"));
        $this->addTag(new Tag("SDLG", "&7[&8&lSD&4LG&r&7]"));
        $this->getMain()->getLogger()->info("§aTags have been loaded! Number of tags: " . count($this->getTags()));
    }

    /**
     * @param Tag $tag
     */
    public function addTag(Tag $tag) : void
    {
        $this->tags[$tag->getName()] = $tag;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param string|null $name
     * @return Tag|null
     */
    public function getTag(null|string $name) : ?Tag
    {
        if (is_null($name)) return null;
        return $this->tags[$name] ?? null;
    }

    private function getMain() : HCF
    {
        return self::$main;
    }
}