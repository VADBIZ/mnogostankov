<?php
namespace Cache;
class Memcached {
	const CACHEDUMP_LIMIT = 9999;
	private $expire;
	private $memcached;

	public function __construct($expire) {
		$this->expire = $expire;
		$this->memcached = new \Memcached();

		$this->memcached->addServer(CACHE_HOSTNAME, CACHE_PORT);
	}

	public function get($key) {
		return $this->memcached->get(CACHE_PREFIX . $key);
	}

	public function set($key, $value) {
		return $this->memcached->set(CACHE_PREFIX . $key, $value, $this->expire);
	}

	public function delete($key) {
		$this->memcached->delete(CACHE_PREFIX . $key);
	}
}
