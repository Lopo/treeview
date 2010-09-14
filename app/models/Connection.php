<?php
use Nette\Object,
	Nette\Environment;

class Connection
extends Object
{
	static public function initialize()
	{
		self::connect();
	}

	static public function connect()
	{
		$conf=Environment::getConfig('database');
		dibi::connect($conf['sqlite'], 'sqlite');
		if ($conf->profiler) {
			$profiler=is_numeric($conf->profiler) || is_bool($conf->profiler) ?
				new DibiProfiler(array()) : new $conf->profiler;
			$profiler->setFile(Environment::expand('%logDir%').'/sqlite.log');
			dibi::getConnection('sqlite')->setProfiler($profiler);
			}
	}

	static public function disconnect()
	{
		dibi::disconnect();
	}

	static public function reconnect()
	{
		self::disconnect();
		self::connect();
	}
}