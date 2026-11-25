=== BuddyForms ===
Contributors: themekraft, svenl77, konrads, buddyforms, shabushabu, projectkarol, gfirem, jnfdev, camiloluna
Tags: custom form, form builder, registration, user registration, forms
Requires at least: 5.9
Tested up to: 7.1
Stable tag: 2.10.11
Requires PHP: 7.4
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Drag-and-drop form builder for contact, registration and front-end post forms. Pro adds any post type, file uploads, permissions and drafts.

== Description ==

BuddyForms is a drag-and-drop form builder that lets visitors and members create content on your WordPress site. One plugin covers contact forms, registration and profile forms, and front-end post forms, so users can submit, edit and manage their posts without ever opening wp-admin.

= Free features =

* Three form types: contact forms, registration forms and post forms
* Drag-and-drop form builder with ready-made form templates
* Form Designer: style labels, descriptions, buttons and layout, use labels as placeholders, or turn off the built-in CSS
* Create posts and pages from the front end, and let members list and edit their own submissions
* Choose what happens after submit: a message, the form again, a page, the new post, the user's post list, or a redirect
* Guest submissions or logged-in only, with an optional login form and registration link
* Create a user account while submitting a post
* Registration options: role of the new user, page after activation, replace the WordPress registration page
* Email notification when a form is submitted
* Submissions screen in wp-admin with every form's entries
* Import and export forms between sites
* Five Gutenberg blocks and matching shortcodes
* GDPR tools: personal data export of form submissions, plus a shortcode where visitors request a data export or erasure

= Want more? BuddyForms Pro adds =

* Any post type: WooCommerce products, events, listings or any custom post type (the free version covers Posts, Pages and Submissions)
* File and Upload fields with size and file-type limits
* Per-role permissions: choose which roles can use a form, and which can create, edit, delete, save drafts or see submissions in wp-admin
* Save as draft, and a default post status for new posts: published, pending review or draft
* Email notifications when a post's status changes
* Front-end revisions: users can see and restore earlier versions of their posts
* Open or close comments on submitted posts
* More field types: Taxonomy, Hidden, HTML, Range slider, Price, State and Form Actions
* Show form fields as a meta box in the wp-admin post editor
* Optional submitter insights: IP address, browser, platform and user agent stored with each submission
* Front-end editing everywhere, including the admin bar and the "My Posts" list
* Registration extras: auto-generated passwords, username from the email address, and profile changes sent back to moderation
* Advanced form options: custom form slug and labels, and AJAX submission
* No "Proudly brought to you by BuddyForms" link and no upgrade notices

BuddyForms Pro is a yearly license for 1 site, 5 sites or unlimited sites, and every Pro feature is included at every size. [Compare Free and Pro on themekraft.com](https://themekraft.com/plugins/buddyforms/?utm_source=wordpress.org&utm_medium=referral&utm_campaign=free-vs-pro&utm_content=buddyforms)

[youtube https://www.youtube.com/watch?v=DoPLWBBlRvA]


### Full Gutenberg Support

#### Embed Forms
Embed any BuddyForms Form as Gutenberg Block. Just select the form you like to embed in the block sidebar.

#### List Submissions
You can list form submissions form any form and post type. Filter post lists by author or only display posts from the logged in user. Use the options in the Block sidebar.

#### Embed Navigation
Link to form endpoints or user posts lists for every post form with an attached page to create and edit submissions. You can select the attached page under the "Edit Submissions" tab in the Form Builder.

#### Login/ Logout Form
Display a login form or a logout button if the user is logged in.


### Shortcodes

#### Display a Form
Use this shortcode if you wanna show a form on frontend.
> [bf form_slug="YOUR-FORM-SLUG"]

#### Display Submissions
Use this shortcode if you wanna show a list of entries belonging to a Form.
The attribute "list_posts_style" is optional and its possible values are "table" or "list"
> [bf_posts_list form_slug="YOUR-FORM-SLUG" list_posts_style=""]

#### Link to Form
This shortcode will create a link to the form for creating or editing submissions.
The attribute "label" is optional (the default value is "Add New").
> [bf_link_to_form form_slug="YOUR-FORM-SLUG" label=""]

#### Link to User Posts
For logged-in users you can use the following shortcode to display their submissions.
The attribute "label" is optional (the default value is "View").
> [bf_link_to_user_posts form_slug="YOUR-FORM-SLUG" label=""]

#### User Posts List
For logged-in users you can use the following shortcode to display a list of posts.
> [bf_user_posts_list form_slug="YOUR-FORM-SLUG"]

= Free field types =

* Basic fields: text, email, textarea, phone, number, dropdown, radio, checkbox, URL, date, time and country, plus a Message field
* User fields: username, email, first and last name, password, website, display name and bio
* Post fields: title, content, excerpt, category, tags, post formats, comments, post status and featured image
* GDPR agreement and Google reCAPTCHA

= Pro field types =

* Taxonomy field for any custom taxonomy
* File and Upload (drag-and-drop uploads with size and file-type limits)
* Hidden and HTML
* Range slider, Price and State
* Form Actions, to control the Publish and Draft buttons

= Your data stays on your server =

Submissions are stored on your own WordPress site. BuddyForms includes GDPR tools: personal data export of form submissions, plus a shortcode where visitors request a data export or erasure.

= Add-ons and integrations =

BuddyForms add-ons are free and connect your forms to BuddyPress and BuddyBoss, Ultimate Member, Advanced Custom Fields and WooCommerce. Add-ons that create custom post types, like the WooCommerce add-on, need BuddyForms Pro. [Browse the BuddyForms add-ons](https://themekraft.com/plugins/buddyforms/addons/)

= Solutions =

* [Guest post management](https://themekraft.com/solution/guest-posts-submission/)
* [Display form submission data](https://themekraft.com/solution/display-form-data/)
* [More BuddyForms solutions](https://themekraft.com/solutions/)

= Forms you can build =

* Contact forms to collect leads and support requests
* Registration forms to sign up users
* Profile forms so users can update their profile information
* Login forms to give users access to restricted content
* Post forms for blog posts, so you can collect content from different authors

== Installation ==

Upload the entire BuddyForms folder to the /wp-content/plugins/ directory or install the plugin through the WordPress plugins screen directly.
Activate the plugin through the 'Plugins' menu in WordPress.
Head to the 'BuddyForms' menu item in your admin sidebar

== Frequently Asked Questions ==

= What is free and what is Pro? =

The free version covers contact, registration and post forms, the drag-and-drop builder, the Form Designer, front-end posting and editing of Posts and Pages, submissions in wp-admin, five Gutenberg blocks and GDPR tools. Pro adds any post type, file uploads, per-role permissions, drafts, post status emails, front-end revisions and more field types.

= Which post types can I use? =

The free version creates Posts, Pages and Submissions (contact form entries). BuddyForms Pro adds any post type, including WooCommerce products, events, listings and other custom post types.

= Can users attach files to the forms? =

File and Upload fields are part of BuddyForms Pro. The free version includes a Featured Image field.

= How is Pro licensed? =

BuddyForms Pro is a yearly license for 1 site, 5 sites or unlimited sites. Every Pro feature is included at every size. Pricing and purchase are on themekraft.com.

= Is there a free trial of Pro? =

Yes. BuddyForms Pro has a 7-day free trial. A payment method is required to start it.

= How easy is it to create a post form using BuddyForms? =

Creating a post form using BuddyForms is incredibly simple. Our intuitive user interface allows you to build and customize your post forms to meet your unique requirements.

= Can I customize the fields in the registration form? =

Absolutely! BuddyForms allows you to fully customize your registration form fields. You can add or remove fields as needed, ensuring a seamless registration process for your users.

= Is it possible to create multiple profile forms for different user roles? =

Yes. You can build several registration and profile forms and choose the role each one assigns. Limiting which roles can use a form, setting create, edit and delete permissions per role, and some registration options (auto-generated passwords, username from the email address, moderating profile updates) are part of BuddyForms Pro.

= What types of content forms can I create with BuddyForms? =

With the free version you can create blog post, article and page forms. With BuddyForms Pro you can also create forms for any custom post type, such as WooCommerce products, events or listings.

= Can I create a normal Contact Form? =

Yes, you can create Simple Contact Forms or Complex Contact Forms with different custom fields.

= Can I Combine Registration Forms and Content Forms? =

Yes, you can add login and registration Form Elements to any form and ask your user to log in or Register during form submission.

== Documentation & Support ==
Documentation: [docs.buddyforms.com](http://docs.buddyforms.com/)

If you still get stuck somewhere, our support gets you back on the right track. You can find all help buttons in your BuddyForms Settings Panel in your WP Dashboard and the Help Center! Our goal is to help you, that's why if you have any questions or concerns, on our website you can find all the information related to BuddyForms.

== Screenshots ==

1. Embed Forms - The easy way to embed forms
2. List Submissions - Display the user Submissions
3. Embed Navigation -  Link to forms and Submissions
4. Login/ Logout Form - Add Login Forms and define a custom redirect after login

== Changelog ==
= 2.10.11 - 25 Nov 2026 =
* Security: array-shaped form field values are now fully sanitized, and the Submissions list prints stored values as plain text.
* Changed: aligned the Go Pro page copy with the readme.

= 2.10.10 - 20 Nov 2026 =
* Security: upload and featured image fields now only accept files uploaded by the submitter or already attached to the entry.
* Security: forms now check edit permissions before showing an existing entry or one of its revisions.
* Security: only administrators can create and edit forms by default. To let other roles build forms, use the `buddyforms_form_builder_capability` filter.
* Security: new entries are always saved with the submitter as author.
* Security: entries can only be saved with the post statuses the form offers.
* Security: deleting an entry from the front end now checks the form nonce.
* Security: per-visitor form settings are no longer written to a shared file.
* Security: form builder actions now check the user can manage forms.

= 2.10.9 - 19 Nov 2026 =
* Fixed a critical error when a registration form fails validation and shows the login form again.

= 2.10.8 - 18 Nov 2026 =
* Changed: the Go Pro page now sells BuddyForms Pro. The BuddyForms add-ons are free and are no longer listed there.
* Changed: the Go Pro boxes in the form builder and settings sidebars now list only features that need BuddyForms Pro.
* Fixed product forms creating a new product draft every time the form was shown.
* Fixed the Permission tab of the form builder, which kept the role permissions and the options for unregistered users hidden after changing the form type, until the form was saved and reloaded.
* Fixed PHP 8 deprecation notices in the form builder and the form classes, and a WordPress deprecation notice when activating the plugin.
* Updated the bundled Guzzle and reCAPTCHA libraries, which logged deprecation notices on PHP 8.4 and later.
* Fixed reCAPTCHA v3 rejecting every submission on servers with allow_url_fopen turned off, and a PHP warning when a form was sent without the reCAPTCHA token.
= 2.10.7 - 12 Nov 2026 =
* Security: the title of the login form shortcode and block is always printed as plain text, and output built from shortcode attributes (login form, navigation links) no longer allows script tags or event handler attributes.
* Fixed the `[bf_new_submission_link]` shortcode, which always linked to `#` and printed a broken `target` attribute.
= 2.10.6 - 19 Oct 2026 =
* Security: front-end submissions always save entries as the post type configured in the form, and never as WordPress-internal types or as forms.
* Security: anonymous registration cannot assign a role that can manage the site or its users. The list of blocked capabilities can be changed with the `buddyforms_registration_blocked_capabilities` filter.
* Security: saving a form in the form builder and saving the entry fields meta box now check a nonce and the user's permission to edit the post.
* Changed: the `post_type` attribute of the `[buddyforms_form]` shortcode no longer changes the post type entries are saved as. Use the `buddyforms_submission_post_type` filter instead.
* Fixed a fatal error when a submission arrived without an entry ID.
* Updated the Go Pro links to the current themekraft.com product pages.
= 2.10.5 - 07 Oct 2026 =
* Fixed a fatal error when activating the premium version while the free version is active. Activating one version now deactivates the other one.
= 2.10.4 - 06 Oct 2026 =
* Security: the List Submissions block only accepts the list styles BuddyForms and its add-ons register, and templates are only loaded by plain names.
* Security: taxonomy searches in forms only return terms from a taxonomy field configured in that form, for users allowed to use the form.
= 2.10.3 - 06 Oct 2026 =
* Security: only users who can edit a form can export it, and the export link now carries a nonce.
= 2.10.2 - 03 Oct 2026 =
* The premium version now satisfies add-ons that list BuddyForms as a required plugin (WordPress 6.5+ plugin dependencies), and ACF PRO satisfies add-ons that require ACF.
= 2.10.1 - 03 Oct 2026 =
* Security: tightened permission checks when submitting and editing entries, uploading and removing files, viewing and deleting submissions in the admin, and listing entries with shortcodes.
* Security: escaped login error messages and kept failed submissions private to the visitor who sent them.
* Fixed the personal data export, which could include entries that did not belong to the requested email address.
* Removed build scripts that were included in the 2.10.0 package by mistake.

= 2.10.0 - 03 Oct 2026 =
* Plugin Check: fixed plugin-header License field, removed the invalid Network header, stripped hidden macOS metadata, removed vestigial empty Freemius leftover directories, and dropped the manual `load_plugin_textdomain()` call (WordPress.org loads translations automatically since WP 4.6).
* Cleaned up the user-facing English copy: "Admin rights can not get changed" → "cannot be changed", "logged of user" → "logged-out users", "logged off users / not need to get checked" → "logged-out users / do not need to be checked", missing text-domain on a login-form fallback string, and three template-error messages that were embedding `$form_slug` directly into `esc_html__()` calls (now sprintf'd so translators can localize the template). POT regenerated; bundled de_DE / es_ES / fr_FR / pt_BR / pt_PT / zh_CN translations carried forward where the meaning was preserved.
* Updated Freemius SDK to 2.13.1.
* Reworked the Go Pro page to use the parameterized shared pricing-page submodule with three site-license tiers. It now sells the BuddyForms Bundle instead of the all-products bundle.
* The Go Pro menu is hidden on sites with an active license, including bundle licenses.
* The Upgrade Now buttons in the form builder now open the Go Pro page instead of the Freemius pricing page.
* Refreshed the Go Pro page card layout to a responsive CSS grid.
* Highlighted the Go Pro menu link in the admin sidebar.
* Cleaned up the Freemius SDK init: removed the dead first-path option lookup, the duplicate has_paid_plans key, and the empty try/catch wrapper.
* Swapped file_get_contents() for cURL in the captcha implementation.
* Plugin Check: replaced the forked thickbox library at `assets/resources/bf-thickbox/bf-thickbox.{js,css}` with a 17-line shim that calls WordPress core's `tb_init()` against the `.bf-thickbox` selector list. The 700-line forked library and its bundled stylesheet are gone; the public `bf-thickbox` CSS class and `buddyforms-thickbox` enqueue handle remain stable for downstream add-ons (BuddyForms-Modal, BuddyForms-Moderation, ...).
* Plugin Check: aligned the readme `===` heading with the plugin-header `Plugin Name: BuddyForms` (was a long SEO-keyword string), aligned the plugin-header License field with the readme (`GPLv3` → `GPLv2`), and aligned `@package` docblocks across `BuddyForms.php` and `includes/` with the readable Plugin Name. Three TextDomainMismatch warnings on the shared pricing-page submodule are now suppressed at the submodule layer.
* Plugin Check: every PHP file now prevents direct access, translatable strings use the buddyforms text domain and carry translator comments, and date(), rand(), strip_tags() and utf8_decode() were replaced with their WordPress equivalents.
* Requires WordPress 5.9 or later and PHP 7.4 or later.
* Tested up to WordPress 7.1.

= 2.9.0 - 30 May 2025 =
* Replaced file_get_contents with curl in captcha implementation.
* Fixed trailing slash inconsistency when using constants.
* Update Go Pro page.
* Tested up to WordPress 6.8.1.

= 2.8.17 - 27 Feb 2025 =
* Fixed permissions roles not showing when choosing contact form.
* Fixed issues with reCaptcha v2.

= 2.8.16 - 20 Feb 2025 =
* Update Freemius SDK.
* Tested up to WordPress 6.7.2.
* Fixed vulnerability with buddyforms_nav shortcode (issue found by Max Boll - @_b0lli).

= 2.8.15 - 29 Jan 2025 =
* Update Freemius SDK.

= 2.8.14 - 9 Jan 2025 =
* Fixed vulnerability with bf_new_submission_link shortcode (issue found by Max Boll - @_b0lli).
* Tested up to WordPress 6.7.1.

= 2.8.13 - 23 Sep 2024 =
* Fixed security issue.
* Updated Freemius SDK.

= 2.8.12 - 11 Sep 2024 =
* Only administrators can modify the default role "subscriber".
* Updated Freemius SDK.
* Tested up to WordPress 6.6.2.

= 2.8.11 - 30 Jul 2024 =
* Updated Freemius SDK.
* Tested up to WordPress 6.6.1.

= 2.8.10 - 11 Jun 2024 =
* Fixed issue with activation code.
* Updated Freemius SDK.
* Tested up to WordPress 6.5.4.

= 2.8.9 - 22 Mar 2024 =
* Fixed partial issue with arbitrary file download vulnerability.

= 2.8.8 - 06 Mar 2024 =
* Improved security.
* Fixed issue with creation of pages from submissions setup tab.

= 2.8.7 - 06 Feb 2024 =
* Added new hook to filter custom field value.
* Tested up to WordPress 6.4.3.

= 2.8.6 - 18 Jan 2024 =
* Fixed XSS issue.
* Fixed arbitrary file download issue.
* Fixed issue related with filter submissions by author.
* Updated Freemius SDK.
* Tested up to WordPress 6.4.2.

= 2.8.5 - 21 Nov 2023 =
* Fixed issue with some file format.

= 2.8.4 - 16 Nov 2023 =
* Updated Freemius SDK.
* Tested up to WordPress 6.4.1.

= 2.8.3 - 05 Jul 2023 =
* Fixed XSS vulnerability.
* Fixed issue with an upload field and allowed formats.
* Fixed issue with string max length validation.
* Tested up to WordPress 6.2.2.

= 2.8.2 - 11 May 2023 =
* Fixed vulnerability issue in shortcode.

= 2.8.1 - 04 May 2023 =
* Added a new hook to filter form html content.
* Updated Freemius SDK.
* Tested up to WordPress 6.2.

= 2.8.0 - 22 Mar 2023 =
* Added a new option to the Form Builder Edit Submission Settings to dynamically define the output from BuddyForms.
