<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 * 这注释不太好写啊(
 *
 */

namespace pocketmine\entity;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item as ItemItem;
use pocketmine\math\Vector3;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;
use function mt_rand;

class Pig extends Animal {
    const NETWORK_ID = 12;

    public $width = 0.3;
    public $length = 0.9;
    public $height = 1.9;

    public $dropExp = [1, 3];

    private $moveCooldown = 0; // 移动的冷却时间
    private $moveDuration = 0; // 移动的持续时间
    private $fleeing = false; // 是否正在逃跑
    private $fleeDistance = 0; // 逃跑距离
    private $followingPlayer = null; // 正在跟随的玩家

    public function getName(): string {
        return "Pig";
    }

    public function initEntity() {
        parent::initEntity();
    }

    public function spawnTo(Player $player) {
        $pk = new AddEntityPacket();
        $pk->eid = $this->getId();
        $pk->type = Pig::NETWORK_ID;
        $pk->x = $this->x;
        $pk->y = $this->y;
        $pk->z = $this->z;
        $pk->speedX = $this->motionX;
        $pk->speedY = $this->motionY;
        $pk->speedZ = $this->motionZ;
        $pk->yaw = $this->yaw;
        $pk->pitch = $this->pitch;
        $pk->metadata = $this->dataProperties;
        $player->dataPacket($pk);

        parent::spawnTo($player);
    }

    public function getDrops() {
        $lootingL = 0;
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent && $cause->getDamager() instanceof Player) {
            $lootingL = $cause->getDamager()->getItemInHand()->getEnchantmentLevel(Enchantment::TYPE_WEAPON_LOOTING);
        }
        $drops = [ItemItem::get(ItemItem::RAW_PORKCHOP, 0, mt_rand(1, 3 + $lootingL))];
        return $drops;
    }

    public function onUpdate($currentTick) {
        if ($this->closed) {
            return false;
        }

        parent::onUpdate($currentTick);

        // 我觉得这路得这么走（）
        if ($this->isAlive()) {
            if ($this->fleeing) {
                $this->flee();
            } else {
                $this->checkForCarrotHolder();
                if ($this->followingPlayer !== null) {
                    $this->followPlayer(); // 跟随玩家
                } else {
                    $this->moveAround(); // 随机移动
                }
            }
        }

        return true;
    }

    protected function checkForCarrotHolder() {
        $players = $this->getLevel()->getPlayers();
        foreach ($players as $player) {
            if ($player->getInventory()->getItemInHand()->getId() === ItemItem::CARROT) {
                if ($this->distance($player) <= 10) {
                    $this->followingPlayer = $player;
                    return;
                }
            }
        }

        $this->followingPlayer = null;
    }

    protected function followPlayer() {
        if ($this->followingPlayer === null || !$this->followingPlayer->isOnline()) {
            $this->followingPlayer = null;
            return;
        }

        // 获取玩家位置
        $target = $this->followingPlayer;
        $direction = $this->getDirectionVectorTo($target);

        // 调整高度
        $targetY = $target->y;
        $currentY = $this->y;

        // 移动
        $this->motionX = $direction->x * 0.15; // 移动速度
        $this->motionZ = $direction->z * 0.15;

        // 调整朝向
        $this->yaw = rad2deg(atan2(-$direction->x, $direction->z));
        $this->updateMovement();
    }

    protected function moveAround() {
        // 移动冷却时间
        if ($this->moveCooldown > 0) {
            $this->moveCooldown--;
            return;
        }

        // 移动持续时间
        if ($this->moveDuration > 0) {
            $this->moveDuration--;
            $this->moveForward();
            return;
        }

        // 随机选择移动方向和持续时间
        $this->moveDuration = mt_rand(20, 40); // 持续1到2秒
        $this->moveCooldown = mt_rand(40, 80); // 冷却2到4秒

        // 调整方向
        $this->yaw = mt_rand(0, 360); // 随机旋转角度
        $this->updateMovement();
    }

    protected function moveForward() {
        // 获取当前方向
        $direction = $this->getDirectionVector();

        // 检查前方地形
        $nextBlock = $this->getLevel()->getBlock($this->add($direction->x, 0, $direction->z));
        $nextBlockUp = $this->getLevel()->getBlock($this->add($direction->x, 1, $direction->z));
        $nextBlockDown = $this->getLevel()->getBlock($this->add($direction->x, -1, $direction->z));

        // 判断前方地形
        if ($nextBlock->isSolid()) {
            if (!$nextBlockUp->isSolid()) {
                $this->motionY = 0.42;
            } else {
                $this->moveDuration = 0;
                return;
            }
        } elseif ($nextBlockDown->getId() === 0) {
            $depth = 0;
            for ($i = 1; $i <= 2; $i++) {
                $block = $this->getLevel()->getBlock($this->add($direction->x, -$i, $direction->z));
                if ($block->getId() !== 0) {
                    break;
                }
                $depth++;
            }

            if ($depth >= 2) {
                $this->moveDuration = 0;
                return;
            }
        }

        // 移动
        $this->motionX = $direction->x * 0.10; // 速度
        $this->motionZ = $direction->z * 0.10;
    }

    public function attack($damage, EntityDamageEvent $source) {
        parent::attack($damage, $source);

        // 这已经不是普通的猪了，必须出重拳！
        if ($source instanceof EntityDamageByEntityEvent && $source->getDamager() instanceof Player) {
            $this->fleeing = true;
            $this->fleeDistance = 0;

            // 向后！转！
            $this->yaw += 180;
            $this->updateMovement();
        }
    }

    protected function flee() {
        // 获取当前方向
        $direction = $this->getDirectionVector();

        // 移动
        $this->motionX = $direction->x * 0.14; // 逃跑速度
        $this->motionZ = $direction->z * 0.14;

        // 更新逃跑距离
        $this->fleeDistance += sqrt($this->motionX ** 2 + $this->motionZ ** 2);

        // 如果逃跑距离超过 6 格，则停止逃跑
        if ($this->fleeDistance >= 6) {
            $this->fleeing = false;
            $this->fleeDistance = 0;
        }
    }

    protected function getDirectionVectorTo(Player $player) {
        $dx = $player->x - $this->x;
        $dz = $player->z - $this->z;
        $length = sqrt($dx * $dx + $dz * $dz);
        if ($length == 0) {
            return new Vector3(0, 0, 0);
        }
        return new Vector3($dx / $length, 0, $dz / $length);
    }
}
