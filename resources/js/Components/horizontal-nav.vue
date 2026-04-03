<script>
import { menuItems } from "./horizontal-menu";
import megamenu from "../../images/logo-dark.svg";
import { BscNavigator } from "../Composables/BscNavigator.js";
import { BscRequestsNavigator } from "../Composables/BscRequestsNavigator.js";
import { LeaveNavigator } from "../Composables/LeaveNavigator.js";
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
} from "../Composables/AdminNavigator.js";
import { HrNavigator } from "../Composables/HrNavigator.js";
import { SupplyPlanNavigator } from "../Composables/SupplyPlanNavigator.js";
import { LowValueNavigator } from "../Composables/LowValueNavigator.js";
import { showLoader } from "@/mixins/helpers";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { notify } from "@/mixins/notify";
import { FinanceInvoiceNavitor } from "../Composables/FinanceInvoiceNavitor.js";
import { TravelPlannerNavigator } from "../Composables/TravelPlannerNavigator.js";

import { useStore } from "vuex";

export default {
    setup() {
        return {
            BscNavigator,
            LeaveNavigator,
            AccidentReportingNavigator,
            diplomaticVehicleRegistration,
            officeSupplies,
            SupplyPlanNavigator,
            LowValueNavigator,
            BscRequestsNavigator,
            HrNavigator,
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
        };
    },
    data() {
        return {
            menuItems: menuItems,
            megamenu,
            currentLink: "",
            currentUser: {},
        };
    },
    computed: {
        userDetails: function () {
            const store = useStore();
            return store.getters["LoggedInUser/getUserDetails"];
        },
    },
    mounted() {
        this.currentUser = usePage().props.auth.user;
        var links = document.getElementsByClassName("side-nav-link-ref");
        var matchingMenuItem = null;
        for (var i = 0; i < links.length; i++) {
            if (window.location.pathname === links[i].pathname) {
                matchingMenuItem = links[i];
                break;
            }
        }

        if (matchingMenuItem) {
            matchingMenuItem.classList.add("active");
            var parent = matchingMenuItem.parentElement;

            /**
             * TODO: This is hard coded way of expading/activating parent menu dropdown and working till level 3.
             * We should come up with non hard coded approach
             */
            if (parent) {
                parent.classList.add("active");
                const parent2 = parent.parentElement;
                if (parent2) {
                    parent2.classList.add("active");
                }
                const parent3 = parent2.parentElement;
                if (parent3) {
                    parent3.classList.add("active");
                    var childAnchor = parent3.querySelector(".has-dropdown");
                    if (childAnchor) childAnchor.classList.add("active");
                }

                const parent4 = parent3.parentElement;
                if (parent4) parent4.classList.add("active");
                const parent5 = parent4.parentElement;
                if (parent5) parent5.classList.add("active");
            }
        }
    },
    methods: {
        /**
         * Menu clicked show the submenu
         */
        onMenuClick(event) {
            event.preventDefault();
            const nextEl = event.target.nextElementSibling;
            if (nextEl && !nextEl.classList.contains("show")) {
                const parentEl = event.target.parentNode;
                if (parentEl) {
                    parentEl.classList.remove("show");
                }
                nextEl.classList.add("show");
            } else if (nextEl) {
                nextEl.classList.remove("show");
            }
            return false;
        },
        /**
         * Returns true or false if given menu item has child or not
         * @param item menuItem
         */
        hasItems(item) {
            return item.subItems !== undefined ? item.subItems.length > 0 : false;
        },
        topbarLight() {
            document.body.setAttribute("data-topbar", "light");
            document.body.removeAttribute("data-layout-size", "boxed");
        },
        boxedWidth() {
            document.body.setAttribute("data-layout-size", "boxed");
            document.body.removeAttribute("data-topbar", "light");
            document.body.setAttribute("data-topbar", "dark");
        },
        coloredHeader() {
            document.body.setAttribute("data-topbar", "colored");
            document.body.removeAttribute("data-layout-size", "boxed");
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
        toVehicleUtilization() {
            const VHCL_UTIL_URL = import.meta.env.VITE_ADMIN_VEHICLE_UTILIZATION_URL;
            showLoader("Applying user session ...");
            return (window.location.href = `${VHCL_UTIL_URL}/loginFromMain?token=${this.currentUser.token}`);
        },
        toFuelManagement() {
            const FUEL_MGMT_URL = import.meta.env.VITE_ADMIN_FUEL_MANAGEMENT_URL;
            showLoader("Applying user session ...");
            return (window.location.href = `${FUEL_MGMT_URL}/loginFromMain?token=${this.currentUser.token}`);
        },
        toVehicleMaintenance() {
            const VHCL_MAIN_URL = import.meta.env.VITE_ADMIN_VEHICLE_MAINTENANCE_URL;
            showLoader("Applying user session ...");
            return (window.location.href = `${VHCL_MAIN_URL}/loginFromMain?token=${this.currentUser.token}`);
        },
    },
};
</script>

<template>
    <div class="topnav" style="background-color: #fff">
        <div class="container-fluid">
            <nav class="navbar navbar-light navbar-expand-lg topnav-menu">
                <div id="topnav-menu-content" class="collapse navbar-collapse">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link" @click="onMenuClick($event)" href="javascript: void(0);"
                                id="topnav-components" role="button">
                                <i :class="`bx bx-home-circle mr-1`"></i>
                                Quick Links
                                <i class="mdi mdi-chevron-down"></i>
                            </a>
                            <div class="dropdown-menu dropdown-mega-menu-xl p-4" aria-labelledby="topnav-dashboard">
                                <div class="d-flex flex-row mb-5 jutify-content-center">
                                    <div class="col-sm-8">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="mb-5">
                                                    <h5 class="font-size-14 mt-0 fw-bold">
                                                        Profile Management
                                                        <!-- {{ $t("navbar.dropdown.megamenu.uicontent.title") }} -->
                                                    </h5>
                                                    <ul class="list-unstyled megamenu-list">
                                                        <li>
                                                            <div role="button" @mouseenter="currentLink = 'invoices'"
                                                                @mouseleave="currentLink = ''"
                                                                :class="currentLink === 'invoices' ? 'text-secondary' : ''"
                                                                @click="FinanceInvoiceNavitor(1)">
                                                                See User Details
                                                            </div>
                                                        </li>

                                                        <li>
                                                            <div role="button" @mouseenter="currentLink = 'bscRequests'"
                                                                @mouseleave="currentLink = ''"
                                                                :class="currentLink === 'bscRequests' ? 'text-secondary' : ''"
                                                                @click="BscRequestsNavigator('face-forms')">
                                                                Edit User Details
                                                            </div>
                                                        </li>

                                                        <li>
                                                            <div role="button" @mouseenter="currentLink = 'bscRequests'"
                                                                @mouseleave="currentLink = ''"
                                                                :class="currentLink === 'bscRequests' ? 'text-secondary' : ''"
                                                                @click="BscRequestsNavigator('myRequests')">
                                                                Log in As User
                                                            </div>
                                                        </li>

                                                        <li>
                                                            <div role="button" @mouseenter="currentLink = 'settings'"
                                                                @mouseleave="currentLink = ''"
                                                                :class="currentLink === 'settings' ? 'text-secondary' : ''"
                                                                @click="BscRequestsNavigator('settings')">
                                                                Create User Roles & Permissions
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div role="button" @mouseenter="currentLink = 'settings'"
                                                                @mouseleave="currentLink = ''"
                                                                :class="currentLink === 'settings' ? 'text-secondary' : ''"
                                                                @click="BscRequestsNavigator('settings')">
                                                                Assign User Roles & Permissions
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div role="button" @mouseenter="currentLink = 'settings'"
                                                                @mouseleave="currentLink = ''"
                                                                :class="currentLink === 'settings' ? 'text-secondary' : ''"
                                                                @click="BscRequestsNavigator('settings')">
                                                                Revoke User Roles & Permissions
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>

                                            </div>

                                            <div class="col-md-3">
                                                <h5 class="font-size-14 mt-0 fw-bold">
                                                    Request Medication
                                                    <!-- {{ $t("navbar.dropdown.megamenu.application.title") }} -->
                                                </h5>
                                                <ul class="list-unstyled megamenu-list">
                                                    <li @click="vehiclePrivateUse()">
                                                        <div role="button"
                                                            @mouseenter="currentLink = 'privateUnicefVehicle'"
                                                            @mouseleave="currentLink = ''"
                                                            :class="currentLink === 'privateUnicefVehicle' ? 'text-secondary' : ''">
                                                            Request ARVs Medication Refill 
                                                        </div>
                                                    </li>
                                                    <li @click="toVehicleMaintenance()">
                                                        <div role="button"
                                                            @mouseenter="currentLink = 'vehicleMaintenance'"
                                                            @mouseleave="currentLink = ''"
                                                            :class="currentLink === 'vehicleMaintenance' ? 'text-secondary' : ''">
                                                            Select Medication Type
                                                        </div>
                                                    </li>
                                                    <li @click="toVehicleUtilization()">
                                                        <div role="button"
                                                            @mouseenter="currentLink = 'vehicleUtilization'"
                                                            @mouseleave="currentLink = ''"
                                                            :class="currentLink === 'vehicleUtilization' ? 'text-secondary' : ''">
                                                            Delivery Request
                                                        </div>
                                                    </li>
                                                    <li @click="vehicleFuelManagement()">
                                                        <div role="button" @mouseenter="currentLink = 'fuelManagement'"
                                                            @mouseleave="currentLink = ''"
                                                            :class="currentLink === 'fuelManagement' ? 'text-secondary' : ''">
                                                            Review previous medication request
                                                        </div>
                                                    </li>
                                                  
                                                </ul>
                                            </div>

                                            <div class="col-md-3">
                                                <h5 class="font-size-14 mt-0 fw-bold">
                                                    Health Records
                                                    <!-- {{ $t("navbar.dropdown.megamenu.extrapages.title") }} -->
                                                </h5>
                                                <ul class="list-unstyled megamenu-list">
                                                    <li>
                                                        <div role="button" @mouseenter="currentLink = 'staffManagement'"
                                                            @mouseleave="currentLink = ''"
                                                            :class="currentLink === 'staffManagement' ? 'text-secondary' : ''"
                                                            @click="HrNavigator('staff')">
                                                            Patient Medical History
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div role="button"
                                                            @mouseenter="currentLink = 'staffOrientation'"
                                                            @mouseleave="currentLink = ''"
                                                            :class="currentLink === 'staffOrientation' ? 'text-secondary' : ''"
                                                            @click="HrNavigator('staff-orientation')">
                                                            HIV Viral Load Results
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div role="button" @mouseenter="currentLink = 'staffOnboarding'"
                                                            @mouseleave="currentLink = ''"
                                                            :class="currentLink === 'staffOnboarding' ? 'text-secondary' : ''"
                                                            @click="toStaffOnboarding">
                                                            Lab test Results
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div role="button" @mouseenter="currentLink = 'tors'"
                                                            @mouseleave="currentLink = ''"
                                                            :class="currentLink === 'tors' ? 'text-secondary' : ''"
                                                            @click="HrNavigator('consultant-tor')">
                                                            Doctor Notes
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div role="button" @mouseenter="currentLink = 'staffExit'"
                                                            @mouseleave="currentLink = ''"
                                                            :class="currentLink === 'staffExit' ? 'text-secondary' : ''"
                                                            @click="HrNavigator('staff-exit')">
                                                            Treatment History
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div role="button" @mouseenter="currentLink = 'staffHeadcount'"
                                                            @mouseleave="currentLink = ''"
                                                            :class="currentLink === 'staffHeadcount' ? 'text-secondary' : ''"
                                                            @click="HrNavigator('radio-call')">
                                                            Upload/Download Medical Document
                                                        </div>
                                                    </li>
                                                   
                                                </ul>
                                            </div>
                                            <div class="col-md-3">
                                                <h5 class="font-size-14 mt-0 fw-bold">
                                                    My Appointments
                                                    <!-- {{ $t("navbar.dropdown.megamenu.extrapages.title") }} -->
                                                </h5>
                                                <ul class="list-unstyled megamenu-list">
                                                    <li>
                                                        <div role="button" @mouseenter="currentLink = 'staffManagement'"
                                                            @mouseleave="currentLink = ''"
                                                            :class="currentLink === 'staffManagement' ? 'text-secondary' : ''"
                                                            @click="HrNavigator('staff')">
                                                            List of Upcoming appointments
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div role="button"
                                                            @mouseenter="currentLink = 'staffOrientation'"
                                                            @mouseleave="currentLink = ''"
                                                            :class="currentLink === 'staffOrientation' ? 'text-secondary' : ''"
                                                            @click="HrNavigator('staff-orientation')">
                                                            Appointment History
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div role="button" @mouseenter="currentLink = 'staffOnboarding'"
                                                            @mouseleave="currentLink = ''"
                                                            :class="currentLink === 'staffOnboarding' ? 'text-secondary' : ''"
                                                            @click="toStaffOnboarding">
                                                            Appointment Status
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div role="button" @mouseenter="currentLink = 'tors'"
                                                            @mouseleave="currentLink = ''"
                                                            :class="currentLink === 'tors' ? 'text-secondary' : ''"
                                                            @click="HrNavigator('consultant-tor')">
                                                            Doctor Assigned
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div role="button" @mouseenter="currentLink = 'staffExit'"
                                                            @mouseleave="currentLink = ''"
                                                            :class="currentLink === 'staffExit' ? 'text-secondary' : ''"
                                                            @click="HrNavigator('staff-exit')">
                                                            Clinic Location
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div role="button" @mouseenter="currentLink = 'staffHeadcount'"
                                                            @mouseleave="currentLink = ''"
                                                            :class="currentLink === 'staffHeadcount' ? 'text-secondary' : ''"
                                                            @click="HrNavigator('radio-call')">
                                                            Cancel Appointment
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div role="button" @mouseenter="currentLink = 'Hrsettings'"
                                                            @mouseleave="currentLink = ''"
                                                            :class="currentLink === 'Hrsettings' ? 'text-secondary' : ''"
                                                            @click="HrNavigator('settings')">
                                                            Schedule Appointments
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-5">
                                                    <h5 class="font-size-14 mt-0 fw-bold">
                                                        Help Crenter
                                                        <!-- {{ $t("navbar.dropdown.megamenu.uicontent.title") }} -->
                                                    </h5>
                                                    <ul class="list-unstyled megamenu-list">
                                                        <li>
                                                            <div role="button" @mouseenter="currentLink = 'lowValue'"
                                                                @mouseleave="currentLink = ''"
                                                                :class="currentLink === 'lowValue' ? 'text-secondary' : ''"
                                                                @click="LowValueNavigator('home')">
                                                                Talk to a Doctor
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div role="button" @mouseenter="currentLink = 'supplyPlan'"
                                                                @mouseleave="currentLink = ''"
                                                                :class="currentLink === 'supplyPlan' ? 'text-secondary' : ''"
                                                                @click="SupplyPlanNavigator('home')">
                                                                Data Recovery
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div role="button"
                                                                @mouseenter="currentLink = 'supplySettings'"
                                                                @mouseleave="currentLink = ''"
                                                                :class="currentLink === 'supplySettings' ? 'text-secondary' : ''">
                                                                FAQ
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>

                                            </div>
                                            <div class="col-sm-4 d-flex align-items-center">
                                                <div class="d-none d-lg-block">
                                                    <img :src="megamenu" alt class="img-fluid mx-auto d-block" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</template>
