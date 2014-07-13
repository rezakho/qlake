<?php

namespace Framework\Database\Grammar;

use Framework\Database\Query;
use Framework\Database\Expression;
use Framework\Database\Operator;
use Closure;

class Grammar
{
	protected $wrapper = '`%s`';

	protected $selectComponents = [
		'aggregate',
		'columns',
		'from',
		'joins',
		'wheres',
		'groups',
		'havings',
		'orders',
		'limit',
		'offset',
		'unions',
		'lock',
	];

	protected function compileComponents(Query $query)
	{
		$sql = array();

		foreach ($this->selectComponents as $component)
		{
			if ( ! is_null($query->$component))
			{
				$method = 'compile'.ucfirst($component);

				$sql[$component] = $this->$method($query, $query->$component);
			}
		}

		return $sql;
	}


	public function compileColumns(Query $query)
	{
		if (is_array($query->columns))
		{
			$columns = [];

			foreach ($query->columns as $value)
			{
				$columns[] = $this->wrap($value);
			}
		}

		$columns = is_null($query->columns) ? '*' : implode(', ', $columns);

		return 'SELECT ' . ($query->distinct ? 'DISTINCT ' : '') . $columns;
	}

	public function wrap($column)
	{
		$column = trim($column);

		if ($column == '*')
		{
			return '*';
		}

		if (strpos(strtolower($column), ' as ') !== false)
		{
			$segments = explode(' ', $column);

			return $this->wrap($segments[0]).' AS '.$this->wrap($segments[2]);
		}

		if (preg_match('/\(.*\)/', $column) >= 1)
		{
			return $column;
		}

		if (strpos($column, '.') !== false)
		{
			$wrapped = array();

			$segments = explode('.', $column);
	
			foreach ($segments as $key => $segment)
			{
				// if $segment is table
				/*if ($key == 0 && count($segments) > 1)
				{
					$wrapped[] = $this->wrapTable($segment);
				}
				if
				{
					$wrapped[] = $this->wrapValue($segment);
				}*/$wrapped[] = $this->wrapTable($segment);
			}

			return implode('.', $wrapped);
		}

		return sprintf($this->wrapper, $column);
		
trace($column);
		/*if (strpos($column, ' '))
		{
			return $column;
		}
		elseif (strpos($column, '('))
		{
			return $column;
		}
		else
		{
			return $this->wrapperColumn($column);
		}*/
	}

	public function wrapTable($table)
	{
		return $this->wrap(/*$this->tablePrefix . */$table);
	}

	public function wrapValue($value)
	{
		/*if ($value === '*') return $value;

		return "'".str_replace("'", "''", $value)."'";*/
	}

	public function compileFrom(Query $query)
	{
		if (is_string($query->from))
		{
			return 'FROM ' . $this->wrapperTable($query->from);
		}
		elseif ($query->from instanceof \Closure)
		{
			$subQuery = $query->newQuery();

			call_user_func_array($query->from, [$subQuery]);

			//return 'FROM (' . $subQuery->toSql() . ') AS ' . $this->wrapperTable($subQuery->from) . ' ';
			return 'FROM (' . $subQuery->toSql() . ') AS tmp';
		}
		elseif ($query->from instanceof Query)
		{
			return 'FROM (' . $query->from->toSql() . ') AS tmp';
		}
	}

	public function compileJoins()
	{
		
	}

	public function compileWheres(Query $query)
	{
		$wheres = $query->wheres;

		$sql = [];

		foreach ($wheres as $where)
		{
			if ($where instanceof Expression)
			{
				switch ($where->type)
				{
					case 'raw':

						$sql[] = $this->compileRawWhere($where);

						break;

					case 'disjunct':
						
						$sql[] = $this->compileBasicWhere($where);

						break;

					case 'builder':

						$query = Query::$self->newQuery();

						call_user_func($where->clause, $query);

						$sql[] = '(' . substr($this->compileWheres($query), 6) . ')';

						break;
					
					default:
						# code...
						break;
				}
			}
			elseif (($operator = $where) instanceof Operator)
			{
				$sql[] = $operator->getType();
			}
		}

		return 'WHERE ' . implode(' ', $sql);
	}
	
	public function compileRawWhere($where)
	{
		return $where->clause;
	}

	public function correctOperator($operator)
	{
		return strtoupper(trim(preg_replace('/\\s+/', ' ', $operator)));
	}

	public function compileBasicWhere($where)
	{
		$field    = $this->wrap($where->clause['field']);

		$operator = $this->correctOperator($where->clause['operator']);

		$value    = $where->clause['value'];

		switch ($operator)
		{
			case '=':
			case '<':
			case '>':
			case '<=':
			case '>=':
			case '<>':
			case '!=':		
				return $this->compileComparisonOperationsWhere($field, $operator, $value);
				break;

			case 'IS NULL':
				return $this->compileIsNullWhere($field, $operator, $value = null);
				break;

			case 'IS NOT NULL':
				return $this->compileIsNotNullWhere($field, $operator, $value = null);
				break;

			case 'IN':
				return $this->compileInWhere($field, $operator, $value);
				break;

			case 'NOT IN':
				return $this->compileNotInWhere($field, $operator, $value);
				break;

			case 'LIKE':
				return $this->compileLikeWhere($field, $operator, $value);
				break;

			case 'NOT LIKE':
				return $this->compileNotLikeWhere($field, $operator, $value);
				break;

			case 'BETWEEN':
				return $this->compileBetweenWhere($field, $operator, $value);
				break;

			case 'NOT BETWEEN':
				return $this->compileNotBetweenWhere($field, $operator, $value);
				break;

			default:
				return $this->wrap($field) . ' ' . $this->correctOperator($operator) . ' ' . $this->parseValue($value);
				break;
		}
	}

	public function compileComparisonOperationsWhere($field, $operator, $value)
	{
		return $field . ' ' . $operator . ' ' . $this->parseValue($value);
	}

	public function compileIsNullWhere($field, $operator, $value)
	{
		return $field . ' IS NULL';
	}

	public function compileIsNotNullWhere($field, $operator, $value)
	{
		return $field . ' IS NOT NULL';	
	}

	public function compileInWhere($field, $operator, $value)
	{
		if (is_array($value))
		{
			$self = $this;

			$sql = $field . " $operator (" . implode(', ', $value) . ')';
		}
		elseif ($value instanceof Closure)
		{
			$query = Query::$self->newQuery();

			call_user_func($value, $query);

			$value = $query;

			$sql = $field . " $operator (" . $value->toSql() . ')';
		}

		return $sql;
	}

	public function compileNotInWhere($field, $operator, $value)
	{
		return $this->compileInWhere($field, 'NOT IN', $value);
	}

	public function compileNestedWhere(Query $query)
	{
		
	}

	public function compileSubQueryWhere(Query $query)
	{
		
	}

	public function compileExistsWhere(Query $query)
	{
		
	}

	public function compileNotExistsWhere(Query $query)
	{
		
	}

	public function compileBetweenWhere($field, $operator, $value)
	{
		return $field . ' ' . $operator . ' ' . $value[0] . ' AND ' . $value[1];
	}


	public function compileGroups()
	{
		
	}

	public function compileHavings(Query $query)
	{
		$havings = $query->havings;

		$sql = [];

		foreach ($havings as $having)
		{
			if ($having instanceof Expression)
			{
				switch ($having->type)
				{
					case 'raw':

						$sql[] = $having->clause;

						break;

					case 'disjunct':
						
						if (!$having->clause['value'] instanceof Query)
						{
							$sql[] = $having->clause['field'] . ' ' . $having->clause['operator'] . ' ' . $this->parseValue($having->clause['value']);
						}

						break;

					case 'builder':

						$sql[] = '(' . substr($this->compileHavings($having->clause), 6) . ')';
						//$sql[] = $having->clause[0] . $having->clause[1] . $this->parseValue($having->clause[2]);
						break;
					
					default:
						# code...
						break;
				}
			}
			elseif (($operator = $having) instanceof Operator)
			{
				$sql[] = $operator->getType();
			}
		}

		return 'HAVING ' . implode(' ', $sql);
	}

	public function compileOrders(Query $query)
	{
		$orders = [];

		foreach ($query->orders as $order)
		{
			$orders[] = $this->wrapperColumn($order['field']) . ' ' . $order['type'];
		}

		return 'ORDER BY ' . implode(', ', $orders);
	}

	public function compileLimit(Query $query)
	{
		return 'LIMIT ' . $query->limit;
	}

	public function compileOffset(Query $query)
	{
		return 'OFFSET ' . $query->offset;
	}

	public function compileUnions()
	{
		
	}


	public function wrapperTable($table)
	{
		return sprintf($this->wrapper, $table);
	}

	public function wrapperColumn($column)
	{
		return sprintf($this->wrapper, $column);
	}

	public function parseValue($value)
	{
		if (is_string($value))
		{
			return "'" . $value . "'";
		}
		elseif (is_numeric($value))
		{
			return $value;
		}
		elseif (is_array($value))
		{
			foreach ($value as $k => $v)
			{
				$value[$k] = is_string($v) ? "'" . $v . "'" : $v;
			}

			return $value;
		}
		else
		{
			return $value;
		}
	}

	public function compile($query)
	{
		return implode(' ', $this->compileComponents($query));
	}
}
