<?php

/*
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

/**
 * Minecraft: PE multiplayer protocol implementation
 */
namespace pocketmine\network\mcpe\protocol;


interface Info{

	/**
	 * Actual Minecraft: PE protocol version
	 */
	const CURRENT_PROTOCOL = 107;
	const ACCEPTED_PROTOCOLS = [105,106,107];
	const MINECRAFT_VERSION = ["v1.0.5","v1.0.6","v1.0.7","v1.0.8","v1.0.9"];
	const MINECRAFT_VERSION_NETWORK = "1.0.9";

	const LOGIN_PACKET = 0x01;
	const PLAY_STATUS_PACKET = 0x02;
	const SERVER_TO_CLIENT_HANDSHAKE_PACKET = 0x03;
	const CLIENT_TO_SERVER_HANDSHAKE_PACKET = 0x04;
	const DISCONNECT_PACKET = 0x05;
	const BATCH_PACKET = 0x06;
	const RESOURCE_PACKS_INFO_PACKET = 0x07;
	const RESOURCE_PACK_STACK_PACKET = 0x08; //ResourcePacksStackPacket
	const RESOURCE_PACK_CLIENT_RESPONSE_PACKET = 0x09;
	const TEXT_PACKET = 0x0a;
	const SET_TIME_PACKET = 0x0b;
	const START_GAME_PACKET = 0x0c;
	const ADD_PLAYER_PACKET = 0x0d;
	const ADD_ENTITY_PACKET = 0x0e;
	const REMOVE_ENTITY_PACKET = 0x0f;
	const ADD_ITEM_ENTITY_PACKET = 0x10;
	const ADD_HANGING_ENTITY_PACKET = 0x11;
	const TAKE_ITEM_ENTITY_PACKET = 0x12;
	const MOVE_ENTITY_PACKET = 0x13;
	const MOVE_PLAYER_PACKET = 0x14;
	const RIDER_JUMP_PACKET = 0x15;
	const REMOVE_BLOCK_PACKET = 0x16;
	const UPDATE_BLOCK_PACKET = 0x17;
	const ADD_PAINTING_PACKET = 0x18;
	const EXPLODE_PACKET = 0x19;
	const LEVEL_SOUND_EVENT_PACKET = 0x1a;
	const LEVEL_EVENT_PACKET = 0x1b;
	const BLOCK_EVENT_PACKET = 0x1c;
	const ENTITY_EVENT_PACKET = 0x1d;
	const MOB_EFFECT_PACKET = 0x1e;
	const UPDATE_ATTRIBUTES_PACKET = 0x1f;
	const MOB_EQUIPMENT_PACKET = 0x20;
	const MOB_ARMOR_EQUIPMENT_PACKET = 0x21;
	const INTERACT_PACKET = 0x22;
	const BLOCK_PICK_REQUEST_PACKET = 0x23;
	const USE_ITEM_PACKET = 0x24;
	const PLAYER_ACTION_PACKET = 0x25;
	const PLAYER_FALL_PACKET = 0x26;
	const HURT_ARMOR_PACKET = 0x27;
	const SET_ENTITY_DATA_PACKET = 0x28;
	const SET_ENTITY_MOTION_PACKET = 0x29;
	const SET_ENTITY_LINK_PACKET = 0x2a;
	const SET_HEALTH_PACKET = 0x2b;
	const SET_SPAWN_POSITION_PACKET = 0x2c;
	const ANIMATE_PACKET = 0x2d;
	const RESPAWN_PACKET = 0x2e;
	const DROP_ITEM_PACKET = 0x2f;
	const INVENTORY_ACTION_PACKET = 0x30;
	const CONTAINER_OPEN_PACKET = 0x31;
	const CONTAINER_CLOSE_PACKET = 0x32;
	const CONTAINER_SET_SLOT_PACKET = 0x33;
	const CONTAINER_SET_DATA_PACKET = 0x34;
	const CONTAINER_SET_CONTENT_PACKET = 0x35;
	const CRAFTING_DATA_PACKET = 0x36;
	const CRAFTING_EVENT_PACKET = 0x37;
	const ADVENTURE_SETTINGS_PACKET = 0x38;
	const BLOCK_ENTITY_DATA_PACKET = 0x39;
	const PLAYER_INPUT_PACKET = 0x3a;
	const FULL_CHUNK_DATA_PACKET = 0x3b;
	const SET_COMMANDS_ENABLED_PACKET = 0x3c;
	const SET_DIFFICULTY_PACKET = 0x3d;
	const CHANGE_DIMENSION_PACKET = 0x3e;
	const SET_PLAYER_GAME_TYPE_PACKET = 0x3f;
	const PLAYER_LIST_PACKET = 0x40;
	const EVENT_PACKET = 0x41; //TelemetryEventPacket
	const SPAWN_EXPERIENCE_ORB_PACKET = 0x42;
	const CLIENTBOUND_MAP_ITEM_DATA_PACKET = 0x43; //MapItemDataPacket
	const MAP_INFO_REQUEST_PACKET = 0x44;
	const REQUEST_CHUNK_RADIUS_PACKET = 0x45;
	const CHUNK_RADIUS_UPDATED_PACKET = 0x46;
	const ITEM_FRAME_DROP_ITEM_PACKET = 0x47;
	const REPLACE_ITEM_IN_SLOT_PACKET = 0x48; //ReplaceSelectedItemPacket
	const GAME_RULES_CHANGED_PACKET = 0x49;
	const CAMERA_PACKET = 0x4a;
	const ADD_ITEM_PACKET = 0x4b;
	const BOSS_EVENT_PACKET = 0x4c;
	const SHOW_CREDITS_PACKET = 0x4d;
	const AVAILABLE_COMMANDS_PACKET = 0x4e;
	const COMMAND_STEP_PACKET = 0x4f;
	const COMMAND_BLOCK_UPDATE_PACKET = 0x50;
	const UPDATE_TRADE_PACKET = 0x51;
	const RESOURCE_PACK_DATA_INFO_PACKET = 0x52;
	const RESOURCE_PACK_CHUNK_DATA_PACKET = 0x53;
	const RESOURCE_PACK_CHUNK_REQUEST_PACKET = 0x54;
	const TRANSFER_PACKET = 0x55;
	const PLAY_SOUND_PACKET = 0x56;
	const STOP_SOUND_PACKET = 0x57;
	const SET_TITLE_PACKET = 0x58;

}
