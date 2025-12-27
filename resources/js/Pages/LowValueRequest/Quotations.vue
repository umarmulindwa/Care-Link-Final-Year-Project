<template>
    <Head title="Requests for Quotations"></Head>
    <Layout>
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Requests for Quotations</h4>
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
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>RFQ #</th>
<!--                                            <th>TITLE</th>-->
                                            <th class="text-center">DEADLINE</th>
                                            <th class="text-center">STATUS</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="requests && requests.length > 0">
                                        <tr v-for="req in requests" :key="req.id">

                                            <td style="text-align: left;">
                                                <h6>
                                                <span v-if="!req.is_due">
                                                    <a href="" @click.prevent="sendQuotation(req.id)">
                                                        {{ req.reference_number }}
                                                    </a>
                                                </span>

                                                    <span v-else>{{ req.reference_number }}</span>
                                                </h6>
                                            </td>
<!--                                            <td>-->
<!--                                                <span role="button">-->
<!--                                                    <span v-html="formatSentence(req.requesters_message,35)"></span>-->
<!--                                                </span>-->
<!--                                            </td>-->
                                            <td class="text-center">
                                                <p>{{ formatDate(req.quotation_date,"DD/MMM/YYYY HH:mm") }}</p>
                                                <span class="text-success">
                                                    {{ req.quotation_time_ago }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <h6 class="text-muted" v-if="req.is_due">
                                                    <span class="text-danger">Closed</span>
                                                </h6>

                                                <h6 class="text-muted" v-else-if="!(req.sp_ids).includes(parseInt(session.id))">
                                                    <span class="text-primary">Open</span>
                                                </h6>

                                                <h6 class="text-muted" v-else>
                                                    <span>Open & Submitted</span>
                                                    <p class="text-success">(Editable)</p>
                                                </h6>
                                            </td>

                                            <td class="pt-2">

                                                <a v-if="!req.is_due" href="" @click.prevent="sendQuotation(req.id)">
                                                    <i title="Send Quotation" class="fas fa-paper-plane"></i>
                                                </a>

                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr class="text-center"><td colspan="5">{{ isfetching?'Please wait...':'No requests found'}}</td></tr>
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
            requests: null,
            isfetching: true,

            supplyUrl: import.meta.env.VITE_LOW_VALUE_PROCUREMENT_URL,
        };
    },
    mounted() {

        useStore().commit("LoggedInUser/storeCurrentUrl",'/low-value-procurement');

        let app = this;

        app.session = app.$attrs.auth.user;

        app.getRequests()

    },

    methods: {

        getRequests() {
            axios.get(`${this.supplyUrl}/api/sp-requests`, {
                params: { sp: this.session.id },
                headers: {
                    "Accept": "application/json",
                    "Authorization": `Bearer ${this.$attrs.auth.user.api_token}`
                }
            }).then((response) => {
                this.requests = response.data.requests;
                this.isfetching = false;
            })
            .catch((error) => {
                console.error("Error fetching requests:", error);
                this.isfetching = false;
            });
        },


        sendQuotation(id){
            router.get(route('low-value-procurement.show',{id:id}))
        },

        formatDate(date,format) {
            return moment(date).format(format || "DD/MMM/YYYY");
        },

        formatSentence(sentence,limit = 25) {
            if (sentence.length > limit) {
                return sentence.substr(0, limit) + "...";
            } else {
                return sentence
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
