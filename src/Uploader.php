<?php

namespace Upload;

use Upload\Provider\ProviderInterface;
use Upload\Provider\UpyunProvider;
use Upload\Helper;

class Uploader {
	private $provider;

	private $files;

	public function __construct($files, $env = array()) {
		$this->files = $files;

		Helper::setenv($env);

		$provider = 'Upload\\Provider\\' . ucfirst(strtolower(Helper::getenv('UPLOAD_PROVIDER', 'upyun'))) . 'Provider';
		if (!class_exists($provider)) {
			throw new \Exception("{$provider} is not defined");
		}

		$this->provider = new $provider();
	}

	public function upload() {
		$res = array();
		foreach ($this->files as $file) {
			$url = $this->provider->upload($file);
			$res[] = array(
				'file' => $file,
				'url' => $url,
				'time' => time()
			);
		}

		return $res;
	}
}