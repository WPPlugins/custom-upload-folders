<?php
/**
 * Uninstall Procedure for Custom Upload Folders
 */

// Make sure that we are uninstalling
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

// Leave no trail
delete_option( 'custom_upload_folders' );