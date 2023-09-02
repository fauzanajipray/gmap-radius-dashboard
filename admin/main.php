<?php

function gmapradius_map()
{
?>
    <div class="wrap">
        <h1 class="wp-heading-inline">GMap Radius Map</h1>

        <div x-data="initialApp()" x-init="initMap(); $watch('selectedTypes', value => console.log(value)); $watch('showRadius', value => toggleShowRadius(value))">
            <div>
                <!-- The buttons to filter type -->
                <template x-for="type in ['salesUnit', 'service', 'spareParts']">
                    <div>
                        <input type="checkbox" x-model="selectedTypes" x-bind:value="type" x-bind:name="type">
                        <span x-text="type" x-bind:for="type"></span>
                    </div>
                </template>
                <!-- The button to dhow hide radius -->
                <label>
                    <input type="checkbox" x-model="showRadius">
                    Show Radius
                </label>
                <button></button>
            </div>
            <div id="map" class="mt-5"></div>
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
            key: "AIzaSyBUcar4Z79lBhn8tcU9QdJNqOAUHuZOSPo",
            v: "weekly"
        });
    </script>
    <!-- <script>
    function initialApp() {
    return {
        selectedTypes: ["salesUnit", "service", "spareParts"],
        showRadius: true,
        map: null,
        markers: [],
        async initMap() {
            const {
                Map
            } = await google.maps.importLibrary("maps");
            const dataJson = this.getDataJson();
            let centerOption = this.calculateCenter(dataJson);
            this.map = new Map(document.getElementById("map"), {
                center: centerOption,
                zoom: 6,
            });
            dataJson.forEach((location) => {
                this.addMarker({
                    lat: location.lat,
                    lng: location.lng
                });
                this.addCircle(location);
            });
        },
        addMarker(position) {
            const marker = new google.maps.Marker({
                position: position,
                map: this.map,
                title: location.name,
            });
            this.markers.push(marker);
        },
        addCircle(location, type) {
            let radius = new google.maps.Circle({
                strokeColor: this.getRadiusColor(location.type),
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: this.getRadiusColor(location.type),
                fillOpacity: 0.35,
                map: this.map,
                center: {
                    lat: location.lat,
                    lng: location.lng
                },
                radius: parseInt(location.radius) * 1000, // Convert to meters
            });
        },
        calculateCenter: (coordinates) => {
            if (coordinates.length === 0) {
                return null;
            }
            let totalLat = 0;
            let totalLng = 0;
            for (const coord of coordinates) {
                totalLat += coord.lat;
                totalLng += coord.lng || coord.lng; // Assuming your longitude key can be 'lng'
            }
            const avgLat = totalLat / coordinates.length;
            const avgLng = totalLng / coordinates.length;
            return {
                lat: avgLat,
                lng: avgLng
            };
        },
        getRadiusColor(type) {
            switch (type) {
                case "salesUnit":
                    return "#FF0000"; // Red
                case "service":
                    return "#00FF00"; // Green
                case "spareParts":
                    return "#0000FF"; // Blue
                default:
                    return "#000000"; // Black (fallback)
            }
        },
        setMapOnAll(map) {
            for (let i = 0; i < this.markers.length; i++) {
                console.log(this.markers[i]);
                this.markers[i].setMap(map);
            }
        },
        hideMarkers() {
            this.setMapOnAll(null);
        },
        showMarkers() {
            this.setMapOnAll(map);
        },
        toggleShowRadius(val) {
            if (val) {
                this.showMarkers();
            } else {
                this.hideMarkers();
            }
        },
        getDataJson() {
            // for development purpose
            var data = initCordinate();
            return initCordinate();
        },
    };
}

/**
 *   For Test
 */
function generateRandomCoordinates() {
    const minLat = -11.0;
    const maxLat = 5.0;
    const minLng = 95.0;
    const maxLng = 141.0;

    const randomLat = Math.random() * (maxLat - minLat) + minLat;
    const randomLng = Math.random() * (maxLng - minLng) + minLng;

    return {
        lat: randomLat,
        lng: randomLng
    };
}

function initCordinate() {
    let dataJson = [];
    for (let i = 1; i <= 50; i++) {
        const coordinates = generateRandomCoordinates();
        let type = "";
        if (i % 3 === 0) {
            type = "salesUnit";
        } else if (i % 3 === 1) {
            type = "spareParts";
        } else {
            type = "service";
        }

        dataJson.push({
            type: type,
            radius: Math.floor(Math.random() * 30) + 1,
            name: `${type.charAt(0).toUpperCase() + type.slice(1)} ${i}`,
            lat: coordinates.lat,
            lng: coordinates.lng,
        });
    }

    return dataJson;
}
</script> -->

<?php
}

?>