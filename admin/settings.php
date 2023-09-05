<?php

function pingGoogleMapsAPI($api_key)
{
    // URL to the Google Maps API Geocoding Service (you can change this to any Google Maps API endpoint)
    $api_url = "https://maps.googleapis.com/maps/api/geocode/json?address=test&key=" . $api_key;

    // Send a GET request to the Google Maps API
    $response = wp_safe_remote_get($api_url);

    if (is_wp_error($response)) {
        return false; // Failed to connect or retrieve data
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body);

    if ($data && isset($data->status)) {
        if ($data->status === "OK" || $data->status === "ZERO_RESULTS") {
            return true; // API key is valid
        } elseif ($data->status === "REQUEST_DENIED" && isset($data->error_message)) {
            return false; // API key is invalid
        }
    }

    return false; // Unable to determine API key status
}

function gmapradius_settings()
{
    global $wpdb;
    $success = null;

    if (isset($_POST['update'])) {
        $api_key = sanitize_text_field($_POST["api"]);

        // Ping Google Maps API to check if the API key is valid
        $api_key_valid = pingGoogleMapsAPI($api_key);

        if ($api_key_valid) {
            // API key is valid, you can save it in the database
            $table_name = $wpdb->prefix . 'gmapradius_settings';
            $wpdb->update(
                $table_name,
                array('setting_value' => $api_key),
                array('setting_name' => 'GMAP_API_KEY'),
                array('%s'),
                array('%s')
            );
            $success = true;
            $message = 'API key updated successfully.';
        } else {
            // API key is invalid
            $message = 'Invalid API key. Please provide a valid Google Maps API Key.';
        }
    }
    $table_name = $wpdb->prefix . 'gmapradius_settings';
    $data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE setting_name = %s", 'GMAP_API_KEY'), ARRAY_A);
    $api_key = $data['setting_value'];

    if (empty($api_key)) {
        $success = false;
        $message = 'Please provide Google Map API Key';
    } else if (!pingGoogleMapsAPI($api_key)) {
        $success = false;
        $message = 'Invalid API key. Please provide a valid Google Maps API Key.';
    }

?>
    <div class="wrap">
        <div class="">
            <h1 class="wp-heading-inline">Settings</h1>
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
                        <th class="ss-th-width" class="form-label">Google Map API Key</th>
                        <td>
                            <div x-data="{ showPassword: false }" class="input-group">
                                <input x-bind:type="showPassword ? 'text' : 'password'" class="form-control" name="api" value="<?php echo esc_attr($api_key); ?>">
                                <div class="input-group-text">
                                    <a href="#" x-on:click="showPassword = !showPassword" class="showPassword">
                                        <span x-show="!showPassword" class="dashicons dashicons-hidden" style="color:#333"></span>
                                        <span x-show="showPassword" class="dashicons dashicons-visibility" style="color:#333"></span>
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                <div>
                </div>
                <div class="d-flex justify-content-end align-items-center">
                    <input type='submit' name="update" value='Update' class='btn btn-outline-primary ml-2'>
                </div>
            </form>
        </div>
    </div>
<?php
}
