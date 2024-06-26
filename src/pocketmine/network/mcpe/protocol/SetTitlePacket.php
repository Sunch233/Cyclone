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

class SetTitlePacket extends DataPacket{

	const NETWORK_ID = Info::SET_TITLE_PACKET;

	const TYPE_CLEAR = 0, TYPE_CLEAR_TITLE = 0;
	const TYPE_RESET = 1, TYPE_RESET_TITLE = 1;
	const TYPE_TITLE = 2, TYPE_SET_TITLE = 2;
	const TYPE_SUB_TITLE = 3 ,TYPE_SET_SUBTITLE = 3;
	const TYPE_ACTION_BAR = 4, TYPE_SET_ACTIONBAR_MESSAGE = 4;
	const TYPE_TIMES = 5, TYPE_SET_ANIMATION_TIMES = 5;

	public $type;
	public $text;
	public $fadeInDuration;
	public $duration;
	public $fadeOutDuration;

	public function decode(){
		$this->type = $this->getVarInt();
		$this->text = $this->getString();
		$this->fadeInDuration = $this->getVarInt();
		$this->duration = $this->getVarInt();
		$this->fadeOutDuration = $this->getVarInt();
	}

	public function encode(){
		$this->reset();
		$this->putVarInt($this->type);
		$this->putString($this->text);
		$this->putVarInt($this->fadeInDuration);
		$this->putVarInt($this->duration);
		$this->putVarInt($this->fadeOutDuration);
	}

	/**
	 * @return PacketName|string
	 */
	public function getName(){
		return "SetTitlePacket";
	}

}
