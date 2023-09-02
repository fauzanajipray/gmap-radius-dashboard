<?php

function gmap_location_edit()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'gmapradius_locations';
    $message = '';
    $success = null;
    $id = null;

    // Check if an ID parameter is provided in the URL
    if (isset($_GET['id'])) {
        $location_id = intval($_GET['id']);
        $location = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $location_id), ARRAY_A);

        if (!$location) {
            $message = 'Location not found.';
            $success = false;
        } else {
            $name = $location['name'];
            $radius = $location['radius'];
            $type_id = $location['type_id'];
            $lat = $location['lat'];
            $lng = $location['lng'];
            $id = $location['id'];
        }
    } else {
        $message = 'Location ID not provided.';
        $success = false;
    }

    // Update data
    if (isset($_POST['update'])) {
        $name = sanitize_text_field($_POST["name"]);
        $radius = intval($_POST["radius"]);
        $type_id = intval($_POST["type_id"]);
        $lat = floatval($_POST["lat"]);
        $lng = floatval($_POST["lng"]);

        // Validate input before updating
        if (empty($name)) {
            $message = "Name field cannot be empty.";
            $success = false;
        } elseif ($radius <= 0) {
            $message = "Radius must be a positive integer.";
            $success = false;
        } elseif (validateRelationTypeId($type_id)) {
            $message = "Invalid Type ID.";
            $success = false;
        } elseif ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            $message = "Invalid latitude or longitude values.";
            $success = false;
        } else {
            $wpdb->update(
                $table_name,
                array('name' => $name, 'radius' => $radius, 'type_id' => $type_id, 'lat' => $lat, 'lng' => $lng),
                array('id' => $location_id),
                array('%s', '%d', '%d', '%f', '%f'),
                array('%d')
            );
            $message = "Data updated";
            $success = true;
        }
    } else if (isset($_POST['delete'])) {
        $id = intval($_GET['id']);
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %s", $id));
        $id = null;
        $message = 'Location Deleted';
        $success = true;
    }

    // Output HTML for the edit/update page
?>
    <div class="wrap">
        <a href="<?php echo admin_url('admin.php?page=gmap_location_list'); ?>" class="btn btn-outline-secondary btn-sm float-end" style="margin-top:9px">Back</a>
        <div class="">
            <h1 class="wp-heading-inline">Edit Location</h1>
        </div>
        <!-- Display Message -->
        <?php if (!empty($message)) : ?>
            <div class="<?php echo ($success) ? 'updated' : 'error'; ?>">
                <p><?php echo esc_html($message); ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($id)) { 
            $type_options = $wpdb->get_results("SELECT id, name FROM {$wpdb->prefix}gmapradius_type");
        ?>
            <div class="mt-3">
                <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                    <table class='wp-list-table widefat fixed' style="width: 100%; margin-bottom:20px;">
                        <tr>
                            <th class="ss-th-width" class="form-label">Name</th>
                            <td><input type="text" name="name" value="<?php echo esc_attr($name); ?>" class="form-control" /></td>
                        </tr>
                        <tr>
                            <th class="ss-th-width" class="form-label">Radius</th>
                            <td><input type="number" name="radius" value="<?php echo esc_attr($radius); ?>" class="form-control" /></td>
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
                        <input type='submit' name="delete" value='Delete' class='btn text-danger m-1' onclick="return confirm('Are you sure want to delete this?')">
                        <input type='submit' name="update" value='Update' class='btn btn-outline-primary ml-2'>
                    </div>
                </form>
            </div>
        <?php } ?>
    </div>
<?php
}
