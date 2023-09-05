<?php

/**
 * Plugin Name: Google Maps Radius
 * Description: This plugin adds Google Maps integration for radius visualization.
 * Version: 1.0
 */

// function to create the DB / Options / Defaults					
function gmapradius_activation()
{
	global $wpdb;

    // Tabel untuk menyimpan jenis lokasi
    $table_types = $wpdb->prefix . 'gmapradius_type';
    $sql_types = "CREATE TABLE IF NOT EXISTS $table_types (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(220) DEFAULT NULL,
        `color` varchar(220) DEFAULT NULL,
        PRIMARY KEY(id)
    )";
    $wpdb->query($sql_types);

    // Tabel untuk menyimpan lokasi dan radius
    $table_locations = $wpdb->prefix . 'gmapradius_locations';
    $sql_locations = "CREATE TABLE IF NOT EXISTS $table_locations (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(220) DEFAULT NULL,
        `radius` int(11) DEFAULT 0,
        `type_id` int(11),
        `lat` DECIMAL(10, 6),
        `lng` DECIMAL(10, 6),
        PRIMARY KEY(id),
        FOREIGN KEY (`type_id`) REFERENCES $table_types (`id`) ON DELETE CASCADE
    )";
    $wpdb->query($sql_locations);

	// Tabel untuk menyimpan pengaturan, termasuk API Key
    $table_settings = $wpdb->prefix . 'gmapradius_settings';
    $sql_settings = "CREATE TABLE IF NOT EXISTS $table_settings (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `setting_name` varchar(220) NOT NULL,
        `setting_value` text,
        PRIMARY KEY(id),
        UNIQUE KEY `unique_setting_name` (`setting_name`)
    )";
    $wpdb->query($sql_settings);

    // Check if 'GMAP_API_KEY' setting already exists
    $existing_setting = $wpdb->get_var($wpdb->prepare("SELECT setting_name FROM $table_settings WHERE setting_name = %s", 'GMAP_API_KEY'));

    // If 'GMAP_API_KEY' setting doesn't exist, insert it with a null value
    if (empty($existing_setting)) {
        $wpdb->insert(
            $table_settings,
            array('setting_name' => 'GMAP_API_KEY', 'setting_value' => null),
            array('%s', '%s')
        );
    }
}
// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'gmapradius_activation');

//menu items
add_action('admin_menu', 'gmapradius_modifymenu');
function gmapradius_modifymenu()
{
	add_menu_page(
        'Google Maps Radius',
        'GMap Radius',
        'manage_options',
        'gmapradius',
        'gmapradius_map'
    );

	add_submenu_page(
		'gmapradius',
		'GMap Type',
		'Types',
		'manage_options',
		'gmap_type_list',
		'gmap_type_list',
	);

	add_submenu_page(
		null, //parent slug
		'Add New Type', //page title
		'Add New Type', //menu title
		'manage_options', //capability
		'gmap_type_create', //menu slug
		'gmap_type_create'
	);

	add_submenu_page(
		null, //parent slug
		'Edit Type', //page title
		'Edit Type', //menu title
		'manage_options', //capability
		'gmap_type_edit', //menu slug
		'gmap_type_edit'
	);

    add_submenu_page(
		'gmapradius',
		'GMap Location',
		'Locations',
		'manage_options',
		'gmap_location_list',
		'gmap_location_list',
	);

    add_submenu_page(
		null, //parent slug
		'Add New Location', //page title
		'Add New Location', //menu title
		'manage_options', //capability
		'gmap_location_create', //menu slug
		'gmap_location_create'
	);

    add_submenu_page(
		null, //parent slug
		'Edit Location', //page title
		'Edit Location', //menu title
		'manage_options', //capability
		'gmap_location_edit', //menu slug
		'gmap_location_edit'
	);

    add_submenu_page(
		'gmapradius',
		'Setting',
		'Settings   ',
		'manage_options',
		'gmapradius_settings',
		'gmapradius_settings',
	);

}

function gmapradius_load_assets_only_on_plugin_pages() {
    global $pagenow;
    
    // Check if we are on one of the specific plugin pages
    $plugin_pages = ['gmapradius', 'gmap_type_list', 'gmap_type_create', 'gmap_type_edit', 'gmap_location_list', 'gmap_location_create', 'gmap_location_edit', 'gmapradius_settings'];

    if ($pagenow === 'admin.php' && isset($_GET['page']) && in_array($_GET['page'], $plugin_pages)) {
        // Enqueue assets specific to your plugin's pages
        wp_enqueue_script('polyfill-io', 'https://polyfill.io/v3/polyfill.min.js?features=default', array(), null, false);
        wp_enqueue_style('custom-admin-css', plugins_url('/css/admin-style.css', __FILE__));
        wp_register_script('alpine-js-defer', 'https://cdn.jsdelivr.net/npm/alpinejs@3.13.0/dist/cdn.min.js', array(), '3.13.0', array('strategy' => 'defer'));
        wp_enqueue_script('alpine-js-defer');
        wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css', array(), null);
        wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);
        wp_enqueue_script('custom-admin-js', plugins_url('/js/admin-script.js', __FILE__), '', '1.0', true);
    }
}

// Hook the asset loading function to the admin_enqueue_scripts action
add_action('admin_enqueue_scripts', 'gmapradius_load_assets_only_on_plugin_pages');


define('ROOTDIR', plugin_dir_path(__FILE__));
require_once(ROOTDIR . 'admin/main.php');
require_once(ROOTDIR . 'admin/type-list.php');
require_once(ROOTDIR . 'admin/type-create.php');
require_once(ROOTDIR . 'admin/type-edit.php');
require_once(ROOTDIR . 'admin/location-list.php');
require_once(ROOTDIR . 'admin/location-create.php');
require_once(ROOTDIR . 'admin/location-edit.php');
require_once(ROOTDIR . 'admin/api.php');
require_once(ROOTDIR . 'admin/settings.php');
