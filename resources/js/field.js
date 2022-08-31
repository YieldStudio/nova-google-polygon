import DetailField from './components/DetailField';
import FormField from './components/FormField';

Nova.booting(app => {
    app.component('detail-google-polygon', DetailField);
    app.component('form-google-polygon', FormField);
});
