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
 *
*/

/**
 * Various Utilities used around the code
 */
namespace pocketmine\utils;
use pocketmine\ThreadManager;

/**
 * Big collection of functions
 */
class Utils{
	public static $online = true;
	public static $ip = false;
	public static $os;
	private static $serverUniqueId = null;

	/**
	 * Generates an unique identifier to a callable
	 *
	 * @param callable $variable
	 *
	 * @return string
	 */
	public static function getCallableIdentifier(callable $variable){
		if(is_array($variable)){
			return sha1(strtolower(spl_object_hash($variable[0])) . "::" . strtolower($variable[1]));
		}else{
			return sha1(strtolower($variable));
		}
	}

	/**
	 * Gets this machine / server instance unique ID
	 * Returns a hash, the first 32 characters (or 16 if raw)
	 * will be an identifier that won't change frequently.
	 * The rest of the hash will change depending on other factors.
	 *
	 * @param string $extra optional, additional data to identify the machine
	 *
	 * @return UUID
	 */
	public static function getMachineUniqueId($extra = ""){
		if(self::$serverUniqueId !== null and $extra === ""){
			return self::$serverUniqueId;
		}

		$machine = php_uname("a");
		$machine .= file_exists("/proc/cpuinfo") ? implode(preg_grep("/(model name|Processor|Serial)/", file("/proc/cpuinfo"))) : "";
		$machine .= sys_get_temp_dir();
		$machine .= $extra;
		$os = Utils::getOS();
		if($os === "win"){
			@exec("ipconfig /ALL", $mac);
			$mac = implode("\n", $mac);
			if(preg_match_all("#Physical Address[. ]{1,}: ([0-9A-F\\-]{17})#", $mac, $matches)){
				foreach($matches[1] as $i => $v){
					if($v == "00-00-00-00-00-00"){
						unset($matches[1][$i]);
					}
				}
				$machine .= implode(" ", $matches[1]); //Mac Addresses
			}
		}elseif($os === "linux"){
			if(file_exists("/etc/machine-id")){
				$machine .= file_get_contents("/etc/machine-id");
			}else{
				@exec("ifconfig", $mac);
				$mac = implode("\n", $mac);
				if(preg_match_all("#HWaddr[ \t]{1,}([0-9a-f:]{17})#", $mac, $matches)){
					foreach($matches[1] as $i => $v){
						if($v == "00:00:00:00:00:00"){
							unset($matches[1][$i]);
						}
					}
					$machine .= implode(" ", $matches[1]); //Mac Addresses
				}
			}
		}elseif($os === "android"){
			$machine .= @file_get_contents("/system/build.prop");
		}elseif($os === "mac"){
			$machine .= `system_profiler SPHardwareDataType | grep UUID`;
		}
		$data = $machine . PHP_MAXPATHLEN;
		$data .= PHP_INT_MAX;
		$data .= PHP_INT_SIZE;
		$data .= get_current_user();
		foreach(get_loaded_extensions() as $ext){
			$data .= $ext . ":" . phpversion($ext);
		}

		$uuid = UUID::fromData($machine, $data);

		if($extra === ""){
			self::$serverUniqueId = $uuid;
		}

		return $uuid;
	}

	/**
	 * Gets the External IP using an external service, it is cached
	 *
	 * @param bool $force default false, force IP check even when cached
	 *
	 * @return string
	 */

	public static function getIP($force = false){
		if(Utils::$online === false){
			return false;
		}elseif(Utils::$ip !== false and $force !== true){
			return Utils::$ip;
		}
		$ip = trim(strip_tags(Utils::getURL("https://api.ipify.org")));
		if($ip){
			Utils::$ip = $ip;
		}else{
			$ip = Utils::getURL("http://www.checkip.org/");
			if(preg_match('#">([0-9a-fA-F\:\.]*)</span>#', $ip, $matches) > 0){
				Utils::$ip = $matches[1];
			}else{
				$ip = Utils::getURL("http://checkmyip.org/");
				if(preg_match('#Your IP address is ([0-9a-fA-F\:\.]*)#', $ip, $matches) > 0){
					Utils::$ip = $matches[1];
				}else{
					$ip = trim(Utils::getURL("http://ifconfig.me/ip"));
					if($ip != ""){
						Utils::$ip = $ip;
					}else{
						return false;
					}
				}
			}
		}

		return Utils::$ip;

	}

	/**
	 * Returns the current Operating System
	 * Windows => win
	 * MacOS => mac
	 * iOS => ios
	 * Android => android
	 * Linux => Linux
	 * BSD => bsd
	 * Other => other
	 *
	 * @param bool $recalculate
	 *
	 * @return string
	 */
	public static function getOS($recalculate = false){
		if(self::$os === null or $recalculate){
			$uname = php_uname("s");
			if(stripos($uname, "Darwin") !== false){
				if(strpos(php_uname("m"), "iP") === 0){
					self::$os = "ios";
				}else{
					self::$os = "mac";
				}
			}elseif(stripos($uname, "Win") !== false or $uname === "Msys"){
				self::$os = "win";
			}elseif(stripos($uname, "Linux") !== false){
				if(@file_exists("/system/build.prop")){
					self::$os = "android";
				}else{
					self::$os = "linux";
				}
			}elseif(stripos($uname, "BSD") !== false or $uname === "DragonFly"){
				self::$os = "bsd";
			}else{
				self::$os = "other";
			}
		}
		
		return self::$os;
	}


	public static function getRealMemoryUsage(){
		$stack = 0;
		$heap = 0;

		if(Utils::getOS() === "linux" or Utils::getOS() === "android"){
			$mappings = file("/proc/self/maps");
			foreach($mappings as $line){
				if(preg_match("#([a-z0-9]+)\\-([a-z0-9]+) [rwxp\\-]{4} [a-z0-9]+ [^\\[]*\\[([a-zA-z0-9]+)\\]#", trim($line), $matches) > 0){
					if(strpos($matches[3], "heap") === 0){
						$heap += hexdec($matches[2]) - hexdec($matches[1]);
					}elseif(strpos($matches[3], "stack") === 0){
						$stack += hexdec($matches[2]) - hexdec($matches[1]);
					}
				}
			}
		}

		return [$heap, $stack];
	}

	public static function getMemoryUsage($advanced = false){
		$reserved = memory_get_usage();
		$VmSize = null;
		$VmRSS = null;
		if(Utils::getOS() === "linux" or Utils::getOS() === "android"){
			$status = file_get_contents("/proc/self/status");
			if(preg_match("/VmRSS:[ \t]+([0-9]+) kB/", $status, $matches) > 0){
				$VmRSS = $matches[1] * 1024;
			}

			if(preg_match("/VmSize:[ \t]+([0-9]+) kB/", $status, $matches) > 0){
				$VmSize = $matches[1] * 1024;
			}
		}

		//TODO: more OS

		if($VmRSS === null){
			$VmRSS = memory_get_usage();
		}

		if(!$advanced){
			return $VmRSS;
		}

		if($VmSize === null){
			$VmSize = memory_get_usage(true);
		}

		return [$reserved, $VmRSS, $VmSize];
	}

	public static function getThreadCount(){
		if(Utils::getOS() === "linux" or Utils::getOS() === "android"){
			if(preg_match("/Threads:[ \t]+([0-9]+)/", file_get_contents("/proc/self/status"), $matches) > 0){
				return (int) $matches[1];
			}
		}
		//TODO: more OS

		return count(ThreadManager::getInstance()->getAll()) + 3; //RakLib + MainLogger + Main Thread
	}

	public static function getCoreCount($recalculate = false){
		static $processors = 0;

		if($processors > 0 and !$recalculate){
			return $processors;
		}else{
			$processors = 0;
		}

		switch(Utils::getOS()){
			case "linux":
			case "android":
				if(file_exists("/proc/cpuinfo")){
					foreach(file("/proc/cpuinfo") as $l){
						if(preg_match('/^processor[ \t]*:[ \t]*[0-9]+$/m', $l) > 0){
							++$processors;
						}
					}
				}else{
					if(preg_match("/^([0-9]+)\\-([0-9]+)$/", trim(@file_get_contents("/sys/devices/system/cpu/present")), $matches) > 0){
						$processors = (int) ($matches[2] - $matches[1]);
					}
				}
				break;
			case "bsd":
			case "mac":
				$processors = (int) `sysctl -n hw.ncpu`;
				$processors = (int) `sysctl -n hw.ncpu`;
				break;
			case "win":
				$processors = (int) getenv("NUMBER_OF_PROCESSORS");
				break;
		}
		return $processors;
	}

	/**
	 * Returns a prettified hexdump
	 *
	 * @param string $bin
	 *
	 * @return string
	 */
	public static function hexdump($bin){
		$output = "";
		$bin = str_split($bin, 16);
		foreach($bin as $counter => $line){
			$hex = chunk_split(chunk_split(str_pad(bin2hex($line), 32, " ", STR_PAD_RIGHT), 2, " "), 24, " ");
			$ascii = preg_replace('#([^\x20-\x7E])#', ".", $line);
			$output .= str_pad(dechex($counter << 4), 4, "0", STR_PAD_LEFT) . "  " . $hex . " " . $ascii . PHP_EOL;
		}

		return $output;
	}


	/**
	 * Returns a string that can be printed, replaces non-printable characters
	 *
	 * @param $str
	 *
	 * @return string
	 */
	public static function printable($str){
		if(!is_string($str)){
			return gettype($str);
		}

		return preg_replace('#([^\x20-\x7E])#', '.', $str);
	}

	/*
	public static function angle3D($pos1, $pos2){
		$X = $pos1["x"] - $pos2["x"];
		$Z = $pos1["z"] - $pos2["z"];
		$dXZ = sqrt(pow($X, 2) + pow($Z, 2));
		$Y = $pos1["y"] - $pos2["y"];
		$hAngle = rad2deg(atan2($Z, $X) - M_PI_2);
		$vAngle = rad2deg(-atan2($Y, $dXZ));

		return array("yaw" => $hAngle, "pitch" => $vAngle);
	}*/

	/**
	 * GETs an URL using cURL
	 * NOTE: This is a blocking operation and can take a significant amount of time. It is inadvisable to use this method on the main thread.
	 *
	 * @param         $page
	 * @param int     $timeout default 10
	 * @param array   $extraHeaders
	 * @param string  &$err    Will be set to the output of curl_error(). Use this to retrieve errors that occured during the operation.
	 * @param array[] &$headers
	 * @param int     &$httpCode
	 *
	 * @return bool|mixed false if an error occurred, mixed data if successful.
	 */
	public static function getURL($page, $timeout = 10, array $extraHeaders = [], &$err = null, &$headers = null, &$httpCode = null){
		try{
			list($ret, $headers, $httpCode) = self::simpleCurl($page, $timeout, $extraHeaders);
			return $ret;
		}catch(\RuntimeException $ex){
			$err = $ex->getMessage();
			return false;
		}
	}

	/**
	 * POSTs data to an URL
	 * NOTE: This is a blocking operation and can take a significant amount of time. It is inadvisable to use this method on the main thread.
	 *
	 * @param string       $page
	 * @param array|string $args
	 * @param int          $timeout
	 * @param array        $extraHeaders
	 * @param string       &$err Will be set to the output of curl_error(). Use this to retrieve errors that occured during the operation.
	 * @param array[]      &$headers
	 * @param int          &$httpCode
	 *
	 * @return bool|mixed false if an error occurred, mixed data if successful.
	 */
	public static function postURL($page, $args, $timeout = 10, array $extraHeaders = [], &$err = null, &$headers = null, &$httpCode = null){
		try{
			list($ret, $headers, $httpCode) = self::simpleCurl($page, $timeout, $extraHeaders, [
				CURLOPT_POST => 1,
				CURLOPT_POSTFIELDS => $args
			]);
			return $ret;
		}catch(\RuntimeException $ex){
			$err = $ex->getMessage();
			return false;
		}

	}

	/**
	 * General cURL shorthand function.
	 * NOTE: This is a blocking operation and can take a significant amount of time. It is inadvisable to use this method on the main thread.
	 *
	 * @param string        $page
	 * @param float|int     $timeout      The maximum connect timeout and timeout in seconds, correct to ms.
	 * @param string[]      $extraHeaders extra headers to send as a plain string array
	 * @param array         $extraOpts    extra CURLOPT_* to set as an [opt => value] map
	 * @param callable|null $onSuccess    function to be called if there is no error. Accepts a resource argument as the cURL handle.
	 *
	 * @return array a plain array of three [result body : string, headers : array[], HTTP response code : int]. Headers are grouped by requests with strtolower(header name) as keys and header value as values
	 *
	 * @throws \RuntimeException if a cURL error occurs
	 */
	public static function simpleCurl(string $page, $timeout = 10, array $extraHeaders = [], array $extraOpts = [], callable $onSuccess = null){
		if(Utils::$online === false){
			throw new \RuntimeException("Server is offline");
		}

		$ch = curl_init($page);

		curl_setopt_array($ch, $extraOpts + [
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => 2,
				CURLOPT_FORBID_REUSE => 1,
				CURLOPT_FRESH_CONNECT => 1,
				CURLOPT_AUTOREFERER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CONNECTTIMEOUT_MS => (int) ($timeout * 1000),
				CURLOPT_TIMEOUT_MS => (int) ($timeout * 1000),
				CURLOPT_HTTPHEADER => array_merge(["User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0 PocketMine-MP"], $extraHeaders),
				CURLOPT_HEADER => true
			]);
		try{
			$raw = curl_exec($ch);
			$error = curl_error($ch);
			if($error !== ""){
				throw new \RuntimeException($error);
			}
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$rawHeaders = substr($raw, 0, $headerSize);
			$body = substr($raw, $headerSize);
			$headers = [];
			foreach(explode("\r\n\r\n", $rawHeaders) as $rawHeaderGroup){
				$headerGroup = [];
				foreach(explode("\r\n", $rawHeaderGroup) as $line){
					$nameValue = explode(":", $line, 2);
					if(isset($nameValue[1])) $headerGroup[trim(strtolower($nameValue[0]))] = trim($nameValue[1]);
				}
				$headers[] = $headerGroup;
			}
			if($onSuccess !== null){
				$onSuccess($ch);
			}
			return [$body, $headers, $httpCode];
		}finally{
			curl_close($ch);
		}
	}

	/**
	 * Generator which forces array keys to string during iteration.
	 * This is necessary because PHP has an anti-feature where it casts numeric string keys to integers, leading to
	 * various crashes.
	 *
	 * @phpstan-template TKeyType of string
	 * @phpstan-template TValueType
	 * @phpstan-param array<TKeyType, TValueType> $array
	 * @phpstan-return \Generator<TKeyType, TValueType, void, void>
	 */
	public static function stringifyKeys(array $array): \Generator{
		foreach($array as $key => $value){
			yield (string)$key => $value;
		}
	}

	/**
	 * @throws Exception
	 */
	public static function assumeNotFalse(mixed $value, \Closure|string $context = "This should never be false"): mixed{
		if($value === false){
			throw new \Exception("Assumption failure: " . (is_string($context) ? $context : $context()) . " (THIS IS A BUG)");
		}
		return $value;
	}

	public static function javaStringHash($string){
		$hash = 0;
		for($i = 0; $i < strlen($string); $i++){
			$ord = ord($string[$i]);
			if($ord & 0x80){
				$ord -= 0x100;
			}
			$hash = 31 * $hash + $ord;
			while($hash > 0x7FFFFFFF){
				$hash -= 0x100000000;
			}
			while($hash < -0x80000000){
				$hash += 0x100000000;
			}
			$hash &= 0xFFFFFFFF;
		}
		return $hash;
	}

}
