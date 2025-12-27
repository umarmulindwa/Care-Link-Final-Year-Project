<template>
    <div class="row">
        <div class="col-md-12">
            <h5>Travel Planner</h5>
            <h6 class="text-muted">Driver trips as of tomorrow - {{ formatDate(period) }}</h6>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12" style="text-align:left;">
            <div class="mb-4 mt-4" v-for="(driver,index) in drivers" :key="index">
                <h6 style="text-transform: uppercase;text-align: center"><b>{{ driver.driver?.name }}</b></h6>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th style="width: 20%;">Trip</th>
                        <th style="width: 10%;">Vehicle</th>
                        <th style="width: 30%;">Destination</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 25%;">Travellers</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(trip,index) in driver.trips" :key="index">
                            <td>{{ trip.trip.ref_code }}<br>
                            </td>
                            <td>{{ trip.vehicle?.license_plate }}</td>
                            <td>{{ trip.trip.destination_and_distance }}</td>
                            <td>
                                Departs at {{ trip.trip.departure_time }}
                            </td>
                            <td>{{ trip.trip.user?.name }}, {{ trip.trip.other_staff_list }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="drivers.length === 0">
                <div class="row mt-5 mb-5">
                    <div class="col-md-12 text-center" v-if="!fetching">
                        <img src="/images/icons/icon.no-data.png" alt="" style="width:100px;">
                        <p>No trips tomorrow</p>
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
import axios from "axios";
import {usePage} from "@inertiajs/vue3";

export default {
    name:'driver-trips-tomorrow',
    computed: {
        moment() {
            return moment
        }
    },
    data(){
        return {
            period: moment().add(1, 'day'),
            drivers:[],
            fetching: true,
            url: import.meta.env.VITE_ADMIN_TRAVEL_PLANNER_URL,
        }
    },
    mounted(){
        this.fetchTrips()
        this.update();
    },
    methods:{
        formatDate(date) {
            return date.format('dddd,DD MMMM,YYYY');
        },
        fetchTrips(){
            axios.get(`${this.url}/api/driver-trips/${this.period.format('DD-MM-YYYY')}`, {
                headers: {
                    "Accept": "application/json",
                    "Authorization": `Bearer ${usePage().props.auth.user.api_token}`
                }
            }).then((response) => {
                this.drivers = response.data.drivers;
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
    }
}
</script>


<style scoped>
td{
    background:#FFFFFF !important;
}
</style>
