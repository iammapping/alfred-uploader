<?php

namespace Upload\Provider;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

use Upload\Provider\ProviderInterface;
use Upload\Helper;


class QiniuProvider implements ProviderInterface {
	private $qiniu;

	private $bucket;
	private $accessKey;
	private $secretKey;
	private $token;
	private $baseurl;

	public function __construct() {
		$this->bucket = Helper::getenv('QINIU_BUCKET');
		$this->accessKey = Helper::getenv('QINIU_ACCESS_KEY');
		$this->secretKey = Helper::getenv('QINIU_SECRET_KEY');
		$this->baseurl = Helper::getenv('QINIU_BASEURL');

		$auth = new Auth($this->accessKey, $this->secretKey);
		$this->token = $auth->uploadToken($this->bucket);

		$this->qiniu = new UploadManager();
	}

	public function upload($file) {
		$dirname = Helper::dirname();
		$filename = Helper::filename($file);

	    list($ret, $err) = $this->qiniu->putFile($this->token, $dirname . '/' . $filename, $file);
	    if ($err !== null) {
	        throw new \Exception($err->message());
	    }

		return $this->baseurl . '/' . $dirname . '/' . $filename;
	}
}
