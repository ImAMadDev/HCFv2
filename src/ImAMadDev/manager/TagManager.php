<?php

namespace ImAMadDev\manager;

use ImAMadDev\HCF;
use ImAMadDev\tags\Tag;

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
        $this->addTag(new Tag("9000IQ", "&c[9000IQ]"));
        $this->addTag(new Tag("OnixClient", "&9[OnixClient]"));
        $this->addTag(new Tag("Heart", "&c[❤]"));
        $this->addTag(new Tag("GOD", "&e[GOD]"));
        $this->addTag(new Tag("UwU", "&b[UwU]"));
        $this->addTag(new Tag("Gang", "&5[Gang]"));
        $this->addTag(new Tag("Hot", "&c[#Hot]"));
        $this->addTag(new Tag("OG", "&d[OG]"));
        $this->addTag(new Tag("MEX", "&2M&fE&cX"));
        $this->addTag(new Tag("ARG", "&bA&rR&bG"));
        $this->addTag(new Tag("ECU", "&eE&9C&cU"));
        $this->addTag(new Tag("COL", "&eC&9O&cL"));
        $this->addTag(new Tag("TrapKing", "&6[TrapKing]"));
        $this->addTag(new Tag("AutoClick", "&c[AutoClick]"));
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