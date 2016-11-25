<?php

namespace Upload;

use Upload\Provider\ProviderInterface;
use Upload\Provider\UpyunProvider;

class Helper {
	public static $env = array();

	public static function getenv($name, $default = '') {
		if (isset(self::$env[$name])) {
			return self::$env[$name];
		} else {
			return !empty(getenv($name)) ? getenv($name) : $default;
		}
	}

	public static function setenv($name, $value = null) {
		if (!is_array($name)) {
			$name = array($name => $value);
		}

		self::$env = array_merge(self::$env, $name);
	}

	public static function dirname() {
		return date('Y/m/d');
	}

	public static function filename($rawname) {
		$info = pathinfo($rawname);
		$useRawName = self::getenv('USE_RAW_FILENAME', false);
		if (!$useRawName) {
			$rand = '';
			$i = 8;
			while ($i--) {
				$rand .= rand(0, 9);
			}
			return time() . $rand . '.' . $info['extension'];
		} else {
			return preg_replace('/(\s|[?#%:])+/', '_', $info['filename']) . '.' . $info['extension'];
		}
	}
}