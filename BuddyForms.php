<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Name: BuddyForms
 * Plugin URI:  https://themekraft.com/buddyforms/
 * Description: Contact Forms, Post Forms for User Generated Content and Registration Forms easily build in minutes. Extendable with Addons!
 * Version: 2.10.0-beta.6
 * Author: ThemeKraft
 * Author URI: https://themekraft.com/buddyforms/
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: buddyforms
 * Domain Path: /languages
 * Svn: buddyforms
 *
 * @fs_premium_only /includes/admin/form-metabox.php
 *
 * ****************************************************************************
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA    02111-1307    USA
 *
 * ***************************************************************************
 */

require_once __DIR__ . '/vendor/autoload.php';

if ( ! class_exists( 'BuddyForms' ) ) {
	/**
	 * Class BuddyForms
	 */
	class BuddyForms {

		/**
		 * @var string
		 */
		public $version = '2.10.0-beta.6';

		/**
		 * @var array Frontend Global JS parameters
		 */
		private static $global_js_parameters;

		/**
		 * Initiate the class
		 *
		 * @package BuddyForms
		 * @since 0.1-beta
		 */
		public function __construct() {
			global $wp_session;

			register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );

			$this->load_constants();

			add_action( 'init', array( $this, 'init_hook' ), 1, 1 );
			require_once BUDDYFORMS_INCLUDES_PATH . 'form/form-assets.php';
			new BuddyFormsAssets();
			add_action( 'init', array( $this, 'includes' ), 4, 1 );
			add_action( 'init', array( $this, 'update_db_check' ), 10 );

			add_action( 'admin_menu', array( $this, 'buddyforms_bundle_screen_menu' ), 9999 );

			register_deactivation_hook( __FILE__, array( $this, 'plugin_deactivation' ) );
		}

		/**
		 * Add the BuddyForms Bundle screen menu.
		 */
		public function buddyforms_bundle_screen_menu() {
			if ( buddyforms_core_fs()->is_not_paying() ) {
				add_submenu_page( 'edit.php?post_type=buddyforms', __( 'Bundle', 'buddyforms' ), __( 'Go Pro!', 'buddyforms' ), 'manage_options', 'buddyforms_bundle_screen', 'tk_pricing_page_render', 99 );
			}
		}

		/**
		 * Defines constants needed throughout the plugin.
		 *
		 * These constants can be overridden in bp-custom.php or wp-config.php.
		 *
		 * @package BuddyForms
		 * @since 0.1-beta
		 */
		public function load_constants() {

			/**
			 * Define the plugin version
			 */
			define( 'BUDDYFORMS_VERSION', $this->version );

			if ( ! defined( 'BUDDYFORMS_PLUGIN_URL' ) ) {
				/**
				 * Define the plugin url
				 */
				define( 'BUDDYFORMS_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
			}

			if ( ! defined( 'BUDDYFORMS_INSTALL_PATH' ) ) {
				/**
				 * Define the install path
				 */
				define( 'BUDDYFORMS_INSTALL_PATH', __DIR__ . '/' );
			}

			if ( ! defined( 'BUDDYFORMS_INCLUDES_PATH' ) ) {
				/**
				 * Define the include path
				 */
				define( 'BUDDYFORMS_INCLUDES_PATH', BUDDYFORMS_INSTALL_PATH . 'includes/' );
			}

			if ( ! defined( 'BUDDYFORMS_TEMPLATE_PATH' ) ) {
				/**
				 * Define the template path
				 */
				define( 'BUDDYFORMS_TEMPLATE_PATH', BUDDYFORMS_INSTALL_PATH . 'templates/' );
			}

			if ( ! defined( 'BUDDYFORMS_ADMIN_VIEW' ) ) {
				/**
				 * Define the template path
				 */
				define( 'BUDDYFORMS_ADMIN_VIEW', BUDDYFORMS_INCLUDES_PATH . 'admin/view/' );
			}

			if ( ! defined( 'BUDDYFORMS_ASSETS' ) ) {
				/**
				 * Define the template path
				 */
				define( 'BUDDYFORMS_ASSETS', plugins_url( 'assets/', __FILE__ ) );
			}
		}

		/**
		 * Defines buddyforms_init action
		 *
		 * This action fires on WP's init action and provides a way for the rest of WP,
		 * as well as other dependent plugins, to hook into the loading process in an
		 * orderly fashion.
		 *
		 * @package BuddyForms
		 * @since 0.1-beta
		 */
		public function init_hook() {
			$this->set_globals();
			do_action( 'buddyforms_init' );

			if ( function_exists( 'buddyforms_core_fs' ) ) {
				$bf_fr = buddyforms_core_fs();
				if ( ! empty( $bf_fr ) ) {
					$bf_fr->add_filter( 'plugin_icon', array( $this, 'buddyforms_freemius_icon' ) );
				}
			}
		}

		public function buddyforms_freemius_icon() {
			return BUDDYFORMS_INSTALL_PATH . 'assets/images/buddyforms.jpg';
		}

		/**
		 * Enable the localization of the fields adding the strings to js and hold the global localization
		 *
		 * @return array
		 */
		public static function localize_fields() {
			return apply_filters(
				'buddyforms_field_localization',
				array(
					'error_strings' => array(
						'error_string_start'    => __( 'The following', 'buddyforms' ),
						'error_string_singular' => __( 'error was', 'buddyforms' ),
						'error_string_plural'   => __( 'errors were', 'buddyforms' ),
						'error_string_end'      => __( 'found: ', 'buddyforms' ),
					),
					'bf_trans'      => array(
						array(
							'msgid'  => 'The following',
							'msgstr' => __( 'The following', 'buddyforms' ),
						),
						array(
							'msgid'  => 'error was',
							'msgstr' => __( 'error was', 'buddyforms' ),
						),
						array(
							'msgid'  => 'errors were',
							'msgstr' => __( 'errors were', 'buddyforms' ),
						),
						array(
							'msgid'  => 'found: ',
							'msgstr' => __( 'found: ', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Delete Permanently',
							'msgstr' => __( 'Delete Permanently', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Use a few words, avoid common phrases',
							'msgstr' => __( 'Use a few words, avoid common phrases', 'buddyforms' ),
						),
						array(
							'msgid'  => 'No need for symbols, digits, or uppercase letters',
							'msgstr' => __( 'No need for symbols, digits, or uppercase letters', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Add another word or two. Uncommon words are better.',
							'msgstr' => __( 'Add another word or two. Uncommon words are better.', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Straight rows of keys are easy to guess',
							'msgstr' => __( 'Short keyboard patterns are easy to guess', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Short keyboard patterns are easy to guess',
							'msgstr' => __( 'Short keyboard patterns are easy to guess', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Use a longer keyboard pattern with more turns',
							'msgstr' => __( 'Use a longer keyboard pattern with more turns', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Repeats like "aaa" are easy to guess',
							'msgstr' => __( 'Repeats like "aaa" are easy to guess', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Repeats like "abcabcabc" are only slightly harder to guess than "abc"',
							'msgstr' => __( 'Repeats like "abcabcabc" are only slightly harder to guess than "abc"', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Avoid repeated words and characters',
							'msgstr' => __( 'Avoid repeated words and characters', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Sequences like abc or 6543 are easy to guess',
							'msgstr' => __( 'Sequences like abc or 6543 are easy to guess', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Recent years are easy to guess',
							'msgstr' => __( 'Recent years are easy to guess', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Avoid recent years',
							'msgstr' => __( 'Avoid recent years', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Avoid years that are associated with you',
							'msgstr' => __( 'Avoid years that are associated with you', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Dates are often easy to guess',
							'msgstr' => __( 'Dates are often easy to guess', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Avoid dates and years that are associated with you',
							'msgstr' => __( 'Avoid dates and years that are associated with you', 'buddyforms' ),
						),
						array(
							'msgid'  => 'This is a top-10 common password',
							'msgstr' => __( 'This is a top-10 common password', 'buddyforms' ),
						),
						array(
							'msgid'  => 'This is a top-100 common password',
							'msgstr' => __( 'This is a top-100 common password', 'buddyforms' ),
						),
						array(
							'msgid'  => 'This is a very common password',
							'msgstr' => __( 'This is a very common password', 'buddyforms' ),
						),
						array(
							'msgid'  => 'This is similar to a commonly used password',
							'msgstr' => __( 'This is similar to a commonly used password', 'buddyforms' ),
						),
						array(
							'msgid'  => 'A word by itself is easy to guess',
							'msgstr' => __( 'A word by itself is easy to guess', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Names and surnames by themselves are easy to guess',
							'msgstr' => __( 'Names and surnames by themselves are easy to guess', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Common names and surnames are easy to guess',
							'msgstr' => __( 'Common names and surnames are easy to guess', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Capitalization doesn\'t help very much',
							'msgstr' => __( 'Capitalization doesn\'t help very much', 'buddyforms' ),
						),
						array(
							'msgid'  => 'All-uppercase is almost as easy to guess as all-lowercase',
							'msgstr' => __( 'All-uppercase is almost as easy to guess as all-lowercase', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Reversed words aren\'t much harder to guess',
							'msgstr' => __( 'Reversed words aren\'t much harder to guess', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Predictable substitutions like \'@\' instead of \'a\' don\'t help very much',
							'msgstr' => __( 'Predictable substitutions like \'@\' instead of \'a\' don\'t help very much', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Avoid sequences',
							'msgstr' => __( 'Avoid sequences', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Warning: ',
							'msgstr' => __( 'Warning: ', 'buddyforms' ),
						),
						array(
							'msgid'  => 'Suggestions: ',
							'msgstr' => __( 'Suggestions: ', 'buddyforms' ),
						),
					),
				)
			);
		}

		/**
		 * Hold the global variables to put in the frontend
		 *
		 * @param $array
		 *
		 * @return array
		 */
		public static function buddyforms_js_global_set_parameters( $array ) {
			if ( ! empty( self::$global_js_parameters ) ) {
				self::$global_js_parameters = array_merge( $array, self::$global_js_parameters );
			} else {
				self::$global_js_parameters = $array;
			}

			return self::$global_js_parameters;
		}

		/**
		 * Get the global variables to put in the frontend
		 *
		 * @param string $form_slug
		 *
		 * @return array
		 */
		public static function buddyforms_js_global_get_parameters( $form_slug = '' ) {
			return apply_filters( 'buddyforms_js_parameters', self::$global_js_parameters, $form_slug );
		}

		/**
		 * Setup all globals
		 *
		 * @package BuddyForms
		 * @since 0.1-beta
		 */
		static function set_globals() {
			global $buddyforms;

			/*
			 * Get BuddyForms options
			 *
			 * @filter: buddyforms_set_globals
			 *
			 */
			$buddyforms = apply_filters( 'buddyforms_set_globals', get_option( 'buddyforms_forms' ) );

			return $buddyforms;
		}

		/**
		 * Include files needed by BuddyForms
		 *
		 * @package BuddyForms
		 * @since 0.1-beta
		 */
		public function includes() {
			require_once BUDDYFORMS_INSTALL_PATH . 'vendor-scope/buddyforms/vendor/autoload.php';
			require_once BUDDYFORMS_INCLUDES_PATH . 'resources/pfbc/Encoding.php';

			if ( ! function_exists( 'PFBC_Load' ) ) {
				require_once BUDDYFORMS_INCLUDES_PATH . 'resources/pfbc/Form.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'resources/pfbc/FieldControl.php';
				new FieldControl();

				$global_error = ErrorHandler::get_instance();
			}

			require_once BUDDYFORMS_INCLUDES_PATH . 'admin/class-bf-admin-notices.php';
			new BfAdminNotices();

			require_once BUDDYFORMS_INCLUDES_PATH . 'admin/register-post-types.php';
			require_once BUDDYFORMS_INCLUDES_PATH . 'admin/admin-analytics.php';

			// Compatibility
			require_once BUDDYFORMS_INCLUDES_PATH . 'compatibility.php';

			require_once BUDDYFORMS_INCLUDES_PATH . 'functions.php';
			require_once BUDDYFORMS_INCLUDES_PATH . 'gdpr.php';
			require_once BUDDYFORMS_INCLUDES_PATH . 'change-password.php';
			require_once BUDDYFORMS_INCLUDES_PATH . 'multisite.php';
			require_once BUDDYFORMS_INCLUDES_PATH . 'the-content.php';
			require_once BUDDYFORMS_INCLUDES_PATH . 'rewrite-roles.php';
			require_once BUDDYFORMS_INCLUDES_PATH . 'shortcodes.php';
			require_once BUDDYFORMS_INCLUDES_PATH . 'wp-mail.php';
			require_once BUDDYFORMS_INCLUDES_PATH . 'wp-insert-user.php';
			require_once BUDDYFORMS_INCLUDES_PATH . 'revisions.php';

			// Gutenberg
			require_once BUDDYFORMS_INCLUDES_PATH . 'gutenberg/gutenberg.php';

			require_once BUDDYFORMS_INCLUDES_PATH . 'form/form.php';
			require_once BUDDYFORMS_INCLUDES_PATH . 'form/form-preview.php';
			require_once BUDDYFORMS_INCLUDES_PATH . 'form/form-render.php';
			require_once BUDDYFORMS_INCLUDES_PATH . 'form/form-ajax.php';
			require_once BUDDYFORMS_INCLUDES_PATH . 'form/form-elements.php';
			require_once BUDDYFORMS_INCLUDES_PATH . 'form/form-control.php';
			require_once BUDDYFORMS_INCLUDES_PATH . 'form/form-validation.php';
			require_once BUDDYFORMS_INCLUDES_PATH . 'admin/user-meta.php';

			if ( is_admin() ) {
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/form-builder/form-builder-elements.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/form-builder/form-templates.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/admin-ajax.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/pricing-page/pricing-page.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/pricing-page-config.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/welcome-screen.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/submissions.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/settings.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/password-strengh-settings.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/functions.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/deregister.php';

				// GDPR
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/personal-data-exporter.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/personal-data-eraser.php';

				if ( buddyforms_core_fs()->is__premium_only() ) {
					if ( buddyforms_core_fs()->is_plan( 'professional' ) || buddyforms_core_fs()->is_trial() ) {
						require_once BUDDYFORMS_INCLUDES_PATH . 'admin/form-metabox.php';
					}
				}

				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/mce-editor-button.php';

				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/form-builder/meta-boxes/metabox-mail-notification.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/form-builder/meta-boxes/metabox-permissions.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/form-builder/meta-boxes/metabox-layout.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/form-builder/meta-boxes/metabox-registration.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/form-builder/meta-boxes/metabox-shortcodes.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/form-builder/meta-boxes/metabox-select-form.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/form-builder/meta-boxes/metabox-form-elements.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/form-builder/meta-boxes/metabox-form-setup.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/form-builder/meta-boxes/metabox-form-header.php';
				require_once BUDDYFORMS_INCLUDES_PATH . 'admin/form-builder/meta-boxes/metabox-form-footer.php';
			}
		}

		/**
		 * Update form 1.x version
		 *
		 * @package BuddyForms
		 * @since 2.0
		 */
		function update_db_check() {

			if ( ! is_admin() ) {
				return;
			}

			$buddyforms_old = get_option( 'buddyforms_options' );

			if ( ! $buddyforms_old ) {
				return;
			}

			update_option( 'buddyforms_options_old', $buddyforms_old );

			foreach ( $buddyforms_old['buddyforms'] as $key => $form ) {
				$bf_forms_args = array(
					'post_title'  => $form['name'],
					'post_type'   => 'buddyforms',
					'post_status' => 'publish',
				);

				// Insert the new form
				$post_id    = wp_insert_post( $bf_forms_args, true );
				$form['id'] = $post_id;

				update_post_meta( $post_id, '_buddyforms_options', $form );

				// Update the option _buddyforms_forms used to reduce queries
				$buddyforms_forms = get_option( 'buddyforms_forms' );

				$buddyforms_forms[ $form['slug'] ] = $form;
				update_option( 'buddyforms_forms', $buddyforms_forms );

			}

			update_option( 'buddyforms_version', BUDDYFORMS_VERSION );

			delete_option( 'buddyforms_options' );

			buddyforms_attached_page_rewrite_rules( true );
		}

		/**
		 * Plugin activation
		 *
		 * @since  2.0
		 */
		function plugin_activation() {

			$title        = apply_filters( 'buddyforms_preview_page_title', __( 'BuddyForms Preview Page', 'buddyforms' ) );
			$preview_page = get_page_by_title( $title );
			if ( ! $preview_page ) {
				// Create preview page object
				$preview_post = array(
					'post_title'   => $title,
					'post_content' => __( 'This is a preview of how this form will appear on your website', 'buddyforms' ),
					'post_status'  => 'draft',
					'post_type'    => 'page',
					'post_name'    => sanitize_title( 'BuddyForms Preview Page' ),
				);

				// Insert the page into the database
				$page_id = wp_insert_post( $preview_post );
			} else {
				$page_id = $preview_page->ID;
			}

			update_option( 'buddyforms_preview_page', $page_id );

			$title            = apply_filters( 'buddyforms_submissions_page_title', __( 'BuddyForms Submissions Page', 'buddyforms' ) );
			$submissions_page = get_page_by_title( $title );
			if ( ! $submissions_page ) {
				// Create submissions page object
				$preview_post = array(
					'post_title'   => $title,
					'post_content' => __( 'This is the default page use like endpoint to the page submissions', 'buddyforms' ),
					'post_status'  => 'publish',
					'post_type'    => 'page',
					'post_name'    => sanitize_title( 'BuddyForms Submissions Page' ),
				);

				// Insert the page into the database
				$page_id = wp_insert_post( $preview_post );
			} else {
				$page_id = $submissions_page->ID;
			}

			update_option( 'buddyforms_submissions_page', $page_id );

			set_transient( '_buddyforms_welcome_screen_activation_redirect', true, 30 );

			$registration_gdpr_template                   = __( "By signing up on our site you agree to our terms and conditions [link]. We'll create a new user account for you based on your submissions. All data you submit will be stored on our servers.After your registration we'll instantly send you an email with an activation link to verify your mail address. ", 'buddyforms' );
			$post_gdpr_template                           = __( 'By submitting this form you grant us the rights <br>• to store your submitted contents in our database <br>• to generate a post on our site based on your data <br>• to make this post publicly accessible ', 'buddyforms' );
			$contact_gdpr_template                        = __( 'By submitting these data you agree that we store all the data from the form our server. We may answer you via mail.', 'buddyforms' );
			$buddyforms_gdpr                              = array();
			$buddyforms_gdpr['templates']['registration'] = $registration_gdpr_template;
			$buddyforms_gdpr['templates']['post']         = $post_gdpr_template;
			$buddyforms_gdpr['templates']['contact']      = $contact_gdpr_template;
			$buddyforms_gdpr['templates']['other']        = '';
			update_option( 'buddyforms_gdpr', $buddyforms_gdpr );

		}

		/**
		 * Plugin deactivation
		 *
		 * @since  2.0
		 */
		function plugin_deactivation() {
			$buddyforms_page = get_option( 'buddyforms_preview_page', true );
			wp_delete_post( $buddyforms_page, true );
			delete_option( 'buddyforms_preview_page' );

			$buddyforms_page = get_option( 'buddyforms_submissions_page', true );
			wp_delete_post( $buddyforms_page, true );
			delete_option( 'buddyforms_submissions_page' );
		}

		public static function error_log( $message ) {
			if ( ! empty( $message ) ) {
				error_log( 'buddyforms -- ' . $message );
			}
		}
	}

	/**
	 * Create a helper function for easy SDK access.
	 *
	 * @return Freemius
	 */
	if ( ! function_exists( 'buddyforms_core_fs' ) ) {
		function buddyforms_core_fs() {
			global $buddyforms_core_fs;

			if ( ! isset( $buddyforms_core_fs ) ) {
				$buddyforms_core_fs = fs_dynamic_init( array(
					'id'                             => '391',
					'slug'                           => 'buddyforms',
					'type'                           => 'plugin',
					'public_key'                     => 'pk_dea3d8c1c831caf06cfea10c7114c',
					'is_premium'                     => true,
					'has_premium_version'            => true,
					'has_addons'                     => true,
					'has_paid_plans'                 => true,
					'is_org_compliant'               => true,
					'wp_org_gatekeeper'              => 'OA7#BoRiBNqdf52FvzEf!!074aRLPs8fspif$7K1#4u4Csys1fQlCecVcUTOs2mcpeVHi#C2j9d09fOTvbC0HloPT7fFee5WdS3G',
					'trial'                          => array(
						'days'               => 7,
						'is_require_payment' => true,
					),
					'has_affiliation'                => 'selected',
					'menu'                           => array(
						'slug'        => 'edit.php?post_type=buddyforms',
						'first-path'  => 'edit.php?post_type=buddyforms&page=buddyforms_welcome_screen',
						'support'     => false,
						'contact'     => true,
						'addons'      => true,
						'affiliation' => false,
						'pricing'     => false,
					),
					'bundle_license_auto_activation' => true,
				) );
			}

			return $buddyforms_core_fs;
		}

		buddyforms_core_fs();
		do_action( 'buddyforms_core_fs_loaded' );
	}

	function buddyforms_php_version_admin_notice() {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'PHP Version Update Required!', 'buddyforms' ); ?></p>
			<p><?php /* translators: %s: PHP version running on the server. */ printf( esc_html__( 'You are using PHP Version %s', 'buddyforms' ), esc_html( PHP_VERSION ) ); ?></p>
			<p><?php esc_html_e( 'Please make sure you have at least php version 5.3 installed.', 'buddyforms' ); ?></p>
		</div>
		<?php
	}

	function activate_buddyform_at_plugin_loader() {
		// BuddyForms requires php version 5.3 or higher.
		if ( PHP_VERSION < 5.3 ) {
			add_action( 'admin_notices', 'buddyforms_php_version_admin_notice' );
		} else {
			// Init BuddyForms.
			$GLOBALS['buddyforms_new'] = new BuddyForms();
			// Init Freemius.
			buddyforms_core_fs();
			// GDPR Admin Notice
			buddyforms_core_fs()->add_filter( 'handle_gdpr_admin_notice', '__return_true' );

			if ( buddyforms_core_fs()->is__premium_only() ) {
				define( 'BUDDYFORMS_PRO_VERSION', 'pro' );
			}
		}
	}

	activate_buddyform_at_plugin_loader();
}
