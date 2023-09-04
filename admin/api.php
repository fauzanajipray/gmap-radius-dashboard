<?php

use function PHPSTORM_META\type;

function gmapradius_api()
{
    register_rest_route('api/gmapradius/v1', '/locations/', array(
        'methods' => 'GET',
        'callback' => 'gmapradius_api_location',
    ));
    register_rest_route('api/gmapradius/v1', '/types/', array(
        'methods' => 'GET',
        'callback' => 'gmapradius_api_type',
    ));
}
add_action('rest_api_init', 'gmapradius_api');


function gmapradius_api_location($request)
{
    global $wpdb;

    // Get the 'types' parameter from the request
    $types_json = $request->get_param('types');

    $types = json_decode($types_json);

    // Ensure that $types is an array
    if (!is_array($types)) {
        $types = array();
    }

    // Validate and sanitize the type IDs
    $valid_types = array();

    foreach ($types as $type_id) {
        if (is_numeric($type_id) && intval($type_id) > 0) {
            $valid_types[] = intval($type_id);
        }
    }

    if (empty($valid_types)) {
        return array();
    }

    // Prepare the SQL query with a WHERE clause to filter by valid types
    $sql = "SELECT Location.*, Type.name as type_name FROM {$wpdb->prefix}gmapradius_locations as Location";
    $sql .= " JOIN {$wpdb->prefix}gmapradius_type as Type ON Location.type_id = Type.id";
    $sql .= " WHERE Location.type_id IN (" . implode(',', $valid_types) . ")";

    $locations = $wpdb->get_results($sql);

    // Prepare the response
    $response = new WP_REST_Response($locations, 200);
    $response->set_headers(array('Content-Type' => 'application/json'));

    return $response;
}

function gmapradius_api_type()
{
    global $wpdb;
    $types = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}gmapradius_type");

    // Prepare the response
    $response = new WP_REST_Response($types, 200);
    $response->set_headers(array('Content-Type' => 'application/json'));

    return $response;
}
