<?php

namespace Framework\Html;

class FormBuilder
{
	public function __construct(HtmlBuilder $html, $csrfToken)
	{
		$this->html = $html;

		$this->csrfToken = $csrfToken;
	}

	public function open()
	{
		return '<form ' . $this->html->createAttributeStrings($attrs) . '>';
	}

	public function text($name, $value = null, array $attrs = [])
	{
		$attrs = array_merge($attrs, ['type' => 'text', 'name' => $name, 'value' => $value]);

		return $this->html->createElement('input', $attrs);
	}

	public function password($name, $value = null, array $attrs = [])
	{
		$attrs = array_merge($attrs, ['type' => 'password', 'name' => $name, 'value' => $value]);

		return $this->html->createElement('input', $attrs);
	}

	public function hidden($name, $value = null, array $attrs = [])
	{
		$attrs = array_merge($attrs, ['type' => 'hidden', 'name' => $name, 'value' => $value]);

		return $this->html->createElement('input', $attrs);
	}

	public function email($name, $value = null, array $attrs = [])
	{
		$attrs = array_merge($attrs, ['type' => 'email', 'name' => $name, 'value' => $value]);

		return $this->html->createElement('input', $attrs);
	}

	public function url($name, $value = null, array $attrs = [])
	{
		$attrs = array_merge($attrs, ['type' => 'url', 'name' => $name, 'value' => $value]);

		return $this->html->createElement('input', $attrs);
	}

	public function file($name, array $attrs = [])
	{
		$attrs = array_merge($attrs, ['type' => 'file', 'name' => $name]);

		return $this->html->createElement('input', $attrs);
	}
}