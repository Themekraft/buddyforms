<?php

/**
 * Class View_Vertical
 */
class View_Vertical extends FormView {
	/**
	 * @var int
	 */
	private $sharedCount = 0;

	/**
	 * @param Element $element
	 */
	public function renderElement( $element ) {
		if ( $element instanceof Element_Hidden || $element instanceof Element_HTML || $element instanceof Element_Button ) {
			$element->render();

			return;
		}
		if ( ! $element instanceof Element_Radio && ! $element instanceof Element_Checkbox && ! $element instanceof Element_File ) {
			$element->appendAttribute( 'class', 'form-control' );
		}

		$attr_error = $element->getAttribute( 'error' );
		$opt_error  = $element->getOption( 'error' );
		if ( ! empty( $attr_error ) || ! empty( $opt_error ) ) {
			$element->appendAttribute( 'class', 'error' );
		}

		if ( $this->sharedCount == 0 ) {
			$rowClass = $element->getShared() ? 'row' : '';
			echo '<div class="' . esc_attr( $rowClass ) . ' bf_field_group elem-' . esc_attr( $element->getAttribute( 'id' ) ) . '"> ';
			$this->echoRendered( $this->renderLabel( $element ) );
		}

		if ( $element->getShared() ) {
			$colSize            = $element->getShared();
			$this->sharedCount += $colSize[ strlen( $colSize ) - 1 ];
			echo wp_kses( " <div class='$colSize'> ", buddyforms_wp_kses_allowed_atts() );
		}

		$element->setAttribute( 'placeholder', $element->getLabel() );
		$this->echoRendered( $element->render() );
		$this->echoRendered( $this->renderDescriptions( $element ) );
		if ( $element->getShared() ) {
			echo ' </div> ';
		}

		if ( $this->sharedCount == 0 || $this->sharedCount == 12 ) {
			$this->sharedCount = 0;
			echo ' </div> ';
		}
	}

	/**
	 * @param Element $element
	 */
	protected function renderLabel( Element $element ) {
	}
}
