<?php namespace KodiCMS\Support\Model\Fields;

use WYSIWYG;

class WYSIWYGField extends TextAreaField
{
	/**
	 * @param string $name
	 * @param mixed $value
	 * @param array $attributes
	 * @return mixed
	 */
	protected function getFormFieldHTML($name, $value, array $attributes)
	{
		$this->addScriptToView();
		return parent::getFormFieldHTML($name, $value, $attributes);
	}

	protected function addScriptToView()
	{
		WYSIWYG::loadHTMLEditors();
		$id = $this->getId();
		view()->startSection('scripts', "<script>CMS.filters.switchOn('{$id}', DEFAULT_HTML_EDITOR)</script>");
	}

}