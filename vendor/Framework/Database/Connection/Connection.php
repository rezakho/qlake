<?php

namespace Framework\Database\Connection;

use PDO;
use Framework\Exception\ClearException;

class Connection
{
	protected $pdo;

	protected $fetchStyle = PDO::FETCH_CLASS;

	public function __construct(PDO $pdo = null, array $config = [])
	{
		$this->pdo = $pdo;

		//$this->database = $database;

		$this->config = $config;
	}

	public function executeSelect($sql, $bindings = [])
	{
		return function()use($sql, $bindings)
		{
			$statement = $this->pdo->prepare($sql);

			$res = $statement->execute($bindings);

			if ($res == false)
			{
				throw new ClearException($statement->errorInfo()[2], 1);
			}

			return $statement;

			$items = $statement->fetchAll(PDO::FETCH_OBJ);

			return $items;
		};
	}

}