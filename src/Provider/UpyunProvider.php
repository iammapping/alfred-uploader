<?php

namespace Upload\Provider;

use UpYun;

use Upload\Provider\ProviderInterface;
use Upload\Helper;

class UpyunProvider implements ProviderInterface {
	private $upyun;

	private $bucket;
	private $user;
	private $pwd;
	private $useStream;
	private $baseurl;

	private $options;

	public function __construct() {
		$this->bucket = Helper::getenv('UPYUN_BUCKET');
		$this->user = Helper::getenv('UPYUN_USER');
		$this->pwd = Helper::getenv('UPYUN_PWD');
		$this->useStream = Helper::getenv('UPYUN_USE_STREAM', false);
		$this->baseurl = Helper::getenv('UPYUN_BASEURL', 'https://' . $this->bucket . '.b0.upaiyun.com');

		$this->upyun = new UpYun($this->bucket, $this->user, $this->pwd);
	}

	public function upload($file) {
		// key start with '/'
		$dirname = '/' . Helper::dirname();
		$filename = Helper::filename($file);
		if ($this->useStream) {
			$fh = fopen($file, 'r');
			$this->upyun->writeFile($dirname . '/' . $filename, $fh, true);
			fclose($fh);
		} else {
			$this->upyun->writeFile($dirname . '/' . $filename, file_get_contents($file), true);
		}

		return $this->baseurl . $dirname . '/' . $filename;
	}
}
