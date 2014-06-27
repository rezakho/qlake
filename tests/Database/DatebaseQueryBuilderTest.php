<?php

class DatabaseQueryBuilderTest extends PHPUnit_Framework_TestCase
{
	public function testSimpleSelect()
	{
		$query = $this->getQuery();

		$sql = $query->select('*', 'id')->from('table')->toSql();

		$this->assertEquals('SELECT *, `id` FROM `table`', $sql);
	}

	public function testSimpleColumnsSelect()
	{
		$query = $this->getQuery();

		$sql = $query->select('id', 'name')->from('table')->toSql();

		$this->assertEquals('SELECT `id`, `name` FROM `table`', $sql);
	}

	public function testAliasedColumnsSelect()
	{
		$query = $this->getQuery();

		$sql = $query->select('family AS f')->from('table')->toSql();

		$this->assertEquals('SELECT family AS f FROM `table`', $sql);
	}

	public function testAggregatedColumnsSelect()
	{
		$query = $this->getQuery();

		$sql = $query->select('COUNT(*)')->from('table')->toSql();

		$this->assertEquals('SELECT COUNT(*) FROM `table`', $sql);
	}

	public function testComplexColumnsSelect()
	{
		$query = $this->getQuery();

		$sql = $query->select('id', 'family AS f', 'COUNT(id)', '*')->from('table')->toSql();

		$this->assertEquals('SELECT `id`, family AS f, COUNT(id), * FROM `table`', $sql);
	}

	public function testDistinctSelect()
	{
		$query = $this->getQuery();

		$sql = $query->select('*')->distinct()->from('table')->toSql();

		$this->assertEquals('SELECT DISTINCT * FROM `table`', $sql);
	}

	public function testClosureFrom()
	{
		$query = $this->getQuery();

		$sql = $query->select('*')->from(function($subQuery){
			$subQuery->select('id')->from('table');
		})->toSql();

		$this->assertEquals('SELECT * FROM (SELECT `id` FROM `table`)', $sql);
	}

	public function testLimitByOnParameter()
	{
		$query = $this->getQuery();

		$sql = $query->select('*')->from('table')->limit(1)->toSql();

		$this->assertEquals('SELECT * FROM `table` LIMIT 1', $sql);
	}

	public function testLimitByTwoParameter()
	{
		$query = $this->getQuery();

		$sql = $query->select('*')->from('table')->limit(1, 10)->toSql();

		$this->assertEquals('SELECT * FROM `table` LIMIT 10 OFFSET 1', $sql);
	}

	public function testOffset()
	{
		$query = $this->getQuery();

		$sql = $query->select('*')->from('table')->limit(10)->offset(1)->toSql();

		$this->assertEquals('SELECT * FROM `table` LIMIT 10 OFFSET 1', $sql);
	}

	public function testOrderBy()
	{
		$query = $this->getQuery();

		$sql = $query->select('*')->from('table')->orderBy('id')->toSql();

		$this->assertEquals('SELECT * FROM `table` ORDER BY `id` ASC', $sql);

		//
		$query = $this->getQuery();

		$sql = $query->select('*')->from('table')->orderDescBy('id')->toSql();

		$this->assertEquals('SELECT * FROM `table` ORDER BY `id` DESC', $sql);
	}

	public function testRawWhere()
	{
		$query = $this->getQuery();

		$sql = $query->select('*')->from('table')->where('id = 12')->toSql();

		$this->assertEquals('SELECT * FROM `table` WHERE id = 12', $sql);
	}

	public function testDisjunctWhereComparisonOperators()
	{
		$operators = [
			'=', '<', '>', '<=', '>=', '<>', '!=', 
		];

		foreach ($operators as $operator)
		{
			$query = $this->getQuery();

			$sql = $query->select('*')->from('table')->where('id', $operator, 12)->toSql();

			$this->assertEquals("SELECT * FROM `table` WHERE `id` {$operator} 12", $sql);
		}
	}

	public function testDisjunctWhereIsNullOperators()
	{
		$operators = [
			'IS NULL', 'IS NOT NULL',
		];

		foreach ($operators as $operator)
		{
			$query = $this->getQuery();

			$sql = $query->select('*')->from('table')->where('id', $operator)->toSql();

			$this->assertEquals("SELECT * FROM `table` WHERE `id` {$operator}", $sql);
		}

	}



	public function testDisjunctWhereInOperators()
	{
		$operators = [
			'IN', 'NOT IN',
		];

		foreach ($operators as $operator)
		{
			$query = $this->getQuery();

			$sql = $query->select('*')->from('table')->where('id', $operator, '1,2,3')->toSql();

			$this->assertEquals("SELECT * FROM `table` WHERE `id` {$operator} (1,2,3)", $sql);
		}

		foreach ($operators as $operator)
		{
			$query = $this->getQuery();

			$sql = $query->select('*')->from('table')->where('id', $operator, [1,2,'3'])->toSql();

			$this->assertEquals("SELECT * FROM `table` WHERE `id` {$operator} (1, 2, '3')", $sql);
		}

		foreach ($operators as $operator)
		{
			$query = $this->getQuery();

			$sql = $query->select('*')->from('table')->where('id', $operator, function($query)
			{
				$query->select('pid')->from('innertable');
			})->toSql();

			$this->assertEquals("SELECT * FROM `table` WHERE `id` {$operator} (SELECT `pid` FROM `innertable`)", $sql);
		}

	}

	/*public function testDisjunctWhereIsNullOperators($value='')
	{
		$operators = [
			'IS NULL', 'IS NOT NULL',
			//'IN', 'NOT IN', 'LIKE', 'NOT LIKE', 'BETWEEN', 'NOT BETWEEN'
		];

		foreach ($operators as $operator)
		{
			$query = $this->getQuery();

			$sql = $query->select('*')->from('table')->where('id', $operator, 12)->toSql();

			$this->assertEquals("SELECT * FROM `table` WHERE `id` {$operator} 121", $sql);
		}

	}*/

	/*public function testBasicWhere()
	{
		$query = $this->getQuery();

		$sql = $query->select('*')->from('table')->where('id', '=', 12)->toSql();

		$this->assertEquals('SELECT * FROM `table` WHERE `id` = 12', $sql);

		//
		$query = $this->getQuery();

		$sql = $query->select('*')->from('table')->where('id', '>')->toSql();

		//$this->assertEquals('SELECT * FROM `table` ORDER BY `id` DESC', $sql);
	}*/



	public function getQuery()
	{
		return new DB;
	}
}