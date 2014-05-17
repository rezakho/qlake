<?php 
namespace Framework\Cache;

class FileCache implements CacheDriverInterface
{
	public $config;

	public $currentCacheFile;

	public function set($key, $value, $expirationTimeStamp=false)
	{
		if (gettype($value)=='object' || gettype($value)=='resource')
		{
			throw new ClearException("The Given Value Is Object Or resource!", 4);
		}

		if (!$expirationTimeStamp)
		{
			$expirationTimeStamp = time() + $this->config['defaultCacheLifeTime'];
		}
		else
		{
			$expirationTimeStamp = time()+$expirationTimeStamp;
		}

		if ($this->has($key))
		{
			return false;
		}

		$cacheFilePath = $this->getCacheFilePath($key);

		$cacheFileName = $this->getCacheFileName($key,$expirationTimeStamp);

		$fullPath = $cacheFilePath . '/' . $cacheFileName;

		@file_put_contents($fullPath, serialize($value));
		
		if (file_exists($fullPath))
		{
			return true;
		}

		return false;
	}

	public function get($key,$default = false)
	{
		if ($this->has($key))
		{
			return unserialize(file_get_contents($this->currentCacheFile));
		}
		else
		{
			if ($default)
			{
				return $default;
			}

			return false;
		}
	}

	public function has($key)
	{
		$cacheFilePath = $this->getCacheFilePath($key);
		
		$filePrefix = md5($key);

		$dirs = scandir($cacheFilePath);

		$existCacheFileName = "";
		
		foreach ($dirs as $dir)
		{
			if (!is_dir($dir))
			{
				if (strpos(" ".$dir, $filePrefix)>0)
				{
					// this is Our File
					$existCacheFileName = $dir;

					break;
				}
			}
		}
		
		if($existCacheFileName)
		{
			// check timestamp
			$strTime = end(explode('.',$existCacheFileName));

			$this->currentCacheFile = $cacheFilePath . '/' . $existCacheFileName;

			if ($strTime > time())
			{
				// return unserialize(file_get_contents($cachePath.'/'.$cacheFile));
				return true;
			}
			else
			{
				@unlink($this->currentCacheFile);

				$this->currentCacheFile = false;

				return false;
			}
		}
		
		return false;
	}

	public function remove($key)
	{
		if ($this->has($key))
		{
			@unlink($this->currentCacheFile);

			if (!file_exists($this->currentCacheFile))
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		return true;
	}

	

	public function remember($key, $value, $expiration=false)
	{
		if (gettype($value) == 'object' || gettype($value) == 'resource')
		{
			throw new ClearException("The Given Value Is Object Or resource!", 4);
		}

		if ($this->has($key))
		{
			return unserialize(file_get_contents($this->currentCacheFile));
		}
		else
		{
			$this->set($key, $value, $expiration);

			return $value;
		}
	}

	public function getCacheFilePath($key)
	{
		$fileNamePrefix = md5($key);

		$dir1 = substr($fileNamePrefix, 0,3);

		$dir2 = substr($fileNamePrefix, 3,3);

		if (!file_exists($this->config['path'] . '/' . $dir1))
		{
			@mkdir($this->config['path'] . '/' . $dir1);
		}

		if (!file_exists($this->config['path'] . '/' . $dir1 . '/' . $dir2))
		{
			@mkdir($this->config['path'] . '/' . $dir1 . '/' . $dir2);
		}

		$cachePath = $this->config['path'] . '/' . $dir1 . '/' . $dir2;

		return $cachePath;
	}


	public function getCacheFileName($key,$expiration)
	{
		$filename = md5($key) . '.' . "$expiration";

		return $filename;
	}

	public function setConfig($config)
	{
		$this->config = $config;
	}
}