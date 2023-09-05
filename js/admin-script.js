function initialApp() {
  let circles = [];
  let markers = [];
  let currentInfoWindow = null;

  return {
    types: [],
    selectedTypes: [],
    locations: [],
    tempLocation: [],
    map: null,
    showRadius: false,
    tempLocation: [],
    isLoading: false,
    async initMap() {
      const { Map } = await google.maps.importLibrary("maps");
      this.types = await this.fetchTypes();
      this.map = new Map(document.getElementById("map"), {
        center: { lat: -0.789275, lng: 113.921327 },
        zoom: 6,
        mapTypeId: "satellite",
        mapTypeControlOptions: {
          style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
          position: google.maps.ControlPosition.TOP_RIGHT,
        },
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

      const avgLat = totalLat / coordinates.length;
      const avgLng = totalLng / coordinates.length;

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

      markers.forEach((marker) => {
        google.maps.event.clearListeners(marker, "click");
        marker.setMap(null);
      });
      markers = [];
      circles.forEach((circle) => {
        google.maps.event.clearListeners(circle, "click");
        circle.setMap(null);
      });
      circles = []; // Clear the array completely

      this.locations = await this.fetchLocations(types_id);
      let centerOption = this.calculateCenter(this.locations);
      this.map.setCenter(centerOption);
      this.map.setZoom(6);

      if (currentInfoWindow) {
        currentInfoWindow.close();
      }

      // Add new circles for the updated locations
      this.locations.forEach((location) => {
        this.addCircle(location);
        this.addMarker(location);
      });

      this.isLoading = false;
    },
    addMarker(location) {
      const lat = parseFloat(location.lat);
      const lng = parseFloat(location.lng);
      const marker = new google.maps.Marker({
        position: { lat: lat, lng: lng },
        map: this.map,
        title: location.name,
      });
      const infoWindow = new google.maps.InfoWindow({
        content: `${location.name}`,
      });
      marker.addListener("click", () => {
        if (currentInfoWindow) {
          currentInfoWindow.close();
        }
        this.map.setZoom(8.5);
        this.map.setCenter(marker.getPosition());
        infoWindow.setPosition(marker);
        infoWindow.open(this.map, marker);
        currentInfoWindow = infoWindow;
      });
      markers.push(marker);
    },
    addCircle(location) {
      const lat = parseFloat(location.lat);
      const lng = parseFloat(location.lng);
      const color = this.getRadiusColor(location.type_id);
      let circle = new google.maps.Circle({
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
      const infoWindow = new google.maps.InfoWindow({
        content: `${location.name}`,
      });
      circle.addListener("click", () => {
        if (currentInfoWindow) {
          currentInfoWindow.close();
        }
        this.map.setZoom(8.5);
        this.map.setCenter(circle.getCenter());
        infoWindow.setPosition(circle.getCenter());
        infoWindow.open(this.map);
        currentInfoWindow = infoWindow;
      });
      circles.push(circle);
    },
    getRadiusColor(id) {
      const type = this.types.find((e) => id === e.id);
      return type ? type.color : "#000000";
    },
  };
}
