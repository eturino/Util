<?php

class EtuDev_Util_Form {


	/**
	 * @param array $attributes
	 * @param array $exclude
	 *
	 * @return string
	 */
	static public function parseAttributesToString($attributes, $exclude = array()) {
		$loop_attributes = array_intersect(array_keys($attributes), array('title',
																		  'multiple',
																		  'checked',
																		  'disabled',
																		  'readonly',
																		  'style',
																		  'maxlength',
																		  'autocomplete',
																		  'name',
																		  'onfocus',
																		  'onblur',
																		  'onclick',
																		  'onchange',
																		  'onkeyup',
																		  'onkeydown',
																		  'onmouseover',
																		  'onmouseout',
																		  'oninvalid',
																		  'required',
																		  'placeholder',
																		  'spellcheck'));

		$attributes_string = '';

		$changeToSame = array('required', 'checked', 'selected');

		foreach ($loop_attributes as $a) {
			if (!$exclude || !in_array($a, $exclude)) {
				$v = $attributes[$a];
				if (in_array($a, $changeToSame)) {
					$v = $a;
				}
				$attributes_string .= ' ' . $a . '="' . $v . '"';
			}
		}

		return $attributes_string;
	}

	/**
	 * @static
	 *
	 * @param $classes
	 *
	 * @return string
	 */
	static public function parseClasses($classes) {
		return ' class="' . implode(' ', array_unique($classes)) . '"';
	}
}