<?php

namespace ImAMadDev\entity\projectile;

use pocketmine\block\Block;
use pocketmine\math\RayTraceResult;

abstract class Throwable extends NewProjectile {

    /** @var float|Int */
    public int|float $width = 0.25;
    public int|float $height = 0.25;

    /** @var float */
    //protected $drag = 0.01;
    //protected $gravity = 0.03;

    /**
     * @param Block $blockHit
     * @param RayTraceResult $hitResult
     * @return void
     */
    protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult) : void {
        parent::onHitBlock($blockHit, $hitResult);
        $this->flagForDespawn();
    }
}
