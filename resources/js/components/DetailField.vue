<template>
    <PanelItem :index="index" :field="field">
        <template #value>
            <Map :center="center" :shape-options="shapeOptions" :readonly="true" :value="value" />
        </template>
    </PanelItem>
</template>

<script>
import Map from './Map';

export default {
    components: { Map },
    props: ['index', 'field'],
    computed: {
        value() {
            return this.field.value || [];
        },
        center() {
            return {
                lat: parseFloat(Nova.config('googlePolygon').center.lat),
                lng: parseFloat(Nova.config('googlePolygon').center.lng),
            };
        },
        shapeOptions() {
            return {
                clickable: false,
                draggable: false,
                editable: false,
                strokeColor: '#7f1d1d',
                strokeWeight: 3,
                fillColor: '#fca5a5',
                fillOpacity: 0.6,
                zIndex: 99999,
            };
        },
    },
};
</script>
