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
		$pathParts = array_map(function ($p) {
			if ($p[strlen($p)-1] == '\\') {
				return substr($p, 0, strlen($p) - 1);
			} else {
				return $p;
			}
		}, $pathParts);
		$pathParts = array_unique($pathParts);
		sort($pathParts);
		echo 'Unique paths: ', sizeof($pathParts), PHP_EOL;
		file_put_contents('path.txt', implode(PHP_EOL, $pathParts));
	}

	function merge() {
		$pathParts = file('path.txt');
		$pathParts = array_map('trim', $pathParts);
		$path = implode(';', $pathParts);
		echo 'Merged path length: ', strlen($path), PHP_EOL;
		$cmd = 'setx PATH "'.$path.'"';
		echo '> ', $cmd, PHP_EOL;
		exec($cmd);
	}

}

$p = new PathManagement();
echo $p->run();
