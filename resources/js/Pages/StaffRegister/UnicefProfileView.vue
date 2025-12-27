<script setup>
import PageHeader from "../../Components/page-header.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { reactive, computed, onMounted } from "vue";
import Layout from "../../Layouts/main.vue";
import logoDarkSm from "../../../images/logo.svg";
import moment from "moment";
import UnicefStaffLoginActivity from "./UnicefStaffLoginActivity.vue";
import axios from "axios";
import Swal from "sweetalert2";

import { ChartData } from "./ChartData";
// import Stat from "../../components/widgets/stat.vue";
// import profile from "../../images/logo-dark.svg";
// import avatar1 from "../../images/logo-dark.svg";

// import profile from "../../../images/profile-img.png";
// import avatar1 from "../../../images/users/avatar-1.jpg";

//props
const props = defineProps({
    staffDetails: Object,
    invoices: Array,
    userbscRequests: Array,
});

const coutryOffice = import.meta.env.VITE_COUNTRY_OFFICE;

//Default Layout
defineOptions({ layout: Layout });

//state
const state = reactive({
    items: [
        {
            text: "Staff Register",
            // href: "javascript:void(0)"
        },
        {
            text: "Profile",
            active: true,
        },
    ],
});

//computed
const isSuperAdmin = computed(() => {
    const isSuperAdminPerm = usePage().props.auth.user.allPermissions.includes("s_admin");
    if (isSuperAdminPerm != null) {
        return true;
    } else {
        return false;
    }
});

function loginAs(userEmail, userName) {
    Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to login as <strong>${userName}</strong>.</p>`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Yes, Proceed",
    }).then((result) => {
        if (result.value) {
            router.post(
                "/loginAs",
                { user: userEmail },
                {
                    onFinish: () => {
                        router.visit("/");
                    },
                    onError: () => {
                        Swal.fire({
                            title: "Something Went Wrong",
                            icon: "error",
                            html: `<p style="font-size: 14px">There is something that went wrong and it is not your fault. Please, reach out to ICT for help.</p>`,
                            showCloseButton: false,
                            showCancelButton: false,
                            focusConfirm: true,
                            confirmButtonText: "OK",
                            confirmButtonColor: "#43ad60",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            closeOnClickOutside: false,
                        }).then((result) => {
                            if (result.value) {
                                router.reload({
                                    preserveState: false,
                                });
                            }
                        });
                    },
                }
            );
        }
    });
}

function revokePermission(permName) {
    const options = {
        headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
        },
    };

    Swal.fire({
        title: "Revoke Permission?",
        icon: "info",
        html: `<p style="font-size: 14px">Are you sure you want to revoke this permission from ${props.staffDetails.name}.</p>`,
        showCancelButton: true,
        focusConfirm: true,
        confirmButtonText: "Yes, Proceed",
        confirmButtonColor: "#43ad60",
        allowOutsideClick: false,
        allowEscapeKey: false,
        closeOnClickOutside: false,
    }).then((result) => {
        if (result.value) {
            Swal.fire({
                title: "Please wait...",
                allowOutsideClick: false,
                showCancelButton: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            axios
                .post("api/staff-register/revokePermision", { staffEmail: props.staffDetails.email, permission: permName }, options)
                .then((res) => {
                    if (res.data.results === "success") {
                        Swal.fire({
                            title: "Permission Revoked.",
                            icon: "success",
                            html: `<p style="font-size: 14px">The permission has been successfully revoked.</p>`,
                            showCloseButton: false,
                            showCancelButton: false,
                            focusConfirm: true,
                            confirmButtonText: "OK",
                            confirmButtonColor: "#43ad60",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            closeOnClickOutside: false,
                        }).then((result) => {
                            if (result.value) {
                                Swal.close();
                                // router.visit("/staff_register");
                                router.reload({
                                    preserveState: false,
                                });
                            }
                        });
                    }
                })
                .catch((err) => {
                    console.log("Error:", err);
                    Swal.fire({
                        title: "Something Went Wrong",
                        icon: "error",
                        html: `<p style="font-size: 14px">There is something that went wrong and it is not your fault. Please, reach out to ICT for help.</p>`,
                        showCloseButton: false,
                        showCancelButton: false,
                        focusConfirm: true,
                        confirmButtonText: "OK",
                        confirmButtonColor: "#43ad60",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        closeOnClickOutside: false,
                    }).then((result) => {
                        if (result.value) {
                            router.reload({
                                preserveState: false,
                            });
                        }
                    });
                });
        }
    });
}
function revokeRole(roleName) {
    const options = {
        headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
        },
    };

    Swal.fire({
        title: "Revoke Role?",
        icon: "info",
        html: `<p style="font-size: 14px">Are you sure you want to revoke this role from ${props.staffDetails.name}.</p>`,
        showCancelButton: true,
        focusConfirm: true,
        confirmButtonText: "Yes, Proceed",
        confirmButtonColor: "#43ad60",
        allowOutsideClick: false,
        allowEscapeKey: false,
        closeOnClickOutside: false,
    }).then((result) => {
        if (result.value) {
            Swal.fire({
                title: "Please wait...",
                allowOutsideClick: false,
                showCancelButton: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            axios
                .post("api/staff-register/revokeRole", { staffEmail: props.staffDetails.email, role: roleName }, options)
                .then((res) => {
                    if (res.data.results === "success") {
                        Swal.fire({
                            title: "Role Revoked.",
                            icon: "success",
                            html: `<p style="font-size: 14px">The role has been successfully revoked.</p>`,
                            showCloseButton: false,
                            showCancelButton: false,
                            focusConfirm: true,
                            confirmButtonText: "OK",
                            confirmButtonColor: "#43ad60",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            closeOnClickOutside: false,
                        }).then((result) => {
                            if (result.value) {
                                Swal.close();
                                // router.visit("/staff_register");
                                router.reload({
                                    preserveState: false,
                                });
                            }
                        });
                    }
                })
                .catch((err) => {
                    console.log("Error:", err);
                    Swal.fire({
                        title: "Something Went Wrong",
                        icon: "error",
                        html: `<p style="font-size: 14px">There is something that went wrong and it is not your fault. Please, reach out to ICT for help.</p>`,
                        showCloseButton: false,
                        showCancelButton: false,
                        focusConfirm: true,
                        confirmButtonText: "OK",
                        confirmButtonColor: "#43ad60",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        closeOnClickOutside: false,
                    }).then((result) => {
                        if (result.value) {
                            router.reload({
                                preserveState: false,
                            });
                        }
                    });
                });
        }
    });
}
</script>

<template>
    <Head title="Profile" />

    <PageHeader :title="'Profile'" :items="state.items" />
    <div class="row">
        <div class="col-xl-4">
            <div class="card overflow-hidden">
                <div class="bg-soft bg-primary">
                    <div class="row">
                        <div class="col-9">
                            <div class="text-primary p-3">
                                <!-- <h5 class="text-primary">Welcome Back !</h5> -->
                            </div>
                        </div>
                        <div class="col-3 align-self-end pt-3 pb-3 pe-4">
                            <img :src="logoDarkSm" height="75" width="75" alt class="img-fluid" />
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-sm-7">
                            <div class="avatar-md profile-user-wid mb-4">
                                <img :src="props.staffDetails.user.profile_photo_url" alt class="img-thumbnail rounded-circle" />
                            </div>
                            <div class="mt-4">
                                <h5 class="font-size-15 text-truncate">{{ props.staffDetails.name }}</h5>
                                <p class="text-muted mb-0 text-truncate">{{ props.staffDetails.position_text }}</p>
                            </div>
                        </div>

                        <div class="col-sm-5">
                            <div class="pt-4">
                                <div v-if="props.staffDetails.is_on_leave" class="fw-bold text-end text-muted">
                                    Status:
                                    <span class="badge text-bg-danger">On Leave</span>
                                </div>
                                <div v-else class="fw-bold text-end text-muted">
                                    Status:
                                    <span class="badge text-bg-success ms-2">Available</span>
                                </div>
                                <div class="mt-4 d-flex justify-content-end" v-if="isSuperAdmin">
                                    <div class="btn btn-primary btn-sm mt-2" @click="loginAs(props.staffDetails.email, props.staffDetails.name)">
                                        Login As
                                        <i class="mdi mdi-arrow-right ms-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end card -->

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Personal Information</h4>

                    <div class="table-responsive mb-0">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th scope="row">Full Name :</th>
                                    <td>{{ props.staffDetails.name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">E-mail :</th>
                                    <td>{{ props.staffDetails.email }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Position:</th>
                                    <td>{{ props.staffDetails.position_text }}</td>
                                </tr>

                                <tr>
                                    <th scope="row">Mobile :</th>
                                    <td>{{ props.staffDetails.phone }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">WhatsApp :</th>
                                    <td>{{ props.staffDetails.phone_whatsapp }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">UNICEF Number :</th>
                                    <td>{{ props.staffDetails.allocated_phone_number }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Gender :</th>
                                    <td>{{ props.staffDetails.gender }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Section :</th>
                                    <td>{{ props.staffDetails.sectionname }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Organisation Unit :</th>
                                    <td>{{ props.staffDetails.organisation_unit }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Pillar :</th>
                                    <td>{{ props.staffDetails.pillar }}</td>
                                </tr>

                                <tr>
                                    <th scope="row">Address :</th>
                                    <td>{{ props.staffDetails.address }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        Joined UNICEF<span><small>(Country)</small></span>
                                    </th>
                                    <td>
                                        {{
                                            props.staffDetails.date_began_working_with_unicef_country_level != null
                                                ? moment(props.staffDetails.date_began_working_with_unicef_country_level).format(`Do
                                        MMM, YYYY`)
                                                : ""
                                        }}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        Joined UNICEF<span><small>(Global)</small></span> :
                                    </th>
                                    <td>{{ moment(props.staffDetails.date_began_working_with_unicef).format(`Do MMM, YYYY`) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive mt-5">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="fw-bold mb-3 flex-wrap col-2" style="word-wrap: normal">Permissions:</div>
                                        <div v-if="props.staffDetails.user.hasOwnProperty('allPermissions')">
                                            <span role="button" v-for="permission in props.staffDetails.user.allPermissions" :key="`${permission.id}_${permission.name}`">
                                                <span class="badge badge-soft-primary font-size-11 me-4 mb-3" @click="revokePermission(permission.name)"
                                                    >{{ permission }}<i class="bx bxs-x-circle text-danger ps-1 pe-1" role="button"></i
                                                ></span>
                                            </span>
                                        </div>

                                        <div class="fw-bold mt-4 mb-3 flex-wrap col-2" style="word-wrap: normal">Roles:</div>
                                        <div v-if="props.staffDetails.user.hasOwnProperty('roles')">
                                            <span role="button" v-for="role in props.staffDetails.user.roles" :key="`${role.id}_${role.name}`">
                                                <span class="badge badge-soft-primary font-size-11 me-4 mb-3" @click="revokeRole(role.name)"
                                                    >{{ role.name }}<i class="bx bxs-x-circle text-danger ps-1 pe-1" role="button"></i
                                                ></span>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="row">
                <div v-for="stat of statData" :key="stat.icon" class="col-md-4">
                    <Stat :icon="stat.icon" :title="stat.title" :value="stat.value" />
                </div>
            </div>
            <div class="">
                <div class="">
                    <UnicefStaffLoginActivity />
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Latest Invoices</h4>
                    <div class="table-responsive mb-0">
                        <div v-if="props.invoices.length > 0">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th :style="{ backgroundColor: '#eff2f7' }" scope="col">#</th>
                                        <th :style="{ backgroundColor: '#eff2f7' }" scope="col">Supplier</th>
                                        <th :style="{ backgroundColor: '#eff2f7' }" scope="col" class="text-center">Type of Service/Product</th>
                                        <th :style="{ backgroundColor: '#eff2f7' }" scope="col" class="text-center">Status</th>
                                        <th :style="{ backgroundColor: '#eff2f7' }" scope="col" class="text-end">Invoice Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(invoice, index) in props.invoices" :key="`${index}-${invoice.id}`">
                                        <th :style="{ backgroundColor: '#fff' }" scope="row">{{ index + 1 }}</th>
                                        <td :style="{ backgroundColor: '#fff' }">
                                            <div style="max-width: 15vw">
                                                <div class="col-11 text-truncate"><span class="fw-medium">SP:</span> {{ invoice.provider_name }}</div>
                                                <div class="col-11 text-truncate"><span class="fw-medium">Invoice #: </span> {{ invoice.invoice_number }}</div>
                                            </div>
                                        </td>
                                        <td :style="{ backgroundColor: '#fff' }">
                                            <div style="max-width: 10vw" class="text-center col-11 text-truncate">{{ invoice.service_name }}</div>
                                        </td>
                                        <td :style="{ backgroundColor: '#fff' }">
                                            <div class="text-center">
                                                <span class="badge badge-soft-primary font-size-11 m-1">{{ invoice.status }}</span>
                                            </div>
                                        </td>
                                        <td :style="{ backgroundColor: '#fff' }">
                                            <div class="text-end">{{ invoice.invoice_currency }} {{ Intl.NumberFormat("en-US").format(invoice.total_amount) }}</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-else>
                            <div class="text-center mt-5 mb-5">
                                <div class="mb-2"><i class="fas fa-ban text-muted" style=""></i></div>
                                No Inovices
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Latest BSC Requests</h4>
                    <div class="table-responsive mb-0">
                        <div v-if="props.invoices.length > 0">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th :style="{ backgroundColor: '#eff2f7' }" scope="col">#</th>
                                        <th :style="{ backgroundColor: '#eff2f7' }" scope="col">Originator</th>
                                        <th :style="{ backgroundColor: '#eff2f7' }" scope="col" class="">Type of Request</th>
                                        <th :style="{ backgroundColor: '#eff2f7' }" scope="col" class="text-center">Status</th>
                                        <!-- <th :style="{ backgroundColor: '#eff2f7' }" scope="col" class="text-end">Invoice Amount</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(request, index) in props.userbscRequests" :key="`${index}-${request.id}`">
                                        <th :style="{ backgroundColor: '#fff' }" scope="row">{{ index + 1 }}</th>
                                        <td :style="{ backgroundColor: '#fff' }">
                                            <div style="max-width: 15vw">
                                                <div class="col-11 text-truncate"><span class="fw-medium">Originator:</span> {{ request.submitted_by }}</div>
                                                <div class="col-11 text-truncate"><span class="fw-medium">Ref #: </span> {{ request.reference_number }}</div>
                                            </div>
                                        </td>
                                        <td :style="{ backgroundColor: '#fff' }">
                                            <div style="max-width: 10vw" class="text-center col-11 text-truncate">{{ request.request_type }}</div>
                                        </td>
                                        <td :style="{ backgroundColor: '#fff' }">
                                            <div class="text-center">
                                                <span class="badge badge-soft-primary font-size-11 m-1">{{ request.status }}</span>
                                            </div>
                                        </td>
                                        <!-- <td :style="{ backgroundColor: '#fff' }">
                                            <div class="text-end">{{invoice.invoice_currency}} {{ Intl.NumberFormat("en-US").format(invoice.total_amount) }}</div>
                                        </td> -->
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-else>
                            <div class="text-center mt-5 mb-5">
                                <div class="mb-2"><i class="fas fa-ban text-muted" style=""></i></div>
                                No Requests
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Latest Emails</h4>
                    <div>
                                <div class="text-center mt-5 mb-5">
                                    <div class="mb-2"><i class="fas fa-ban text-muted" style=""></i></div>
                                    No Blacklisted Emails
                                </div>
                            </div>
                  
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
</template>
