/**
 * BuddyForms thickbox shim.
 *
 * The BuddyForms ecosystem (core, BuddyForms-Modal, BuddyForms-Moderation, ...)
 * emits trigger links with the public `bf-thickbox` CSS class. WordPress core's
 * bundled thickbox library only binds to `.thickbox` selectors out of the box,
 * so this shim re-binds it to our class as well. No forked library code, no
 * duplicate styles — core thickbox does the actual work.
 *
 * Public API kept stable:
 *   - script handle: `buddyforms-thickbox` (depends on core `thickbox`)
 *   - CSS class: `bf-thickbox` on trigger anchors / inputs / area maps
 */
jQuery( function ( $ ) {
	if ( typeof tb_init === 'function' ) {
		tb_init( 'a.bf-thickbox, area.bf-thickbox, input.bf-thickbox' );
	}
} );
