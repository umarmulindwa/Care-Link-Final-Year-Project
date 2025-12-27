<template>
    <b-tabs pills>
        <b-tab active title="Equipment Request">
            <div class="row mt-3">
                <div class="col-md-12 mt-2">
                    <template v-if="equipment_data.equipment_request && equipment_data.equipment_request.length > 0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width:2%;">#</th>
                                    <th>ITEM</th>
                                    <th>CONFIRMED</th>
                                    <th>IN POSSESSION SINCE</th>
                                    <th>EXPIRY</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item,item_index) in equipment_data.equipment_request" :key="item_index">
                                <td><b>{{ item_index + 1 }}</b></td>
                                <td>
                                    {{ item.asset_item.description }}<br>
                                    <span class="text-muted">
                                        <b>Tag Number:</b> {{ item.asset_item.tag_number }}<br>
                                        <b>Serial Number:</b> {{ item.asset_item.serial_number }}<br>
                                        <b>AMR Number:</b> {{ item.asset_item.amr_number }}<br>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-success" v-if="item.receipt_confirmed">Yes</span>
                                    <span class="badge badge-soft-warning" v-else>No</span>
                                </td>
                                <td>{{ formatDate(item.updated_at) }}</td>
                                <td>{{ formatDate(item.asset_item.expiry_date,'DD/MMM/YYYY') }}</td>
                                <td>{{ item.asset_item.asset_current_journey.status }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </template>
                    <template v-else>
                        <div class="row mt-5 mb-5">
                            <div class="col-md-12 text-center" v-if="!fetching">
                                <img src="/images/icons/icon.no-data.png" alt="" style="width:100px;">
                                <p>No equipment given</p>
                            </div>
                            <div class="col-md-12 text-center" v-else>
                                <span class="spinner spinner-border"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </b-tab>
        <b-tab title="Other Places">
            <div class="row mt-3">
                <div class="col-md-12 mt-2">
                    <template v-if="equipment_data.other_systems && equipment_data.other_systems.length > 0">
                        <table class="table table-striped mb-0">
                            <thead>
                            <tr>
                                <th style="width:2%;">#</th>
                                <th>ITEM</th>
                                <th>EXPIRY</th>
                                <th>STATUS</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item,item_index) in equipment_data.other_systems" :key="item_index">
                                <td><b>{{ item_index + 1 }}</b></td>
                                <td>
                                    {{ item.description }}<br>
                                    <span class="text-muted">
                                        <b>Tag Number:</b> {{ item.tag_number }}<br>
                                        <b>Serial Number:</b> {{ item.serial_number }}<br>
                                        <b>AMR Number:</b> {{ item.amr_number }}<br>
                                    </span>
                                </td>
                                <td>{{ formatDate(item.expiry_date,'DD/MMM/YYYY') }}</td>
                                <td>{{ item.asset_current_journey.status }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </template>
                    <template v-else>
                        <div class="row mt-5 mb-5">
                            <div class="col-md-12 text-center" v-if="!fetching">
                                <img src="/images/icons/icon.no-data.png" alt="" style="width:100px;">
                                <p>No equipment given</p>
                            </div>
                            <div class="col-md-12 text-center" v-else>
                                <span class="spinner spinner-border"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </b-tab>
    </b-tabs>

</template>

<script>
import {usePage} from "@inertiajs/vue3";
import axios from "axios";
import moment from "moment";

export default {
    props:['user'],
    data(){
        return {
            url : `${import.meta.env.VITE_ADMIN_ASSET_EQUIPMENT_REQUEST_URL}/api/my-items`,
            user: usePage().props.auth.user,
            equipment_data: {
                equipment_request:null,
                other_systems:null,
            },
            fetching:true,
        }
    },
    mounted() {
        this.getEquipment();
    },
    methods:{
        getEquipment(){
            const options = {
                headers: {
                    "Content-Type": "multipart/form-data",
                    Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
                },
            };

            axios.get(`${this.url}/${this.user.email}`, options)
                .then((response) => {
                    this.equipment_data.other_systems = response.data.other_systems;
                    this.equipment_data.equipment_request = response.data.equipment_request;

                    this.fetching = false;
                })
                .catch((err) => {
                    console.log("this is the error", err);
                });
        },
        formatDate(dateString,format = null){
            return moment(dateString).format(format || 'DD/MMM/YYYY HH:mm:ss')
        }
    },
    watch:{
        user:function(){
            this.getEquipment();
        }
    }
}
</script>

<style scoped>
td{
    background:#FFFFFF !important;
}
</style>
