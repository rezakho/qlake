<?php

namespace Framework\Html;

class Html{

	protected $url;

	protected $charset = 'UTF-8';

	public function __construct($url = null)
	{
		$this->url = $url;
	}

	public function getCharSet()
	{
		return $this->charset;
	}

	public function style($url, $attributes = '')
	{

		$defaults = array('href' => '', 'rel' => 'stylesheet', 'type' => 'text/css', 'media' => 'all');

		$defaults['href'] = $url;

		if (!empty($attributes))
		{
			foreach ((array)$attributes as $key => $value) 
			{
				$defaults[$key] = $value;
			}
		}

		return '<link ' . $this->setAttribute($defaults) . '/>' . PHP_EOL;
	}


	public function script($url, $attributes = '')
	{
		$attributes['src'] = $url;

		return '<script ' . $this->setAttribute($attributes) . '></script>' . PHP_EOL;
	}


	public function favIcon($url = '')
	{
		return '<link rel="shortcut icon" href="'. $url .'">';
	}


	public function meta($attributs)
	{
		return $this->createElement('meta', $attributs);
	}


	public function setAttribute($attributs)
	{
		$html = [];

		foreach ((array)$attributs as $key => $value)
		{
			$html[] = $key . '=' . '"' . $value . '"';
		}

		return implode($html, ' ');
	}

	public function label($name = '', $label = '', $attributs = [])
	{
		$attributs['for'] = $name;

		return $this->createElement('label', $attributs, $label);
	}

	public function input($type, $name = '', $value = '', $attributs = [])
	{
		!empty($type) ? $attributs['type'] = $type : $attributs['type'] = 'text';

		$attributs['name'] = $name;

		$attributs['value'] = $value;

		return $this->createElement('input', $attributs);
	}


	public function password($name = '', $lable = '', $attributs = [])
	{
		!empty($type) ? $attributs['type'] = $type : $attributs['type'] = 'password';

		$attributs['name'] = $name;

		return $this->createElement('input', $attributs);
	}

	public function hidden($name = '', $value = '', $attributs = [])
	{
		!empty($type) ? $attributs['type'] = $type : $attributs['type'] = 'hidden';

		$attributs['value'] = $value;

		return $this->createElement('input', $attributs);
	}

	public function image($src, $alt = '', $attributs = [])
	{
		$attributs['src'] = $src;

		$attributs['alt'] = $alt;

		return $this->createElement('img', $attributs);
	}

	public function imageLink($url, $src, $alt = '', $attributs = [])
	{
		$attributs['src'] = $src;

		$attributs['alt'] = $alt;

		$imgTag = $this->createElement('img', $attributs);

		$linkAttributs['href'] = $url; 

		return $this->createElement('a', $linkAttributs, $imgTag);
	}

	public function link($url, $label, $target = '_self', $attributs = [])
	{
		$attributs['href'] = $url;

		return $this->createElement('a', $attributs, $label);
	}

	public function ul($values, $attributs = [])
	{
		$html = [];

		foreach ($values as $key => $value)
		{
			$html[] = '<li>' . $value . '</li>' ;
		}

		return $this->createElement('ul', $attributs, implode($html, ''));
	}

	public function ol($values, $attributs = [])
	{
		$html = [];

		foreach ($values as $key => $value) 
		{
			$html[] = '<li>' . $value . '</li>' ;
		}

		return $this->createElement('ol', $attributs, implode($html, ''));
	}

	public function radio($name, $value, $label = null, $attributs = []){

		if (isset($label))
		{
			$labelAttributs['for'] = $name;

			$label = $this->label($name, $label, $labelAttributs);
		}
		else
		{
			$label = '';
		}

		if (is_array($value))
		{
			$html = [];

			$stop = count($value) - 1;

			foreach (range(0, $stop) as $i)
			{
				$attributs['type'] = 'radio';

				$attributs['name'] = $name;

				$attributs['value'] = $value[$i];

				$html[] = $this->createElement('input', $attributs);
			}

			return $label . implode($html, '');
		}
		else
		{
			$attributs['type'] = 'radio';

			$attributs['name'] = $name;

			$attributs['value'] = $value;

			return $label . $this->createElement('input', $attributs);
		}
	}

	public function checkbox($name, $value, $label = null, $attributs = [])
	{
		if (isset($label))
		{
			$labelAttributs['for'] = $name;

			$label = $this->label($name, $label, $labelAttributs);
		}
		else 
		{
			$label = '';
		}

		if (is_array($value))
		{
			$html = [];

			$stop = count($value) -1 ;

			foreach (range(0, $stop) as $i) 
			{
				$attributs['type'] = 'checkbox';

				$attributs['name'] = $name;

				$attributs['value'] = $value[$i];

				$html[] = $this->createElement('input', $attributs);
			}

			return $label . implode($html, '');
		}
		else
		{
			$attributs['type'] = 'checkbox';

			$attributs['name'] = $name;

			$attributs['value'] = $value;

			return $label . $this->createElement('input', $attributs);
		}
	}

	public function select($name, $values, $default = '0', $attributs = [])
	{
		$html = [];

		$attributs['name'] = $name;

		$selected = '';

		if (!$this->is_multi($values))
		{
			foreach ($values as $value => $title)
			{
				if ($value == $default)
				{
					$selected = "selected='selected'";
				}

				$html[] = "\t<option value = '$value' $selected>" . $title . '</option>' . PHP_EOL ;

				$selected = '';
			}
		}
		else
		{
			foreach ($values as $key => $value)
			{
				$html[] = "\t<optgroup label='$key'>" . PHP_EOL;

				foreach ($value as $selectValue => $title)
				{
					if ($selectValue == $default)
					{
						$selected = "selected='selected'";
					}

					$html[] = "\t\t<option value = '$selectValue' $selected>" . $title . '</option>' . PHP_EOL ;

					$selected = '';
				}

				$html[] = "\t</optgroup>" . PHP_EOL;
			}
		}

		$selectTag = '<select ' . $this->setAttribute($attributs) . '>' . PHP_EOL;

		return $this->createElement('select', $attributs, implode($html, ''));
	}

	public function formTable($rows, $singleRow = false, $attributs = [])
	{
		$html = [];

		$attributsTable = null;

		isset($attributs) ? $attributsTable = $this->setAttribute($attributs) : null;

		$html[] = '<table ' . $attributsTable . '>' . PHP_EOL;

		$html[] = '<tbody>' . PHP_EOL;

		foreach ($rows as $row) 
		{
			$singleRow ? $html[] = '<tr>' . PHP_EOL : null;

			foreach ($row as $key => $value) 
			{
				!$singleRow ? $html[] = '<tr>' . PHP_EOL : null;

				if ($key == 'label')
				{
					list($title, $name) = explode('|', $value);

					$html[] = '<td>' . "<label for='$name'> "  . $title . "</label>". '</td>' . PHP_EOL;
				}

				if ($key == 'input')
				{
					$html[] = '<td>' . "<input name='$name' value='$value'" . " />". '</td>'  . PHP_EOL;
				}

				!$singleRow ? $html[] = '</tr>' . PHP_EOL : null;
			}	

			$singleRow ? $html[] = '</tr>' . PHP_EOL : null;
		}

		$html[] = '</tbody>' . PHP_EOL;

		$html[] = '</table>' . PHP_EOL;

		return implode($html, '');
	}

	public function selectRange($name, $range, $values = [], $default = '0', $attributs = [])
	{
		$html = [];

		$attributs['name'] = $name;

		if (strpos($range, '~'))
		{
			list($start, $stop) = explode('~', $range) ;

			if ($start != 0){
				$start--;
				$stop--;
			}
		}
		else
		{
			$start = 0;

			$stop = count($values) -1 ;
		}

		$selected = '';

		$i = 0;

		foreach (range($start, $stop) as $i)
		{
			if ($i == $default)
			{
				$selected = "selected='selected'";
			}

			$html[] = "\t<option value = '$values[$i]' $selected>" . $values[$i][0] . '</option>' . PHP_EOL ;

			$selected = '';
		}

		return $this->createElement('select', $attributs, implode($html, ''));
	}

	public function is_multi($array) 
	{
		$rv = array_filter($array, 'is_array');

		if(count($rv) > 0)
		{
			return true;
		}

		return false;
	}

	public function head($tags = [])
	{
		$tagsArray = [];

		foreach ($tags as $keytag => $valuetag) 
		{
			switch ($keytag) 
			{
				case 'title':

				$this->simpleCloseTag = false;

				$tagsArray[] = '<title>'. $valuetag . '</title>';

				break;

				case  'style':

				$tagsArray[] = $this->addStyle($valuetag) . PHP_EOL;

				break;

				case  'script':

				$tagsArray[] = $this->addScript($valuetag) . PHP_EOL;

				break;

				case 'meta':

				$this->simpleCloseTag = true;

				$tagsArray[] = $this->createElement('meta', $valuetag) . PHP_EOL;

				break;

				case 'twitter':

				$this->simpleCloseTag = true;

				$tagsArray[] = $this->createElement('meta', array('name' => $keytag . ':' . $valuetag['name'], 'content' => $valuetag['content'])) . PHP_EOL;

				break;

				case 'favIcon':

				$tagsArray[] = $this->favIcon($valuetag) . PHP_EOL;

				break;
			}
		}

		return implode($tagsArray, ' ');
	}

	public function createElement($tag, $attributs = [], $content = false, $closeTag = true)
	{
		$html = '<' . $tag . ' ' . $this->setAttribute($attributs);

		if($content === false)
		{
			return $closeTag || $this->simpleCloseTag ? $html . ' />' : $html . '>';
		}
		else
		{
			return $closeTag ? $html . '>' . $content . '</' . trim($tag) . '>' : $html . '>' . $content;
		}
	}


	public function decode($text)
	{
		return htmlspecialchars_decode($text, ENT_QUOTES);
	}


	public function encode($text)
	{
		return htmlspecialchars($text, ENT_QUOTES, $this->getCharSet());
	}


	public function encodeArray($data)
	{
		$d = [];

		foreach($data as $key => $value)
		{
			if(is_string($key))
			{
				$key = htmlspecialchars($key, ENT_QUOTES, $this->getCharSet());
			}

			if(is_string($value))
			{
				$value = htmlspecialchars($value, ENT_QUOTES, $this->getCharSet());
			}
			elseif(is_array($value))
			{
				$value = self::encodeArray($value);
			}

			$d[$key] = $value;
		}

		return $d;
	}
}


