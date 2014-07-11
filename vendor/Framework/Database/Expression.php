<?php

namespace Framework\Database;

use Closure;

class Expression
{
	public $type;

	public $clause;

	public function create()
	{
		// Basic raw and closure where
		if (func_num_args() == 1)
		{
			$clause = func_get_arg(0);

			// Raw where clause
			if (is_string($clause))
			{
				$this->type = 'raw';

				$this->clause = $clause;
			}
			// Nested AND - OR clauses
			elseif ($clause instanceof Closure)
			{
				$this->type = 'builder';

				$this->clause = $clause;
			}
		}
		// disjunct where by 1 operand
		elseif (func_num_args() == 2)
		{
			list($field, $operator) = func_get_args();

			$this->type = 'disjunct';
			
			$value = null;

			$this->clause = compact('field', 'operator', 'value');
		}
		// disjunct where by 2 operands
		elseif (func_num_args() == 3)
		{
			list($field, $operator, $value) = func_get_args();

			$this->type = 'disjunct';

			$this->clause = compact('field', 'operator', 'value');
		}
		else
		{
			throw new ClearException("Invalid arquments", 4);		
		}
	}
}
