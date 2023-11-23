<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' )){
    exit;
}

global $wpdb;

$wpdb->query( "DROP TABLE IF EXISTS `" . $wpdb->prefix . "gift_club`, `" . $wpdb->prefix . "received_club`;" );
$wpdb->query( "ALTER TABLE `" . $wpdb->prefix . "users` DROP `count_club`;" );