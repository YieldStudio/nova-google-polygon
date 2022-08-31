<template>
    <div ref="map" class="nova-google-polygon-map"></div>
</template>

<style>
.nova-google-polygon-map {
    border-radius: 5px;
    overflow: hidden;
    height: 500px;
    width: 100%;
}

.nova-google-polygon-map a[href^="http://maps.google.com/maps"],
.nova-google-polygon-map a[href^="https://maps.google.com/maps"],
.nova-google-polygon-map a[href^="https://www.google.com/maps"],
.nova-google-polygon-map .gm-style-cc {
    display: none !important;
}
</style>

<script>
import loadGoogleMapsApi from 'load-google-maps-api';

export default {
    props: ['value', 'center', 'shapeOptions', 'readonly'],
    data: function() {
        return {
            ready: false,
            map: null,
            shape: null,
            drawingManager: null,
            localValue: [...this.value],
        };
    },
    methods: {
        watchShape() {
            const { google } = window;

            // Hide drawing manager
            this.drawingManager.setMap(null);

            this.shape.addListener('rightclick', e => {
                if (e.vertex != null) {
                    this.shape.getPath().removeAt(e.vertex);
                }

                if (this.shape.getPath().length === 0) {
                    // Destroy shape
                    this.shape.setMap(null);
                    this.shape = null;

                    // Show the drawing manager
                    this.drawingManager.setMap(this.map);
                }
            });

            this.shape.getPaths().forEach(path => {
                google.maps.event.addListener(path, 'insert_at', () => this.syncValue());
                google.maps.event.addListener(path, 'remove_at', () => this.syncValue());
                google.maps.event.addListener(path, 'set_at', () => this.syncValue());
            });
        },
        syncValue() {
            if (!this.shape) {
                this.updateValue([]);
                return;
            }

            const path = this.shape.getPath();
            const value = [];

            for (let i = 0; i < path.length; i++) {
                const data = {
                    lat: path.getAt(i).lat(),
                    lng: path.getAt(i).lng(),
                };

                value.push(data);
            }

            this.updateValue(value);
        },
        refreshMap() {
            const { google } = window;

            if (this.shape) {
                // Destroy shape
                this.shape.setMap(null);
                this.shape = null;
            }

            // Hide or show the drawing manager
            if (!this.readonly) {
                this.drawingManager.setMap(this.value.length === 0 ? this.map : null);
            }

            if (this.value.length !== 0) {
                const bounds = new google.maps.LatLngBounds();

                for (let i = 0; i < this.value.length; i++) {
                    const BoundLatLng = new google.maps.LatLng({
                        lat: parseFloat(this.value[i].lat),
                        lng: parseFloat(this.value[i].lng),
                    });

                    bounds.extend(BoundLatLng);
                }

                this.shape = new google.maps.Polygon({
                    paths: this.value,
                    map: this.map,
                    ...this.shapeOptions,
                });

                this.map.fitBounds(bounds);

                this.watchShape();
            } else {
                this.map.setCenter(new google.maps.LatLng(this.center));
                this.map.setZoom(12);
            }
        },
        updateValue(value) {
            this.localValue = value;
            this.$emit('change', value);
        },
    },
    watch: {
        value(newValue) {
            if (!this.ready) {
                return;
            }

            if (JSON.stringify(newValue) !== JSON.stringify(this.localValue)) {
                this.localValue = newValue;
                this.refreshMap();
            }
        },
    },
    async mounted() {
        await loadGoogleMapsApi({
            key: Nova.config('googlePolygon').key,
            libraries: ['places', 'drawing', 'geometry'],
        });

        const { google } = window;

        this.map = new google.maps.Map(this.$refs.map, {
            zoom: 12,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            center: new google.maps.LatLng(this.center),
            mapTypeControl: false,
            streetViewControl: false,
            clickableIcons: false,
        });

        this.drawingManager = new google.maps.drawing.DrawingManager({
            drawingMode: null,
            drawingControl: true,
            drawingControlOptions: {
                position: google.maps.ControlPosition.TOP_LEFT,
                drawingModes: [google.maps.drawing.OverlayType.POLYGON],
            },
            polygonOptions: this.shapeOptions,
        });

        google.maps.event.addListener(this.drawingManager, 'overlaycomplete', event => {
            this.shape = event.overlay;
            this.watchShape();
            this.syncValue();
        });

        this.refreshMap();

        this.ready = true;
    },
};
</script>
