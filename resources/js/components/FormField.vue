<template>
    <DefaultField :field="field" :errors="errors" :show-help-text="showHelpText">
        <template #field>
            <Map
                :center="center"
                :shape-options="shapeOptions"
                :readonly="isReadonly"
                :value="value"
                @change="handleChange"
            />
        </template>
    </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova';
import Map from './Map';

export default {
    components: { Map },
    mixins: [FormField, HandlesValidationErrors],
    props: ['field', 'showHelpText'],
    data: () => ({
        value: [],
    }),
    computed: {
        center() {
            return {
                lat: parseFloat(Nova.config('googlePolygon').center.lat),
                lng: parseFloat(Nova.config('googlePolygon').center.lng),
            };
        },
        shapeOptions() {
            return {
                clickable: true,
                draggable: false,
                editable: true,
                strokeColor: '#7f1d1d',
                strokeWeight: 3,
                fillColor: '#fca5a5',
                fillOpacity: 0.6,
                zIndex: 99999,
            };
        },
    },
    methods: {
        fill(formData) {
            this.fillIfVisible(formData, this.field.attribute, JSON.stringify(this.value));
        },
        setInitialValue() {
            this.value = this.field.value || [];
        },
        handleChange(value) {
            this.value = value;

            if (this.field) {
                this.emitFieldValueChange(this.field.attribute, this.value);
            }
        },
    },
};
</script>
