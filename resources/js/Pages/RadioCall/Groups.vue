<template>

    <div class="d-flex flex-row justify-content-between col-12 mb-4">
        <div class="d-flex align-items-center">
            <div class="rounded-pill d-none d-lg-block" style="background-color: #eaeaea; border: 0px">
<!--                            <div class="input-group d-flex flex-row align-items-center">-->
<!--                                <div class="">-->
<!--                                    <span class="" style="font-size: medium"><i class="bx bx-search mt-2"></i></span>-->
<!--                                </div>-->
<!--                                <div class=""><input type="text" class="form-control" placeholder="Search.." style="background-color: #eaeaea; border: 0px" /></div>-->
<!--                            </div>-->
            </div>
        </div>
        <div class="">
            <button class="btn btn-primary" style="margin-right: 10px;" @click="checkGroups()">Check</button>
            <b-dropdown class="btn-group mb-2 mb-sm-0" variant="primary">
                <template #button-content>
                    Create Groups By
                    <i class="mdi mdi-dots-vertical ms-2"></i>
                </template>
                <b-dropdown-item @click="createBy('section')">Section
                    <span v-if="state.generated_by === 'section'">(Current)</span>
                </b-dropdown-item>
                <b-dropdown-item @click="createBy('address')">Physical Address
                    <span v-if="state.generated_by === 'address'">(Current)</span>
                </b-dropdown-item>
                <b-dropdown-item @click="createBy('random')">Random Order
                    <span v-if="state.generated_by === 'random'">(Current)</span>
                </b-dropdown-item>
            </b-dropdown>
        </div>
    </div>
    <div class="accordion accordion-flush" id="accordionFlushExample">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-light">
                <tr>
                    <th style="background-color: #eff2f7; width:30%;">GROUP MEMBER</th>
                    <th style="background-color: #eff2f7; width:15%;">CALL SIGN</th>
                    <th style="background-color: #eff2f7; width:20%;">SECTION</th>
                    <th style="background-color: #eff2f7; width: 25%;">ADDRESS</th>
                    <th style="background-color: #eff2f7; width: 10%;">LEADER</th>
                </tr>
                </thead>
            </table>
        </div>

<!--                    dynamic :::: non section chiefs/representatives-->
        <div class="accordion-item" v-for="group in state.staff_groups" :key="group.id">
            <h2 class="accordion-header" :id="`flush-headline${group.group_number}`" style="display: flex; justify-content: space-between; align-items: center;">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" :data-bs-target="`#flush-collapse${group.id}`" aria-expanded="false" data-bs-placement="left">
                    Group {{ group.group_number }}
                </button>

                <a href="" @click.prevent="openAddStaffModal(group)" class="text-info font-size-14" style="margin-left: auto; margin-right: 15px;">
                    +Add
                </a>
            </h2>

<!--                        omit -> data-bs-parent="#accordionFlushExample such that panels stay open when opened-->
            <div :id="`flush-collapse${group.id}`" class="accordion-collapse collapse" :class="state.selected_group_id === `#flush-headline${group.group_number}`? 'show':''">
                <div class="accordion-body">
                    <table class="table table-striped">
                        <tbody class="bg-white">
                            <tr v-for="(staff,staff_index) in group.group_staff" :key="staff_index">
                                <td style="background-color: #eff2f7; width: 30%;">
                                    <span v-if="staff.is_leader && staff.leads_group !== group.group_number">
                                         <a href="" @click.prevent="goTo(`#flush-headline${staff.leads_group}`,staff.leads_group)">
                                            {{ staff.staff_profile.name }}
                                        </a>
                                    </span>
                                    <span v-else>
                                         {{ staff.staff_profile.name }}
                                    </span>
                                </td>
                                <td style="background-color: #eff2f7; width: 15%;">LC {{ group.group_number }}.{{ staff_index }}</td>
                                <td style="background-color: #eff2f7; width: 20%;">{{ staff.staff_profile.section_name }}</td>
                                <td style="background-color: #eff2f7; width: 20%;">{{ staff.staff_profile.address }}</td>
                                <td style="background-color: #eff2f7; width: 15%;text-align: center">
                                    <div class="row">
                                        <div class="col-8" style="text-align: right;">
<!--                                                        <a href="" @click.prevent="openChangeStaffModal(staff.staff_profile_id,group.group_number)" class="text-info font-size-12" style="margin-left: auto; padding-right: 0; margin-right: 0;">-->
<!--                                                            Change-->
<!--                                                        </a>-->
                                        </div>
                                        <div class="col-4">
                                            <span v-if="group.is_special || (staff.is_leader && staff.leads_group !== group.group_number)">
                                                <input type="radio" class="form-radio-primary" @change="makeGroupLeader(staff.staff_profile_id,group.id)" :checked="staff.is_leader" disabled>
                                            </span>
                                            <span v-else>
                                                  <input :name="`is_group_${group.group_number}_leader`" type="radio" class="form-radio-primary" @change="makeGroupLeader(staff.staff_profile_id,group.id)" :checked="staff.is_leader">
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <b-modal v-model="state.open_select_staff_modal" centered hide-footer hide-header>
        <h5 class="text-center">Add a member to group {{ state.selected_group_no }}</h5>

        <div class="row">
            <div class="col-12">
                <label for="">Select staff</label>
                <select v-model="state.selected_staff" class="form-control">
                    <option :value="staff.id" v-for="staff in unicefStaff">
                        {{ staff.name }}
                    </option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-4 offset-4 mt-4">
                <button class="btn btn-primary w-100" @click.prevent="add()">Proceed</button>
            </div>
        </div>

    </b-modal>

    <b-modal v-model="state.open_change_staff_modal" centered hide-footer hide-header>
        <h5 class="text-center">Change group member {{ state.selected_group_no }}</h5>

        <div class="row">
            <div class="col-12">
                <label for="">Name</label>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <label for="">Select new group</label>
                <select v-model="state.selected_staff" class="form-control">
                    <option :value="staff.id" v-for="staff in unicefStaff">
                        {{ staff.name }}
                    </option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-4 offset-4 mt-4">
                <button class="btn btn-primary w-100" @click.prevent="add()">Proceed</button>
            </div>
        </div>

    </b-modal>

    <b-modal v-model="state.open_check_modal" scrollable centered hide-footer hide-header>

        <div class="row">
            <div class="col-12 text-center">
                <template v-if="state.check_results.length > 0">
                    <span class="bx bx-error-circle text-danger" style="font-size: 5rem;"></span><br>
                    <h5>CALL TREE INCOMPLETE</h5>
                </template>
                <template v-else>
                    <span class="bx bx-check-circle text-success" style="font-size: 5rem;"></span><br>
                    <h5>CALL TREE COMPLETE</h5>
                </template>
            </div>
        </div>

        <div class="row">
           <div class="col-12 text-center">
               <template v-if="state.check_results.length > 0">
                   <p>The following groups have not been checked on</p>
                   <table class="table table-striped">
                       <thead>
                            <tr>
                                <th>GROUP</th>
                                <th>NO.OF MEMBERS</th>
                                <th>DETAIL</th>
                            </tr>
                       </thead>
                       <tbody>
                           <tr v-for="result in state.check_results" :key="result.id">
                               <td>Group {{ result.group_number }}</td>
                               <td>{{ result.group_staff_count }}</td>
                               <td>Has no group leader</td>
                           </tr>
                       </tbody>
                   </table>
               </template>
               <template v-else>
                   <p>All groups have been checked on.</p>
               </template>
           </div>
        </div>

    </b-modal>

</template>

<script setup>
import {onMounted, reactive,computed} from "vue";
import {usePage} from "@inertiajs/vue3";
import axios from "axios";
import SweetAlert from "sweetalert2";
import { notify } from "@/mixins/notify";

const HR_URL = import.meta.env.VITE_HR_URL;

const props = defineProps({
    unicefStaff: Array,
});

onMounted(() => {
    getStaffGroups();
});

const state = reactive({
    staff_groups: [],

    generated_by: null,

    check_results: [],
    open_check_modal: false,

    selected_group_id: null,

    max_members_per_group:9,

    selected_staff: null,
    selected_group_no: null,
    open_select_staff_modal: false,

    api_token_header : {
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` }
    }
});


async function getStaffGroups(){
    showLoading('Please wait...')
    await axios.get(`${HR_URL}/api/radio-call`,state.api_token_header)
        .then((response) => {
            state.staff_groups = response.data.staff_groups;
            state.generated_by = response.data.generated_by;
            SweetAlert.close();
        }).catch((e) => {
            console.log("Error while fetching radio call groups",e)
        });
}

function createBy(by){
    showLoading('Generating groups...')
    axios.get(`${HR_URL}/api/generate-groups/${by}`,state.api_token_header)
        .then((response) => {
            if(response.data.status === 200){
                getStaffGroups();
            }
        }).catch((e) => {
            SweetAlert.close()
            notify.toastErrorMessage("Failed to generate groups!")
        });
}

function makeGroupLeader(staff_id,group_id){

    showLoading('Please wait...')

    let formData = new FormData();
    formData.append('staff_id',staff_id);
    formData.append('group_id',group_id);

    axios.post(`${HR_URL}/api/make-group-leader`,formData,state.api_token_header)
        .then((response) => {
            if(response.data.status === 200){
                getStaffGroups();
                notify.toastSuccessMessage("Selected group member is now their group leader!");
            }
        }).catch((e) => {
        console.log("Error while fetching radio call groups",e)
    });
}


function goTo(id,group_number){
    state.selected_group_id = id;
    // Create an anchor element
    const linkElement = document.createElement('a');
    linkElement.href = id;
    document.body.appendChild(linkElement);

    // Simulate a click on the link right after creating it
    linkElement.click();

    notify.toastInfoMessage(`Group ${group_number} leader`)
}

function openAddStaffModal(group){
    if(group.group_staff_count >= state.max_members_per_group)
        return notify.toastWarningMessage(`Group ${group.group_number} is full!`);

    state.open_select_staff_modal = true;
    state.selected_group_no = group.id;
}

function openChangeStaffModal(id,group){
    state.open_change_staff_modal = true;
    state.selected_group_no = group;
    state.selected_staff = id;
}


function add(){
    showLoading('Please wait...')
    state.open_select_staff_modal = false;

    let formData = new FormData();
    formData.append('group_id',state.selected_group_no);
    formData.append('staff_id',state.selected_staff);

    axios.post(`${HR_URL}/api/add-member-to-group`,formData,state.api_token_header)
        .then((response) => {
            if(response.data.status === 200){
                getStaffGroups();
                notify.toastSuccessMessage(`A new member has been added to group`);
            }else{
                SweetAlert.close();
                notify.toastErrorMessage(`A member may not belong to more than two groups`);
            }
        }).catch((e) => {
        console.log("Error while fetching radio call groups",e)
    });
}


function checkGroups(){
    showLoading('Please wait...')
    axios.get(`${HR_URL}/api/check-groups`,state.api_token_header)
        .then((response) => {
            state.check_results = response.data.check_result;
            state.open_check_modal = true;
            SweetAlert.close();
        }).catch((e) => {
        SweetAlert.close()
        notify.toastErrorMessage("Failed to check groups!")
    });
}

function showLoading(message){
    SweetAlert.fire({
        title: message,
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            SweetAlert.showLoading();
        },
    });
}


</script>

<style scoped>
.accordion-button:after {
    order: -1;
    margin-left: 0;
    margin-right:0.5em;
}

.accordion-button:not(.collapsed) {
    color: #OOOOOO !important;
    background-color: #FFF !important;
}

</style>
