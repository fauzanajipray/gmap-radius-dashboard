<?php

function gmap_type_list()
{
    // Mendapatkan daftar jenis lokasi dari database
    global $wpdb;
    $types = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}gmapradius_type");
    
?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Type</h1>
        <a href="<?php echo admin_url('admin.php?page=gmap_type_create'); ?>" class="page-title-action">Add New</a>
        
        <table class="wp-list-table widefat fixed striped mt-3" style="width: 100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>color</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($types) > 0) {
                    foreach ($types as $type) { ?>
                        <tr>
                            <td><?php echo $type->id; ?></td>
                            <td><?php echo $type->name; ?></td>
                            <td><span style="background-color: <?php echo $type->color ?>;"><?php echo $type->color; ?></span></td>
                            <td>
                                <a href="?page=gmap_type_edit&id=<?php echo $type->id; ?>" class="text-primary">Edit</a>
                            </td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="4" class="text-center">No types found.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php
}
