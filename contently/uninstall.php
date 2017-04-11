<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	// not defined, abort
	exit ();
}
// it was defined, now delete
delete_option( 'cl_profiles' );
delete_option( 'cl_post_types' );
delete_option( 'cl_custom_fields' );
delete_option( 'contently_temp' );
delete_option( 'cl_deprecated_options' );
delete_option( 'cl_analytics' );
delete_option( 'cl_options' );

//@todo Remove @deprecated. This option migrated to cl_options.
delete_option( 'cl_debug' );