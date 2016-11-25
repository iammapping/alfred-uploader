<?php

namespace Upload\Provider;

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Exception\S3Exception;

use Upload\Provider\ProviderInterface;
use Upload\Helper;


class S3Provider implements ProviderInterface {
	private $s3;

	private $bucket;
	private $accessKey;
	private $secretKey;
	private $region;
	private $baseurl;

	public function __construct() {
		$this->bucket = Helper::getenv('S3_BUCKET');
		$this->accessKey = Helper::getenv('S3_ACCESS_KEY');
		$this->secretKey = Helper::getenv('S3_SECRET_KEY');
		$this->region = Helper::getenv('S3_REGION');
		$this->baseurl = Helper::getenv('S3_BASEURL');

		$credentials = new Credentials($this->accessKey, $this->secretKey);
		$this->s3 = S3Client::factory(array(
			'version' => 'latest',
            'region'  => $this->region,
			'credentials' => $credentials
		));
	}

	public function upload($file) {
		$dirname = Helper::dirname();
		$filename = Helper::filename($file);

		$ret = $this->s3->putObject([
			'Bucket' => $this->bucket,
			'Key'    => $dirname . '/' . $filename,
			'Body'   => fopen($file, 'r'),
			'ACL'    => 'public-read',
		]);

		if ($this->baseurl) {
			return $this->baseurl . '/' . $dirname . '/' . $filename;
		} else {
			return $ret->get('ObjectURL');
		}
	}
}
