<?php

/**
 * Class FormView
 */
abstract class FormView extends Base {
	/**
	 * @var bool
	 */
	public $noLabel = false;
	/**
	 * @var Form
	 */
	protected $_form;
	/**
	 * @var null
	 */
	protected $class = null;

	/**
	 * FormView constructor.
	 *
	 * @param array|null $properties
	 */
	public function __construct( ?array $properties = null ) {
		$this->configure( $properties );
	}

	/**
	 * @param Form $form
	 */
	public function _setForm( Form $form ) {
		$this->_form = $form;
	}

	/*jQuery is used to apply css entries to the last element.*/
	public function jQueryDocumentReady() {
	}

	/**
	 * @param null $onlyElement
	 */
	public function render( $onlyElement = null ) {
		global $form_slug;
		$this->class = apply_filters( 'buddyforms_forms_classes', $this->class, $this, $form_slug );
		if ( $this->class ) {
			$this->_form->appendAttribute( 'class', $this->class );
		}
		$this->_form->getErrorView()->render();
		echo '<form ', wp_kses( $this->_form->getAttributes(), buddyforms_wp_kses_allowed_atts() ), '><!--csrftoken--><fieldset> ';
		if ( $onlyElement && $onlyElement == 'open' ) {
			return;
		}

		$elements = $this->_form->getElements();
		foreach ( $elements as $element ) {
			$this->renderElement( $element );
		}
		$this->renderFormClose();
	}

	public function renderFormClose() {
		do_action( 'buddyforms_after_form_closing' );
		echo ' </fieldset></form> ';
	}

	public function renderCSS() {
		global $buddyforms, $form_slug;

		if ( ! isset( $buddyforms[ $form_slug ]['layout']['desc_disable_css'] ) ) {
			echo 'span.help-inline, span.help-block { color: #888; font-size: .9em; font-style: italic; }';
		}

		echo 'label span.required { color: #B94A48; }';
	}

	public function renderJS() {
	}

	/**
	 * @param Element $element
	 */
	protected function renderDescriptions( $element ) {
		$shortDesc = $element->getShortDesc();
		if ( ! empty( $shortDesc ) ) {
			echo '<span class="help-inline">', wp_kses( $shortDesc, buddyforms_wp_kses_allowed_atts() ), '</span>';
		};

		$longDesc = $element->getLongDesc();
		if ( ! empty( $longDesc ) ) {
			echo '<span class="help-block">', wp_kses( $longDesc, buddyforms_wp_kses_allowed_atts() ), '</span>';
		};
	}

	/**
	 * @param Element $element
	 */
	protected function renderLabel( Element $element ) {
	}
}
