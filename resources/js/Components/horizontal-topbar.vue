<script>
import simplebar from "simplebar-vue";

import logoDarkLg from "../../images/logo-dark.svg";
import logoDarkSm from "../../images/logo.svg";
import logoLightLg from "../../images/logo-dark.svg";
import logoLightSm from "../../images/logo-light.svg";

import avatar1 from "../../images/users/avatar-1.jpg";
import avatar3 from "../../images/users/avatar-3.jpg";
import avatar4 from "../../images/users/avatar-4.jpg";

import megamenu from "../../images/logo-dark.svg";

import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { BscNavigator } from "../Composables/BscNavigator.js";
import { BscRequestsNavigator } from "../Composables/BscRequestsNavigator.js";
import { LeaveNavigator } from "../Composables/LeaveNavigator.js";
import { LowValueNavigator } from "../Composables/LowValueNavigator.js";
import { SupplyPlanNavigator } from "../Composables/SupplyPlanNavigator.js";
import { HrNavigator } from "../Composables/HrNavigator.js";
import { notify } from "@/mixins/notify";
import {
    AccidentReportingNavigator,
    diplomaticVehicleRegistration,
    officeSupplies,
    goToAssetSubModule,
    vehicleFuelManagement,
    vehiclePrivateUse,
    administrationSettings,
    vehicleUtilization,
    vehicleMaintenance,
    carQueueManagement,
    AirtimeDataNavigator,
    visitortrackerNavigator,
    IncidentReportingNavigator,
    GuestHouseNavigator,
} from "../Composables/AdminNavigator.js";
import { FinanceInvoiceNavitor } from "../Composables/FinanceInvoiceNavitor.js";
import { TravelPlannerNavigator } from "../Composables/TravelPlannerNavigator.js";
import { IctNavigator } from "../Composables/IctNavigator.js";

import axios from "axios";
import Swal from "sweetalert2";
import moment from "moment";
import { useStore } from "vuex";

export default {
    setup() {
        return {
            BscNavigator,
            LeaveNavigator,
            AccidentReportingNavigator,
            diplomaticVehicleRegistration,
            LowValueNavigator,
            SupplyPlanNavigator,
            officeSupplies,
            HrNavigator,
            goToAssetSubModule,
            BscRequestsNavigator,
            router,
            notify,
            vehicleFuelManagement,
            vehiclePrivateUse,
            administrationSettings,
            vehicleUtilization,
            vehicleMaintenance,
            carQueueManagement,
            FinanceInvoiceNavitor,
            AirtimeDataNavigator,
            visitortrackerNavigator,
            TravelPlannerNavigator,
            IncidentReportingNavigator,
            GuestHouseNavigator,
            IctNavigator,
        };
    },
    components: {
        simplebar,
        Link,
    },

    data() {
        return {
            logoDarkLg,
            logoDarkSm,
            logoLightLg,
            logoLightSm,
            avatar1,
            avatar3,
            avatar4,
            megamenu,
            lan: this.$i18n.locale,
            text: null,
            flag: null,
            value: null,
            currentUser: {},
            currentLink: "",
            searchWithIn: "Users",
            searchText: "",
        };
    },

    //using vuex
    computed: {
        userDetails: function () {
            const store = useStore();
            return store.getters["LoggedInUser/getUserDetails"];
        },
        allRoles: function () {
            const allUserRoles = usePage().props.auth.user.roles.map((role) => role.name);
            return allUserRoles;
        },
        allPermissions: function () {
            const allPermissions = usePage().props.auth.user.allPermissions;
            return allPermissions;
        },
        dashboardView() {
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
        },
    },
    mounted() {
        this.currentUser = usePage().props.auth.user;
    },
    methods: {
        doSearch() {

            const app = this;
            if (app.searchText == "" || app.searchText == null) {
                router.visit("/dashboard");
            } else {
                router.visit(
                    "/search",
                    {
                        method: "get",
                        data: {
                            searchWithIn: app.searchWithIn,
                            searchText: app.searchText,
                        },
                        replace: false,
                        preserveState: true,
                        preserveScroll: true,
                    },

                );
            }
        },
        async goToFinanceModule(system_index) {
            const systems = [import.meta.env.VITE_BSC_APP_URL, import.meta.env.VITE_FINANCE_APP_URL];
            const url = systems[system_index];

            const token = attrs.auth.user.api_token;
            const authHeaders = {
                auth_token: props.user.token,
                Authorization: `Bearer ${token}`,
            };

            return (window.location.href = `${url}/loginFromMain?token=${props.user.token}`);
        },
        logout() {
            // router.post(route("logout"));
            router.visit("/logout", {
                method: "post",
            });
        },
        toStaffOnboarding() {
            const staffOnboardingUrl = import.meta.env.VITE_STAFFONBOARDING_URL;
            const userToken = usePage().props.userToken;

            if (userToken != null) {
                Swal.fire({
                    html: `<p style='fontsize:16px'>Loading ...</p>`,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    allowOutsideClick: false,
                });
                return (window.location.href = `${staffOnboardingUrl}/loginFromMain?token=${userToken}`);
            } else {
                notify.toastErrorMessage("Invalid Session");
            }
        },
        toBSC() {
            const authHeaders = {
                auth_token: this.userDetails.token,
            };
            const BSCURL = import.meta.env.VITE_BSC_APP_URL;

            axios
                .get(`${BSCURL}/api/loginFromMain`, { headers: authHeaders })
                .then((res) => {
                    if (res.status == 200) {
                        window.location.href = BSCURL;
                    }
                })
                .catch((e) => {
                    console.log(e);
                });
        },
        toggleRightSidebar() {
            this.$parent.toggleRightSidebar();
        },
        toOfficeSupplies() {
            const officeUrl = import.meta.env.VITE_OFFICESUPPLIES_URL;
            const userToken = usePage().props.userToken;

            if (userToken != null) {
                Swal.fire({
                    html: `<p style='fontsize:16px'>Loading ...</p>`,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    allowOutsideClick: false,
                });
                return (window.location.href = `${officeUrl}/loginFromMain?token=${userToken}`);
            } else {
                notify.toastErrorMessage("Invalid Session");
            }
        },
        toggleMenu() {
            let element = document.getElementById("topnav-menu-content");
            element.classList.toggle("show");
        },
        initFullScreen() {
            document.body.classList.toggle("fullscreen-enable");
            if (!document.fullscreenElement && /* alternative standard method */ !document.mozFullScreenElement && !document.webkitFullscreenElement) {
                // current working methods
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                } else if (document.documentElement.mozRequestFullScreen) {
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullscreen) {
                    document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                }
            } else {
                if (document.cancelFullScreen) {
                    document.cancelFullScreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitCancelFullScreen) {
                    document.webkitCancelFullScreen();
                }
            }
        },
        setLanguage(locale, country, flag) {
            this.lan = locale;
            this.text = country;
            this.flag = flag;
            this.$i18n.locale = locale;
            localStorage.setItem("locale", locale);
        },
        toVehicleMaintenance() {
            const VHCL_MAIN_URL = import.meta.env.VITE_ADMIN_VEHICLE_MAINTENANCE_URL;
            showLoader("Applying user session ...");
            return (window.location.href = `${VHCL_MAIN_URL}/loginFromMain?token=${this.currentUser.token}`);
        },
        toDIM() {
            const URL = import.meta.env.VITE_ADMIN_DIM;
            Swal.fire({
                html: `<p style='font-size:16px'>Loading...</p>`,
                didOpen: () => {
                    Swal.showLoading();
                },
                allowOutsideClick: false,
            });
            return (window.location.href = `${URL}/loginFromMain?token=${this.currentUser.token}`);
        },
        formatDate(theDate) {
            return moment(theDate).fromNow();
        },
    },
};
</script>

<template>
    <header id="page-topbar">
        <div class="navbar-header" style="background-color: #0160a0">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box">
                    <Link :href="route('dashboard')" class="logo logo-dark">
                    <span class="logo-sm">
                        <img :src="logoDarkSm" alt height="22" />
                    </span>
                    <span class="logo-lg">
                        <img :src="logoDarkLg" alt height="17" />
                    </span>
                    </Link>

                    <Link :href="route('dashboard')" class="logo logo-light">
                    <span class="logo-sm">
                        <img :src="logoLightSm" alt height="22" />
                    </span>
                    <span class="logo-lg">
                        <img :src="logoLightLg" alt height="19" />
                    </span>
                    </Link>
                </div>

                <button id="toggle" type="button" class="btn btn-sm me-2 font-size-16 d-lg-none header-item"
                    @click="toggleMenu">
                    <i class="fa fa-fw fa-bars"></i>
                </button>

                <!-- App Search-->
                <form class="d-flex align-items-center">
                    <div class="rounded-pill d-none d-lg-block"
                        style="background-color: #126ba7; border: 0px; color: #c6c6ca">
                        <div class="input-group d-flex flex-row align-items-center">
                            <div class="mt-1">
                                <span class="" style="font-size: medium"><i class="bx bx-search-alt"></i></span>
                            </div>
                            <div>
                                <input type="text" class="form-control" :placeholder="$t('navbar.search.text')"
                                    style="background-color: #126ba7; border: 0px; color: #c6c6ca; font-size: 13px"
                                    v-model="searchText" @input="doSearch" />
                            </div>

                            <div class="hstack gap-3" role="button">
                                <div class="vr" style="color: #dbdbdb"></div>
                                <div class="btn-group dropbottomstart">
                                    <div data-bs-toggle="dropdown" class="d-flex flex-row gap-3">
                                        <div class="">{{ searchWithIn }}</div>
                                        <div>
                                            <i class="mdi mdi-chevron-down" aria-expanded="false"></i>
                                        </div>
                                    </div>

                                    <ul class="dropdown-menu">
                                        <b-dropdown-item class="mb-1"
                                            @click="searchWithIn = 'Users'">Users</b-dropdown-item>
                                        <b-dropdown-item class="mb-1"
                                            @click="searchWithIn = 'Hospitals'">Hospitals</b-dropdown-item>
                                        <b-dropdown-item class="mb-1"
                                            @click="searchWithIn = 'Appointments'">Appointments</b-dropdown-item>
                                        <b-dropdown-item class="mb-1"
                                            @click="searchWithIn = 'Referals'">Referals</b-dropdown-item>
                                        <b-dropdown-item class="mb-1" @click="searchWithIn = 'Health Records'">Health
                                            Records</b-dropdown-item>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>


            </div>

            <div class="d-flex" style="color: #f3f3f9">
                <b-dropdown class="d-inline-block d-lg-none ml-2" variant="black"
                    menu-class="dropdown-menu-lg p-0 dropdown-menu-end" toggle-class="header-item noti-icon" right>
                    <template v-slot:button-content>
                        <i class="mdi mdi-magnify"></i>
                    </template>

                    <form class="p-3">
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search ..."
                                    aria-label="Recipient's username" />
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="mdi mdi-magnify"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </b-dropdown>

                <!-- <b-dropdown variant="white" right toggle-class="header-item">
                    <template v-slot:button-content>
                        <img class :src="flag" alt="Header Language" height="16" />
                        {{ text }}
                    </template>
                    <b-dropdown-item
                        class="notify-item"
                        v-for="(entry, i) in languages"
                        :key="`Lang${i}`"
                        :value="entry"
                        @click="setLanguage(entry.language, entry.title, entry.flag)"
                        :class="{ active: lan === entry.language }"
                    >
                        <img :src="`${entry.flag}`" alt="user-image" class="me-1" height="12" />
                        <span class="align-middle">{{ entry.title }}</span>
                    </b-dropdown-item>
                </b-dropdown> -->

                <!-- <b-dropdown class="d-none d-lg-inline-block noti-icon" menu-class="dropdown-menu-lg dropdown-menu-end" right toggle-class="header-item" variant="black">
                    <template v-slot:button-content>
                        <i class="bx bx-customize"></i>
                    </template>

                    <div class="px-lg-2">
                        <div class="row no-gutters">
                            <div class="col">
                                <a class="dropdown-icon-item" href="javascript: void(0);">
                                    <img :src="github" alt="Github" />
                                    <span>{{ $t("navbar.dropdown.site.list.github") }}</span>
                                </a>
                            </div>
                            <div class="col">
                                <a class="dropdown-icon-item" href="javascript: void(0);">
                                    <img :src="bitbucket" alt="bitbucket" />
                                    <span>{{ $t("navbar.dropdown.site.list.github") }}</span>
                                </a>
                            </div>
                            <div class="col">
                                <a class="dropdown-icon-item" href="javascript: void(0);">
                                    <img :src="dribbble" alt="dribbble" />
                                    <span>{{ $t("navbar.dropdown.site.list.dribbble") }}</span>
                                </a>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col">
                                <a class="dropdown-icon-item" href="javascript: void(0);">
                                    <img :src="dropbox" alt="dropbox" />
                                    <span>{{ $t("navbar.dropdown.site.list.dropbox") }}</span>
                                </a>
                            </div>
                            <div class="col">
                                <a class="dropdown-icon-item" href="javascript: void(0);">
                                    <img :src="mail_chimp" alt="mail_chimp" />
                                    <span>{{ $t("navbar.dropdown.site.list.mailchimp") }}</span>
                                </a>
                            </div>
                            <div class="col">
                                <a class="dropdown-icon-item" href="javascript: void(0);">
                                    <img :src="slack" alt="slack" />
                                    <span>{{ $t("navbar.dropdown.site.list.slack") }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </b-dropdown> -->

                <div class="dropdown d-none d-lg-inline-block ml-1">
                    <button type="button" class="btn header-item noti-icon" @click="initFullScreen">
                        <i class="bx bx-fullscreen"></i>
                    </button>
                </div>

                <b-dropdown right menu-class="dropdown-menu-lg p-0 dropdown-menu-end"
                    toggle-class="header-item noti-icon" variant="black">
                    <template v-slot:button-content>
                        <i class="bx bx-bell"></i>
                        <span class="badge bg-danger rounded-pill" v-if="currentUser.notifications == null">0</span>
                        <span class="badge bg-danger rounded-pill"
                            v-else-if="currentUser.notifications.length > 99">99+</span>
                        <span class="badge bg-danger rounded-pill" v-else>{{ currentUser.notifications.length }}</span>
                    </template>
                    <div v-if="currentUser.notifications != null">
                        <div class="p-3">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0">
                                        {{ $t("navbar.dropdown.notification.text") }}
                                    </h6>
                                </div>
                                <div class="col-auto" v-if="currentUser.notifications.length > 0">
                                    <a href="#" class="small">Mark All as Read</a>
                                </div>
                            </div>
                        </div>
                        <simplebar style="max-height: 230px" v-if="currentUser.notifications.length > 0">
                            <div v-if="currentUser.notifications.length > 0">
                                <a class="text-reset notification-item"
                                    v-for="notification in currentUser.notifications" :key="`${notification.id}`">
                                    <div class="d-flex">
                                        <img v-if="notification?.profile_photo_path == null"
                                            :src="`https://ui-avatars.com/api/?name=${notification?.submitted_by}?background=cdd7f7&color=7f9cf5`"
                                            class="me-3 rounded-circle avatar-xs" alt="user-pic" />

                                        <img v-else :src="`/storage/${notification?.profile_photo_path}`" :alt="'p'"
                                            class="me-3 rounded-circle header-profile-user object-fit-cover" />
                                        <div class="flex-grow-1">
                                            <h6 class="mt-0 mb-1">
                                                {{ notification?.submitted_by }}
                                            </h6>
                                            <div class="font-size-12 text-muted">
                                                <p class="mb-1">
                                                    {{ notification?.title }}
                                                </p>
                                                <p class="mb-0">
                                                    <i class="mdi mdi-clock-outline"></i>
                                                    {{ formatDate(notification.created_at) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </simplebar>
                        <div v-else class="text-center mt-5 mb-5">You have no Notifications</div>
                        <div class="p-2 border-top d-grid" v-if="currentUser.notifications.length > 0">
                            <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
                                <i class="mdi mdi-arrow-down-circle me-1"></i>
                                <span key="t-view-more">
                                    {{ $t("navbar.dropdown.notification.button") }}
                                </span>
                            </a>
                        </div>
                    </div>
                </b-dropdown>

                <b-dropdown right variant="black" toggle-class="header-item" menu-class="dropdown-menu-end">
                    <template v-slot:button-content>
                        <!-- <img class="rounded-circle header-profile-user" :src="avatar1" alt="Header Avatar" /> -->
                        <img :src="currentUser.profile_photo_url" :alt="'p'"
                            class="rounded-circle header-profile-user object-fit-cover" />

                        <span class="d-none d-xl-inline-block ms-1" style="color: #f3f3f9">{{ currentUser.name }}</span>
                        <!-- <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i> -->
                    </template>
                    <!-- item-->
                    <Link :href="route('profile.show')">
                    <b-dropdown-item>
                        <i class="bx bx-user font-size-16 align-middle me-1 p-1"
                            style="color: #639099; background-color: #e0e8f1; border-radius: 50%"></i>

                        My Profile
                        <!-- {{ $t("navbar.dropdown.henry.list.profile") }} -->
                    </b-dropdown-item>
                    </Link>
                    <Link :href="route('AllLogs')">

                    </Link>
                    <b-dropdown-item href="javascript: void(0);">
                        <i class="bx bx bxs-report font-size-16 align-middle me-1 p-1"
                            style="color: #639099; background-color: #e0e8f1; border-radius: 50%"></i>
                        Reports
                        <!-- {{ $t("navbar.dropdown.henry.list.mywallet") }} -->
                    </b-dropdown-item>
                    <Link v-if="allPermissions.includes('s_admin')"
                        :href="route('supportCenter', {}, { preserveState: false, preserveScroll: false })">
                    <b-dropdown-item href="javascript: void(0);">
                        <i class="bx bx bxs-help-circle font-size-16 align-middle me-1 p-1"
                            style="color: #639099; background-color: #e0e8f1; border-radius: 50%"></i>
                        Support Centers
                    </b-dropdown-item>
                    </Link>
                    <Link v-else :href="route('supportRequest', {}, { preserveState: false, preserveScroll: false })">
                    <b-dropdown-item href="javascript: void(0);">
                        <i class="bx bx bxs-help-circle font-size-16 align-middle me-1 p-1"
                            style="color: #639099; background-color: #e0e8f1; border-radius: 50%"></i>
                        Support
                    </b-dropdown-item>
                    </Link>
                    <!-- <Link :href="route(`${allRoles.includes('Super Admin')?'supportCenter':'supportRequest'}`,)">
                     <b-dropdown-item href="javascript: void(0);">
                        <i class="bx bx bxs-help-circle font-size-16 align-middle me-1 p-1" style="color: #639099; background-color: #E0E8F1; border-radius: 50%;"></i>
                        {{ allRoles.includes('Super Admin')?'Support Center': 'Support' }}
                    </b-dropdown-item>
                    </Link> -->

                    <template v-if="allPermissions.includes('s_admin')">
                        <b-dropdown-item href="/pulse" target="_blank">
                            <i class="bx bx-health font-size-16 align-middle me-1 p-1"
                                style="color: #639099; background-color: #e0e8f1; border-radius: 50%"></i>
                            System Health
                        </b-dropdown-item>
                    </template>

                    
                    <b-dropdown-divider></b-dropdown-divider>
                    <a @click="logout" class="dropdown-item text-danger" role="button">
                        <i class="mdi mdi-login-variant font-size-16 align-middle me-1 text-danger p-1"></i>
                        {{ $t("navbar.dropdown.henry.list.logout") }}
                    </a>
                </b-dropdown>

                <div class="dropdown d-inline-block">
                    <button type="button" class="btn header-item noti-icon right-bar-toggle toggle-right"
                        @click="toggleRightSidebar">
                        <i class="bx bx-cog toggle-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>
</template>
