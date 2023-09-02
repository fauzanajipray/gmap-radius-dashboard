<?php

function gmap_type_create()
{
    $name = isset($_POST["name"]) ? sanitize_text_field($_POST["name"]) : '';
    $color = isset($_POST["color"]) ? sanitize_hex_color($_POST["color"]) : '';

    $message = '';
    $success = null;

    // Insert data
    if (isset($_POST['insert'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'gmapradius_type';

        // Validate input before inserting
        if (empty($name)) {
            $message = "Name field cannot be empty.";
            $success = false;
        } elseif (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            $message = "Color must be in the format #RRGGBB (e.g., #993303).";
            $success = false;
        } else {
            $wpdb->insert(
                $table_name,
                array('name' => $name, 'color' => $color),
                array('%s', '%s')
            );
            $message = "Data inserted";
            $success = true;
        }
    }
?>
    <div class="wrap">
        <a href="<?php echo admin_url('admin.php?page=gmap_type_list'); ?>" class="btn btn-outline-secondary btn-sm float-end" style="margin-top:9px">Back</a>
        <div class="">
            <h1 class="wp-heading-inline">Add New Type</h1>
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
                        <th class="ss-th-width" class="form-label">Color</th>
                        <td>
                            <input type="color" name="color" value="<?php echo esc_attr($color); ?>" class="form-control form-control-color" title="Choose your color">
                        </td>
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
