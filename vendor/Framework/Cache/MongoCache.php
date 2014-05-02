<?php 
namespace Framework\Cache;

class MongoCache implements CacheDriverInterface
{
	public $config;
	public $db;
	public $collection;
	public $cachedObject;

	public function __construct(){

	}
	public function set($key, $value, $expirationTimeStamp=false)
	{
		if(gettype($value)=='object' || gettype($value)=='resource')
		{
			throw new ClearException("The Given Value Is Object Or Resource!", 4);
		}

		if(!$expirationTimeStamp){
			$expirationTimeStamp = time() + $this->config['defaultCacheLifeTime'];
		}
		else
		{
			$expirationTimeStamp = time()+$expirationTimeStamp;
		}

		if($this->has($key))
		{
			return false;
		}

		$obj = new \stdClass;
		$obj->key = $key;
		$obj->value = $value;
		$obj->expirationTime = $expirationTimeStamp;

		$result = $this->collection->insert($obj);
		if($result->nInserted > 0)
			return true;
		return false;

	}

	public function get($key,$default = false)
	{
		if($this->has($key))
		{
			return $this->cachedObject['value'];
		}
		else
		{
			if($default)
			{
				return $default;
			}

			return false;
		}
	}

	public function has($key)
	{

		$document = $this->collection->findOne(json_decode('{"key":"'.$key.'"}'));
		if(!$document)
			return false;
		if($document['expirationTime'] > time())
		{
			$this->cachedObject = $document;
			return true;
		}
		else
		{
			$this->cachedObject = false;
			$this->remove($key);
			return false;
		}
	}

	public function remove($key)
	{
		$result = $this->collection->remove(json_decode('{"key":"'.$key.'"}','{"justOne":true}'));
		if($result->nInserted > 0)
			return true;
		return false;
	}

	

	public function remember($key, $value, $expiration=false)
	{
		if(gettype($value) == 'object' || gettype($value) == 'resource')
		{
			throw new ClearException("The Given Value Is Object Or resource!", 4);
		}

		if($this->has($key))
		{
			return $this->cachedObject['value'];
		}
		else
		{
			$this->set($key, $value, $expiration);

			return $value;
		}
	}

	public function setConfig($config)
	{
		$this->config = $config;
		if(!empty($this->config['username']))
			$strUserPass = $this->config['username'].':'.$this->config['password'].'@';
		$connectionString = 'mongodb://'.(isset($strUserPass)?$strUserPass:'').$this->config['host'].':'.$this->config['port'];
		$connection = new \MongoClient($connectionString);
		if(!$connection)
			throw new ClearException("Could Not Connect To MongoDB Server!", 4);
		$this->db = $connection->{$this->config['database']};

		if(!$this->db)
			throw new ClearException("Could Not Select Mongo DataBase!", 4);
		$this->collection = $this->db->{$this->config['collection']};
	}
}