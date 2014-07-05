<?php

namespace Framework\Database;

use PDO;
use Framework\Exception\ClearException;

class Connection
{
	public $pdo;

	public function __construct(PDO $pdo = null, array $config = [])
	{
		$this->pdo = $pdo ?: $this->createDefualtPdoConnection();

		//$this->database = $database;

		$this->config = $config;
	}

	public function createDefualtPdoConnection()
	{
		$connection = $this->config['connections'][$this->config['default']];

		$connectionString = "{$connection['driver']}:host={$connection['host']};dbname={$connection['database']}";

		try 
		{
			$dbh = new PDO($connectionString, $connection['username'], $connection['password']);
		}
		catch (PDOException $e)
		{
			throw new ClearException($e->getMessage(), 4);
		}

		return $pdo;
	}

	public function prepare($query)
	{
		return $this->pdo->prepare($query);
	}

	public function execute(PDOStatement $statement, array $parameters = [])
	{
		$statement->execute($parameters);
	}

}