<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 *  _____            _               _____           
 * / ____|          (_)             |  __ \          
 *| |  __  ___ _ __  _ ___ _   _ ___| |__) | __ ___  
 *| | |_ |/ _ \ '_ \| / __| | | / __|  ___/ '__/ _ \ 
 *| |__| |  __/ | | | \__ \ |_| \__ \ |   | | | (_) |
 * \_____|\___|_| |_|_|___/\__, |___/_|   |_|  \___/ 
 *                         __/ |                    
 *                        |___/                     
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author GenisysPro
 * @link https://github.com/GenisysPro/GenisysPro
 *
 *
*/

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

class PlaySoundPacket extends DataPacket{

	const NETWORK_ID = Info::PLAY_SOUND_PACKET;

	public $soundName;
	public $x;
	public $y;
	public $z;
	public $volume;
	public $pitch;

	public function decode(){
		$this->soundName = $this->getString();
		$this->getBlockCoords($this->x, $this->y, $this->z); //Not sure if it's BlockCoords
		$this->volume = $this->getFloat();
		$this->pitch = $this->getFloat();
	}

	public function encode(){
		$this->reset();
		$this->putString($this->soundName);
		$this->putBlockCoords($this->x, $this->y, $this->z); //Not sure if it's BlockCoords
		$this->putFloat($this->volume);
		$this->putFloat($this->pitch);
	}

	/**
	 * @return PacketName|string
     */
	public function getName(){
		return "PlaySoundPacket";
	}

}