<?php

/**
 * Class Validation_Captcha
 */
class Validation_Captcha extends Validation {
	/**
	 * @var string
	 */
	protected $message = 'Error: The reCATPCHA response provided was incorrect.  Please try again.';
	/**
	 * @var string Represent the Secret from reCaptcha
	 */
	protected $privateKey;

	/**
	 * Validation_Captcha constructor.
	 *
	 * @param string        $privateKey
	 * @param string        $message
	 * @param $field_options
	 */
	public function __construct( $privateKey, $message = '', $field_options = array() ) {
		$this->privateKey = $privateKey;
		if ( ! empty( $message ) ) {
			$this->message = $message;
		} else {
			$this->message = __( 'Error: The reCATPCHA response provided was incorrect.  Please try again.', 'buddyforms' );
		}
		parent::__construct( $message, $field_options );
	}

	/**
	 * @param $value
	 *
	 * @return bool
	 */
	public function isValid( $value, $element ) {
		$version = $this->getOption( 'version' );
		if ( empty( $version ) ) {
			$version = 'v2';
		}
		if ( $version === 'v2' ) {
			$captcha = sanitize_text_field( $_POST['g-recaptcha-response'] );
			$resp    = $this->validate_google_captcha( $captcha, $this->privateKey );
			$result  = ! empty( $resp['success'] ) && boolval( $resp['success'] ) === true;
		} else {
			$score  = $this->getOption( 'captcha_v3_score' );
			$action = $this->getOption( 'captcha_v3_action' );
			if ( empty( $score ) ) {
				$score = 0.5;
			}
			if ( empty( $action ) ) {
				$action = 'form';
			}
			$action     = preg_replace( '/[^a-zA-Z0-9]+/', '', $action );
			$captcha    = sanitize_text_field( $_POST['bf-cpchtk'] );
			$recaptcha  = new \tk\ReCaptcha\ReCaptcha( $this->privateKey );
			$resp       = $recaptcha->setExpectedHostname( sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) )
									->setExpectedAction( $action )
									->setScoreThreshold( floatval( $score ) )
									->verify( $captcha, sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) );
			$is_success = $resp->isSuccess();
			if ( ! $is_success ) {
				$errors = $resp->getErrorCodes();// Todo write to the logs
				if ( ! empty( $errors ) ) {
					BuddyForms::error_log( join( ', ', $errors ) );
				}
			}
			$result = ! empty( $is_success ) && boolval( $is_success ) === true;
		}

		return apply_filters( 'buddyforms_element_captcha_validation', $result, $element );
	}

	public function validate_google_captcha( $captcha, $secret ) {
		$url = 'https://www.google.com/recaptcha/api/siteverify';

		$data = [
			'secret'   => $secret,
			'response' => $captcha,
			'remoteip' => sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ),
		];

		// Initialize cURL
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true ); // Ensure SSL verification
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 ); // Set timeout

		$response = curl_exec( $ch );
		$error    = curl_error( $ch ); // Check for errors

		curl_close( $ch );

		if ( $error ) {
			error_log( 'Google CAPTCHA cURL error: ' . $error ); // Log errors if any
			return false;
		}

		return json_decode( $response, true );
	}
}
