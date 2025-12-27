<script setup>
import Layout from "../../Layouts/main.vue";
import { reactive, onMounted, computed, watch } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import moment from "moment";
import { userGridData } from "./data-user";
import Swal from "sweetalert2";
import axios from "axios";
import { read, utils } from "xlsx";
import vSelect from "vue-select";
import AvailabilityPlanner from "./AvailabilityPlanner.vue";
import "vue-select/dist/vue-select.css";
import UserPermissionsLegend from "./UserPermissionsLegend.vue";
import StaffGroups from "@/Pages/RadioCall/Groups.vue";
import InfiniteLoading from "v3-infinite-loading";
import "v3-infinite-loading/lib/style.css";
import { notify } from "../../mixins/notify.js";
import staffTitleOptions from "../../Components/StaticData/StaffTitles.js";
import SingleStaffInput from "@/Pages/StaffRegister/NewStaff/SingleInput.vue";
import { RequestHelper } from "@/mixins/helpers";

const coutryOffice = import.meta.env.VITE_COUNTRY_OFFICE;

//Default Layout
defineOptions({ layout: Layout });

//props
const props = defineProps({
    unicefStaff: Array,
    serviceProviders: Array,
    userRoles: Array,
    allStaffNames: Array,
    seededPermissions: Array,
    upcomingLeaves: Array,
    ongoingLeaves: Array,
    // usersWithRoles: Array,
});
//data
const state = reactive({
    title: "Staff Register",
    userGridData: userGridData,
    showLess: true,
    items: [
        {
            text: "Email",
            href: "/",
        },
        {
            text: "Inbox",
            active: true,
        },
    ],
    addSingleStaffModal: false,
    singleStaff: {
        phone_number: "",
        personal_id: "",
        index_number: "",
        marital_status: "",
        name: "",
        gender: "",
        staff_type: "",
        email: "",
        whatsapp_number: "",
        position_id: "",
        address: "",
        position: "",
        section: "",
        pillar: "",
        organisation_unit: "",
        report_to: "",
        category: "",
        appointment_type: "",
        date_joined_global: "",
        date_joined_country: "",
        contract_end: "",
    },
    batchStaffUploadFile: null,
    batchUploadStaff: [],
    addSingleStaffLoader: false,
    addRoleModal: false,
    userRoleDetails: {
        roleName: "",
        roleDescription: "",
        rolePermissions: [],
    },
    activePermissions: [],
    addRoleLoader: false,
    assignRolePermissionModal: false,
    assignRolesPermissions: {
        roles: [],
        permissions: [],
        staff: null,
    },
    assignRolesPermissionsLoader: false,
    staffProfiles: [],
    serviceProvidersList: [],
    deleteRoleLoader: false,
    roleNames: [],
    assignDirectRole: "",
    editRoleModal: false,
    editRoleName: "",
    editRoleDescription: "",
    editRolePermissions: [],
    editRoleID: null,
    searchWithIn: "All",
    searchText: "",
    //serive providers
    addSingleSPModal: false,
    addSingleSPLoader: false,
    spName: "",
    spEmail: "",
    spVendorNumber: "",
    viewSPDetailsModal: false,
    spDetails: {},
    batchSPUploadFile: null,
    batchUploadSP: [],
    editUserRoleModal: false,
});

//methods
//mounted
onMounted(() => {
    //removing default background blue color
    document.body.classList.remove("side-bg");
    //setting active permissions
    props.seededPermissions.forEach((permission) => state.activePermissions.push(permission.name));
    //first list of staffprofiles
    state.staffProfiles = [...state.staffProfiles, ...props.unicefStaff.data];
    //first list of service providers
    state.serviceProvidersList = [...state.serviceProvidersList, ...props.serviceProviders.data];
    //setting rolenames
    props.userRoles.forEach((role) => {
        if (role != null) {
            state.roleNames.push(role.name);
        }
    });
    //Load data for adding single staff
});

//watch
watch(
    () => state.searchText,
    (searchTextInput) => {
        let searchByText = searchTextInput.toLowerCase();
        router.get("/staff_register", { seachFor: searchByText, seachWithIn: state.searchWithIn }, { preserveState: true, only: ["unicefStaff"] });
    }
);

//updating staffprofiles during search
watch(
    () => props.unicefStaff.data,
    (searchResults) => {
        state.staffProfiles = searchResults;
    }
);

//computed
const currentUser = computed(() => {
    return usePage().props.auth.user;
});

const isSuperAdmin = computed(() => {
    const isSuperAdminPerm = usePage().props.auth.user.allPermissions.includes("s_admin");
    if (isSuperAdminPerm != null) {
        return true;
    } else {
        return false;
    }
});

const isHrStaff = computed(() => {
    const hrPerm = usePage().props.auth.user.allPermissions.includes("hr_staff");
    if (hrPerm != null) {
        return true;
    } else {
        return false;
    }
});

const rolesByUsers = computed(() => {
    const allstaff = props.allStaffNames;
    const useroles = props.userRoles;
    const staffWithRoles = allstaff.map((staff) => {
        const structuredStaff = {
            name: staff.name,
            roles: staff.roles,
        };
        return structuredStaff;
    });

    const rolesWithUsers = useroles.map((role) => {
        role.users = [];
        staffWithRoles.forEach((staff) => {
            const userRole = staff.roles.find((staffRole) => staffRole.name == role.name);
            if (userRole != undefined) {
                role.users.push(staff.name);
            }
        });
        return role;
    });

    //sorting
    rolesWithUsers.sort((a, b) => {
        const nameA = a.name.toUpperCase();
        const nameB = b.name.toUpperCase();
        if (nameA < nameB) {
            return -1;
        }
        if (nameA > nameB) {
            return 1;
        }

        // names must be equal
        return 0;
    });

    return rolesWithUsers;
});

const reversedUserRoles = computed(() => {
    return props.userRoles.slice().reverse();
});
const roleNames = computed(() => {
    const names = props.userRoles.map((role) => {
        if (role != null) {
            return role.name;
        }
    });
    return names;
});
const unicefStaffNames = computed(() => {
    const names = props.allStaffNames.map((staff) => ({ label: staff.name, code: staff.id }));
    return names;
});
//methods

function viewStaffProfile(id) {
    router.visit("/unice_staff_profile", {
        method: "get",
        data: { staffProfileID: id },
    });
}

function getBatchFile() {
    document.getElementById("batchStaffUploadFile").click();
}
function getSPBatchFile() {
    document.getElementById("batchSPUploadFile").click();
}
const resetMyPassword = async (staff_id) => {
    Swal.fire({
        text: "Processing, please wait...",
        didOpen: () => {
            Swal.showLoading();
        },
    });
    await RequestHelper.postRequest(
        "/api/staff-register/password-reset",
        {
            staff_id,
        },
        {},
        currentUser.api_token
    )
        .then(({ data }) => {
            console.log({ data });
            Swal.fire({
                icon: "success",
                text: "Password Reset",
            });
        })
        .catch((err) => {
            Swal.fire({
                icon: "error",
                text: "Something went wrong!",
            });
            console.error({ err });
        });
};
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
async function onChange(event) {
    Swal.fire({
        title: "Please wait...",
        allowOutsideClick: false,
        showCancelButton: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });
    state.batchStaffUploadFile = event.target.files ? event.target.files[0] : null;
    //save staff if sheet passes validation
    const options = {
        headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
        },
    };

    if (state.batchStaffUploadFile != null) {
        const staffSheetArrayBuffer = await state.batchStaffUploadFile.arrayBuffer();

        //parsing
        const wb = read(staffSheetArrayBuffer);

        /* generate array of objects from first worksheet */
        const ws = wb.Sheets[wb.SheetNames[0]]; // get the first worksheet
        const data = utils.sheet_to_json(ws); // generate objects

        /* update state */
        state.batchUploadStaff = data;

        //validating data (current validation only checks form email and name)
        let isDataClear = true;
        for (let index = 0; index < state.batchUploadStaff.length; index++) {
            const obj = state.batchUploadStaff[index];

            if (!obj.hasOwnProperty("Email") || !obj.hasOwnProperty("Names")) {
                Swal.fire({
                    title: "Something Went Wrong",
                    icon: "error",
                    html: `<p style="font-size: 14px">The record at <b>row ${index + 2}</b> has missing information<br> Email and name are required.</p>`,
                    showCloseButton: false,
                    showCancelButton: false,
                    focusConfirm: true,
                    confirmButtonText: "OK",
                    confirmButtonColor: "#43ad60",
                    allowOutsideClick: true,
                    allowEscapeKey: false,
                    closeOnClickOutside: false,
                }).then((result) => {
                    if (result.value) {
                        Swal.close();
                    }
                });
                isDataClear = false;
                break;
            }
        }
        if (!isDataClear || state.batchUploadStaff.length == 0) {
            Swal.close();
            return;
        }

        axios
            .post("api/staff-register/batchUploadStaff", { dataArray: state.batchUploadStaff }, options)
            .then((res) => {
                console.log(res);
                if (res.data.success) {
                    Swal.fire({
                        title: "New Staff Registered",
                        icon: "success",
                        html: `<p style="font-size: 14px">A temporary password will be sent to all registered Emails shortly.</p>`,
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
                            router.visit("/staff_register");
                        }
                    });
                }
            })
            .catch((err) => {
                console.log("this is the error", err);
                Swal.fire({
                    title: "Something Went Wrong",
                    icon: "error",
                    html:
                        err.message === "Request failed with status code 422"
                            ? "The email has already been taken."
                            : `<p style="font-size: 14px">There is something that went wrong and it is not your fault. Please, reach out to ICT for help.</p>`,
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
                        if (err.message === "Request failed with status code 422") {
                            Swal.close();
                        } else {
                            router.visit("/staff_register");
                        }
                    }
                });
            });
    } else {
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
                Swal.close();
            }
        });
    }
}

function registerServiceProvider() {
    if (state.spName != "" && state.spEmail != "" && state.spVendorNumber != "") {
        state.addSingleSPLoader = true;

        Swal.fire({
            title: "Please wait...",
            allowOutsideClick: false,
            showCancelButton: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });
        const options = {
            headers: {
                "Content-Type": "multipart/form-data",
                Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
            },
        };

        axios
            .post("api/staff-register/registerServiceProvider", { name: state.spName, email: state.spEmail, vendorNumber: state.spVendorNumber }, options)
            .then((res) => {
                console.log(res);
                if (res.data.results === "success") {
                    state.addSingleSPLoader = false;
                    state.addSingleSPModal = false;
                    state.spName = "";
                    state.spEmail = "";
                    state.spVendorNumber = "";

                    Swal.fire({
                        title: "Service Provider Registered",
                        icon: "success",
                        html: `<p style="font-size: 14px">New Service Provider has been registered successfully.</p>`,
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
                            router.visit("/staff_register", { preserveState: false, only: ["serviceProviders"] });
                            // router.reload();
                        }
                    });
                }
            })
            .catch((err) => {
                state.addSingleSPLoader = false;
                if (err.response.data.message == "The email has already been taken.") {
                    Swal.fire({
                        title: "Email Taken",
                        icon: "error",
                        html: `<p style="font-size: 14px">This Email is already registered.</p>`,
                        showCloseButton: false,
                        showCancelButton: false,
                        focusConfirm: true,
                        confirmButtonText: "OK",
                        confirmButtonColor: "#43ad60",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        closeOnClickOutside: false,
                    }).then((result) => {
                        Swal.close();
                    });
                } else {
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
                        Swal.close();
                    });
                }
            });
    } else {
        notify.toastErrorMessage("All Fields are required");
    }
}

function selectedSP(details) {
    state.viewSPDetailsModal = true;
    state.spDetails = details;
}

function activateSPAccount(detail) {
    Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to activate <strong>${detail.name}'s</strong> account.</p>`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Yes, Proceed",
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

            const options = {
                headers: {
                    "Content-Type": "multipart/form-data",
                    Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
                },
            };

            axios
                .post("api/staff-register/activateServiceProvider", { email: detail.email }, options)
                .then((res) => {
                    console.log(res);
                    if (res.data.results === "success") {
                        state.addSingleSPLoader = false;
                        state.addSingleSPModal = false;
                        state.spName = "";
                        state.spEmail = "";
                        state.spVendorNumber = "";

                        Swal.fire({
                            title: "Account Activated",
                            icon: "success",
                            html: `<p style="font-size: 14px">Service Provider account has been activated successfully.</p>`,
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
                                router.visit("/staff_register", { preserveState: false, only: ["serviceProviders"] });
                                // router.reload();
                            }
                        });
                    }
                })
                .catch((err) => {
                    console.log(err);
                    state.addSingleSPLoader = false;

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
                        Swal.close();
                    });
                });
        }
    });
}
function deActivateSPAccount(detail) {
    Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to deactivate <strong>${detail.name}'s</strong> account.</p>`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Yes, Proceed",
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
            const options = {
                headers: {
                    "Content-Type": "multipart/form-data",
                    Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
                },
            };

            axios
                .post("api/staff-register/deactivateServiceProvider", { email: detail.email }, options)
                .then((res) => {
                    console.log(res);
                    if (res.data.results === "success") {
                        state.addSingleSPLoader = false;
                        state.addSingleSPModal = false;
                        state.spName = "";
                        state.spEmail = "";
                        state.spVendorNumber = "";

                        Swal.fire({
                            title: "Account Deactivated",
                            icon: "success",
                            html: `<p style="font-size: 14px">Service Provider account has been deactivated successfully.</p>`,
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
                                router.visit("/staff_register", { preserveState: false, only: ["serviceProviders"] });
                                // router.reload();
                            }
                        });
                    }
                })
                .catch((err) => {
                    console.log(err);
                    state.addSingleSPLoader = false;

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
                        Swal.close();
                    });
                });
        }
    });
}
function downlaodSpBatchTemplate() {
    const mainUrl = import.meta.env.VITE_MAIN_URL;
    axios
        .get(`/storage/templates/ServiceProvidersIPTemplate.xlsx`, {
            responseType: "blob",
        })
        .then((response) => {
            // Create a Blob from the response data
            const file = new Blob([response.data], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });

            const fileURL = URL.createObjectURL(file);

            const a = document.createElement("a");
            a.href = fileURL;
            a.download = "ServiceProvidersIPTemplate.xlsx";
            document.body.appendChild(a);
            a.click();

            URL.revokeObjectURL(fileURL);
            document.body.removeChild(a);
        })
        .catch((error) => {
            console.log(error);
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
                    Swal.close();
                }
            });
        });
}
function downlaodSpBatchUnicefStaffTemplate() {
    axios
        .get(`/storage/templates/UNICEFSSDStaffTemplate.xlsx`, {
            responseType: "blob",
        })
        .then((response) => {
            // Create a Blob from the response data
            const file = new Blob([response.data], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });

            const fileURL = URL.createObjectURL(file);

            const a = document.createElement("a");
            a.href = fileURL;
            a.download = "UNICEFSSDStaffTemplate.xlsx";
            document.body.appendChild(a);
            a.click();

            URL.revokeObjectURL(fileURL);
            document.body.removeChild(a);
        })
        .catch((error) => {
            console.log(error);
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
                    Swal.close();
                }
            });
        });
}

async function spBatchUplaod(event) {
    Swal.fire({
        title: "Please wait...",
        allowOutsideClick: false,
        showCancelButton: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });
    state.batchSPUploadFile = event.target.files ? event.target.files[0] : null;
    //save staff if sheet passes validation
    const options = {
        headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
        },
    };

    if (state.batchSPUploadFile != null) {
        const staffSheetArrayBuffer = await state.batchSPUploadFile.arrayBuffer();

        //parsing
        const wb = read(staffSheetArrayBuffer);

        /* generate array of objects from first worksheet */
        const ws = wb.Sheets[wb.SheetNames[0]]; // get the first worksheet
        const data = utils.sheet_to_json(ws); // generate objects

        /* update state */
        state.batchUploadSP = data;

        //validating data (current validation only checks form email and name)
        let isDataClear = true;
        for (let index = 0; index < state.batchUploadSP.length; index++) {
            const obj = state.batchUploadSP[index];

            if (!obj.hasOwnProperty("Email") || !obj.hasOwnProperty("Name") || !obj.hasOwnProperty("Vendor No")) {
                Swal.fire({
                    title: "Something Went Wrong",
                    icon: "error",
                    html: `<p style="font-size: 14px">The record at <b>row ${index + 2}</b> has missing information<br> Vendor Number, Name and Email are required.</p>`,
                    showCloseButton: false,
                    showCancelButton: false,
                    focusConfirm: true,
                    confirmButtonText: "OK",
                    confirmButtonColor: "#43ad60",
                    allowOutsideClick: true,
                    allowEscapeKey: false,
                    closeOnClickOutside: false,
                }).then((result) => {
                    if (result.value) {
                        Swal.close();
                    }
                });
                isDataClear = false;
                break;
            }
        }
        if (!isDataClear || state.batchUploadSP.length == 0) {
            Swal.close();
            return;
        }
        axios
            .post("api/staff-register/batchUploadSP", { dataArray: state.batchUploadSP }, options)
            .then((res) => {
                console.log(res);
                if (res.data.success) {
                    Swal.fire({
                        title: "Service Provider(s) Registered",
                        icon: "success",
                        html: `<p style="font-size: 14px">A temporary password will be sent to all registered Emails shortly.</p>`,
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
                            router.visit("/staff_register", { only: ["serviceProviders"] });
                        }
                    });
                }
            })
            .catch((err) => {
                console.log("this is the error", err);
                Swal.fire({
                    title: "Something Went Wrong",
                    icon: "error",
                    html:
                        err.message === "Request failed with status code 422"
                            ? "The email has already been taken."
                            : `<p style="font-size: 14px">There is something that went wrong and it is not your fault. Please, reach out to ICT for help.</p>`,
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
                        if (err.message === "Request failed with status code 422") {
                            Swal.close();
                        } else {
                            router.visit("/staff_register");
                        }
                    }
                });
            });
    } else {
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
                Swal.close();
            }
        });
    }
}

function addNewRole() {
    state.addRoleModal = true;
}
function addUserRole() {
    state.addRoleLoader = true;
    const options = {
        headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
        },
    };

    axios
        .post("api/staff-register/addUserRole", { roleData: state.userRoleDetails }, options)
        .then((res) => {
            console.log(res);
            if (res.data.results === "success") {
                state.addRoleLoader = false;
                state.addRoleModal = false;
                state.userRoleDetails = {
                    roleName: "",
                    roleDescription: "",
                    rolePermissions: [],
                };

                Swal.fire({
                    title: "User Role Added",
                    icon: "success",
                    html: `<p style="font-size: 14px">The user role has been added successfully.</p>`,
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
                        //  router.visit("/staff_register",{preserveState:true});
                        router.reload();
                    }
                });
            }
        })
        .catch((err) => {
            console.log("this is the error", err.response.data.message.includes("already exists for guard"));
            state.addRoleLoader = false;

            if (err.response.data.message.includes("already exists for guard")) {
                Swal.fire({
                    title: "Something Went Wrong",
                    icon: "error",
                    html: `<p style="font-size: 14px">${err.response.data.message}</p>`,
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
            } else {
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
            }
        });
}
function assignRolesPermissions() {
    state.assignRolesPermissionsLoader = true;
    const options = {
        headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
        },
    };

    axios
        .post("api/staff-register/assignRolesPermissions", { rolesPermissions: state.assignRolesPermissions }, options)
        .then((res) => {
            console.log(res);

            state.assignRolesPermissionsLoader = false;
            state.assignRolePermissionModal = false;
            state.assignRolesPermissions = {
                roles: [],
                permissions: [],
                staff: null,
            };

            Swal.fire({
                title: "Assignment Successful",
                icon: "success",
                html: `<p style="font-size: 14px">You have successfully assigned Roles/Permissions.</p>`,
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
                    router.reload();
                }
            });
        })
        .catch((err) => {
            console.log(err);
            console.log("this is the error", err.response.data.message.includes("already exists for guard"));
            state.addRoleLoader = false;

            if (err.response.data.message.includes("already exists for guard")) {
                Swal.fire({
                    title: "Something Went Wrong",
                    icon: "error",
                    html: `<p style="font-size: 14px">${err.response.data.message}</p>`,
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
            } else {
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
            }
        });
}

function loadMoreStaffProfiles() {
    if (props.unicefStaff.next_page_url === null) {
        return;
    }

    router.get(
        props.unicefStaff.next_page_url,
        {},
        {
            preserveState: true,
            preserveScroll: true,
            only: ["unicefStaff"],
            onSuccess: () => {
                state.staffProfiles = [...state.staffProfiles, ...props.unicefStaff.data];
                //disabling route change
                window.history.replaceState({}, usePage().title, usePage().url);
            },
        }
    );
}
function loadMoreServiceProviders() {
    if (props.serviceProviders.next_page_url === null) {
        return;
    }

    router.get(
        props.serviceProviders.next_page_url,
        {},
        {
            preserveState: true,
            preserveScroll: true,
            only: ["serviceProviders"],
            onSuccess: () => {
                state.serviceProvidersList = [...state.serviceProvidersList, ...props.serviceProviders.data];
                //disabling route change
                window.history.replaceState({}, usePage().title, usePage().url);
            },
        }
    );
}

function deleteRole(role) {
    Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to delete the <strong>${role.name}</strong> role.</p>`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Yes, delete",
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
            state.deleteRoleLoader = true;
            const options = {
                headers: {
                    "Content-Type": "multipart/form-data",
                    Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
                },
            };

            axios
                .post("api/staff-register/deleteUserRole", { roleId: role.id }, options)
                .then((res) => {
                    console.log(res);
                    Swal.fire({
                        title: "User Role Deleted",
                        icon: "success",
                        html: `<p style="font-size: 14px">The user role has been deleted successfully.</p>`,
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
                            //  router.visit("/staff_register",{preserveState:true});
                            router.reload();
                        }
                    });
                })
                .catch((err) => {
                    state.deleteRoleLoader = false;

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

function editRole(roleTobeEdit) {
    state.editRoleName = roleTobeEdit.name;
    state.editRoleDescription = roleTobeEdit.description;
    state.editRolePermissions = roleTobeEdit.permissions;
    state.editRoleID = roleTobeEdit.id;

    //opening the modal
    state.editRoleModal = true;
}

function editUserRole(roleTobeEdit) {
    state.editRoleName = roleTobeEdit.name;
    state.editRoleDescription = roleTobeEdit.description;
    state.editRolePermissions = roleTobeEdit.permissions;
    state.editRoleID = roleTobeEdit.id;

    //opening the modal
    state.editUserRoleModal = true;
}

function roleEdit(roleToEdit) {
    state.editUserRoleModal = false;

    Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to make changese to the selected role <strong>(${state.editRoleName})</strong></p>`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Yes, Proceed",
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

            const options = {
                headers: {
                    "Content-Type": "multipart/form-data",
                    Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
                },
            };
            let formData = new FormData();

            formData.append("roleName", state.editRoleName);
            formData.append("roleDescriptsion", state.editRoleDescription);
            formData.append("roleID", state.editRoleID);
            //flattening permissions array
            if (state.editRolePermissions[0] === "string") {
                formData.append("rolePermisions", JSON.stringify(state.editRolePermissions));
                console.log('dd',state.editRolePermissions)

            } else {
                const permissionNames = [];
                state.editRolePermissions.forEach((perm) => {
                    permissionNames.push(perm);
                });
                formData.append("rolePermisions", JSON.stringify(permissionNames));
                console.log('dd',state.editRolePermissions)
            }

            axios
                .post("api/staff-register/editRole", formData, options)
                .then((res) => {
                    Swal.fire({
                        title: "Role Edited Successfully",
                        icon: "success",
                        html: `<p style="font-size: 14px">You have succesfully made changes to the role</p>`,
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
                            router.visit("/staff_register");
                        }
                    });
                })
                .catch((err) => {
                    console.log(err);
                    state.addSingleSPLoader = false;

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
                        Swal.close();
                    });
                });
        }
    });
}
</script>

<template>
    <Head title="Staff Register" />
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center mb-5 mt-4">
                    <h4>{{ state.title }}</h4>
                </div>
            </div>
        </div>
        <div class="w-100 d-flex flex-row justify-content-between">
            <div class="d-flex flex-column gap-3" style="width: 10%">
                <div class="card" role="button" :class="state.title == 'Staff Register' ? 'bg-primary text-white' : ''" @click="state.title = 'Staff Register'">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                        <div class="mt-2">
                            <i class="fas fa-user-friends nav-icon d-block mb-2" style="font-size: small"></i>
                        </div>
                        <div class="mb-2">
                            <p class="fw-bold mb-0 text-center">UNICEF Staff</p>
                        </div>
                    </div>
                </div>
                <div class="card" role="button" @click="state.title = 'Service Providers'" :class="state.title == 'Service Providers' ? 'bg-primary text-white' : ''">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                        <div>
                            <i class="fas fa-users nav-icon d-block mb-2" style="font-size: small"></i>
                        </div>
                        <div>
                            <p class="fw-bold mb-0 text-center">Service Providers</p>
                        </div>
                    </div>
                </div>
                <div class="card" role="button" @click="state.title = 'Roles & Permissions'" :class="state.title == 'Roles & Permissions' ? 'bg-primary text-white' : ''">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                        <div>
                            <i class="fas fa-address-card nav-icon d-block mb-2" style="font-size: small"></i>
                        </div>
                        <div>
                            <p class="fw-bold mb-0">Roles & Permissions</p>
                        </div>
                    </div>
                </div>
                <div class="card" role="button" @click="state.title = 'Radio Call Groups'" :class="state.title == 'Radio Call Groups' ? 'bg-primary text-white' : ''">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="mt-2">
                            <i class="fas fa-user-friends nav-icon d-block mb-2" style="font-size: small"></i>
                        </div>
                        <div class="mb-2">
                            <p class="fw-bold mb-0">Radio Call Groups</p>
                        </div>
                    </div>
                </div>
                <div class="card" role="button" @click="state.title = 'Availability Planner'" :class="state.title == 'Availability Planner' ? 'bg-primary text-white' : ''">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="mt-2">
                            <i class="fa fa-users nav-icon d-block mb-2" style="font-size: small"></i>
                        </div>
                        <div class="mb-2">
                            <p class="fw-bold mb-0">Availability Planner</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" style="width: 87%">
                <div class="card-body" id="staff_reg" v-if="state.title == 'Staff Register'">
                    <div class="d-flex flex-row justify-content-between col-12 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded-pill d-none d-lg-block" style="background-color: #f9f9f9; border: 0px">
                                <div class="input-group d-flex flex-row align-items-center">
                                    <div class="mt-1">
                                        <span class="" style="font-size: medium"><i class="bx bx-search-alt"></i></span>
                                    </div>
                                    <div class=""><input type="text" class="form-control" placeholder="Search.." style="background-color: #f9f9f9; border: 0px" v-model="state.searchText" /></div>

                                    <div class="hstack gap-3" role="button">
                                        <div class="vr" style="color: #dbdbdb"></div>
                                        <div class="btn-group dropbottomstart">
                                            <div data-bs-toggle="dropdown" class="d-flex flex-row gap-3">
                                                <div class="">{{ state.searchWithIn }}</div>
                                                <div>
                                                    <i class="mdi mdi-chevron-down" aria-expanded="false"></i>
                                                </div>
                                            </div>

                                            <ul class="dropdown-menu">
                                                <!-- Dropdown menu links -->
                                                <b-dropdown-item class="mb-1" @click="state.searchWithIn = 'All'">All</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="state.searchWithIn = 'Name'">Name</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="state.searchWithIn = 'Section'">Section</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="state.searchWithIn = 'Roles'">Roles</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="state.searchWithIn = 'Permissions'">Permissions</b-dropdown-item>
                                                <b-dropdown-item class="mb-1" @click="state.searchWithIn = 'Availability'">Availability</b-dropdown-item>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-row gap-3">
                            <div class="" v-if="isSuperAdmin || isHrStaff">
                                <b-dropdown class="btn-group mb-2 mb-sm-0" variant="primary">
                                    <template #button-content>
                                        Add New Staff
                                        <i class="mdi mdi-dots-vertical ms-2"></i>
                                    </template>
                                    <b-dropdown-item>
                                        <div class="d-flex flex-row gap-2 align-items-center" @click="$refs.singleStaffInput.open_single_input_modal = true">
                                            <div>
                                                <i class="fas fa-user-alt" style="font-size: small"></i>
                                            </div>
                                            <div>Single Input</div>
                                        </div>
                                    </b-dropdown-item>
                                    <b-dropdown-item>
                                        <div class="d-flex flex-row gap-2 align-items-center" @click="getBatchFile">
                                            <div><i class="mdi mdi-cloud-upload" style="font-size: medium"></i></div>
                                            <div>Batch Upload</div>
                                        </div>
                                    </b-dropdown-item>
                                    <b-dropdown-item>
                                        <div class="d-flex flex-row gap-2 align-items-center" @click="downlaodSpBatchUnicefStaffTemplate">
                                            <div><i class="mdi mdi-microsoft-excel" style="font-size: medium"></i></div>
                                            <div>Download Template</div>
                                        </div>
                                    </b-dropdown-item>
                                </b-dropdown>
                            </div>
                            <!-- <div class="btn-group">
                                <b-dropdown id="dropdown-dropright" right variant="primary">
                                    <template v-slot:button-content>
                                        <i class="bx bx-filter"></i>
                                    </template>
                                    <b-dropdown-item>Section</b-dropdown-item>
                                    <b-dropdown-item>On Leave</b-dropdown-item>
                                    <b-dropdown-item>Available</b-dropdown-item>
                                    <b-dropdown-item>Mute</b-dropdown-item>
                                </b-dropdown>
                            </div> -->
                        </div>
                    </div>
                    <div class="table-responsive" v-if="state.staffProfiles.length > 0">
                        <input type="file" @change="onChange" hidden id="batchStaffUploadFile" accept=".xls ,.xlsx" />
                        <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100" id="userList-table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }">#</th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Name</th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="text-center">Section</th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="text-center">Permissions</th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="text-center">Roles</th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="text-center">Availability</th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7', textAlign: 'center' }" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(list, index) in state.staffProfiles" :key="index + list.email + list.name">
                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div v-if="list.user != null">
                                            <img :src="list.user.profile_photo_url" :alt="''" class="rounded-circle avatar-xs object-fit-cover" />
                                        </div>
                                    </td>
                                    <td :style="{ backgroundColor: '#fff', maxWidth: '20em', wordWrap: 'break-word' }">
                                        <h5 class="font-size-14 mb-1">
                                            <a href="#" class="text-dark col-11 text-truncate">{{ list.name }}</a>
                                        </h5>
                                        <p class="text-muted mb-0 col-11 text-truncate">{{ list.position_text }}</p>
                                    </td>
                                    <td :style="{ backgroundColor: '#fff' }" class="text-center">
                                        <div v-if="list.sectionname != null">{{ list.sectionname }}</div>
                                        <div v-else><span class="badge badge-soft-secondary font-size-11 m-1">No Section</span></div>
                                    </td>
                                    <td :style="{ backgroundColor: '#fff' }" class="text-center">
                                        <div v-if="list.user?.allPermissions.length > 0">
                                            <div v-if="state.showLess">
                                                <div v-for="(permission, index) in list.user?.allPermissions.slice(0, 3)" :key="index">
                                                    <span class="badge badge-soft-primary font-size-11 m-1"> {{ permission }}</span>
                                                </div>
                                            </div>
                                            <div v-else-if="state.showLess == false">
                                                <div v-for="(permission, index) in list.user?.allPermissions" :key="index">
                                                    <span class="badge badge-soft-primary font-size-11 m-1"> {{ permission }}</span>
                                                </div>
                                            </div>
                                            <div
                                                class="d-flex align-items-center justify-content-center mt-2"
                                                v-if="list.user?.allPermissions.length > 2 && state.showLess"
                                                @click="state.showLess = false"
                                            >
                                                <small class="text-primary" role="button"
                                                    ><span class="badge text-bg-primary">See More<i class="bx bx-plus ms-1"></i></span
                                                ></small>
                                            </div>
                                            <div
                                                class="d-flex align-items-center justify-content-center mt-2"
                                                v-if="list.user?.allPermissions.length > 2 && state.showLess == false"
                                                @click="state.showLess = true"
                                            >
                                                <small class="text-primary" role="button"
                                                    ><span class="badge text-bg-primary">See Less<i class="bx bx-minus ms-1"></i></span
                                                ></small>
                                            </div>
                                        </div>
                                        <div v-else><span class="badge badge-soft-secondary font-size-11 m-1">No Permissions</span></div>
                                    </td>
                                    <td :style="{ backgroundColor: '#fff' }" class="text-center">
                                        <div v-if="list.user?.roles.length > 0">
                                            <div v-if="state.showLess">
                                                <div v-for="(role, index) in list.user.roles.slice(0, 3)" :key="`${role.id}_${role.name}`">
                                                    <span class="badge badge-soft-primary font-size-11 m-1"> {{ role.name }}</span>
                                                </div>
                                            </div>
                                            <div v-else-if="state.showLess == false">
                                                <div v-for="(role, index) in list.user.roles" :key="index">
                                                    <span class="badge badge-soft-primary font-size-11 m-1"> {{ role.name }}</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center mt-2" v-if="list.user.roles.length > 2 && state.showLess" @click="state.showLess = false">
                                                <small class="text-primary" role="button"
                                                    ><span class="badge text-bg-primary">See More<i class="bx bx-plus ms-1"></i></span
                                                ></small>
                                            </div>
                                            <div
                                                class="d-flex align-items-center justify-content-center mt-2"
                                                v-if="list.user.roles.length > 2 && state.showLess == false"
                                                @click="state.showLess = true"
                                            >
                                                <small class="text-primary" role="button"
                                                    ><span class="badge text-bg-primary">See Less<i class="bx bx-minus ms-1"></i></span
                                                ></small>
                                            </div>
                                        </div>
                                        <div v-else><span class="badge badge-soft-secondary font-size-11 m-1">No Roles</span></div>
                                    </td>
                                    <td :style="{ backgroundColor: '#fff' }" class="text-center">
                                        <div v-if="list.is_on_leave">
                                            <span class="badge text-bg-danger">On Leave</span>
                                        </div>
                                        <div v-else>
                                            <span class="badge text-bg-success">Available</span>
                                        </div>
                                    </td>
                                    <td :style="{ backgroundColor: '#fff' }" class="text-center">
                                        <div class="d-flex justify-content-end">
                                            <div class="list-unstyled hstack gap-1 mb-0">
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="View" @click="viewStaffProfile(list.id)">
                                                    <div class="btn btn-sm btn-soft-primary"><i class="mdi mdi-eye-outline"></i></div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit" @click="$refs.singleStaffInput.editStaff(list)">
                                                    <div href="#" class="btn btn-sm btn-soft-info"><i class="mdi mdi-pencil-outline"></i></div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Reset Password" v-if="isSuperAdmin">
                                                    <b-button variant="soft-primary" class="btn-sm" @click="resetMyPassword(list.id)"><i class="mdi mdi-lock-reset"></i></b-button>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Delete" v-if="isSuperAdmin">
                                                    <b-button variant="soft-danger" class="btn-sm" @click="loginAs(list.email, list.name)"><i class="mdi mdi-login-variant"></i></b-button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="w-100 d-flex align-items-center justify-content-center pt-4 pb-4" v-if="props.unicefStaff.next_page_url != null">
                            <InfiniteLoading @infinite="loadMoreStaffProfiles" class="invisible" />
                            <div class="text-success"><i class="bx bx-hourglass bx-spin me-2"></i> Loading more</div>
                        </div>
                    </div>
                    <div v-else class="text-center pt-5 pb-5"><div class="pt-5 pb-5">Nothing Found</div></div>
                </div>
                <div class="card-body" id="SP" v-else-if="state.title == 'Service Providers'">
                    <div class="">
                        <div class="d-flex flex-row justify-content-between col-12 mb-4">
                            <div class="d-flex align-items-center">
                                <div class="rounded-pill d-none d-lg-block" style="background-color: #f9f9f9; border: 0px">
                                    <div class="input-group d-flex flex-row align-items-center">
                                        <div class="mt-1">
                                            <span class="" style="font-size: medium"><i class="bx bx-search-alt"></i></span>
                                        </div>
                                        <div class=""><input type="text" class="form-control" placeholder="Search.." style="background-color: #f9f9f9; border: 0px" v-model="state.searchText" /></div>

                                        <div class="hstack gap-3" role="button">
                                            <div class="vr" style="color: #dbdbdb"></div>
                                            <div class="btn-group dropbottomstart">
                                                <div data-bs-toggle="dropdown" class="d-flex flex-row gap-3">
                                                    <div class="">{{ state.searchWithIn }}</div>
                                                    <div>
                                                        <i class="mdi mdi-chevron-down" aria-expanded="false"></i>
                                                    </div>
                                                </div>

                                                <ul class="dropdown-menu">
                                                    <!-- Dropdown menu links -->
                                                    <b-dropdown-item class="mb-1" @click="state.searchWithIn = 'All'">All</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="state.searchWithIn = 'Name'">Name</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="state.searchWithIn = 'Section'">Vendor Number</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="state.searchWithIn = 'Role'">Active</b-dropdown-item>
                                                    <b-dropdown-item class="mb-1" @click="state.searchWithIn = 'Availability'">Inactive</b-dropdown-item>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-row gap-3">
                                <div class="" v-if="isSuperAdmin || isHrStaff">
                                    <b-dropdown class="btn-group mb-2 mb-sm-0" variant="primary">
                                        <template #button-content>
                                            Add Service Provider / IP
                                            <i class="mdi mdi-dots-vertical ms-2"></i>
                                        </template>
                                        <b-dropdown-item>
                                            <div class="d-flex flex-row gap-2 align-items-center" @click="state.addSingleSPModal = true">
                                                <div>
                                                    <i class="fas fa-user-alt" style="font-size: small"></i>
                                                </div>
                                                <div>Single Input</div>
                                            </div>
                                        </b-dropdown-item>
                                        <b-dropdown-item>
                                            <div class="d-flex flex-row gap-2 align-items-center" @click="getSPBatchFile">
                                                <div><i class="mdi mdi-cloud-upload" style="font-size: medium"></i></div>
                                                <div>Batch Upload</div>
                                            </div>
                                        </b-dropdown-item>
                                        <b-dropdown-item>
                                            <div class="d-flex flex-row gap-2 align-items-center" @click="downlaodSpBatchTemplate">
                                                <div><i class="mdi mdi-microsoft-excel" style="font-size: medium"></i></div>
                                                <div>Download Template</div>
                                            </div>
                                        </b-dropdown-item>
                                    </b-dropdown>
                                </div>
                            </div>
                        </div>
                        <b-card no-body class="">
                            <div class="table-responsive">
                                <input type="file" @change="spBatchUplaod" hidden id="batchSPUploadFile" accept=".xls ,.xlsx" />

                                <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100" id="userList-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" :style="{ backgroundColor: '#eff2f7' }">#</th>
                                            <th scope="col" :style="{ backgroundColor: '#eff2f7' }">Name</th>
                                            <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="text-center">Vendor Number</th>
                                            <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="text-center">Account Status</th>

                                            <th scope="col" :style="{ backgroundColor: '#eff2f7', textAlign: 'end' }">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(list, index) in state.serviceProvidersList" :key="index + list.email">
                                            <td :style="{ backgroundColor: '#fff' }">
                                                <div v-if="list.user != null">
                                                    <img :src="list.user.profile_photo_url" :alt="''" class="rounded-circle avatar-xs object-fit-cover" />
                                                </div>
                                            </td>
                                            <td :style="{ backgroundColor: '#fff', maxWidth: '25vw', wordWrap: 'break-word' }">
                                                <h5 class="font-size-14 mb-1">
                                                    <a href="#" class="text-dark col-11 text-truncate">{{ list.name }}</a>
                                                </h5>
                                            </td>
                                            <td :style="{ backgroundColor: '#fff' }" class="text-center">
                                                <div v-if="list.vendor_number != null && list.vendor_number != ''">{{ list.vendor_number }}</div>
                                                <div v-else><span class="badge badge-soft-secondary font-size-11 m-1">Not Specified</span></div>
                                            </td>
                                            <td :style="{ backgroundColor: '#fff', maxWidth: '10vw' }" class="text-center">
                                                <div v-if="list?.user?.status == 'pending'">
                                                    <span class="badge badge-soft-danger font-size-11 m-1">Inactive</span>
                                                </div>
                                                <div v-else-if="list?.user?.status == 'active'">
                                                    <span class="badge badge-soft-success font-size-11 m-1">Active</span>
                                                </div>
                                                <div v-else><span class="badge badge-soft-secondary font-size-11 m-1">Disabled</span></div>
                                            </td>

                                            <td :style="{ backgroundColor: '#fff' }">
                                                <div class="d-flex justify-content-end">
                                                    <div class="list-unstyled hstack gap-1 mb-0">
                                                        <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="View" @click="selectedSP(list)">
                                                            <div class="btn btn-sm btn-soft-primary"><i class="mdi mdi-eye-outline"></i></div>
                                                        </div>
                                                        <div
                                                            v-if="list?.user?.status == 'active'"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            aria-label="Disable"
                                                            @click="deActivateSPAccount(list)"
                                                        >
                                                            <b-button variant="soft-danger" class="btn-sm"><i class="fas fa-times"></i></b-button>
                                                        </div>
                                                        <div v-else data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Activate" @click="activateSPAccount(list)">
                                                            <div href="#" class="btn btn-sm btn-soft-success"><i class="fas fa-check"></i></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="w-100 d-flex align-items-center justify-content-center pt-4 pb-4" v-if="props.serviceProviders.next_page_url != null">
                                    <InfiniteLoading @infinite="loadMoreServiceProviders" class="invisible" />
                                    <div class="text-success"><i class="bx bx-hourglass bx-spin me-2"></i> Loading more</div>
                                </div>
                            </div>
                        </b-card>
                    </div>
                </div>
                <div class="card-body" id="r&p" v-else-if="state.title == 'Roles & Permissions'">
                    <div class="">
                        <div class="d-flex flex-row justify-content-between mb-5">
                            <div class="d-flex align-items-center"></div>
                            <div class="d-flex flex-row gap-3" v-if="isSuperAdmin">
                                <div>
                                    <b-button variant="primary" @click="state.assignRolePermissionModal = true">
                                        <i class="bx bx bx-plus font-size-16 align-middle me-2"></i>
                                        Assign Roles & Permissions
                                    </b-button>
                                </div>
                                <div>
                                    <b-button variant="secondary" @click="addNewRole">
                                        <i class="bx bx bx-plus font-size-16 align-middle me-2"></i>
                                        Create New Role
                                    </b-button>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-row justify-content-between" style="width: 100%">
                            <div style="width: 100%">
                                <!-- <div class="mb-5 invisible">Roles</div> -->
                                <div>
                                    <div v-if="rolesByUsers.length != null && rolesByUsers.length > 0">
                                        <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100" id="userList-table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }">Role</th>
                                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="text-center">Currently With</th>
                                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="text-center">Created By</th>
                                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="text-end">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(list, index) in rolesByUsers" :key="`${index} _${list.email}`">
                                                    <td v-if="list != null" :style="{ backgroundColor: '#fff', maxWidth: '25vw', wordWrap: 'break-word' }">
                                                        <h5 class="font-size-14 mb-1">
                                                            <a href="#" class="text-dark col-11 text-truncate">{{ list?.name }}</a>
                                                        </h5>
                                                    </td>
                                                    <td :style="{ backgroundColor: '#fff' }" class="text-center">
                                                        <div v-if="list.users.length > 0">
                                                            <div v-if="state.showLess">
                                                                <div v-for="(username, index) in list.users.slice(0, 2)" :key="index">
                                                                    <span class="badge badge-soft-primary font-size-11 m-1"> {{ username }}</span>
                                                                </div>
                                                            </div>
                                                            <div v-else-if="state.showLess == false">
                                                                <div v-for="(username, index) in list.users" :key="index">
                                                                    <span class="badge badge-soft-primary font-size-11 m-1"> {{ username }}</span>
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="d-flex align-items-center justify-content-center mt-2"
                                                                v-if="list.users.length > 2 && state.showLess"
                                                                @click="state.showLess = false"
                                                            >
                                                                <small class="text-primary" role="button"
                                                                    ><span class="badge text-bg-primary">See More<i class="bx bx-plus ms-1"></i></span
                                                                ></small>
                                                            </div>
                                                            <div
                                                                class="d-flex align-items-center justify-content-center mt-2"
                                                                v-if="list.users.length > 2 && state.showLess == false"
                                                                @click="state.showLess = true"
                                                            >
                                                                <small class="text-primary" role="button"
                                                                    ><span class="badge text-bg-primary">See Less<i class="bx bx-minus ms-1"></i></span
                                                                ></small>
                                                            </div>
                                                        </div>
                                                        <div v-else><span class="badge badge-soft-secondary font-size-11 m-1">Not Assigned</span></div>
                                                    </td>
                                                    <td v-if="list != null" :style="{ backgroundColor: '#fff', maxWidth: '25vw', wordWrap: 'break-word' }">
                                                        <div class="text-center w-100">
                                                            <h5 class="font-size-14 mb-1">
                                                                <a href="#" class="text-dark col-11 text-truncate">{{ list.created_by }}</a>
                                                            </h5>
                                                        </div>
                                                    </td>

                                                    <td v-if="list != null" :style="{ backgroundColor: '#fff' }">
                                                        <div class="d-flex justify-content-end">
                                                            <div class="list-unstyled hstack gap-1 mb-0">
                                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="View">
                                                                    <div class="btn btn-sm btn-soft-primary" @click="editRole(list)"><i class="mdi mdi-eye-outline"></i></div>
                                                                </div>

                                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit" @click="editUserRole(list)">
                                                                    <div href="#" class="btn btn-sm btn-soft-info"><i class="mdi mdi-pencil-outline"></i></div>
                                                                </div>

                                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Delete">
                                                                    <b-button variant="soft-danger" class="btn-sm" @click="deleteRole(list)"><i class="mdi mdi-delete-outline"></i></b-button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div v-else class="d-flex flex-column gap-3 align-items-center justify-content-center p-5" style="height: 50vh; background-color: #f7f7f79b">
                                        <div class="fw-bold text-muted">Roles have not been created yet.</div>
                                        <b-button variant="secondary" @click="addNewRole">
                                            <i class="bx bx bx-plus font-size-16 align-middle me-2"></i>
                                            Create New Role
                                        </b-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body" id="availabilityPlanner" v-else-if="state.title == 'Availability Planner'">
                    <AvailabilityPlanner :upcomingLeaves="props.upcomingLeaves" :ongoingLeaves="props.ongoingLeaves" />
                </div>
                <div class="card-body" id="radio_call" v-else>
                    <staff-groups :unicef_staff="unicefStaff" />
                </div>
            </div>
        </div>
        <!-- view service provider details -->
        <b-modal v-model="state.viewSPDetailsModal" id="SPDetails" title-class="font-18" title="" centered header-class="d-flex justify-content-center" hide-header hide-footer>
            <div class="mb-4">
                <div class="text-center fw-bold">Service Provider Details</div>
            </div>
            <div class="mb-4">
                <div class="mb-3">
                    <label>Name:</label>
                    <div>{{ state.spDetails.name }}</div>
                </div>
                <div class="mb-3">
                    <label>Email:</label>
                    <div>{{ state.spDetails.email }}</div>
                </div>
                <div class="mb-3">
                    <label>Vendor Number:</label>
                    <div>{{ state.spDetails.vendor_number }}</div>
                </div>
                <div class="mb-3">
                    <label>Contact:</label>
                    <div>{{ state.spDetails.phone == null ? "Not Specified" : state.spDetails.phone }}</div>
                </div>
            </div>
        </b-modal>
        <!-- Add service provider modal -->
        <b-modal v-model="state.addSingleSPModal" id="addSP" title="Register Service Provider" title-class="font-18" centered hide-footer>
            <div class="mb-4">
                <label for="contract_end">Name</label>
                <input class="form-control" id="contract_end" type="text" v-model="state.spName" />
            </div>
            <div class="mb-4">
                <label for="contract_end">Email</label>
                <input class="form-control" id="contract_end" type="email" v-model="state.spEmail" />
            </div>
            <div class="mb-4">
                <label for="contract_end">Vendor Number</label>
                <input class="form-control" id="contract_end" type="text" v-model="state.spVendorNumber" />
            </div>

            <div class="mt-4 d-flex justify-content-center">
                <b-button variant="primary" @click="registerServiceProvider" :disabled="state.addRoleLoader"
                    ><i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.addRoleLoader"></i>Register</b-button
                >
            </div>
        </b-modal>

        <b-modal v-model="state.addRoleModal" id="addRole" title="Add User Role" title-class="font-18" centered hide-footer>
            <div class="mb-4">
                <label for="contract_end">Role Name</label>
                <input class="form-control" id="contract_end" type="text" v-model="state.userRoleDetails.roleName" />
            </div>
            <div class="mb-4">
                <label for="address">Role Description</label>
                <textarea class="form-control" :maxlength="225" rows="3" v-model="state.userRoleDetails.roleDescription"></textarea>
            </div>
            <div class="mb-5">
                <label for="address">Role Permissions</label>
                <v-select multiple v-model="state.userRoleDetails.rolePermissions" :options="state.activePermissions"></v-select>
                <!-- <input class="form-control" id="contract_end" type="text" v-model="state.userRoleDetails.rolePermissions" /> -->
            </div>
            <div class="mt-4 d-flex justify-content-center">
                <b-button variant="primary" @click="addUserRole" :disabled="state.addRoleLoader"
                    ><i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.addRoleLoader"></i>Add Role</b-button
                >
            </div>
        </b-modal>
        <b-modal v-model="state.assignRolePermissionModal" id="assignRolePermission" title="Assign Roles & Permissions" title-class="font-18" centered hide-footer>
            <div class="mb-4 mt-3">
                <label for="contract_end">Staff to Assign to:</label>
                <v-select v-model="state.assignRolesPermissions.staff" multiple :options="unicefStaffNames"></v-select>
            </div>

            <div class="mb-4">
                <b-form-checkbox id="customControlInline" name="checkbox-1" value="accepted" unchecked-value="not_accepted" v-model="state.assignDirectRole">
                    Assign Direct Permission
                </b-form-checkbox>
            </div>

            <div class="mb-5" v-if="state.assignDirectRole === 'accepted'">
                <label for="address">Permission:</label>
                <v-select multiple v-model="state.assignRolesPermissions.permissions" :options="state.activePermissions"></v-select>
            </div>
            <div class="mb-5" v-else>
                <label for="address">Role:</label>
                <v-select multiple v-model="state.assignRolesPermissions.roles" :options="state.roleNames"></v-select>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                <b-button variant="primary" @click="assignRolesPermissions" :disabled="state.assignRolesPermissionsLoader"
                    ><i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.assignRolesPermissionsLoader"></i>Assign</b-button
                >
            </div>
        </b-modal>
        <!-- editing role -->
        <b-modal v-model="state.editRoleModal" id="addRole" title="User Role" title-class="font-18" centered hide-footer>
            <div class="mb-4">
                <label for="contract_end">Role Name</label>
                <input class="form-control" id="contract_end" type="text" v-model="state.editRoleName" disabled="true" />
            </div>
            <div class="mb-4">
                <label for="address">Role Description</label>
                <textarea class="form-control" :maxlength="225" rows="3" v-model="state.editRoleDescription" disabled="true"></textarea>
            </div>
            <div class="mb-5">
                <label for="address">Role Permissions</label>
                <v-select multiple v-model="state.editRolePermissions" :options="state.activePermissions" :label="'name'" disabled="true"></v-select>
                <!-- <input class="form-control" id="contract_end" type="text" v-model="state.userRoleDetails.rolePermissions" /> -->
            </div>
            <!-- <div class="mt-4 d-flex justify-content-center">
                <b-button variant="primary" @click="addUserRole" :disabled="state.addRoleLoader"
                    ><i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.addRoleLoader"></i>Edit Role</b-button
                >
            </div> -->
        </b-modal>
        <!-- editing role -->
        <b-modal v-model="state.editUserRoleModal" id="addRole" title="Edit User Role" title-class="font-18" centered hide-footer>
            <div class="mb-4">
                <label for="contract_end">Role Name</label>
                <input class="form-control" id="contract_end" type="text" v-model="state.editRoleName" />
            </div>
            <div class="mb-4">
                <label for="address">Role Description</label>
                <textarea class="form-control" :maxlength="225" rows="3" v-model="state.editRoleDescription"></textarea>
            </div>
            <div class="mb-5">
                <label for="address">Role Permissions</label>
                <v-select multiple v-model="state.editRolePermissions" :options="state.activePermissions" :label="'name'"></v-select>
                <!-- <input class="form-control" id="contract_end" type="text" v-model="state.userRoleDetails.rolePermissions" /> -->
            </div>
            <div class="mt-4 d-flex justify-content-center">
                <b-button variant="primary" :disabled="state.editRolePermissions.length == 0 || state.editRoleDescription == '' || state.editRoleName == ''" @click="roleEdit"
                    ><i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.addRoleLoader"></i>Edit Role</b-button
                >
            </div>
        </b-modal>

        <!-- Add single user modal -->
        <single-staff-input ref="singleStaffInput" />
    </div>
</template>
