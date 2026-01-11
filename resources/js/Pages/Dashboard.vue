<script setup>
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import Layout from "../Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import finance from "../../images/finance.png";
import staff from "../../images/staff.png";
import travel from "../../images/travel.png";
import administration from "../../images/administration.png";
import leave from "../../images/leave.png";
import visitor from "../../images/visitor.png";
import supply from "../../images/supply.png";
import boq from "../../images/boq.png";
import hrIcon from "../../images/hrIcon.png";
import ictIcon from "../../images/ict-icon.png";
import phonebill from "../../images/phonebill.png";
import signaturemanager from "../../images/signaturemanager.png";
import SignatureRegister from "./SignatureRegister/SignatureRegister.vue";
import mobile from "../../images/mobile.png";
import { useStore } from "vuex";
import { onMounted, reactive, useAttrs, ref, computed } from "vue";
import { RequestHelper, showLoader } from "@/mixins/helpers";
import { BscNavigator } from "../Composables/BscNavigator.js";
import { BscRequestsNavigator } from "../Composables/BscRequestsNavigator.js";
import { LowValueNavigator } from "../Composables/LowValueNavigator.js";
import {
    AccidentReportingNavigator,
    diplomaticVehicleRegistration,
    officeSupplies,
    vehicleFuelManagement,
    vehiclePrivateUse,
    administrationSettings,
    vehicleUtilization,
    vehicleMaintenance,
    carQueueManagement,
} from "../Composables/AdminNavigator";

import { notify } from "@/mixins/notify";
import simplebar from "simplebar-vue";
import moment from "moment";
import requestVehicleVue from "../Components/PrivateUse/requestVehicleModal.vue";
import getMobileAppModalVue from "./modals/getMobileAppModal.vue";
import statInvoiceList from "@/Pages/invoiceStatistics/invoiceList.vue";
import statInvoicePie from "@/Pages/invoiceStatistics/invoicePieChart.vue";
import singleInput from "@/Pages/StaffRegister/NewStaff/SingleInput.vue";
import Swal from "sweetalert2";
import { hide } from "@popperjs/core";

//props
const props = defineProps({
    user: Object,
});

const attrs = useAttrs();

const bscSection = ref("Busincess Support Center");
const singleInputID = ref(null);

const state = reactive({
    showSystems: true,
    quickLink: "",
    currentUser: null,
    isUserAuthorised: false,
    open_incomplete_profile_modal: false,
    open_password_expired_modal: false,
});

//store
const store = useStore();

const downloadMobileAppModal = ref(false);
const downloadMobileApp = () => {
    downloadMobileAppModal.value = true;
};
const downloadMobileEvent = () => {
    downloadMobileAppModal.value = !downloadMobileAppModal.value;
};

async function checkUserProfile(){
    await singleInputID.value.editStaff(props.user.staff_profile,false);
    let inCompleteStatus = singleInputID.value.validateForm(
        singleInputID.value.singleStaff,
        singleInputID.value.excluded
    );

    let reminderDatetime = localStorage.getItem("dont_show_incomplete_profile_modal_" + props.user.id);
    if(inCompleteStatus && (!reminderDatetime || new Date(reminderDatetime) <= new Date())){
        state.open_incomplete_profile_modal = true;
    }
}

function toMyProfile(hash){
    let url = '/user/profile';
    if(hash){
        url += `?${hash}`;
        window.location.href = url;
    }

    router.get(url);
}

function proceedWithApp(){
    setReminderDatetime();
    state.open_incomplete_profile_modal = false;
}

function setReminderDatetime(){
    const now = new Date();
    const thirtyMinutesFromNow = new Date(now.getTime() + 30 * 60000);
    localStorage.setItem("dont_show_incomplete_profile_modal_" + props.user.id,thirtyMinutesFromNow.toISOString());
}

//mounted
onMounted(() => {
    //ading user details to the store
    store.commit("LoggedInUser/storeLoggedInUserDetails", usePage().props.auth.user);
    //removing default blue bacground
    document.body.classList.remove("side-bg");
    //setting current User
    state.currentUser = usePage().props.auth.user;
    bscSection.value = import.meta.env.VITE_BSC_SECTION;
    state.isUserAuthorised = true;
    // checkUserProfile();
    if(props.user.password_expired){
        state.open_password_expired_modal = true;
    }
    // console.log("these are all mfs", import.meta.env.VITE_MODULES_WATER)
});

//methods

const logout = () => {
    router.post(route("logout"));
};
const toBSC = () => {
    const token = attrs.auth.user.api_token;
    const authHeaders = {
        auth_token: props.user.token,
        Authorization: `Bearer ${token}`,
    };

    const BSCURL = import.meta.env.VITE_BSC_APP_URL;
    showLoader("Loading...");
    return (window.location.href = `${BSCURL}/loginFromMain?token=${props.user.token}`);
};

const atClick = async (actionItem) => {
    if (actionItem?.action === null) {
        return;
    }

    const { purpose } = actionItem;
    let BASE_URL = null;
    switch (purpose) {
        case "Invoice":
            BASE_URL = import.meta.env.VITE_BSC_APP_URL;
            break;

        default:
            BASE_URL = actionItem.action;
            break;
    }

    showLoader("Applying user session ...");

    if(BASE_URL.includes('?')){
        const parts = BASE_URL.split('?');
        const partBefore = parts[0];
        const partAfter = parts[1];

        return (window.location.href = `${partBefore}/loginFromMain?token=${props.user.token}&${partAfter}`);
    }

    return (window.location.href = `${BASE_URL}/loginFromMain?token=${props.user.token}`);

};

const toVehicleUtilization = () => {
    const VHCL_UTIL_URL = import.meta.env.VITE_ADMIN_VEHICLE_UTILIZATION_URL;
    showLoader("Applying user session ...");
    return (window.location.href = `${VHCL_UTIL_URL}/loginFromMain?token=${props.user.token}`);
};

const toVehicleMaintenance = () => {
    const VHCL_MAIN_URL = import.meta.env.VITE_ADMIN_VEHICLE_MAINTENANCE_URL;
    showLoader("Applying user session ...");
    return (window.location.href = `${VHCL_MAIN_URL}/loginFromMain?token=${props.user.token}`);
};

const toSupply = async () => {
    const LOW_VALUE_URL = import.meta.env.VITE_LOW_VALUE_PROCUREMENT_URL;
    showLoader("Loading ...");
    return (window.location.href = `${LOW_VALUE_URL}/loginFromMain?token=${props.user.token}`);
};

const toLEAVE = () => {
    const LEAVEURL = import.meta.env.VITE_LEAVE_URL;
    showLoader("Loading ...");
    return (window.location.href = `${LEAVEURL}/loginFromMain?token=${props.user.token}`);
};
const toVisitortracker = () => {
    const VISITORTRACKER_URL = import.meta.env.VITE_VISTORTRACKER_URL;
    showLoader("Loading ...");
    return (window.location.href = `${VISITORTRACKER_URL}/loginFromMain?token=${props.user.token}`);
};
const toOfficeSupplies = () => {
       const OFFICESUPPLIES_URL = import.meta.env.VITE_OFFICESUPPLIES_URL;
    showLoader("Loading ...");
    return (window.location.href = `${OFFICESUPPLIES_URL}/loginFromMain?token=${props.user.token}`);
};
const toHR = (system_index, within = true) => {
    const HR_URL = import.meta.env.VITE_HR_URL;

    const authHeaders = {
        auth_token: props.user.token,
    };
    const systems = ["/", "/consultant-tor", "/staff-exit", "/radio-call", "/staff-orientation", import.meta.env.VITE_STAFFONBOARDING_URL];

    const url = systems[system_index];

    showLoader("Loading ...");

    if (within) {
        return (window.location.href = `${HR_URL}/loginFromMain?token=${props.user.token}&redirect=${url}`);
    } else {
        return (window.location.href = `${url}/loginFromMain?token=${props.user.token}`);
    }
};

const toIctAccess = (module_index) => {
    const ICT_ACCESS_URL = import.meta.env.VITE_ICT_ACCESS_URL;

    showLoader("Loading ...");
    window.location.href = `${ICT_ACCESS_URL}/loginFromMain?token=${props.user.token}&redirect=${module_index}`;
    showLoader("", true);
};

const toAccidentReporting = () => {
    const token = props.user.token;
    const accidentReportingUrl = import.meta.env.VITE_ACCIDENTREPORTING_URL;
    showLoader("Loading ...");

    return (window.location.href = `${accidentReportingUrl}/loginFromMain?token=${token}`);
};
const toIncidentReporting = () => {
    const token = props.user.token;
    const incidentReportingUrl = import.meta.env.VITE_INCIDENTREPORTING_URL;
    showLoader("Loading ...");
    return (window.location.href = `${incidentReportingUrl}/loginFromMain?token=${token}`);
};
const toAirtimeData = () => {
    const token = props.user.token;
    const airtimeDataUrl = import.meta.env.VITE_AIRTIMEDATA_URL;
    showLoader("Loading ...");

    return (window.location.href = `${airtimeDataUrl}/loginFromMain?token=${token}`);
};
const toGuestHouse = () => {
    const token = props.user.token;
    const guestHouseUrl = import.meta.env.VITE_GUESTHOUSE_URL;
    showLoader("Loading ...");

    return (window.location.href = `${guestHouseUrl}/loginFromMain?token=${token}`);
};

const toDIM = () => {
    const URL = import.meta.env.VITE_ADMIN_DIM;
    Swal.fire({
        html: `<p style='font-size:16px'>Loading...</p>`,
        didOpen: () => {
            Swal.showLoading();
        },
        allowOutsideClick: false,
    });
    return (window.location.href = `${URL}/loginFromMain?token=${props.user.token}`);
}
//
const getAuthenticatedUder = () => {
    axios.get("/api/user").then((res) => {
        console.log(res);
    });
};

const goToFinanceModule = async (system_index) => {
    const systems = [import.meta.env.VITE_BSC_APP_URL, import.meta.env.VITE_FINANCE_APP_URL];
    const url = systems[system_index];

    const token = attrs.auth.user.api_token;
    const authHeaders = {
        auth_token: props.user.token,
        Authorization: `Bearer ${token}`,
    };

    return (window.location.href = `${url}/loginFromMain?token=${props.user.token}`);
};

//vehicle and transport
const goToVTSubModule = (system_index) => {
    const authHeaders = {
        auth_token: props.user.token,
    };
    const systems = [import.meta.env.VITE_ADMIN_TRAVEL_PLANNER_URL];

    const url = systems[system_index];

    showLoader("Loading ...");
    return (window.location.href = `${url}/loginFromMain?token=${props.user.token}`);
};

const goToTravelPlannerSubmodule = (module) => {
    const url = import.meta.env.VITE_ADMIN_TRAVEL_PLANNER_URL;
    showLoader("Loading ...");
    return (window.location.href = `${url}/loginFromMain?token=${props.user.token}&redirect=${module}`);
};

const requestVehicleVueModal = ref(false);

const closeRequestVehicleVueModal = () => {
    requestVehicleVueModal.value = false;
};

const goToAssetSubModule = (system_index) => {
    const authHeaders = {
        auth_token: props.user.token,
    };
    if (system_index === 9) {
        requestVehicleVueModal.value = true;
        return;
    }
    const systems = [
        import.meta.env.VITE_ADMIN_ASSET_TAGGING_URL,
        import.meta.env.VITE_ADMIN_ASSET_TRACKING_URL,
        import.meta.env.VITE_ADMIN_ASSET_EQUIPMENT_REQUEST_URL,
        import.meta.env.VITE_ADMIN_ASSET_DISPOSAL_URL,
        import.meta.env.VITE_ADMIN_ASSET_AMR_URL,
        import.meta.env.VITE_ADMIN_CARPARK_URL,
        import.meta.env.VITE_ADMIN_VEHICLE_MAINTENANCE_URL,
        import.meta.env.VITE_ADMIN_VEHICLE_FUEL_URL,
        import.meta.env.VITE_ADMIN_VEHICLE_UTILIZATION_URL,
        "/",
        import.meta.env.VITE_ADMIN_VEHICLE_MANAGER_URL,
    ];

    const url = systems[system_index];

    showLoader("Loading ...");
    return (window.location.href = `${url}/loginFromMain?token=${props.user.token}`);
};

const toSupplyPlanning = () => {
    showLoader("Loading ...");
    const VITE_SUPPLY_PLANNING_URL = import.meta.env.VITE_SUPPLY_PLANNING_URL;
    return (window.location.href = `${VITE_SUPPLY_PLANNING_URL}/loginFromMain?token=${props.user.token}`);
};

function toVehicleRegistration() {
    const vehicleRefURL = import.meta.env.VITE_DIPLOMATICVEHICLEREG_URL;

    showLoader("Loading ...");
    return (window.location.href = `${vehicleRefURL}/loginFromMain?token=${props.user.token}`);
}
function toStaffRegister() {
    const isSuperAdmin = usePage().props.auth.user.allPermissions.includes("s_admin");
    const isHRStaff = usePage().props.auth.user.allPermissions.includes("hr_staff");

    if (isSuperAdmin || isHRStaff) {
        router.visit("/staff_register");
    } else {
        notify.toastErrorMessage("You have no permissions");
    }
}
// function toSubSystem() {
//     const modulesURL = import.meta.env.VITE_MODULES_URL;

//     showLoader("Loading...");
//     return (window.location.href = `${modulesURL}/loginFromMain?token=${props.user.token}`);
// }
function formatDate(theDate) {
    return moment(theDate).fromNow();
}

const dashboardView = computed(() => {
    let showDashboard = import.meta.env.VITE_COUNTRY_OFFICE;
    let isToShow = 0;
    switch (showDashboard) {
        case "Uganda":
            isToShow = 2;
            break;
        default:
            isToShow = 1;
            break;
    }
    return isToShow;
});
</script>

<template>

        <Head title="Dashboard" />

        <!-- <h3>Dashboard</h3> -->

        <!-- <form @submit.prevent="logout">
    <b-button type="submit" variant="primary" class="btn-block" >Log Out</b-button>
</form> -->
        <Layout>
            <div class="d-flex gap-4 mt-2 mb-4">
                <!-- <div role="button" @click="state.showSystems = true">
                    <h5 class="mb-0">
                        <small class="fw-bold" :class="state.showSystems ? 'text-muted' : 'text-primary'">SYSTEMS</small>
                    </h5>
                </div> -->
                <div role="button" v-if="dashboardView != 2" @click="state.showSystems = false">
                    <h5 class="mb-0">
                        <small class="fw-bold" :class="state.showSystems ? 'text-primary' : 'text-muted'">FORMS</small>
                    </h5>
                </div>
            </div>

            <div class="row text-muted" v-if="dashboardView == 2">
                <div class="column col-xl-8" v-if="state.showSystems">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="card" role="button">
                                <div class="card-body p-4 card-height">
                                    <div class="d-flex gap-1 justify-content-between" @click="toStaffRegister">
                                        <div class="d-flex align-items-center pt-2 pb-2 gap-3">
                                            <div><img :src="staff" height="25" /></div>
                                            <div class="column">
                                                <div>
                                                    <h5 class="mb-0">
                                                        <small class="text-muted">Staff Register</small>
                                                    </h5>
                                                </div>
                                                <div class="text-primary">
                                                    <small>Access: Administrators</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end invisible">
                                            <div class="btn-group dropstart">
                                                <i class="bx bx bx-dots-vertical-rounded" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                <ul class="dropdown-menu">
                                                
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card" role="button">
                                <div class="card-body p-4 card-height">
                                    <div class="d-flex gap-1 justify-content-between">
                                        <div class="d-flex align-items-center pt-2 pb-2 gap-3" @click="toBSC">
                                            <div><img :src="finance" height="30" /></div>
                                            <div class="column">
                                                <div>
                                                    <h5 class="mb-0">
                                                        <small class="text-muted">Appointments</small
                                                        ><br />
                                                        <!-- <p></p> -->
                                                    </h5>
                                                </div>
                                                <div class="text-primary">
                                                    <small>Access: All</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <div class="btn-group dropstart">
                                                <i class="bx bx bx-dots-vertical-rounded" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                <ul class="dropdown-menu">
                                                    <b-dropdown-item class="mb-1" @click="goToFinanceModule(1)">Finance</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="BscRequestsNavigator('face-forms')">FACE Forms</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="BscRequestsNavigator('myRequests')">My Requests</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="BscRequestsNavigator('createRequest')">Submit Request</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="BscRequestsNavigator('payroll')">Payroll</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="BscRequestsNavigator('pettyCashReconciliation')">PettyCash Reconciliation</b-dropdown-item>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="card" role="button">
                                <div class="card-body p-4 card-height">
                                    <div class="d-flex gap-1 justify-content-between">
                                        <div class="d-flex align-items-center pt-2 pb-2 gap-3" id="showSignatureManager" data-bs-toggle="modal" data-bs-target="#signatureRegister">
                                            <div><img :src="signaturemanager" height="28" /></div>
                                            <div class="column">
                                                <div>
                                                    <h5 class="mb-0">
                                                        <small class="text-muted">Signature Manager</small>
                                                    </h5>
                                                </div>
                                                <div class="text-primary">
                                                    <small>Access: All</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end invisible">
                                            <div class="btn-group dropstart">
                                                <i class="bx bx bx-dots-vertical-rounded" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                <ul class="dropdown-menu">
                                                    <b-dropdown-item class="mb-1">Add Signature</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1">Update Signature</b-dropdown-item>
                                                    
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <statInvoiceList />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <statInvoicePie />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card" style="height: 585px !important">
                        <div class="card-body p-2">
                            <div>
                                <h5 class="mb-0">
                                    <small class="text-muted">Recent Notifications</small>
                                </h5>
                                <hr class="bg-muted border border-top mt-4 mb-4" />

                                <div v-if="state.currentUser != null">
                                    <simplebar style="max-height: 485px !important" v-if="state.currentUser?.notifications?.length > 0">
                                        <div v-if="state.currentUser?.notifications?.length > 0">
                                            <a class="text-reset notification-item" v-for="notification in state.currentUser?.notifications" :key="`${notification.id}`">
                                                <div class="d-flex">
                                                    <img
                                                        v-if="notification?.profile_photo_path == null"
                                                        :src="`https://ui-avatars.com/api/?name=${notification?.submitted_by}?background=cdd7f7&color=7f9cf5`"
                                                        class="me-3 rounded-circle avatar-xs"
                                                        alt="user-pic"
                                                    />

                                                    <img v-else :src="`/storage/${notification?.profile_photo_path}`" :alt="'p'" class="me-3 rounded-circle header-profile-user object-fit-cover" />
                                                    <div class="flex-grow-1">
                                                        <a @click.prevent="atClick(notification)" href="/">
                                                            <h6 class="mt-0 mb-1">
                                                                {{ notification?.submitted_by }}
                                                            </h6>
                                                        </a>
                                                        <div class="font-size-12 text-muted">
                                                            <p class="mb-1" v-if="notification?.title">
                                                                {{ notification?.title }}
                                                            </p>
                                                            <p class="mb-1" v-else-if="notification?.description != null && notification?.title == null">
                                                                <span :style="{ fontWeight: '14px' }" v-html="notification?.description"></span>
                                                            </p>

                                                            <div class="mb-0">
                                                                <small><i class="mdi mdi-clock-outline me-1"></i></small>
                                                                <small>{{ formatDate(notification.created_at) }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </simplebar>
                                    <div v-else class="text-center mt-5 mb-5">You have no Notifications</div>
                                    <div class="d-flex justify-content-end mb-4" v-if="state.currentUser?.notifications?.length > 0">
                                        <!-- <div role="button"><small class="text-primary">More Notifications</small>
                                        </div> -->
                                    </div>
                                </div>
                          
                            </div>
                        </div>
                    </div>
                 
                </div>
            </div>
            <div class="row text-muted" v-if="dashboardView == 1">
                <div class="column col-xl-8" v-if="state.showSystems">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="card" role="button">
                                <div class="card-body p-4">
                                    <div class="d-flex gap-1 justify-content-between" @click="toStaffRegister">
                                        <div class="d-flex align-items-center pt-2 pb-2 gap-3">
                                            <div><img :src="staff" height="25" /></div>
                                            <div class="column">
                                                <div>
                                                    <h5 class="mb-0">
                                                        <small class="text-muted">Staff Register</small>
                                                    </h5>
                                                </div>
                                                <div class="text-primary">
                                                    <small>Access: Administrators</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end invisible">
                                            <div class="btn-group dropstart">
                                                <i class="bx bx bx-dots-vertical-rounded" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                <ul class="dropdown-menu">
                                                   
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card" role="button">
                                <div class="card-body p-4">
                                    <div class="d-flex gap-1 justify-content-between">
                                        <div class="d-flex align-items-center pt-2 pb-2 gap-3" @click="toBSC">
                                            <div><img :src="finance" height="30" /></div>
                                            <div class="column">
                                                <div>
                                                    <h5 class="mb-0">
                                                        <small class="text-muted">{{ bscSection }}</small>
                                                    </h5>
                                                </div>
                                                <div class="text-primary">
                                                    <small>Access: All</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <div class="btn-group dropstart">
                                                <i class="bx bx bx-dots-vertical-rounded" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                <ul class="dropdown-menu">
                                                    <!-- <b-dropdown-item class="mb-1" @click="goToFinanceModule(0)">BSC </b-dropdown-item> -->
                                                    <b-dropdown-item class="mb-1" @click="goToFinanceModule(1)">Finance</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="BscRequestsNavigator('myRequests')">My Requests</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="BscRequestsNavigator('face-forms')">FACE Forms</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="BscRequestsNavigator('createRequest')">Submit Request</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="BscRequestsNavigator('payroll')">Payroll</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="BscRequestsNavigator('pettyCashReconciliation')">PettyCash Reconciliation</b-dropdown-item>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card" role="button">
                                <div class="card-body p-4">
                                    <div class="d-flex gap-1 justify-content-between">
                                        <div class="d-flex align-items-center pt-2 pb-2 gap-3">
                                            <div><img :src="administration" height="30" /></div>
                                            <div class="column">
                                                <div>
                                                    <h5 class="mb-0">
                                                        <small class="text-muted">Administration</small>
                                                    </h5>
                                                </div>
                                                <div class="text-primary">
                                                    <small>Access: All</small>
                                                </div>
                                            </div>
                                        </div>
                                         <div class="d-flex justify-content-end">
                                        <div class="btn-group dropstart">
                                            <i class="bx bx bx-dots-vertical-rounded" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu">
                                                <!-- Dropdown menu links -->
                                                <b-dropdown-item class="mb-1" @click="goToAssetSubModule(0)">Asset tagging</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="goToAssetSubModule(1)">Asset tracking</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="goToAssetSubModule(2)">Equipment Request</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="goToAssetSubModule(3)">Disposal Process</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="goToAssetSubModule(4)">AMR Creation</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="goToAssetSubModule(5)">Car Park Allocation</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="goToAssetSubModule(6)">Vehicle Maintenance</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="goToAssetSubModule(7)">Vehicle Fuel</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="goToAssetSubModule(8)">Vehicle Utilization</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="goToAssetSubModule(9)">Request Vehicle For Private Use</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="goToAssetSubModule(10)">Private Use of UNICEF Vehicles</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="goToVTSubModule(0)">Travel Planner</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="toOfficeSupplies">Office Supplies</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="toVisitortracker">Visitor Tracker</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="toAccidentReporting">Accident Reporting</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="toIncidentReporting">Incident Reporting</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="toAirtimeData">Airtime & Data</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="toGuestHouse">Guest House</b-dropdown-item>
                                            </ul>
                                        </div>
                                    </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                 
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <div class="card" role="button">
                                <div class="card-body p-4">
                                    <div class="d-flex gap-1 justify-content-between">
                                        <div class="d-flex align-items-center gap-3" @click="toLEAVE">
                                            <div><img :src="leave" height="30" /></div>
                                            <div class="column">
                                                <div>
                                                    <h5 class="mb-0">
                                                        <small class="text-muted">Leave/Availability Planner</small>
                                                    </h5>
                                                </div>
                                                <div class="text-primary">
                                                    <small>Access: All</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <div class="d-flex justify-content-end invisible">
                                                <div class="btn-group dropstart">
                                                    <i class="bx bx bx-dots-vertical-rounded" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                    <ul class="dropdown-menu">
                                                        <!-- Dropdown menu links -->
                                                        <b-dropdown-item class="mb-1">Sub Menu 1</b-dropdown-item>
                                                        <b-dropdown-item class="mb-1">Sub Menu 2</b-dropdown-item>
                                                        <b-dropdown-item class="mb-1">Sub Menu 3</b-dropdown-item>
                                                        <b-dropdown-item class="mb-1">Sub Menu 4</b-dropdown-item>
                                                        <b-dropdown-item class="mb-1">Sub Menu 5</b-dropdown-item>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card" role="button">
                                <div class="card-body p-4">
                                    <div class="d-flex gap-1 justify-content-between">
                                        <div class="d-flex align-items-center pt-2 pb-2 gap-3" @click="toSupply">
                                            <div><img :src="supply" height="28" /></div>
                                            <div class="column">
                                                <div>
                                                    <h5 class="mb-0">
                                                        <small class="text-muted">Supply</small>
                                                    </h5>
                                                </div>
                                                <div class="text-primary">
                                                    <small>Access: All</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <div class="btn-group dropstart">
                                                <i class="bx bx bx-dots-vertical-rounded" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                <ul class="dropdown-menu">
                                                    <!-- Dropdown menu links -->
                                                    <b-dropdown-item class="mb-1" @click="LowValueNavigator('home')">Low Value</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="toSupplyPlanning">Planning</b-dropdown-item>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card" role="button">
                                <div class="card-body p-4">
                                    <div class="d-flex gap-1 justify-content-between">
                                        <div class="d-flex align-items-center pt-2 pb-2 gap-3" @click="toHR(0)">
                                            <div><img :src="hrIcon" height="24" width="24" /></div>
                                            <div class="column">
                                                <div>
                                                    <h5 class="mb-0">
                                                        <small class="text-muted">Human Resource</small>
                                                    </h5>
                                                </div>
                                                <div class="text-primary">
                                                    <small>Access: All</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <div class="btn-group dropstart">
                                                <i class="bx bx bx-dots-vertical-rounded" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                <ul class="dropdown-menu">
                                                    <!-- Dropdown menu links -->
                                                    <b-dropdown-item class="mb-1" @click="toHR(1)">Consultant TOR</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="toHR(2)">Staff Exit</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="toHR(3)">Staff Headcount</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="toHR(4)">Staff Orientation</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="toHR(5, false)">Staff Onboarding</b-dropdown-item>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>

                    <div class="row">
                     

                        <div class="col-sm-4">
                            <div class="card" role="button">
                                <div class="card-body p-4">
                                    <div class="d-flex gap-1 justify-content-between">
                                        <div class="d-flex align-items-center pt-2 pb-2 gap-3" id="showSignatureManager" data-bs-toggle="modal" data-bs-target="#signatureRegister">
                                            <div><img :src="signaturemanager" height="28" /></div>
                                            <div class="column">
                                                <div>
                                                    <h5 class="mb-0">
                                                        <small class="text-muted">Signature Manager</small>
                                                    </h5>
                                                </div>
                                                <div class="text-primary">
                                                    <small>Access: All</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end invisible">
                                            <div class="btn-group dropstart">
                                                <i class="bx bx bx-dots-vertical-rounded" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                <ul class="dropdown-menu">
                                                    <!-- Dropdown menu links -->
                                                    <b-dropdown-item class="mb-1">Add Signature</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1">Update Signature</b-dropdown-item>
                                                   
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card" role="button">
                                <div class="card-body p-4">
                                    <div class="d-flex gap-1 justify-content-between">
                                        <div class="d-flex align-items-center pt-2 pb-2 gap-3" @click="downloadMobileApp">
                                            <div><img :src="mobile" height="28" /></div>
                                            <div class="column">
                                                <div>
                                                    <h5 class="mb-0">
                                                        <small class="text-muted">Phone</small>
                                                    </h5>
                                                </div>
                                                <div class="text-primary">
                                                    <small>Access: All</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <div class="btn-group dropstart invisible">
                                                <i class="bx bx bx-dots-vertical-rounded" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                <ul class="dropdown-menu">
                                                 
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card" role="button">
                                <div class="card-body p-4">
                                    <div class="d-flex gap-1 justify-content-between">
                                        <div class="d-flex align-items-center pt-2 pb-2 gap-3" @click="toIctAccess(0)">
                                            <div><img :src="ictIcon" height="28" /></div>
                                            <div class="column">
                                                <div>
                                                    <h5 class="mb-0">
                                                        <small class="text-muted">ICT</small>
                                                    </h5>
                                                </div>
                                                <div class="text-primary">
                                                    <small>Access: All</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <div class="btn-group dropstart">
                                                <i class="bx bx bx-dots-vertical-rounded" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                <ul class="dropdown-menu">
                                                    <!-- Dropdown menu links -->
                                                    <b-dropdown-item class="mb-1" @click="toIctAccess(1)">Vision Access</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="toIctAccess(2)">Service Access</b-dropdown-item>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                     
                    </div>

                    <div class="row">
                        <!-- <div class="col-sm-4">
                            <div class="card" role="button">
                                <div class="card-body p-4">
                                    <div class="d-flex gap-1 justify-content-between">
                                        <div class="d-flex align-items-center pt-2 pb-2 gap-3">
                                            <div><img :src="boq" height="28" /></div>
                                            <div class="column">
                                                <div>
                                                    <h5 class="mb-0">
                                                        <small class="text-muted">Clocking</small>
                                                    </h5>
                                                </div>
                                                <div class="text-primary">
                                                    <small>Access: All</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <div class="btn-group dropstart invisible">
                                                <i class="bx bx bx-dots-vertical-rounded" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                <ul class="dropdown-menu">
                                                    <b-dropdown-item class="mb-1">Sub Menu 1</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1">Sub Menu 2</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1">Sub Menu 3</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1">Sub Menu 4</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1">Sub Menu 5</b-dropdown-item>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <!--  <div class="col-sm-4">
                            <div class="card" role="button">
                                <div class="card-body p-4">
                                    <div class="d-flex gap-1 justify-content-between">
                                        <div class="d-flex align-items-center gap-3" @click="toAccidentReporting">
                                            <div><img :src="travel" height="28" /></div>
                                            <div class="column">
                                                <div>
                                                    <h5 class="mb-0">
                                                        <small class="text-muted">Accident Reporting</small>
                                                    </h5>
                                                </div>
                                                <div class="text-primary">
                                                    <small>Access: All</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <div class="btn-group dropstart invisible">
                                                <i class="bx bx bx-dots-vertical-rounded" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                <ul class="dropdown-menu">
                                                    <b-dropdown-item class="mb-1">Sub Menu 1</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1">Sub Menu 2</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1">Sub Menu 3</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1">Sub Menu 4</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1">Sub Menu 5</b-dropdown-item>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
                <div class="column col-xl-8" v-else>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="card" role="button">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center pt-2 pb-2 gap-3">
                                        <div><i class="bx bx bx-detail p-2" style="font-size: 20px; color: #514cb9; background-color: #928de7a5; border-radius: 50%"></i></div>
                                        <div class="column">
                                            <div>
                                                <h5 class="mb-0">
                                                    <small class="text-muted">Staff Exit</small>
                                                </h5>
                                            </div>
                                            <div class="text-primary">
                                                <small>Recipient: Multiple Departments</small>
                                            </div>
                                            <div class="text-muted">
                                                <small>Are you leaving UNICEF?</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card" role="button" @click="toBSC">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center pt-2 pb-2 gap-3">
                                        <div><i class="bx bx bx-detail p-2" style="font-size: 20px; color: #2c8054; background-color: #8ce4b7; border-radius: 50%"></i></div>

                                        <div class="column">
                                            <div>
                                                <h5 class="mb-0">
                                                    <small class="text-muted">Request Equipment</small>
                                                </h5>
                                            </div>
                                            <div class="text-primary">
                                                <small>Recipient: ICT</small>
                                            </div>
                                            <div class="text-muted">
                                                <small>Do you require a laptop, phone?</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card" role="button">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center pt-2 pb-2 gap-3">
                                        <div><i class="bx bx bx-detail p-2" style="font-size: 20px; color: #5e32af; background-color: #aa8ae6; border-radius: 50%"></i></div>

                                    <div class="column">
                                        <div>
                                            <h5 class="mb-0">
                                                <small class="text-muted">Finance Request </small>
                                            </h5>
                                        </div>
                                        <div class="text-primary">
                                            <small>Recipient: Finance</small>
                                        </div>
                                        <div class="text-muted">
                                            <small>Submit a request to Finance</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="card" role="button">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center pt-2 pb-2 gap-3">
                                    <div><i class="bx bx bx-detail p-2" style="font-size: 20px; color: #eb9f3d; background-color: #fff0dc; border-radius: 50%"></i></div>

                                    <div class="column">
                                        <div>
                                            <h5 class="mb-0">
                                                <small class="text-muted">Vehicle Request</small>
                                            </h5>
                                        </div>
                                        <div class="text-primary">
                                            <small>Recipient: Administration</small>
                                        </div>
                                        <div class="text-muted">
                                            <small>Request UNICEF Vehicle for Private Use?</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card" role="button" @click="toBSC">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center pt-2 pb-2 gap-3">
                                    <div><i class="bx bx bx-detail p-2" style="font-size: 20px; color: #68677b; background-color: #c8c6eb; border-radius: 50%"></i></div>

                                    <div class="column">
                                        <div>
                                            <h5 class="mb-0">
                                                <small class="text-muted">I require the following</small>
                                            </h5>
                                        </div>
                                        <div class="text-primary">
                                            <small>Recipient: Supply</small>
                                        </div>
                                        <div class="text-muted">
                                            <small>For Consumables, Assets, etc </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card" role="button">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center pt-2 pb-2 gap-3">
                                    <div><i class="bx bx bx-detail p-2" style="font-size: 20px; color: #514cb9; background-color: #928de7a5; border-radius: 50%"></i></div>

                                    <div class="column">
                                        <div>
                                            <h5 class="mb-0">
                                                <small class="text-muted">Update Asset Status</small>
                                            </h5>
                                        </div>
                                        <div class="text-primary">
                                            <small>Status: Stolen, Damaged, etc</small>
                                        </div>
                                        <div class="text-muted">
                                            <small>Applied to assets in your custody</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="card" role="button">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center pt-2 pb-2 gap-3">
                                    <div><i class="bx bx bx-detail p-2" style="font-size: 20px; color: #12a358; background-color: #afe4c9; border-radius: 50%"></i></div>

                                    <div class="column">
                                        <div>
                                            <h5 class="mb-0">
                                                <small class="text-muted">Vehicle Registration</small>
                                            </h5>
                                        </div>
                                        <div class="text-primary">
                                            <small>Beneficiary: [ logged in user ]</small>
                                        </div>
                                        <div class="text-muted">
                                            <small>Diplomatic Vehicle Registration?</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card" style="height: 356px !important">
                    <div class="card-body">
                        <div>
                            <h5 class="mb-0">
                                <small class="text-muted">Recent Notifications </small>
                            </h5>
                            <hr class="bg-muted border border-top mt-4 mb-4" />

                            <div v-if="state.currentUser != null">
                                <simplebar style="max-height: 25vh; padding-bottom: 15px" v-if="state.currentUser?.notifications?.length > 0">
                                    <div v-if="state.currentUser?.notifications?.length > 0">
                                        <a class="text-reset notification-item" v-for="notification in state.currentUser?.notifications" :key="`${notification.id}`">
                                            <div class="d-flex">
                                                <img
                                                    v-if="notification?.profile_photo_path == null"
                                                    :src="`https://ui-avatars.com/api/?name=${notification?.submitted_by}?background=cdd7f7&color=7f9cf5`"
                                                    class="me-3 rounded-circle avatar-xs"
                                                    alt="user-pic"
                                                />

                                                    <img v-else :src="`/storage/${notification?.profile_photo_path}`" :alt="'p'" class="me-3 rounded-circle header-profile-user object-fit-cover" />
                                                    <div class="flex-grow-1">
                                                        <a @click.prevent="atClick(notification)" href="/">
                                                            <h6 class="mt-0 mb-1">
                                                                {{ notification?.submitted_by }}
                                                            </h6>
                                                        </a>
                                                        <div class="font-size-12 text-muted">
                                                            <p class="mb-1" v-if="notification?.title">
                                                                <span class="fw-normal" v-html="notification?.title"></span>
                                                            </p>
                                                            <p class="mb-1" v-else-if="notification?.description != null && notification?.title == null">
                                                                <span :style="{ fontWeight: '14px' }" v-html="notification?.description"></span>
                                                            </p>

                                                        <div class="mb-0">
                                                            <small><i class="mdi mdi-clock-outline me-1"></i></small>
                                                            <small>{{ formatDate(notification.created_at) }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </simplebar>
                                <div v-else class="text-center mt-5 mb-5">You have no Notifications</div>
                                <div class="d-flex justify-content-end mb-4" v-if="state.currentUser?.notifications?.length > 0">
                                    <!-- <div role="button"><small class="text-primary">More Notifications</small></div> -->
                                </div>
                            </div>
                            <!-- <div>
                            <div class="d-flex flex-row gap-4 align-items-center">
                                <div class="text-success">
                                    <i class="fas fa-circle"></i>
                                </div>
                                <div class="d-flex flex-column w-100">
                                    <div class="d-flex flex-row justify-content-between">
                                        <div class="text-muted fw-bold">New Permission Granted</div>
                                        <div><small>12 min ago</small></div>
                                    </div>
                                    <div class="col-10 text-truncate"><small>you have been awarded rights </small></div>
                                </div>
                            </div>
                            <hr class="bg-secondary border border-top mt-2 mb-4" />
                        </div>
                        <div>
                            <div class="d-flex flex-row gap-4 align-items-center">
                                <div class="text-success">
                                    <i class="fas fa-circle"></i>
                                </div>
                                <div class="d-flex flex-column w-100">
                                    <div class="d-flex flex-row justify-content-between">
                                        <div class="text-muted fw-bold">New Permission Granted</div>
                                        <div><small>12 min ago</small></div>
                                    </div>
                                    <div class="col-10 text-truncate"><small>you have been awarded rights </small></div>
                                </div>
                            </div>
                            <hr class="bg-secondary border border-top mt-2 mb-4" />
                        </div>
                        <div>
                            <div class="d-flex flex-row gap-4 align-items-center">
                                <div class="text-success">
                                    <i class="fas fa-circle"></i>
                                </div>
                                <div class="d-flex flex-column w-100">
                                    <div class="d-flex flex-row justify-content-between">
                                        <div class="text-muted fw-bold">New Permission Granted</div>
                                        <div><small>12 min ago</small></div>
                                    </div>

                                    <div class="col-10 text-truncate"><small>you have been awarded the rights of a Petty Cash Custodian</small></div>
                                </div>
                            </div>
                            <hr class="bg-secondary border border-top mt-2 mb-4" />
                        </div> -->
                        </div>
                    </div>
                </div>
                <!-- <div class="card">
                        <div class="card-body">
                            <div>
                                <h5 class="mb-0">
                                    <small class="text-muted">Quick Links</small>
                                </h5>
                                <hr class="bg-muted border border-top mt-4 mb-4" />

                                <div
                                    class="text-primary mb-3"
                                    role="button"
                                    @mouseenter="state.quickLink = 'staffOrientation'"
                                    @mouseleave="state.quickLink = ''"
                                    :class="state.quickLink === 'staffOrientation' ? 'fw-medium' : ''"
                                >
                                    Staff Orientation
                                </div>
                                <div
                                    class="text-primary mb-3"
                                    role="button"
                                    @mouseenter="state.quickLink = 'staffExit'"
                                    @mouseleave="state.quickLink = ''"
                                    :class="state.quickLink === 'staffExit' ? 'fw-medium' : ''"
                                >
                                    Staff Exit
                                </div>
                                <div
                                    class="text-primary mb-3"
                                    role="button"
                                    @mouseenter="state.quickLink = 'equipmentRequest'"
                                    @mouseleave="state.quickLink = ''"
                                    :class="state.quickLink === 'equipmentRequest' ? 'fw-medium' : ''"
                                >
                                    Equipment Request
                                </div>
                                <div
                                    class="text-primary mb-3"
                                    role="button"
                                    @mouseenter="state.quickLink = 'requestToSection'"
                                    @mouseleave="state.quickLink = ''"
                                    :class="state.quickLink === 'requestToSection' ? 'fw-medium' : ''"
                                >
                                    Submit Request To Section
                                </div>
                                <div
                                    class="text-primary mb-3"
                                    role="button"
                                    @mouseenter="state.quickLink = 'vehicleRequest'"
                                    @mouseleave="state.quickLink = ''"
                                    :class="state.quickLink === 'vehicleRequest' ? 'fw-medium' : ''"
                                >
                                    Request UNICEF Vehicle
                                </div>
                                <div
                                    class="text-primary mb-3"
                                    role="button"
                                    @mouseenter="state.quickLink = 'consultant'"
                                    @mouseleave="state.quickLink = ''"
                                    :class="state.quickLink === 'consultant' ? 'fw-medium' : ''"
                                >
                                    Consultant Recruitment
                                </div>
                                <div
                                    class="text-primary mb-4"
                                    role="button"
                                    @mouseenter="state.quickLink = 'diplomaticVehicleReg'"
                                    @mouseleave="state.quickLink = ''"
                                    :class="state.quickLink === 'diplomaticVehicleReg' ? 'fw-medium' : ''"
                                    @click="toVehicleRegistration"
                                >
                                    Diplomatic Vehicle Registration
                                </div>
                            </div>
                        </div>
                    </div> -->
            </div>
        </div>
        <!-- signature register modal -->
        <div v-if="state.currentUser != null">
            <SignatureRegister v-if="state.isUserAuthorised" @closed-modal="checkUserProfile" />
        </div>
    </Layout>
    <requestVehicleVue :requestVehicleForPrivateUse="requestVehicleVueModal" :session="props.user" @close-request-vehicle-modal="closeRequestVehicleVueModal" />
    <getMobileAppModalVue  :options="{page:'dashboard'}" v-model:downloadMobileEvent="downloadMobileEvent" :downloadMobileAppModal="downloadMobileAppModal" />

    <single-input class="d-none" ref="singleInputID" />

    <b-modal v-model="state.open_incomplete_profile_modal" no-close-on-backdrop centered hide-header @ok="toMyProfile">
        <div class="text-center">
            <span class="bx bx-info-circle text-info" style="font-size: 80px"></span>
            <h5>Incomplete Profile Data</h5>
            <div class="font-size-13">
                <p>Your profile has some missing information which may affect your application. We recommend you first update your profile before proceeding.</p>
            </div>
        </div>
        <template #cancel>
            <button class="btn btn-danger" @click.prevent="proceedWithApp">Remind Me Later</button>
        </template>
        <template #ok>
            <button class="btn btn-success" @click.prevent="toMyProfile">Update Profile</button>
        </template>
    </b-modal>

    <b-modal v-model="state.open_password_expired_modal" no-close-on-backdrop centered hide-header @ok="toMyProfile" hide-footer>
        <div class="text-center">
            <img src="/images/icon.lock.png" style="width: 80px" alt="" />
            <h5>Change Password</h5>
            <div class="font-size-13 mt-3">
                <p>
                    Your password has expired. <br />
                    Kindly click the button below to change it
                </p>
            </div>
            <div class="text-center mt-3">
                <button class="btn btn-success" @click.prevent="toMyProfile('#password')">Change Password</button>
            </div>
        </div>
    </b-modal>
</template>
<style lang="scss" scoped>
.card-height {
    height: 100px;
}
</style>
