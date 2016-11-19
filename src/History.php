<?php

namespace Upload;

use Upload\Helper;

class History {
	const historyFile = '/tmp/alfred_workflow_img_uploaded_history';

	public static function get($query = '') {
		if (!file_exists(self::historyFile)) {
			touch(self::historyFile);
		}

		$history = file_get_contents(self::historyFile);
		if (!empty($history)) {
			$history = json_decode($history, true);
		} else {
			$history = array();
		}

		if (empty($query)) {
			return $history;
		} else {
			return array_filter($history, function($it) use ($query) {
				return stripos($it['file'], $query) !== false || stripos($it['url'], $query) !== false;
			});
		}
	}

	public static function set($items) {
		if (!file_exists(self::historyFile)) {
			touch(self::historyFile);
		}

		$handle = fopen(self::historyFile, 'r+');

		if(flock($handle, LOCK_EX)) {
			// get saved history
			$saved = array();
			if (filesize(self::historyFile) > 0) {
			    $saved = fread($handle, filesize(self::historyFile));
			    if (!empty($saved)) {
			    	$saved = json_decode($saved, true);
			    }
			}

		    // sort history by time desc
		    $saved = array_merge($saved, $items);
		    uasort($saved, function($a, $b) {
		    	return $b['time'] - $a['time'];
		    });

		    // just keep history items below UPLOAD_HISTORY_COUNT
		    $saved = array_slice($saved, 0, Helper::getenv('UPLOAD_HISTORY_COUNT', 50));

		    ftruncate($handle, 0);  
		    rewind($handle);
		    fwrite($handle, json_encode($saved));
		    flock($handle, LOCK_UN);
		} else {
		    echo "Could not Lock File!";
		}

		fclose($handle);
	}
}