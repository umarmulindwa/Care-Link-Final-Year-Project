<template>
    <Head title="Vehicle Inspection"></Head>
    <Layout>
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Vehicle Inspection</h4>
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
                                        <th>#</th>
                                        <th> VEHICLE, TYPE</th>
                                        <th> STATUS</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="inspections && inspections.length > 0">
                                    <tr v-for="(item,index) in inspections" :key="index">
                                        <td style="max-width: 30%">
                                            <small> <span class="text-gray-600">{{ item.inspection_code }}</span> </small>
                                        </td>

                                        <td>
                                            <a href="#INSPTimeline{{ item.id }}"
                                               data-toggle="modal">{{ item.vehicle.name }}</a><br>
                                            <small>
                                                <span class="text-gray-600">
                                                    <b>Inspection Tentative Schedule: </b>
                                                    {{ formatDate(item.inspection_date) }}
                                                </span>
                                            </small>
                                        </td>
                                        <td>
                                            {{ item.last_status_change }}<br>
                                            <small>
                                                <span class="text-gray-600">
                                                    {{ item.last_status_change_at }}
                                                </span>
                                            </small>
                                        </td>
                                        <td>
                                            <template v-if="item.vehicle_inspection_status === 'accepted'">
                                                <span class="badge badge-soft-success">Vehicle Accepted</span>
                                            </template>
                                           <template v-else>
                                                <button class="btn btn-soft-primary" style="margin-right:1rem;">
                                                    <span class="bx bx-pencil"></span>
                                                </button>
                                               <button class="btn btn-soft-danger">
                                                   <span class="bx bx-trash"></span>
                                               </button>
                                           </template>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <tbody v-else>
                                    <tr class="text-center"><td colspan="5">No requests found</td></tr>
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
            session: null,
            inspections: null,

            isfetching: false,

            travel_planner_url: import.meta.env.VITE_ADMIN_TRAVEL_PLANNER_URL,

        };
    },
    mounted() {

        useStore().commit("LoggedInUser/storeCurrentUrl",'/vehicle-inspection');

        this.session = this.$attrs.auth.user;

        this.getInspections()

    },

    methods: {

        getInspections() {
            axios.get(`${this.travel_planner_url}/api/sp/vehicle-inspection/list`, {
                params: { sp: this.session.id },
                headers: {
                    "Accept": "application/json",
                    "Authorization": `Bearer ${this.$attrs.auth.user.api_token}`
                }
            }).then((response) => {
                this.inspections = response.data.inspections;
                this.isfetching = false;
            }).catch((error) => {
                    console.error("Error fetching requests:", error);
                    this.isfetching = false;
                });
        },

        formatDate(date) {
            return moment(date).format("DD/MMM/YYYY");
        },

        goTo(routeName, id){
            if(id){
                router.get(route(routeName,{id:id}));
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
