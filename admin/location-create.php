<?php

function gmap_location_create()
{
    $name = isset($_POST["name"]) ? sanitize_text_field($_POST["name"]) : '';
    $radius = isset($_POST["radius"]) ? intval($_POST["radius"]) : 0;
    $type_id = isset($_POST["type_id"]) ? intval($_POST["type_id"]) : 1;
    $lat = isset($_POST["lat"]) ? floatval($_POST["lat"]) : 0.0;
    $lng = isset($_POST["lng"]) ? floatval($_POST["lng"]) : 0.0;

    $message = '';
    $success = null;

    // Insert data
    if (isset($_POST['insert'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'gmapradius_locations';

        // Validate input before inserting
        if (empty($name)) {
            $message = "Name field cannot be empty.";
            $success = false;
        } elseif ($radius <= 0) {
            $message = "Radius must be a positive integer.";
            $success = false;
        } elseif (validateRelationTypeId($type_id)) {
            $message = "Please select a valid type.";
            $success = false;
        } elseif ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            $message = "Invalid latitude or longitude values.";
            $success = false;
        } else {
            $wpdb->insert(
                $table_name,
                array('name' => $name, 'radius' => $radius, 'type_id' => $type_id, 'lat' => $lat, 'lng' => $lng),
                array('%s', '%d', '%d', '%f', '%f')
            );
            $message = "Data inserted";
            $success = true;

            $name = '';
            $radius = 0;
            $type_id = 1;
            $lat = 0.0;
            $lng = 0.0;
        }
    }

    // Get a list of type options from the gmapradius_type table
    global $wpdb;
    $type_options = $wpdb->get_results("SELECT id, name FROM {$wpdb->prefix}gmapradius_type");
    ?>
    <div class="wrap">
        <a href="<?php echo admin_url('admin.php?page=gmap_location_list'); ?>" class="btn btn-outline-secondary btn-sm float-end" style="margin-top: 9px;">Back</a>
        <div class="">
            <h1 class="wp-heading-inline">Add New Location</h1>
        </div>
        <!-- Display Message -->
        <?php if (!empty($message)) : ?>
            <div class="<?php echo ($success) ? 'updated' : 'error'; ?>">
                <p><?php echo esc_html($message); ?></p>
            </div>
        <?php endif; ?>
        <div class="mt-3">
            <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                <table class='wp-list-table widefat fixed' style="width: 100%; margin-bottom:20px;">
                    <tr>
                        <th class="ss-th-width" class="form-label">Name</th>
                        <td><input type="text" name="name" value="<?php echo esc_attr($name); ?>" class="form-control" /></td>
                    </tr>
                    <tr>
                        <th class="ss-th-width" class="form-label">Radius (km)</th>
                        <td><input type="number" name="radius" min=1 value="<?php echo esc_attr($radius); ?>" class="form-control" /></td>
                    </tr>
                    <tr>
                        <th class="ss-th-width" class="form-label">Type</th>
                        <td>
                            <select name="type_id" class="form-control">
                                <?php foreach ($type_options as $option) : ?>
                                    <option value="<?php echo esc_attr($option->id); ?>" <?php selected($type_id, $option->id); ?>><?php echo esc_html($option->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th class="ss-th-width" class="form-label">Latitude</th>
                        <td><input type="text" name="lat" value="<?php echo esc_attr($lat); ?>" class="form-control" /></td>
                    </tr>
                    <tr>
                        <th class="ss-th-width" class="form-label">Longitude</th>
                        <td><input type="text" name="lng" value="<?php echo esc_attr($lng); ?>" class="form-control" /></td>
                    </tr>
                </table>
                <div class="d-flex justify-content-end align-items-center">
                    <input type='submit' name="insert" value='Save' class='btn btn-outline-primary'>
                </div>
            </form>
        </div>
    </div>
    <?php
}

function validateRelationTypeId($type_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gmapradius_type';
    $query = $wpdb->prepare("SELECT id FROM $table_name WHERE id = %d", $type_id);
    $result = $wpdb->get_var($query);
    return is_null($result);
}
