<?php

function gmapradius_map()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'gmapradius_settings';
    $data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE setting_name = %s", 'GMAP_API_KEY'), ARRAY_A);
    $api_key = $data['setting_value'];
    $success = null;

    if (!pingGoogleMapsAPI($api_key)) {
        $success = false;
        $message = 'Invalid API key. Please provide a valid Google Maps API Key. <a href="'. admin_url('admin.php?page=gmapradius_settings').'">Here</a>';
    }
?>
    <script>
        var BASE_URL = "<?php echo home_url(); ?>";
        var API_KEY = "<?php echo $api_key; ?>"
    </script>
    <div class="wrap">
        <div class="mb-3">
            <h1 class="wp-heading-inline">Radius Map</h1>
            <!-- <p>Lorem ipsum dolor sit amet</p> -->
        </div>
        <!-- Display Message -->
        <?php if (!empty($message)) : ?>
            <div class="<?php echo ($success) ? 'updated' : 'error'; ?>">
                <p><?php echo wp_kses_post($message); ?></p>
            </div>
        <?php endif; ?>
        <div x-data="initialApp()" x-init="initMap(); $watch('selectedTypes', value => selectedTypesChange(value));">
            <div style="">
                <table class='wp-list-table widefat fixed' style="width: 100%; margin-bottom:20px;">
                    <template x-for="type in types">
                        <tr>
                            <td>
                                <div class="switch-container">
                                    <div class="switch-label" x-text="type.name"></div>
                                    <label class="switch">
                                        <input type="checkbox" x-model="selectedTypes" x-bind:value="type.id" x-bind:id=`type-${type.id}` x-bind:name="type.name" >
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    </template>
                </table>
            </div>
            <div id="map" class="mt-1"></div>
        </div>
    </div>
    <script>
        // Load Google Maps API
        (g => {
            var h, a, k, p = "The Google Maps JavaScript API",
                c = "google",
                l = "importLibrary",
                q = "__ib__",
                m = document,
                b = window;
            b = b[c] || (b[c] = {});
            var d = b.maps || (b.maps = {}),
                r = new Set,
                e = new URLSearchParams,
                u = () => h || (h = new Promise(async (f, n) => {
                    await (a = m.createElement("script"));
                    e.set("libraries", [...r] + "");
                    for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]);
                    e.set("callback", c + ".maps." + q);
                    a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
                    d[q] = f;
                    a.onerror = () => h = n(Error(p + " could not load."));
                    a.nonce = m.querySelector("script[nonce]")?.nonce || "";
                    m.head.append(a)
                }));
            d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n))
        })({
            key: API_KEY,
            v: "weekly"
        });
    </script>
<?php
}

?>