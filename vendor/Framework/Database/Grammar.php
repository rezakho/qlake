<?php

namespace Framework\Database;

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
				$columns[] = $this->parseColumn($value);
			}
		}

		$columns = is_null($query->columns) ? '*' : implode(', ', $columns);

		return 'SELECT ' . ($query->distinct ? 'DISTINCT ' : '') . $columns;
	}

	public function parseColumn($column)
	{
		$column = trim($column);

		if ($column == '*')
		{
			return '*';
		}

		if (strpos($column, ' '))
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
		}
	}

	public function compileFrom(Query $query)
	{
		if (is_string($query->from))
		{
			return 'FROM ' . $this->wrapperTable($query->from);
		}
		elseif ($query->from instanceof \Closure)
		{
			$subQuery = new Query;

			call_user_func_array($query->from, [$subQuery]);

			//return 'FROM (' . $subQuery->toSql() . ') AS ' . $this->wrapperTable($subQuery->from) . ' ';
			return 'FROM (' . $subQuery->toSql() . ')';
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

						$sql[] = '(' . substr($this->compileWheres($where->clause), 6) . ')';

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

	public function compileBasicWhere($where)
	{
		$field = $where->clause['field'];

		// fix spaces and do uppercase operator
		$operator = strtoupper(trim(preg_replace('/\\s/', ' ', $where->clause['operator'])));

		$value = $where->clause['value'];

		/*if (!$where->clause['value'] instanceof Query)
		{
			$sql[] = $where->clause['field'] . ' ' . $where->clause['operator'] . ' ' . $this->wrapperValue($where->clause['value']);
		}*/

		switch ($operator)
		{
			case '=':
			case '<':
			case '>':
			case '<=':
			case '>=':
			case '<>':
			case '!=':
				
				return $this->wrapperColumn($field) . ' ' . $operator . ' ' . $this->wrapperValue($value);

				break;

			case 'IS NULL':

				return $this->compileIsNullWhere($where);

				break;

			case 'IS NOT NULL':

				return $this->compileIsNotNullWhere($where);

				break;

			case 'IN':

				return $this->compileInWhere($where);

				break;

			case 'NOT IN':

				return $this->compileNotInWhere($where);

				break;

			case 'LIKE':
			case 'NOT LIKE':

				return $field . ' ' . $operator . ' ' . $this->wrapperValue((string)$value);

				break;

			case 'BETWEEN':
			case 'NOT BETWEEN':

				return $field . ' ' . $operator . ' ' . $this->wrapperValue($value[0]) . ' AND ' . $this->wrapperValue($value[1]);

				break;

			default:
exit($operator);
				return $this->wrapperColumn($field) . ' ' . $operator . ' ' . $this->wrapperValue($value);

				break;
		}
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

	public function compileInWhere($where, $operator = 'IN')
	{
		$field = $this->wrapperColumn($where->clause['field']);

		//$operator = strtoupper(trim($where->clause['operator']));

		$value = $where->clause['value'];

		if (is_string($value))
		{
			$sql =  $field . " $operator (" . $value . ')';
		}
		elseif (is_array($value))
		{
			$self = $this;

			$sql = $field . " $operator (" . implode(', ', array_map(function($v){
				return $this->wrapperValue($v);
			}, $value)) . ')';
		}
		elseif ($value instanceof Query)
		{
			$sql = $field . " $operator (" . $value->toSql() . ')';
		}

		return $sql;
	}

	public function compileNotInWhere($where)
	{
		return $this->compileInWhere($where, 'NOT IN');
	}

	public function compileBetweenWhere(Query $query)
	{
		
	}

	public function compileIsNullWhere($where, $operator = 'IS NULL')
	{
		$field = $where->clause['field'];

		return $sql = $this->wrapperColumn($field) . " $operator";
	}

	public function compileIsNotNullWhere($where)
	{
		return $this->compileIsNullWhere($where, 'IS NOT NULL');	
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
							$sql[] = $having->clause['field'] . ' ' . $having->clause['operator'] . ' ' . $this->wrapperValue($having->clause['value']);
						}

						break;

					case 'builder':

						$sql[] = '(' . substr($this->compileHavings($having->clause), 6) . ')';
						//$sql[] = $having->clause[0] . $having->clause[1] . $this->wrapperValue($having->clause[2]);
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

	public function wrapperValue($value)
	{
		return is_string($value) ? "'" . $value . "'" : $value;
	}

	public function compile($query)
	{
		return implode(' ', $this->compileComponents($query));
	}
}
