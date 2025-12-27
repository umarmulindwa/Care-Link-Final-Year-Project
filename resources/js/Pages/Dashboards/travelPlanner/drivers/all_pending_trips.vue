<template>
    <div class="row">
        <div class="col-md-12 text-left">
            <h5>Travel Planner</h5>
            <h6 class="text-muted">Pending trips this month, {{ formatDate(period) }}</h6>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="mb-4 mt-4" v-if="trips.length > 0">
                <full-calendar :options="calendarOptions"/>
            </div>
            <div v-if="trips.length === 0">
                <div class="row mt-5 mb-5">
                    <div class="col-md-12 text-center" v-if="!fetching">
                        <img src="/images/icons/icon.no-data.png" alt="" style="width:100px;">
                        <p>No trips this month</p>
                    </div>
                    <div class="col-md-12 text-center" v-else>
                        <span class="spinner spinner-border"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

import moment from "moment";
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import axios from "axios";
import {usePage} from "@inertiajs/vue3";

export default {
    name:'driver-trips-pending',
    components: {
        FullCalendar,
    },
    computed: {
        moment() {
            return moment
        }
    },
    data(){
        return {
            period: moment(),
            fetching:true,
            calendarOptions: {
                plugins: [ dayGridPlugin, interactionPlugin ],
                initialView: 'dayGridMonth',
                height: '500px',
                headerToolbar: {
                    start: '',
                    center: '',
                    end: ''
                },
                titleFormat: '',
                events: [],
            },
            trips:[],
            url: import.meta.env.VITE_ADMIN_TRAVEL_PLANNER_URL,
        }
    },
    mounted(){
        this.fetchTrips()
        this.update();
    },
    methods:{
        formatDate(date) {
            return date.format('MMMM,YYYY');
        },
        fetchTrips(){
            axios.get(`${this.url}/api/pending-trips/${this.period.format('DD-MM-YYYY')}`, {
                headers: {
                    "Accept": "application/json",
                    "Authorization": `Bearer ${usePage().props.auth.user.api_token}`
                }
            }).then((response) => {
                this.trips = response.data.trips;
                this.fetching = false;
            }).catch((e) => {
                console.log('error',e);
            })
        },
        update(){
            // Interval set to 5 minutes
            setInterval(() => {
                this.fetchTrips();
            }, 300000);
        }
    },
    watch:{
        trips:function(newValue){
            if(newValue.length > 0){
                this.calendarOptions.events = newValue.map((trip,index) => {
                    return {
                        "id": index + 1,
                        "title": `${trip.driver.name} (${trip.trip.departure_time})`,
                        "start": trip.start_date,
                        "end": trip.end_date,
                        "backgroundColor": moment().isAfter(trip.end_date) ? "#28a745":"#17a2b8",
                    }
                })
            }
        }
    }
}
</script>
