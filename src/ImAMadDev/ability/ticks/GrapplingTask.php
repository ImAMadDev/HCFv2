<?php

namespace ImAMadDev\Tasks;

use ImAMadDev\HCF;
use pocketmine\entity\Entity;
use pocketmine\level\Position;
use pocketmine\scheduler\Task;

class GrapplingTask extends Task
{
    /**
     * @var HCF $plugin
     */
    private HCF $plugin;

    /**
     * @var Position $location
     */
    private Position $location;

    /**
     * @var Entity $entity
     */
    private Entity $entity;

    /**
     * @param HCF $plugin
     * @param Position $location
     * @param Entity $entity
     */
    public function __construct(HCF $plugin, Position $location, Entity $entity)
    {
        $this->plugin = $plugin;
        $this->location = $location;
        $this->entity = $entity;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick)
    {
        $location = $this->location;
        $entityloc = $this->entity->getPosition();
        $g = -0.08;
        $d = $location->distance($entityloc);
        $t = $d;
        if ($t > 0) { //$t is zero if collides with the shooter of the grappling arrow, we dont want the shooter to grapple themselves
            $v_x = (1.0 + 0.07 * $t) * ($location->x - $entityloc->x) / $t;
            $v_y = (1.0 + 0.03 * $t) * ($location->y - $entityloc->y) / $t - 0.5 * $g * $t;
            $v_z = (1.0 + 0.07 * $t) * ($location->z - $entityloc->z) / $t;
            $v = $this->entity->getMotion();
            $v->setComponents($v_x, $v_y, $v_z);
            $this->entity->setMotion($v);
        }
    }
}