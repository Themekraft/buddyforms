<?php

/**
 * Class ErrorView
 */
abstract class ErrorView extends Base {
	/**
	 * @var Form
	 */
	protected $_form;

	/**
	 * ErrorView constructor.
	 *
	 * @param array|null $properties
	 */
	public function __construct( ?array $properties = null ) {
		$this->configure( $properties );
	}

	abstract public function render();

	abstract public function renderCSS();

	abstract public function renderAjaxErrorResponse();

	public function clear() {
		echo 'jQuery("#', esc_js( $this->_form->getAttribute( 'id' ) ), ' .alert-error").remove();';
	}
}
