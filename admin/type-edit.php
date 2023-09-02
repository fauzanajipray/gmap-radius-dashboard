<?php
function gmap_type_edit()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'gmapradius_type';
    $message = '';
    $success = null;
    $id = null;

    // Check if an ID parameter is provided in the URL
    if (isset($_GET['id'])) {
        $type_id = intval($_GET['id']);
        $type = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $type_id), ARRAY_A);

        if (!$type) {
            $message = 'Type not found.';
            $success = false;
        } else {
            $name = $type['name'];
            $color = $type['color'];
            $id = $type['id'];
        }
    } else {
        $message = 'Type ID not provided.';
        $success = false;
    }

    // Update data
    if (isset($_POST['update'])) {
        $name = sanitize_text_field($_POST["name"]);
        $color = sanitize_hex_color($_POST["color"]);

        // Validate input before updating
        if (empty($name)) {
            $message = "Name field cannot be empty.";
            $success = false;
        } elseif (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            $message = "Color must be in the format #RRGGBB (e.g., #993303).";
            $success = false;
        } else {
            $wpdb->update(
                $table_name,
                array('name' => $name, 'color' => $color),
                array('id' => $type_id),
                array('%s', '%s'),
                array('%d')
            );
            $message = "Data updated";
            $success = true;
        }
    } else if (isset($_POST['delete'])) {
        $id = intval($_GET['id']);
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %s", $id));
        $id = null;
        $message = 'Type Deleted';
        $success = true;
    }

?>
    <div class="wrap">
        <a href="<?php echo admin_url('admin.php?page=gmap_type_list'); ?>" class="btn btn-outline-secondary btn-sm float-end" style="margin-top:9px">Back</a>
        <div class="">
            <h1 class="wp-heading-inline">Edit Type</h1>
        </div>
        <!-- Display Message -->
        <?php if (!empty($message)) : ?>
            <div class="<?php echo ($success) ? 'updated' : 'error'; ?>">
                <p><?php echo esc_html($message); ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($id)) { ?>
        <div class="mt-3">
            <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                <table class='wp-list-table widefat fixed' style="width: 100%; margin-bottom:20px;">
                    <tr>
                        <th class="ss-th-width" class="form-label">Name</th>
                        <td><input type="text" name="name" value="<?php echo esc_attr($name); ?>" class="form-control" /></td>
                    </tr>
                    <tr>
                        <th class="ss-th-width" class="form-label">Color</th>
                        <td>
                            <input type="color" name="color" value="<?php echo esc_attr($color); ?>" class="form-control form-control-color" title="Choose your color">
                        </td>
                    </tr>
                </table>
                <div>

                </div>
                <div class="d-flex justify-content-end align-items-center">
                    <input type='submit' name="delete" value='Delete' class='btn text-danger m-1' onclick="return confirm('Are you sure want to delete this?')">
                    <input type='submit' name="update" value='Update' class='btn btn-outline-primary ml-2'>
                    <!-- <a href="?page=gmap_type_list&action=delete&id=<?php echo $type->id; ?>" class="text-danger">Delete</a> -->
                </div>
            </form>
        </div>
        <?php } ?>
    </div>
<?php
}
