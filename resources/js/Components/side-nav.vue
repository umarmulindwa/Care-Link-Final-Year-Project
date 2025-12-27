<script>
import MetisMenu from "metismenujs";

import { menuItems } from "./menu";
import { Head, Link, router, useForm, usePage } from "@inertiajs/vue3";
import { useStore } from "vuex"
import { onMounted } from "vue";


/**
 * Side-nav component
 */
export default {
    data() {
        return {
            menuItems: menuItems,
            userType: usePage().props.auth.user.user_type,
            session: usePage().props.auth.user,
        };
    },

    setup() {
        onMounted(() => {
            const store = useStore();
            const page = usePage();

            store.commit("LoggedInUser/storeLoggedInUserDetails", usePage().props.auth.user);
        useStore().commit("LoggedInUser/storeCurrentUrl",page.url);

            //removing default blue bacground
            document.body.classList.remove("side-bg");
            //setting current User
            // state.currentUser = usePage().props.auth.user;
        })
    },
    mounted: function () {
        // eslint-disable-next-line no-unused-vars
        var menuRef = new MetisMenu("#side-menu");
        // alert(menuRef)
        var links = document.getElementsByClassName("side-nav-link-ref");
        var matchingMenuItem = null;
        const paths = [];

        for (var i = 0; i < links.length; i++) {
            paths.push(links[i]["pathname"]);
        }
        var itemIndex = paths.indexOf(window.location.pathname);
        if (itemIndex === -1) {
            const strIndex = window.location.pathname.lastIndexOf("/");
            const item = window.location.pathname.substr(0, strIndex).toString();
            matchingMenuItem = links[paths.indexOf(item)];
        } else {
            matchingMenuItem = links[itemIndex];
        }

        if (matchingMenuItem) {
            matchingMenuItem.classList.add("active");
            var parent = matchingMenuItem.parentElement;

            /**
             * TODO: This is hard coded way of expading/activating parent menu dropdown and working till level 3.
             * We should come up with non hard coded approach
             */
            if (parent) {
                parent.classList.add("mm-active");
                const parent2 = parent.parentElement.closest("ul");
                if (parent2 && parent2.id !== "side-menu") {
                    parent2.classList.add("mm-show");

                    const parent3 = parent2.parentElement;
                    if (parent3) {
                        parent3.classList.add("mm-active");

                        var badgeChildAnchor = parent3.querySelector(".badge");

                        if (!badgeChildAnchor) {
                            var childAnchor = parent3.querySelector(".has-arrow");
                        }

                        var childDropdown = parent3.querySelector(".has-dropdown");
                        if (childAnchor) childAnchor.classList.add("mm-active");
                        if (childDropdown) childDropdown.classList.add("mm-active");

                        const parent4 = parent3.parentElement;
                        if (parent4 && parent4.id !== "side-menu") {
                            parent4.classList.add("mm-show");
                            const parent5 = parent4.parentElement;
                            if (parent5 && parent5.id !== "side-menu") {
                                parent5.classList.add("mm-active");
                                const childanchor = parent5.querySelector(".is-parent");
                                if (childanchor && parent5.id !== "side-menu") {
                                    childanchor.classList.add("mm-active");
                                }
                            }
                        }
                    }
                }
            }
        }
    },
    computed: {

    },

    methods: {
        /**
         * Returns true or false if given menu item has child or not
         * @param item menuItem
         */
        hasItems(item) {
            return item.subItems !== undefined ? item.subItems.length > 0 : false;
        },
        toHomePage() {
            router.get(route("dashboard"));
        },
        toInvoicesDashboard() {
            // router.get(route("dashboard"));
        },
        toNewInvoiceCreation() {
            router.get(route("sp.create-invoice"));
        },
        toAllInvoices() {
            router.get(route("sp.invoices"));
        },
        toVatRefunds() {
            router.get(route("VatRefunds"));
        },
        toBscRequests() {
            router.get(route("BscRequestDashboard"));
        },
        toBscRequestCreation() {
            router.get(route("createBscRequest"));
        },
        toPendingRequests() {
            router.get(route("pendingRequest"));
        },
        toReturnedRequests() {
            router.get(route("returnedRequest"));
        },
        toPayRoll() {
            router.get(route("payRoll"));
        },
        toPettyCashReconciliation() {
            router.get(route("pettyReconciliation"));
        },
        toControlPanel() {
            router.get(route("requestManagement"));
        },
        toHelpCenter() {
            router.get(route("helpCenter"));
        },
        toSettings() {
            router.get(route("settings"));
        },

        goto(routeName) {
            router.get(route(routeName));
        },
    },
};
</script>

<template>
    <!-- ========== Left Sidebar Start ========== -->

    <!--- Sidemenu -->
    <div id="sidebar-menu">
        <!-- Left Menu Start -->
        <ul id="side-menu" class="metismenu list-unstyled" v-if="userType == 'newstaff'">
            <li class="" role="button">
                <a @click="toHomePage">
                    <i class="bx bx-home-circle" style="color: #7db2d5"></i>
                    <span class="text-white">Home</span>
                </a>
            </li>
            <li class="" role="button">
                <a @click="toHelpCenter">
                    <i class="mdi mdi-help-circle-outline" style="color: #7db2d5"></i>
                    <span :class="$store.state.LoggedInUser.currentUrl == '/helpCenter' ? 'text-white' : ''">Help
                        Center</span>
                </a>
            </li>
        </ul>
        <ul v-else id="side-menu" class="metismenu list-unstyled">
            <li class="" role="button">
                <a @click="toHomePage">
                    <i class="bx bx-home-circle" style="color: #7db2d5"></i>
                    <span :class="$store.state.LoggedInUser.currentUrl == '/dashboard' ? 'text-white' : ''">Home</span>
                </a>
            </li>

            <li class="" role="button">
                <a class="is-parent has-arrow has-dropdown" @click="toInvoicesDashboard">
                    <i class="bx bx bx-file" style="color: #7db2d5"></i>
                    <span style="margin-right: 1em">Invoices</span>
                    <span :class="`badge rounded-pill bg-primary   `"></span>
                </a>
                <ul class=" " aria-expanded="false">
                    <li class="mt-3 mb-2 mr-4">
                        <span @click="toNewInvoiceCreation" style="margin-right: 1em"
                            :class="$store.state.LoggedInUser.currentUrl == '/sp/create-invoice' || `${$store.state.LoggedInUser.currentUrl}`.includes('invoice') ? 'text-white' : 'text-grayish'">New
                            Invoice</span>
                        <span class="badge rounded-pill bg-primary"></span>
                    </li>
                    <li class="mt-3 mb-2 mr-4">
                        <span @click="toAllInvoices" style="margin-right: 1em"
                            :class="$store.state.LoggedInUser.currentUrl == '/sp/invoices' ? 'text-white' : 'text-grayish'">All</span>
                        <span class="badge rounded-pill bg-primary"></span>
                    </li>
                    <li class="mt-3 mb-2 mr-4">
                        <span @click="toAllInvoices" style="margin-right: 1em"
                            :class="$store.state.LoggedInUser.currentUrl == '/sp/test-form' ? 'text-white' : 'text-grayish'">Work
                            In Progress</span>
                        <span class="badge rounded-pill bg-primary"></span>
                    </li>
                    <li class="mt-3 mb-2">
                        <span style="margin-right: 1em"
                            :class="$store.state.LoggedInUser.currentUrl == '/returned' ? 'text-white' : 'text-grayish'">Returned</span>
                        <span class="badge rounded-pill bg-primary"></span>
                    </li>
                </ul>
            </li>

            <li class="" role="button" v-if="!session.allPermissions.includes('financial_service_provider')">
                <a @click="goto(`beneficiary-payments`)">
                    <i class="fas fa-users" style="color: #7db2d5"></i>
                    <span
                        :class="$store.state.LoggedInUser.currentUrl == 'beneficiary-payments' || `${$store.state.LoggedInUser.currentUrl || ''}`.includes('benefic') ? 'text-white' : ''">Beneficiary
                        Payments</span>
                </a>
            </li>


            <li class="" role="button" v-else-if="session.allPermissions.includes('financial_service_provider')">
                <a @click="goto(`beneficiary-reconcile-payments`)">
                    <i class="fas fa-users" style="color: #7db2d5"></i>
                    <span
                        :class="$store.state.LoggedInUser.currentUrl == '/beneficiary-reconcile-payments' ? 'text-white' : ''">Beneficiary
                        Reconciliation</span>
                </a>
            </li>
            <li class="" role="button">
                <a @click="goto(`list-faces`)">
                    <i class="bx bx bx-file" style="color: #7db2d5"></i>
                    <span
                        :class="$store.state.LoggedInUser.currentUrl == '/face-forms' ? 'text-white' : ''">FACE FORMS</span>
                </a>
            </li>


            <li class="" role="button">
                <a @click="goto(`myIncidents`)">
                    <i class="bx bxs-file-find" style="color: #7db2d5"></i>
                    <span
                        :class="$store.state.LoggedInUser.currentUrl == '/create-incident' ? 'text-white' : ''">Incident
                        Reporting</span>
                </a>
            </li>

            <li role="button">
                <a @click="goto(`low-value-procurement.index`)">
                    <i class="bx bxs-message" style="color: #7db2d5"></i>
                    <span
                        :class="$store.state.LoggedInUser.currentUrl === '/low-value-procurement' ? 'text-white' : ''">Requests
                        for Quotations</span>
                </a>
            </li>
             <li role="button">
                <a @click="goto(`sp-register-signature`)">
                    <i class="bx bx-pen" style="color: #7db2d5"></i>
                    <span
                        :class="$store.state.LoggedInUser.currentUrl === '/sp-register-signature' ? 'text-white' : ''">Register Signature</span>
                </a>
            </li>
            <li class="d-none" role="button">
                <a @click="goto(`vehicle-inspection.index`)">
                    <i class="bx bxs-calendar-alt" style="color: #7db2d5"></i>
                    <span
                        :class="$store.state.LoggedInUser.currentUrl === '/vehicle-inspection' ? 'text-white' : ''">Vehicle
                        Inspection</span>
                </a>
            </li>
            <li class="d-none" role="button">
                <a @click="toPettyCashReconciliation">
                    <i class="bx bxs-wallet" style="color: #7db2d5"></i>
                    <span
                        :class="$store.state.LoggedInUser.currentUrl == '/pettyReconciliation' ? 'text-white' : ''">Services/Products</span>
                </a>
            </li>
            <li class="d-none" role="button">
                <a @click="toHelpCenter">
                    <i class="mdi mdi-help-circle-outline" style="color: #7db2d5"></i>
                    <span :class="$store.state.LoggedInUser.currentUrl == '/helpCenter' ? 'text-white' : ''">Help
                        Center</span>
                </a>
            </li>
            <li class="d-none" role="button">
                <a @click="toSettings">
                    <i class="fas fa-cog" style="color: #7db2d5"></i>
                    <span
                        :class="$store.state.LoggedInUser.currentUrl == '/settings' ? 'text-white' : ''">Settings</span>
                </a>
            </li>
        </ul>
        
    </div>
    <!-- Sidebar -->
</template>
