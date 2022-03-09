<?php

namespace ImAMadDev\shop\forms;

use formapi\CustomForm;
use ImAMadDev\shop\Shop;
use pocketmine\player\Player;

class CreateSignShop extends CustomForm
{

    public function __construct(?callable $callable)
    {
        $this->setTitle("Shop creation");
        $this->addDropdown("Mode", [Shop::BUY, Shop::SELL], 0, "mode");
        $this->addInput("Item ID", "ItemID", "322", "id");
        $this->addInput("Item ID", "ItemMeta", "0", "meta");
        $this->addInput("Item ID", "Quantity", "16", "quantity");
        parent::__construct($callable);
    }

}