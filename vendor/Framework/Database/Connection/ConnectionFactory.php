<?php

namespace Framework\Database\Connection;

use Framework\Database\Connector\MysqlConnector;

class ConnectionFactory
{

	/*protected function createConnection($driver, PDO $connection, $database, $prefix = '', array $config = array())
	{
		if ($this->container->bound($key = "db.connection.{$driver}"))
		{
			return $this->container->make($key, array($connection, $database, $prefix, $config));
		}

		switch ($driver)
		{
			case 'mysql':
				return new MySqlConnection($connection, $database, $prefix, $config);

			case 'pgsql':
				return new PostgresConnection($connection, $database, $prefix, $config);

			case 'sqlite':
				return new SQLiteConnection($connection, $database, $prefix, $config);

			case 'sqlsrv':
				return new SqlServerConnection($connection, $database, $prefix, $config);
		}

		throw new ClearException("atabase driver '$driver' does not exist!");
	}*/

	public function __construct($config)
	{
		$this->config = $config;
	}

	/*public function loadDSN()
	{
		$connections = $this->config['connections'];

		$defaultConnection = $this->config['default'];

		switch ($defaultConnection['driver'])
		{
			case 'mysql':
				return new MySqlConnection($connection, $database, $prefix, $config);

			case 'pgsql':
				return new PostgresConnection($connection, $database, $prefix, $config);

			case 'sqlite':
				return new SQLiteConnection($connection, $database, $prefix, $config);

			case 'sqlsrv':
				return new SqlServerConnection($connection, $database, $prefix, $config);
		}
	}*/

	public function createConnector()
	{
		$connectionName = $this->config['default'];

		$config = $this->config['connections'][$connectionName];

		switch ($config['driver'])
		{
			case 'mysql':
				return new MysqlConnector($config);

			case 'pgsql':
				return new \Framework\Database\Connector\PostgresConnector($config);

			case 'sqlite':
				return new \Framework\Database\Connector\SQLiteConnector($config);

			case 'sqlsrv':
				return new \Framework\Database\Connector\SqlServerConnector($config);
		}
	}
}