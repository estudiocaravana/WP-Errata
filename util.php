<?php 

class Util {

	static public function getIpAddress() {
	    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
	        if (array_key_exists($key, $_SERVER) === true) {
	            foreach (explode(',', $_SERVER[$key]) as $ip) {
	                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
	                    return $ip;
	                }
	            }
	        }
    	}
	}

}

?>