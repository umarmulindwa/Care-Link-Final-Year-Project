<script setup>
import moment from "moment";
import { usePage } from "@inertiajs/vue3";
import Logo from "./UnicefLogoBase64";
import TickIcon from "./TickIcon.vue";
import CrossIcon from "./CrossIcon.vue";
import { onMounted ,ref} from "vue";
const props = defineProps({
    details: Object,
});

const savedSignature = ref(null);
onMounted(()=>{
    getUserSignature();
})

function formatDate(date) {
    if(date != null && date != ""){
    return moment(date).format("D/MMM/YYYY    HH:mm");
    }
}
function now() {
    return moment().format("D/MMM/YYYY [at] HH:mm");
}
// function formatNumber(number) {
//     let app = this;
//     return app.numerilize(number, "n", 0);
// }
function today() {
    return moment().format("Do MMMM YYYY");
}
function refNumber() {
    return moment().format("D/MM/YYYY    HH:mm").replace(/\//g, "");
}
async function getUserSignature() {
    const options = {
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
    };
    await axios
        .get(`/api/staff-register/get-userSignature`, options)
        .then((res) => {
            if (res.data.results != null) {
                savedSignature.value = res.data.results?.image;
            } 
        })
        .catch((error) => {
            console.log(error);
        });
}
</script>

<template>
    <div>
        <div id="user-incidentform-pdf">
            <div class="unicef-hact-container">
                <div class="mt-3" style="text-align: center !important">
                    <img :src="Logo" alt="" height="30" />
                    <h4 class="mt-1">Incident Report Form</h4>
                    <p class="mb-3 pb-0">Reference #: UNSSD-{{ refNumber() }}</p>
                    <small>To Completed and reported to UNICEF by Head of IP(or OIC) with 24 hours of incident/damage.</small>
                </div>
                <div class="mt-5 col-12 d-flex align-items-center justify-content-center">
                    <div class="col-11">
                        <div class="col-12">
                            
                            <div class="border border-opacity-10 border-secondary">
                                <div class="d-flex col-12">
                                    <div class="col-3 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Incident Date</div>
                                    <div class="col-9 pt-2 pb-2 ps-4">{{ props.details.incidentDate }}</div>
                                </div>
                            </div>
                            <div class="border border-opacity-10 border-secondary">
                                <div class="d-flex col-12">
                                    <div class="col-3 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Incident Cases</div>
                                    <div class="col-9 pt-2 pb-2 ps-4">
                                        <span class="me-3">{{ props.details.isTheft ? "Theft" : "" }}</span
                                        ><span>{{ props.details.isDamage ? "Damaged" : "" }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="border border-opacity-10 border-secondary">
                                <div class="d-flex col-12">
                                    <div class="col-3 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Address</div>
                                    <div class="col-9 pt-2 pb-2 ps-4">{{ props.details.newincidentLocation != null ? props.details.newincidentLocation.optionString : "" }}</div>
                                </div>
                            </div>
                             <div class="border border-opacity-10 border-secondary">
                                <div class="d-flex col-12">
                                    <div class="col-3 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Address Comment</div>
                                    <div class="col-9 pt-2 pb-2 ps-4">{{ props.details.addressComments }}</div>
                                </div>
                            </div>
                            <div class="border border-opacity-10 border-secondary">
                                <div class="d-flex col-12">
                                    <div class="col-3 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Contact Person</div>
                                    <div class="col-9 pt-2 pb-2 ps-4">{{ props.details.contactPerson }}</div>
                                </div>
                            </div>
                             <div class="border border-opacity-10 border-secondary">
                                <div class="d-flex col-12">
                                    <div class="col-3 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Section</div>
                                    <div class="col-9 pt-2 pb-2 ps-4">{{ props.details.section }}</div>
                                </div>
                            </div>
                            <div class="border border-opacity-10 border-secondary">
                                <div class="col-3 pt-2 pb-2 ps-3 fw-bold">Incident Details</div>
                            </div>

                            <div class="border border-opacity-10 border-secondary" id="break">
                                <div class="d-flex">
                                    <div class="col-4 pt-2 pb-2 ps-3 fw-bold">Supplies</div>
                                    <div class="col-2 pt-2 pb-2 fw-bold text-ceter">Unit of Measurement</div>
                                    <div class="col-2 pt-2 pb-2 fw-bold text-ceter">Quantity</div>
                                    <div class="col-2 pt-2 pb-2 fw-bold text-ceter">Unit Cost ($)</div>
                                    <div class="col-2 pt-2 pb-2 fw-bold text-ceter">Value</div>
                                </div>
                                <div class="d-flex border-bottom" v-for="(item, index) in props.details.supplies" :key="index">
                                    <div class="col-4 pt-2 pb-2 ps-3">{{ item?.supplies?.supply }}</div>
                                    <div class="col-2 pt-2 pb-2 text-ceter">{{ item?.measurementUnit }}</div>
                                    <div class="col-2 pt-2 pb-2 text-ceter">{{ item?.qty }}</div>
                                    <div class="col-2 pt-2 pb-2 text-ceter">{{ item?.unitCost }}</div>
                                    <div class="col-2 pt-2 pb-2 text-ceter">{{ item?.value }}</div>
                                </div>
                            </div>

                            <div class="border border-opacity-10 border-secondary">
                                <div class="col-3 pt-2 pb-2 ps-3 fw-bold">Short Description of the incident</div>
                                <div class="pt-2 pb-2 ps-3" v-html="props.details.incidentDetails"></div>
                            </div>
                            <div class="border border-opacity-10 border-secondary">
                                <div class="d-flex col-12">
                                    <div class="col-7 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">
                                        Was the Incident reported to the nearby police station (through UN security office)?
                                    </div>
                                    <div class="col-9 pt-2 pb-2 ps-4">
                                        <TickIcon v-if="props.details.hasbeenReportedToPolice" />
                                        <CrossIcon v-else />
                                    </div>
                                </div>
                            </div>
                            <div class="border border-opacity-10 border-secondary" v-if="props.details.hasbeenReportedToPolice == true ">
                                <div class="col-3 pt-2 pb-2 ps-3 fw-bold">If yes what was their feedback?</div>
                                <div class="pt-2 pb-2 ps-3">{{ props.details.feedbackPolice }}</div>
                            </div>
                             <div class="border border-opacity-10 border-secondary" v-else-if="props.details.hasbeenReportedToPolice == false">
                                <div class="col-3 pt-2 pb-2 ps-3 fw-bold">Why hasn't it been reported?</div>
                                <div class="pt-2 pb-2 ps-3">{{ props.details.whynotReportedPolice }}</div>
                            </div>
                            <div class="border border-opacity-10 border-secondary">
                                <div class="d-flex col-12">
                                    <div class="col-7 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Was the Incident reported to the nearby local leader?</div>
                                    <div class="col-9 pt-2 pb-2 ps-4">
                                        <TickIcon v-if="props.details.hasbeenReportedToLocalLeader" />
                                        <CrossIcon v-else />
                                    </div>
                                </div>
                            </div>
                            <div class="border border-opacity-10 border-secondary" v-if=" props.details.hasbeenReportedToLocalLeader == true">
                                <div class="col-3 pt-2 pb-2 ps-3 fw-bold">If yes what was their feedback?</div>
                                <div class="pt-2 pb-2 ps-3">{{ props.details.feedbacklocal}}</div>
                            </div>
                            <div class="border border-opacity-10 border-secondary" v-else-if=" props.details.hasbeenReportedToLocalLeader == false">
                                <div class="col-3 pt-2 pb-2 ps-3 fw-bold">Why hasn't it been reported?</div>
                                <div class="pt-2 pb-2 ps-3">{{ props.details.whynotReportedlocal }}</div>
                            </div>
                           <div class="border border-opacity-10 border-secondary">
                                <div class="d-flex col-12">
                                    <div class="col-7 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Was the incidence reported to UNICEF on time?</div>
                                    <div class="col-9 pt-2 pb-2 ps-4">
                                        <TickIcon v-if="props.details.reportedOnTime" />
                                        <CrossIcon v-else />
                                    </div>
                                </div>
                            </div>
                            <div class="border border-opacity-10 border-secondary" v-if=" props.details.reportedOnTime == false">
                                <div class="col-3 pt-2 pb-2 ps-3 fw-bold">What led to the delay of reporting the incident?</div>
                                <div class="pt-2 pb-2 ps-3">{{ props.details.reportDelayCause}}</div>
                            </div>
                            <div  v-if="props.details.mitigationMeasures.length > 0">
                                <div class="border border-opacity-10 border-secondary">
                                    <div class="col-3 pt-2 pb-2 ps-3 fw-bold">Mitigation Measures</div>
                                </div>
                                <div class="border border-opacity-10 border-secondary">
                                    <div class="d-flex border-bottom" v-for="(msr, index) in props.details.mitigationMeasures" :key="index">
                                        <div class="pt-2 pb-2 ps-3">{{ msr?.mitigationMeasure }}</div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="props.details.mitigationFollowUps.length > 0" id="break">
                                <div class="border border-opacity-10 border-secondary">
                                    <div class="col-3 pt-2 pb-2 ps-3 fw-bold">Follow Up Actions</div>
                                </div>
                                <div class="border border-opacity-10 border-secondary">
                                    <div class="d-flex border-bottom" v-for="(followup, index) in props.details.mitigationFollowUps" :key="index">
                                        <div class="pt-2 pb-2 ps-3">{{ followup?.followup }} ( Deadline: {{ formatDate(followup.deadline) }})</div>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="border border-opacity-10 border-secondary">
                            <div class="d-flex col-12">
                                <div class="col-3 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Prepared By</div>
                                <div class="col-9 pt-2 pb-2 ps-4">{{ usePage().props.auth.user.name }}</div>
                            </div>
                            <div class="d-flex col-12">
                                <div class="col-3 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Designation</div>
                                <div class="col-9 pt-2 pb-2 ps-4">Implementing Partner / Service Provider</div>
                            </div>
                            <div class="d-flex col-12">
                                <div class="col-3 border-end pt-5 pb-5 ps-3 fw-bold" style="background-color: #fafafa">Signature<br />{{ now() }}</div>
                                <div class="col-9 pt-5 pb-5 ps-3">
                                    <div v-if="savedSignature != null">
                                        <img :src="savedSignature" alt="User Signature" style="width: auto; height: 55px" />
                                    </div>
                                    <div v-else>Implementing Partner/Service Provider</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr style="border-color: #ddd; margin: 2.5rem 3.5rem" />
                <p class="text-center" style="font-style: italic">Form generated by UNICEF Project Operations Platform on {{ now() }}hrs</p>
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
    border: 1px solid rgb(66, 66, 66);
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
    border-left: 1px solid #fafafa;
    border-right: 1px solid #f5f6fa;
    border-top: none !important;
}

.table-bordered .custom-control-label {
    font-weight: normal !important;
    font-size: 12px !important;
}
</style>
