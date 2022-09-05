import './bootstrap';


import {createApp} from 'vue'

import App from './App.vue'

import { createStore } from 'vuex'
import storeData from "./store/index"

const store = createStore(storeData)
const app = createApp(App)


app.use(store)



// app.component('jl-datatable', JlDatatable)

app.mount('#app', store)
