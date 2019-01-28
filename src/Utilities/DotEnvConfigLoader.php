<?php
/**
 * clp-php-sdk
 * DotEnvConfigLoader.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-28, 16:10
 */

namespace Clay\CLP\Utilities;

class DotEnvConfigLoader implements \Illuminate\Contracts\Config\Repository {

	/**
	 * @var string $prefix The prefix for config entries
	 */
	protected $prefix;

	/**
	 * @var \Dotenv\Loader The .env file loader
	 */
	private $env;

	/**
	 * @var array The actual ENV variables loaded
	 */
	private $config = [];

	/**
	 * DotEnvConfigLoader constructor.
	 * @param \Dotenv\Dotenv $env
	 * @param string $prefix [optional] The config prefix.
	 */
	public function __construct(\Dotenv\Dotenv $env, ?string $prefix = null) {
		$this->prefix = $prefix;
		$this->env = $env;

		$this->config = $this->env->load();
	}

	/**
	 * Translates a config key (lorem.ipsum.dolor) into an env key (LOREM_IPSUM_DOLOR).
	 * Respects the set prefix.
	 *
	 * @param string $configKey
	 * @return string
	 */
	protected function translateEnvKey(string $configKey) : string {
		$prefix = !is_null($this->prefix) ? "{$this->prefix}_" : '';
		return strtoupper($prefix . str_replace('.', '_', $configKey));
	}

	/**
	 * Determine if the given configuration value exists.
	 *
	 * @param  string $key
	 * @return bool
	 */
	public function has($key) {
		$envKey = $this->translateEnvKey($key);
		return isset($this->config[$envKey]);
	}

	/**
	 * Get the specified configuration value.
	 *
	 * @param  array|string $key
	 * @param  mixed $default
	 * @return mixed
	 */
	public function get($key, $default = null) {
		$envKey = $this->translateEnvKey($key);
		return $this->config[$envKey] ?? $default;
	}

	/**
	 * Get all of the configuration items for the application.
	 *
	 * @return array
	 */
	public function all() {
		return (array) ($this->config ?? []);
	}

	/**
	 * Set a given configuration value.
	 *
	 * @param  array|string $key
	 * @param  mixed $value
	 * @return void
	 * @deprecated
	 * @throws Exception
	 */
	public function set($key, $value = null) {
		throw new \Exception("Cannot write configuration values with DotEnv.");
	}

	/**
	 * Prepend a value onto an array configuration value.
	 *
	 * @param  string $key
	 * @param  mixed $value
	 * @return void
	 * @deprecated
	 * @throws Exception
	 */
	public function prepend($key, $value) {
		throw new \Exception("Cannot write configuration values with DotEnv.");
	}

	/**
	 * Push a value onto an array configuration value.
	 *
	 * @param  string $key
	 * @param  mixed $value
	 * @return void
	 * @deprecated
	 * @throws Exception
	 */
	public function push($key, $value) {
		throw new \Exception("Cannot write configuration values with DotEnv.");
	}
}