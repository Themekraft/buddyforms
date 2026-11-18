<?php

declare(strict_types=1);

/*
 * php-scoper (0.18+) configuration for the libraries bundled under vendor-scope/buddyforms.
 *
 * Everything is moved under the `tk\` namespace so BuddyForms' copies of Guzzle and reCAPTCHA
 * never clash with other plugins that ship their own. See readme.md ("Scoped libraries") for
 * the build steps.
 */

use Isolated\Symfony\Component\Finder\Finder;

return [
	'prefix'      => 'tk',

	// The plugin still supports PHP 7.4: parse and print code for that version.
	'php-version' => '7.4',

	'finders'     => [
		Finder::create()
			->files()
			->ignoreVCS( true )
			// Keep the code and the LICENSE files, drop docs and tooling files.
			->notName( '/\\.(md|dist|neon|xml|yaml|yml)$|^Makefile$|^composer\\.lock$/' )
			->exclude( [ 'doc', 'docs', 'test', 'tests', 'Tests', 'examples' ] )
			->in( 'vendor-scope/buddyforms/vendor' ),
		Finder::create()->append( [ 'vendor-scope/buddyforms/composer.json' ] ),
	],

	// symfony/polyfill-php80 and ralouphie/getallheaders must stay global: their functions and
	// classes are guarded by function_exists()/PHP_VERSION_ID checks, and Guzzle calls them by
	// their global names (get_debug_type(), getallheaders()...) on older PHP versions.
	'exclude-namespaces' => [
		'Symfony\\Polyfill',
	],
	'exclude-files' => [
		'vendor-scope/buddyforms/vendor/ralouphie/getallheaders/src/getallheaders.php',
		'vendor-scope/buddyforms/vendor/symfony/polyfill-php80/bootstrap.php',
		'vendor-scope/buddyforms/vendor/symfony/polyfill-php80/Resources/stubs/Attribute.php',
		'vendor-scope/buddyforms/vendor/symfony/polyfill-php80/Resources/stubs/PhpToken.php',
		'vendor-scope/buddyforms/vendor/symfony/polyfill-php80/Resources/stubs/Stringable.php',
		'vendor-scope/buddyforms/vendor/symfony/polyfill-php80/Resources/stubs/UnhandledMatchError.php',
		'vendor-scope/buddyforms/vendor/symfony/polyfill-php80/Resources/stubs/ValueError.php',
	],

	'patchers'    => [
		// google/recaptcha 1.2.4 is the last release that supports PHP 7.4, and it declares
		// implicitly nullable parameters (`Type $x = null`), deprecated since PHP 8.4. Make them
		// explicitly nullable (`?Type $x = null`), which is valid from PHP 7.1.
		static function ( string $file_path, string $prefix, string $contents ): string {
			if ( false === strpos( $file_path, '/google/recaptcha/' ) ) {
				return $contents;
			}

			return (string) preg_replace(
				'/([(,]\s*)(?<!\?)([A-Za-z_\\\\][A-Za-z0-9_\\\\]*)(\s+\$\w+\s*=\s*null\b)/',
				'$1?$2$3',
				$contents
			);
		},
		// google/recaptcha 1.2.4 also creates dynamic properties (deprecated since PHP 8.2) and
		// passes the response's null fields to strcasecmp()/strtotime() (deprecated since PHP 8.1).
		// Declare the properties and cast to string, which is what PHP did implicitly.
		static function ( string $file_path, string $prefix, string $contents ): string {
			if ( '/google/recaptcha/src/ReCaptcha/ReCaptcha.php' !== substr( $file_path, -strlen( '/google/recaptcha/src/ReCaptcha/ReCaptcha.php' ) ) ) {
				return $contents;
			}

			$replacements = [
				'    private $requestMethod;'                                              => "    private \$requestMethod;\n    /** @var string|null */\n    private \$hostname;\n    /** @var string|null */\n    private \$apkPackageName;\n    /** @var string|null */\n    private \$action;\n    /** @var float|null */\n    private \$threshold;\n    /** @var int|null */\n    private \$timeoutSeconds;",
				'strcasecmp($this->hostname, $initialResponse->getHostname())'             => 'strcasecmp($this->hostname, (string) $initialResponse->getHostname())',
				'strcasecmp($this->apkPackageName, $initialResponse->getApkPackageName())' => 'strcasecmp($this->apkPackageName, (string) $initialResponse->getApkPackageName())',
				'strcasecmp($this->action, $initialResponse->getAction())'                 => 'strcasecmp($this->action, (string) $initialResponse->getAction())',
				'strtotime($initialResponse->getChallengeTs())'                            => 'strtotime((string) $initialResponse->getChallengeTs())',
			];
			foreach ( $replacements as $search => $replace ) {
				if ( 1 !== substr_count( $contents, $search ) ) {
					throw new RuntimeException( "ReCaptcha.php patch no longer applies: $search" );
				}
				$contents = str_replace( $search, $replace, $contents );
			}

			return $contents;
		},
	],

	// Keep every symbol inside the prefix. The aliases php-scoper writes to scoper-autoload.php
	// are not needed: BuddyForms loads vendor/autoload.php and only uses the tk\ names.
	'expose-global-constants' => false,
	'expose-global-classes'   => false,
	'expose-global-functions' => false,
];
