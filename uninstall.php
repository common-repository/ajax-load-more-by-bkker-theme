<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

$option_name = 'ajax_load_more_by_bkker_theme_option_name';

delete_option( $option_name );
delete_site_option( $option_name );
