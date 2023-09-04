function initialApp() {
  return {
    types: [],
    selectedTypes: [],
    locations: [],
    tempLocation: [],
    map: null,
    markers: [],
    circles: [],
    showRadius: true,
    tempLocation: [],
    isLoading: false,
    async initMap() {
      const { Map } = await google.maps.importLibrary("maps");
      this.types = await this.fetchTypes();
      this.map = new Map(document.getElementById("map"), {
        center: { lat: -0.789275, lng: 113.921327 },
        zoom: 6,
      });
      this.types.forEach((e) => this.selectedTypes.push(e.id));
    },
    calculateCenter: (coordinates) => {
      if (coordinates.length === 0) {
        return null;
      }
      let totalLat = 0.0;
      let totalLng = 0.0;
      for (const coord of coordinates) {
        const lat = parseFloat(coord.lat);
        const lng = parseFloat(coord.lng);
        if (!isNaN(lat)) {
          totalLat += lat;
        }
        if (!isNaN(lng)) {
          totalLng += lng;
        }
      }

      const avgLat = (totalLat / coordinates.length);
      const avgLng = (totalLng / coordinates.length);

      return {
        lat: parseFloat(avgLat),
        lng: parseFloat(avgLng),
      };
    },
    fetchTypes: async () => {
      try {
        const response = await fetch(
          `${BASE_URL}/wp-json/api/gmapradius/v1/types/`
        );
        const data = await response.json();
        return data;
      } catch (error) {
        console.error("Error:", error);
      }
    },
    fetchLocations: async (types) => {
      try {
        var url = `${BASE_URL}/wp-json/api/gmapradius/v1/locations`;
        if (types) {
          url += `?types=${JSON.stringify(types)}`;
        }
        const response = await fetch(url);
        const data = await response.json();
        return data;
      } catch (error) {
        console.error("Error:", error);
      }
    },
    async selectedTypesChange(types_id) {
      this.isLoading = true;
      this.locations = await this.fetchLocations(types_id);
      // remove circle
      this.circles.forEach((circle) => {
        google.maps.event.clearListeners(circle, "click_handler_name");
        google.maps.event.clearListeners(circle, "drag_handler_name");
        circle.setRadius(0);
        circle.setMap(null);
      });
      let centerOption = this.calculateCenter(this.locations);
      this.map.setCenter(centerOption);
      this.map.setZoom(6);
      this.locations.forEach((location) => {
        const lat = parseFloat(location.lat);
        const lng = parseFloat(location.lng);
        // this.addMarker({ lat: lat, lng: lng });
        this.addCircle(location);
      });
      this.isLoading = false;
    },
    addMarker(position) {
      const marker = new google.maps.Marker({
        position: position,
        map: this.map,
        title: location.name,
      });
      this.markers.push(marker);
    },
    addCircle(location) {
      const lat = parseFloat(location.lat);
      const lng = parseFloat(location.lng);
      const color = this.getRadiusColor(location.type_id);
      let radius = new google.maps.Circle({
        strokeColor: color,
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: color,
        fillOpacity: 0.35,
        map: this.map,
        center: {
          lat: lat,
          lng: lng,
        },
        radius: parseInt(location.radius) * 1000, // Convert to meters
      });
      radius.addListener('click', () => {
        // Zoom to level 8 when the circle is clicked
        this.map.setZoom(8);
        // Optionally, you can also center the map on the clicked circle's location
        this.map.setCenter(radius.getCenter());
      });
      this.circles.push(radius);
    },
    getRadiusColor(id) {
      const type = this.types.find((e) => id === e.id);
      return type ? type.color : "#000000";
    },
    setMapOnAll(map) {
      for (let i = 0; i < this.markers.length; i++) {
        // console.log(this.markers[i]);
        this.markers[i].setMap(map);
      }
    },
    hideMarkers() {
      this.setMapOnAll(null);
    },
    showMarkers() {
      this.setMapOnAll(map);
    },
  };
}