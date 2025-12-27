<template>
    <Head title="Vehicle Inspection"></Head>
    <Layout>
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Vehicle Hire Requests, {{ formatDisplayDate(month) }}</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-4">
                                    <form class="app-search d-none d-lg-block" style="margin-top: -10px;">
                                        <div class="position-relative">
                                            <input type="text" v-model="search" class="form-control w-100" :placeholder="$t('navbar.search.text')" />
                                            <!--                                            <span-->
                                            <!--                                                class="spinner spinner-border spinner-border-sm"-->
                                            <!--                                                style="margin-top:0.7rem;" >-->
                                            <!--                                            </span>-->
                                            <span class="bx bx-search-alt"></span>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-8 text-end">
                                    <button class="btn btn-primary" @click.prevent="goTo('vehicle-inspection.create')">Submit Vehicle For Inspection</button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                    <tr>
                                        <th>STAFF</th>
                                        <th> TYPE OF VEHICLE </th>
                                        <th> ADDITIONAL INSTRUCTIONS </th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="trips && trips.length > 0">
                                        <tr v-for="(item,index) in trips" :key="index">
                                            <template v-if="LicensePlates.includes(item.vehicle_hire_request.vehicle.license_plate)">
                                                <td style="max-width: 30%">

                                                    <a href="" @click.prevent="goTo('vehicle-hire.create',{trip_id:item.id,vhr_id:item.vehicle_hire_request.id,vehicle:item.vehicle_hire_request.vehicle.id})">{{ item.user.name }}</a><br />
                                                    <small> <span class="text-gray-600"><b>Position</b>:</span>
                                                        {{ item.user.profile.position_text }}</small><br>
                                                    <small> <span class="text-gray-600"><b>Section</b>:</span>
                                                        {{ item.user.section.name }}</small><br />
                                                    <small> <span class="text-gray-600"><b>Departure Time</b>:</span>
                                                        {{ item.departure_time }} on
                                                        {{ formatDisplayDate(item.departure_date) }}</small><br />
                                                    <small> <span class="text-gray-600"><b>Destination</b>:</span>
                                                        {{ item.destination_and_distance }}</small><br />
                                                </td>
                                                <td>
                                                    {{ item.vehicle_hire_request.vehicle.name }}
                                                    ({{ item.vehicle_hire_request.vehicle.license_plate }})
                                                    <input type="hidden" name="vehicles[]" :value="item.vehicle_hire_request.vehicle.id" />
                                                </td>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <input type="hidden" name="trip_id[]" :value="item.id">
                                                            <span class="text-break">{{ item.sp_request.instructions }}</span>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <template v-if="!item.offer_submitted">
                                                                <span class="badge badge-soft-primary">pending offer <br>submission</span>
                                                            </template>
                                                            <template v-else>
                                                                <span class="badge badge-soft-success">offer submitted</span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </td>
                                            </template>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr class="text-center"><td colspan="4">No requests found</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>


<script>

import moment from "moment";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import PageHeader from "@/Components/page-header.vue";
import Layout from '@/Layouts/verticalmain.vue';
import { RequestHelper } from "@/mixins/helpers";
import { notify } from "@/mixins/notify";
import Swal from "sweetalert2";
import {useStore} from "vuex";
import axios from "axios";

export default {
    name: "List",
    mixins:[RequestHelper,notify],
    components:{
        Layout,
        PageHeader,
        Head,
    },
    props: ['request_id','month'],
    data() {
        return {

            trips: null,
            spVehicles: null,
            spDrivers: null,


            form_error: false,

            session: null,

            travel_planner_url: import.meta.env.VITE_ADMIN_TRAVEL_PLANNER_URL,
        };
    },
    mounted() {

        useStore().commit("LoggedInUser/storeCurrentUrl",'/vehicle-inspection');

        this.session = this.$attrs.auth.user;

        this.getRequestDetails();
    },

    computed:{
        LicensePlates(){
            if(this.spVehicles && this.spVehicles.length > 0){
                const licensePlates = this.spVehicles.map(vehicle => vehicle.license_plate);
                return Array.from(licensePlates)
            }else{
                return [];
            }
        }
    },

    methods: {

        getRequestDetails(){
            axios.get(`${this.travel_planner_url}/api/sp/vehicle-hire/request`, {
                params: { sp: this.session.id, request_id:this.request_id },
                headers: {
                    "Accept": "application/json",
                    "Authorization": `Bearer ${this.$attrs.auth.user.api_token}`
                }
            }).then((response) => {
                this.trips = response.data.trips;
                this.spVehicles = response.data.spVehicles;
                this.spDrivers = response.data.spDrivers;
                this.isfetching = false;
            }).catch((error) => {
                console.error("Error fetching requests:", error);
                this.isfetching = false;
            });
        },

        goTo(routeName, id){
            if(id){
                router.get(route(routeName,id));
            }else{
                router.get(route(routeName));
            }
        },

        formatDisplayDate(date){
            return moment(date).format("YYYY,MMM")
        },
    },
};
</script>

<style scoped>
td{
    background:#FFFFFF !important;
}
</style>
