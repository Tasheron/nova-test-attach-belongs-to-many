import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-attach-belongs-to-many', IndexField)
  app.component('detail-attach-belongs-to-many', DetailField)
  app.component('form-attach-belongs-to-many', FormField)
})

