<script setup>
import moment from "moment";
import { usePage } from "@inertiajs/vue3";
import { useStore } from "vuex";

import { computed } from "vue";

const props = defineProps({
    requestDetails: {
        type: Object,
        required: true,
    },
    iWillSign: Boolean,
    signature: String,
    userPositionText: String,
});

//store
const store = useStore();

function now1() {
    return moment().format("D/MMM/YYYY");
}
</script>
<template>
    <div>
        <div id="onboardedStaffequipmentPdfForm" class="bg-white px-4">
            <div class="unicef-hact-container">
                <div class="hact-content">
                    <div class="hact-header" style="text-align: left !important">
                            <h4 class="mt-2 mb-4">Consent By End User</h4>
                        </div>
                    <table class="table table-bordered mx-0">
                        
                        <tbody>
                            <tr>
                                <th>End User (Staff)</th>
                                <td class="border-end border-1 border-opacity-10 border-secondary">{{ props.requestDetails?.first_Name }} {{ props.requestDetails?.last_Name }}</td>
                            </tr>
                            <tr>
                                <th>Title</th>
                                <td class="border-end border-1 border-opacity-10 border-secondary">{{ props.requestDetails?.title }}</td>
                            </tr>

                            <tr>
                                <th>Signature</th>
                                <td class="border-end border-1 border-opacity-10 border-secondary" v-if="props.signature != null && $store.state.LoggedInUser.iwillSign != 1">
                                    <img :src="props.signature" alt="signature image" style="width: auto; height: 55px" />
                                </td>
                                <td class="border-end border-1 border-opacity-10 border-secondary" v-else>
                                    <div>
                                        <br />
                                        <br />
                                        <br />
                                        <br />
                                        <br />
                                        <br />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <td class="border-end border-1 border-opacity-10 border-secondary">{{ now1() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.table-bordered tr th,
.table-bordered tr td {
    font-size: 12px !important;
}

.table-bordered tr th {
    background-color: #f5f6fa;
}

.table {
    border: 1px solid black;
}

.table thead tr {
    border-top: 1px solid #f5f6fa !important;
    border-bottom: 1px solid #f5f6fa !important;
    border-left: 1px solid #f5f6fa;
    border-right: 1px solid #f5f6fa;
}

.table-striped > tbody > tr:nth-child(odd) > td,
.table-striped > tbody > tr:nth-child(odd) > th {
    background-color: #f5f6fa;

    /* #87CEFA; */
}

.table tr {
    border-left: 1px solid #f5f6fa;
    border-right: 1px solid #f5f6fa;
    border-top: none !important;
}

.table-bordered .custom-control-label {
    font-weight: normal !important;
    font-size: 12px !important;
}
</style>
