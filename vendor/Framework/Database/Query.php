<?php

namespace Framework\Database;

use Closure;
use PDO;
use PDOException;
use Framework\Exception\ClearException;
use Framework\Database\Query;
use Framework\Database\Connection;
use Framework\Database\Grammar;


class Query
{
	public $aggregate;

	public $distinct = false;

	public $columns;

	public $from;

	public $joins;

	public $wheres;

	public $groups;

	public $havings;

	public $orders;

	public $limit;

	public $offset;

	public $unions;

	public $lock;


	protected $sql;

	protected $block;

	protected $operators = [
		'=', '<', '>', '<=', '>=', '<>', '!=',
		//'&', '|', '^', '<<', '>>',
		'IS NULL', 'IS NOT NULL',
		'IN', 'NOT IN', 'LIKE', 'NOT LIKE', 'BETWEEN', 'NOT BETWEEN'
	];

	public function __construct(Connection $connection, Grammar $grammar)
	{
		$this->connection = $connection;

		$this->grammar = $grammar;
	}

	public function newQuery()
	{
		return new static($this->connection, $this->grammar);
	}

	public function select()
	{
		$this->columns = func_get_args();

		return $this;
	}

	public function distinct()
	{
		$this->distinct = true;

		return $this;
	}

	public function where()
	{
		/*if ($this->block == 'where')
		{
			throw new ClearException("Just one 'Where' clause can be use.", 4);
		}

		$this->block = 'where';*/

		$expression = new Expression();

		call_user_func_array([$expression, 'create'], func_get_args());

		$this->wheres[] = $expression;

		return $this;
	}

	public function andWhere()
	{
		$this->wheres[] = new Operator('AND');

		$expression = new Expression();

		call_user_func_array([$expression, 'create'], func_get_args());

		$this->wheres[] = $expression;

		return $this;
	}

	public function orWhere()
	{
		$this->wheres[] = new Operator('OR');

		$expression = new Expression();

		call_user_func_array([$expression, 'create'], func_get_args());

		$this->wheres[] = $expression;

		return $this;
	}

	public function from($from)
	{
		$this->from = $from;

		return $this;
	}

	public function table($table)
	{
		return $this->from($table);
	}

	public function orderBy($field)
	{
		$this->orders[] = ['field' => $field, 'type' => 'ASC'];

		return $this;
	}

	public function orderDescBy($field)
	{
		$this->orders[] = ['field' => $field, 'type' => 'DESC'];

		return $this;
	}

	public function toSql()
	{
		$this->compile();

		return $this->sql;
	}

	public function compile()
	{
		$this->sql = $this->grammar->compile($this);
	}

	public function having()
	{
		if ($this->block == 'having')
		{
			throw new ClearException("Just one 'Having' clause can be use.", 4);
		}

		$this->block = 'having';

		$expression = new Expression();

		call_user_func_array([$expression, 'create'], func_get_args());

		$this->havings[] = $expression;

		return $this;
	}

	public function andHaving()
	{
		$this->havings[] = new Operator('AND');

		$expression = new Expression();

		call_user_func_array([$expression, 'create'], func_get_args());

		$this->havings[] = $expression;

		return $this;
	}

	public function orHaving()
	{
		$this->havings[] = new Operator('OR');

		$expression = new Expression();

		call_user_func_array([$expression, 'create'], func_get_args());

		$this->havings[] = $expression;

		return $this;
	}

	public function limit()
	{
		if (func_num_args() == 1)
		{
			$count = func_get_arg(0);
		}
		elseif (func_num_args() == 2)
		{
			$count = func_get_arg(1);

			$this->offset = func_get_arg(0);
		}

		$this->limit = $count;

		return $this;
	}

	public function offset($offset)
	{
		$this->offset = $offset;

		return $this;
	}

	/*public static function table($table)
	{
		$query = new static;

		return $query->from($table);
	}*/

	public function get()
	{
		$callback = $this->connection->executeSelect($this->toSql());
		$items = $callback();

		$collection = new Collection($items);

		return $collection;
	}

	public function first()
	{
		$query = clone $this;

		$rows = $query->limit(1)->get();

		return count($rows) > 0 ? $rows[0] : null;
	}

	public function all()
	{
		$callback = $this->connection->executeSelect($this->toSql());
		return $callback();
	}

	public function last()
	{
		$countQuery = $this->newQuery();

		$count = $countQuery->select('COUNT(*) AS _count')->from($this)->pluck('_count');

		$offset = $count >= 1 ? $count - 1 : 0;

		$query = clone $this;

		return $query->limit(1)->offset($offset)->first();
	}

	public function column()
	{
		
	}

	public function row()
	{
		
	}

	public function pluck($column)
	{
		$row = (array) $this->first();

		return count($row) > 0 ? isset($row[$column]) ? $row[$column] : null : null;
	}


	public function __call($method, $args)
	{
		switch($method)
		{
			case 'and':

				if ($this->block == 'where')
				{
					return call_user_func_array([$this, 'andWhere'], $args);
				}
				elseif ($this->block == 'having')
				{
					return call_user_func_array([$this, 'andHaving'], $args);
				}

				throw new ClearException("'and' method must be used after 'where, having' methods.", 4);

				break;

			case 'or':

				if ($this->block == 'where')
				{
					return call_user_func_array([$this, 'orWhere'], $args);
				}
				elseif ($this->block == 'having')
				{
					return call_user_func_array([$this, 'orHaving'], $args);
				}

				throw new ClearException("'or' method must be used after 'where, having' methods.", 4);

				break;

			default:

				break;
		}
	}


	/**/

	public function on($connection)
	{
		
	}

	public function connection($connection)
	{
		$connections = Config::get('database.connections');

		$default = Config::get('database.default');

		$connection = $connections[$default];

		$connectionString = "{$connection['driver']}:host={$connection['host']};dbname={$connection['database']}";

		try 
		{
			$pdo = new PDO($connectionString, $connection['username'], $connection['password']);
		}
		catch (PDOException $e)
		{
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}

		$db = new Framework\Database\Query(new Framework\Database\Connection($pdo), new Framework\Database\Grammar);

		return $db;
	}

	public function createConnection($connectionName = null)
	{
		if (is_null($connectionName))
		{
			$connectionName = $this->config['default'];
		}

		$connection = $this->config['connections'][$connectionName];

		$connectionString = "{$connection['driver']}:host={$connection['host']};dbname={$connection['database']}";

		try 
		{
			$pdo = new PDO($connectionString, $connection['username'], $connection['password']);
		}
		catch (PDOException $e)
		{
			throw new ClearException($e->getMessage(), 1);	
		}

		$db = new Query(new Connection($pdo), new Grammar);

		return $db;
	}

	public function reconnect($connection = null)
	{

	}

	public function disconnect($connection = null)
	{
		
	}

	/**/

	public function before(Closure $callback)
	{
		
	}

	public function after(Closure $callback)
	{
		
	}
}