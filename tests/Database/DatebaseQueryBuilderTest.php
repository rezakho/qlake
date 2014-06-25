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



	public function getQuery()
	{
		return new DB;
	}
}