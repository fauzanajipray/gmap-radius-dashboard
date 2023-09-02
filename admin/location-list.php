<?php

function gmap_location_list()
{
    // Get a list of locations from the database
    global $wpdb;
    $locations = $wpdb->get_results("SELECT Location.*,Type.name as type_name  FROM {$wpdb->prefix}gmapradius_locations as Location JOIN {$wpdb->prefix}gmapradius_type as Type ON Location.type_id=Type.id;");

?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Locations</h1>
        <a href="<?php echo admin_url('admin.php?page=gmap_location_create'); ?>" class="page-title-action">Add New</a>

        <table class="wp-list-table widefat fixed striped mt-3" style="width: 100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Radius</th>
                    <th>Type ID</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($locations) > 0) {
                    foreach ($locations as $location) { ?>
                        <tr>
                            <td><?php echo $location->id; ?></td>
                            <td><?php echo $location->name; ?></td>
                            <td><?php echo $location->radius; ?></td>
                            <td><?php echo $location->type_name; ?></td>
                            <td><?php echo $location->lat; ?></td>
                            <td><?php echo $location->lng; ?></td>
                            <td>
                                <a href="?page=gmap_location_edit&id=<?php echo $location->id; ?>">Edit</a>
                            </td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="7" class="text-center">No locations found.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php
}
