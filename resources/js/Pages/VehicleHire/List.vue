<template>
    <Head title="Vehicle Hire"></Head>
    <Layout>
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Vehicle Hire Requests</h4>
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
                                        <th style="text-align: left">REQUEST #</th>
                                        <th style="text-align: left;">SUMMARY</th>
                                        <th style="text-align: left;">STATUS</th>
                                        <th style="text-align: left">DEADLINE</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="monthsData && monthsData.length > 0">
                                    <tr v-for="(request,index) in monthsData" :key="index">
                                        <td>
                                            <a href="" @click.prevent="goTo('vehicle-hire.show',{id:request.id, month: formatUrlDate(request.month)})">
                                                {{ formatDisplayDate(request.month) }}
                                            </a> <br />
<!--                                            {{ request.hire_id }}-->
                                        </td>
                                        <td>
                                            <p>
                                                <small class="text-gray-600"> {{ request.staff }} Requests</small>
                                                <br>
                                                <small class="text-gray-600"> {{ request.staff }} Staff</small>
                                                <br />
                                                <small class="text-gray-600"> {{ request.sections }} Section(s)</small>
                                            </p>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-success" v-if="request.staff === request.offer_submitted_count.length">
                                                submitted
                                            </span>
                                            <span v-else class="badge badge-soft-primary">
                                                pending
                                            </span>
                                        </td>
                                        <td>
                                            {{ request.deadline }}
                                        </td>
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

    data() {
        return {
            monthsData: null,
            session: null,

            isfetching: false,

            travel_planner_url: import.meta.env.VITE_ADMIN_TRAVEL_PLANNER_URL,

        };
    },
    mounted() {

        useStore().commit("LoggedInUser/storeCurrentUrl",'/vehicle-inspection');

        this.session = this.$attrs.auth.user;

        this.getVehicleHireRequest()

    },

    methods: {

        getVehicleHireRequest() {
            axios.get(`${this.travel_planner_url}/api/sp/vehicle-hire/list`, {
                params: { sp: this.session.id },
                headers: {
                    "Accept": "application/json",
                    "Authorization": `Bearer ${this.$attrs.auth.user.api_token}`
                }
            }).then((response) => {
                this.monthsData = response.data.monthsData;
                this.isfetching = false;
            }).catch((error) => {
                    console.error("Error fetching requests:", error);
                    this.isfetching = false;
                });
        },

        formatDate(date) {
            return moment(date).format("DD/MMM/YYYY");
        },

        formatUrlDate(date){
            return moment(date).format('YYYY-MM');
        },

        formatDisplayDate(date){
            return moment(date).format("YYYY,MMM")
        },

        goTo(routeName, id){
            if(id){
                router.get(route(routeName,id));
            }else{
                router.get(route(routeName));
            }
        },
    },
};
</script>

<style scoped>
td{
    background:#FFFFFF !important;
}
</style>
