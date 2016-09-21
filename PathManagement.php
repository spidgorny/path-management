<?php

class PathManagement {

	function run() {
		$cmd = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL;
		if ($cmd) {
			return $this->$cmd();
		} else {
			return $this->help();
		}
	}

	function help() {
		return 'help';
	}

	function split() {
		//print_r($_SERVER);
		$path = $_SERVER['Path'];	// WFT? PATH?
		$pathParts = explode(';', $path);
		echo 'Original paths: ', sizeof($pathParts), PHP_EOL;
		$pathParts = array_map('trim', $pathParts);
		$pathParts = $this->normalize($pathParts);
		echo 'Unique paths: ', sizeof($pathParts), PHP_EOL;

		// marks systems paths with #
		$userPaths = $this->getUserPath();
		$userPaths = $this->normalize($userPaths);
		if ($userPaths) {
			$pathParts = array_map(function ($el) use ($userPaths) {
				if (!in_array($el, $userPaths)) {
					return '# '.$el;
				}
				return $el;
			}, $pathParts);
		}

		file_put_contents('path.txt', implode(PHP_EOL, $pathParts));
	}

	function normalize(array $pathParts) {
		// remove trailing \
		$pathParts = array_map(function ($p) {
			if (strlen($p) && $p[strlen($p)-1] == '\\') {
				return substr($p, 0, strlen($p) - 1);
			} else {
				return $p;
			}
		}, $pathParts);
		$pathParts = array_filter($pathParts);
		$pathParts = array_unique($pathParts);
		sort($pathParts);

		// check path exists
		array_map(function ($el) {
			if (!is_dir($el)) {
				echo 'Path does not exist ['.$el.']', PHP_EOL;
			}
		}, $pathParts);
		return $pathParts;
	}

	function merge() {
		$pathParts = file('path.txt');
		$pathParts = array_map('trim', $pathParts);

		// remove system paths which we can not modify
		$pathParts = array_filter($pathParts, function ($el) {
			return $el[0] != '#';
		});

		$path = implode(';', $pathParts);
		echo 'Merged path length: ', strlen($path), PHP_EOL;
		$cmd = 'setx PATH "'.$path.'"';
		echo '> ', $cmd, PHP_EOL;
		exec($cmd);
	}

	function getUserPath() {
		// requires extension
		//reg_open_key();

		// requires extension
		//$swbemLocator = new \COM('WbemScripting.SWbemLocator', null, CP_UTF8);

		// requires Admin rights
//		exec('regedit /e path.reg HKEY_LOCAL_MACHINE\SYSTEM\CurrentControlSet\Control\Session Manager\Environment');

		// can run anywhere
		exec('regedit /e path.reg HKEY_CURRENT_USER\Environment');
		$pathReg = file_get_contents('path.reg');
		$pathReg = mb_convert_encoding($pathReg, 'UTF-8', 'UTF-16LE');
		$pathReg = explode("\n", $pathReg);
		// convert to INI format
		$pathReg = array_map(function ($el) {
			$el = trim($el);
			$el = str_replace('"="', '="', $el);
			if (strpos($el, '"=') !== false) {
				$el = str_replace('"=', '="', $el);
				$el .= '"';
			}
			if (strlen($el) > 0 && $el[0] != '[') {
				$el = mb_substr($el, 1);    // del quotes
			}
			return $el;
		}, $pathReg);
		unset($pathReg[0]);	// Windows Registry Editor Version 5.00
		unset($pathReg[1]);
		file_put_contents('path.ini', implode("\n", $pathReg));
		$ini = parse_ini_file('path.ini');
		//print_r($ini);
		$userPath = $ini['Path'];
		$userPath = explode(';', $userPath);
		return $userPath;
	}

}

$p = new PathManagement();
echo $p->run();
