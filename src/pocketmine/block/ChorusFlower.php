<?php

/*
 *      ____           _
 *     / ___|   _  ___| | ___  _ __   ___
 *    | |  | | | |/ __| |/ _ \|  _ \ / _ \
 *    | |__| |_| | (__| | (_) | | | |  __/
 *     \____\__, |\___|_|\___/|_| |_|\___|
 *          |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Sunch233
 * @link https://github.com/Sunch233/Cyclone
 *
*/

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;

class ChorusFlower extends Solid{

	protected $id = self::CHORUS_FLOWER;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

    	public function getHardness(){
		return 0.4;
	}

	public function getToolType(){
		return Tool::TYPE_AXE;
	}

	public function getName() : string{
		return "Chorus Flower";
	}

	public function getDrops(Item $item) : array {
		$drops = [];
		if($this->meta >= 0x07){
			$drops[] = [Item::CHORUS_FRUIT, 0, 1];
		}
		return $drops;
	}

}