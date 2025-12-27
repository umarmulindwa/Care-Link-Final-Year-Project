<script setup>
import Swal from "sweetalert2";
import { reactive, onMounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Money3Component } from "v-money3";
import { showLoader } from "@/mixins/helpers";
import { GoogleMap, Marker, Polygon } from "vue3-google-map";
import axios from "axios";
import vSelect from "vue-select";

const props = defineProps({
    generalConfigs: Object,
    disticts: Array,
    banks: Array,
    donors: Array,
});

const state = reactive({
    config: {
        masked: false,
        prefix: "",
        suffix: "",
        thousands: ",",
        decimal: ".",
        precision: 2,
        disableNegative: false,
        disabled: false,
        min: null,
        max: null,
        allowBlank: false,
        minimumNumberOfCharacters: 0,
        shouldRound: true,
        focusOnRight: false,
    },
    //general configuration
    id: null,
    officeLocation: "",
    officeName: "",
    platformName: "",
    mgtGroupEmail: "",
    allStaffGroupEmail: "",
    consultantGroupEmail: "",
    bscGroupMail: "",
    pettyCashCustodian: "",
    maximumPettyCash: "",
    adminTransportAssociate: "",
    reminder3PP: "",
    staffReminder: "",
    vat: "",
    packingDuration: "",
    callTime: "",
    payrollDay: "",
    lowValCeil: "",
    dashboardChangeTime: "",
    supportResTime: "",
    listItemsNumber: "",
    travelPlanDeadline: "",
    disposalTime: "",
    staffRegTime: "",
    callTreeTime: "",
    invoiceCurrency: "SSP",
    invoiceAmount: "",
    // Form Errors
    generalFormErrors: [],
    //modals
    addDistrictModal: false,
    addDistrictLoader: false,

    //map

    //district
    districtID: null,
    districtLng: "",
    districtLat: "",
    selectedDistrictName: "",
    isEditDistrict: false,
    markCenter: { lat: 4.851259724661479, lng: 31.60444601876833 },

    //office location
    officeLocationModal: false,
    officeLocationLat: "",
    officeLocationLng: "",

    //bank
    addBankModal: false,
    addBankLoader: false,
    isEditBank: false,
    newBankName: "",
    bankId: null,

    //Donor
    addDonoeModal: false,
    donors: [
        "Bureau for Humanitarian Assistance",
        "European Commission / ECHO",
        "World Bank - Washington D.C.",
        "The United Kingdom",
        "UNOCHA",
        "United Nations Multi Partner Trust",
        "Global-Nutrition",
        "Canada",
        "Committee for UNICEF Switzerland",
        "New Zealand Committee for UNICEF",
        "German  National Committee",
    ],
    donorName: "",
    addDonorLoader: false,

    //airtimeData
    mtnAirtimeDataDate: [
        {
            label: "22",
            code: "22",
        },
        {
            label: "1",
            code: "1",
        },
        {
            label: "25",
            code: "25",
        },
        {
            label: "End of the Month",
            code: "End of the Month",
        },
    ],
    zainAirtimeDataDate: [
        {
            label: "22",
            code: "22",
        },
        {
            label: "1",
            code: "1",
        },
        {
            label: "25",
            code: "25",
        },
        {
            label: "End of the Month",
            code: "End of the Month",
        },
    ],
    mtnDate: "22",
    zainDate: "End of the Month",
    passwords_to_check: 5,
    password_expiry_days: 30,
});

//mounted
onMounted(() => {
    //setting default values for the general configurations
    state.id = props.generalConfigs.id;
    state.officeLocation = props.generalConfigs.office_location;
    state.officeName = props.generalConfigs.office_name;
    state.platformName = props.generalConfigs.platform_name;
    state.mgtGroupEmail = props.generalConfigs.management_email;
    state.allStaffGroupEmail = props.generalConfigs.all_staff_group_email;
    state.consultantGroupEmail = props.generalConfigs.consultants_group_email;
    state.bscGroupMail = props.generalConfigs.bsc_email;
    state.pettyCashCustodian = props.generalConfigs.bsc_petty_cash_custodian;
    state.maximumPettyCash = props.generalConfigs.maximum_pettycash;
    state.adminTransportAssociate = props.generalConfigs.transport_associate;
    state.reminder3PP = props.generalConfigs.reminder_days;
    state.staffReminder = props.generalConfigs.leave_reminder_days;
    state.vat = props.generalConfigs.vat_percentage;
    state.packingDuration = props.generalConfigs.parking_booking_max_duration;
    state.callTime = props.generalConfigs.identify_call_max_time;
    state.payrollDay = props.generalConfigs.payroll_day;
    state.lowValCeil = props.generalConfigs.low_value_theshold;
    state.dashboardChangeTime = props.generalConfigs.dashboard_interval;
    state.supportResTime = props.generalConfigs.target_response_time;
    state.listItemsNumber = props.generalConfigs.maximum_records;
    state.travelPlanDeadline = props.generalConfigs.travel_deadline;
    state.disposalTime = props.generalConfigs.quaterly_asset_day;
    state.staffRegTime = props.generalConfigs.update_details_day;
    state.callTreeTime = props.generalConfigs.call_tree_status_check_day;
    state.invoiceCurrency = JSON.parse(props.generalConfigs.low_value_theshold)?.invoice_currency;
    state.invoiceAmount = JSON.parse(props.generalConfigs.low_value_theshold)?.invoice_value_amount;
    state.mtnDate = {
        label: props.generalConfigs.mtn_AirtimeDate_date,
        code: props.generalConfigs.mtn_AirtimeDate_date,
    };
    state.zainDate = {
        label: props.generalConfigs.zain_AirtimeData_date,
        code: props.generalConfigs.zain_AirtimeData_date,
    };
    state.passwords_to_check = props.generalConfigs.passwords_to_check;
    state.password_expiry_days = props.generalConfigs.password_expiry_days;
});

//methods

function saveGeneralConfigs() {
    showLoader("Please wait...");
    const options = {
        "content-type": "multipart/form-data",
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
    };

    let formdata = new FormData();

    formdata.append("id", state.id);
    formdata.append("office_location", state.officeLocation);
    formdata.append("office_name", state.officeName);
    formdata.append("platform_name", state.platformName);
    formdata.append("management_email", state.mgtGroupEmail);
    formdata.append("bsc_email", state.bscGroupMail);
    formdata.append("reminder_days", state.reminder3PP);
    formdata.append("leave_reminder_days", state.staffReminder);
    formdata.append("bsc_cit_approval_email", state.bsc_cit_approval_email);
    formdata.append("bsc_task_assigner_email", state.bsc_task_assigner_email);
    formdata.append("bsc_petty_cash_custodian", state.pettyCashCustodian);
    formdata.append("vat_percentage", state.vat);
    formdata.append("parking_booking_max_duration", state.packingDuration);
    formdata.append("identify_call_max_time", state.callTime);
    formdata.append("payroll_day", state.payrollDay);
    formdata.append("dashboard_interval", state.dashboardChangeTime);
    formdata.append("target_response_time", state.supportResTime);
    formdata.append("maximum_records", state.listItemsNumber);
    formdata.append("travel_deadline", state.travelPlanDeadline);
    formdata.append("all_staff_group_email", state.allStaffGroupEmail);
    formdata.append("consultants_group_email", state.consultantGroupEmail);
    formdata.append("transport_associate", state.adminTransportAssociate);
    formdata.append("quaterly_asset_day", state.disposalTime);
    formdata.append("call_tree_status_check_day", state.callTreeTime);
    formdata.append("update_details_day", state.staffRegTime);
    formdata.append("invoice_currency", state.invoiceCurrency);
    formdata.append("invoice_value_amount", state.invoiceAmount);
    formdata.append("maximum_pettycash", state.maximumPettyCash);
    formdata.append("mtnDate", state.mtnDate.code);
    formdata.append("zainDate", state.zainDate.code);
    formdata.append("passwords_to_check", state.passwords_to_check);
    formdata.append("password_expiry_days", state.password_expiry_days);

    axios
        .post("api/system-configuration/generalConfig", formdata, options)
        .then((res) => {
            if (res.data.results === "success") {
                Swal.fire({
                    title: "Updated Successfully.",
                    icon: "success",
                    html: `<p style="font-size: 14px">System configurations have been updated successfully.</p>`,
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
                    Swal.close();
                }
            });
        });
}
//adding new District
function addDistrict() {
    state.isEditDistrict = false;
    state.addDistrictLoader = true;
    const options = {
        "content-type": "multipart/form-data",
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
    };

    let formdata = new FormData();
    formdata.append("id", state.districtID);
    formdata.append("district", state.newDistrictName);
    formdata.append("longtitude", state.districtLng);
    formdata.append("latitude", state.districtLat);

    axios
        .post("api/system-configuration/addNewDistrict", formdata, options)
        .then((res) => {
            state.addDistrictLoader = false;

            if (res.data.results === "success") {
                Swal.fire({
                    title: `${state.isEditDistrict ? "Updated Successfully" : "Added Successfully."}`,
                    icon: "success",
                    html: `<p style="font-size: 14px">${state.isEditDistrict ? "Your changes have been saved successfully" : "A new district has been added successfully."}</p>`,
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
                        router.reload();
                    }
                });
            }
        })
        .catch((err) => {
            console.log("Error:", err);
            state.addDistrictLoader = false;

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
async function showLocation(coord, index) {
    const { latLng } = coord;
    state.districtLat = latLng.lat();
    state.districtLng = latLng.lng();

    //getting the name of the place
    const response = await fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${state.districtLat},${state.districtLng}&result_type=locality&key=AIzaSyCBE7aRb0VJdRBlUnvjcQ_lnKWoAWuXUx8
`);
    const place = await response.json();
    if (place.results.length > 0) {
        state.newDistrictName = place.results[0].formatted_address;
    } else {
        state.newDistrictName = place.plus_code.compound_code;
    }
}

function editDistrict(index) {
    state.isEditDistrict = true;
    const districtToEdit = props.disticts[index];

    state.districtID = districtToEdit.id;
    state.newDistrictName = districtToEdit.name;
    state.districtLng = districtToEdit.longitude;
    state.districtLat = districtToEdit.latitude;
    state.markCenter = { lat: Number(state.districtLat), lng: Number(state.districtLng) };

    //show modal
    state.addDistrictModal = true;
}
function deteleDistrict(district) {
    Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to delete <strong>${district.name}</strong> from the district list.</p>`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Yes, Proceed",
    }).then((result) => {
        if (result.value) {
            const options = {
                "content-type": "multipart/form-data",
                headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
            };

            let formdata = new FormData();
            formdata.append("id", district.id);

            axios
                .post("api/system-configuration/deleteDistrict", formdata, options)
                .then((res) => {
                    if (res.data.results === "success") {
                        Swal.fire({
                            title: `Deleted Successfully.`,
                            icon: "success",
                            html: `<p style="font-size: 14px">The district has been deleted successfully</p>`,
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
                                state.addDistrictModal = false;
                                router.reload();
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
                            Swal.close();
                        }
                    });
                });
        }
    });
}

function selectOfficeLocation(coord, index) {
    const { latLng } = coord;
    state.officeLocationLat = latLng.lat();
    state.officeLocationLng = latLng.lng();
    state.officeLocation = `${state.officeLocationLat} , ${state.officeLocationLng}`;
}
function addBank() {
    state.addBankLoader = true;
    const options = {
        "content-type": "multipart/form-data",
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
    };

    let formdata = new FormData();
    formdata.append("id", state.bankId);
    formdata.append("bank", state.newBankName);

    axios
        .post("api/system-configuration/addNewBank", formdata, options)
        .then((res) => {
            state.addBankLoader = false;
            state.addBankModal = false;

            if (res.data.results === "success") {
                Swal.fire({
                    title: `${state.isEditBank ? "Updated Successfully" : "Added Successfully."}`,
                    icon: "success",
                    html: `<p style="font-size: 14px">${state.isEditBank ? "Your changes have been saved successfully" : "A new bank has been added successfully."}</p>`,
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
                        router.reload();
                    }
                });
            }
        })
        .catch((err) => {
            console.log("Error:", err);
            state.addBankLoader = false;
            state.addBankModal = false;

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

function editBank(bank) {
    state.isEditBank = true;
    state.newBankName = bank.name;
    state.bankId = bank.id;
    state.addBankModal = true;
}

function deleteBank(bank) {
    Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to delete <strong>${bank.name}</strong> from the bank list.</p>`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Yes, Proceed",
    }).then((result) => {
        if (result.value) {
            const options = {
                "content-type": "multipart/form-data",
                headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
            };

            let formdata = new FormData();
            formdata.append("id", bank.id);

            axios
                .post("api/system-configuration/deleteBank", formdata, options)
                .then((res) => {
                    if (res.data.results === "success") {
                        Swal.fire({
                            title: `Deleted Successfully.`,
                            icon: "success",
                            html: `<p style="font-size: 14px">The bank has been deleted successfully</p>`,
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
                                router.reload();
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
                            Swal.close();
                        }
                    });
                });
        }
    });
}

function deleteDonor(donor) {
    Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to delete <strong>${donor.donor_name}</strong> from the donor list.</p>`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Yes, Proceed",
    }).then((result) => {
        if (result.value) {
            showLoader("Please wait...");

            const options = {
                "content-type": "multipart/form-data",
                headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
            };

            let formdata = new FormData();
            formdata.append("id", donor.id);

            axios
                .post("api/system-configuration/deleteDonor", formdata, options)
                .then((res) => {
                    if (res.data.results === "success") {
                        Swal.fire({
                            title: `Deleted Successfully.`,
                            icon: "success",
                            html: `<p style="font-size: 14px">The donor has been deleted successfully</p>`,
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
                                router.reload();
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
                        router.reload()
                            Swal.close();
                    });
                });
        }
    });
}

function addDonor() {
    state.addDonorLoader = true;
    const options = {
        "content-type": "multipart/form-data",
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
    };

    let formdata = new FormData();
    formdata.append("donor", state.donorName);

    axios
        .post("api/system-configuration/addNewDonor", formdata, options)
        .then((res) => {
            state.addDonorLoader = false;
            state.addDonoeModal = false;

            if (res.data.results === "success") {
                Swal.fire({
                    title: `Added Successfully`,
                    icon: "success",
                    html: `<p style="font-size: 14px">A new Donor has been added successfully.</p>`,
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
                        router.reload();
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
             state.addDonorLoader = false;
             state.addDonoeModal = false;
            });
        });
}
</script>
<template>
    <div class="p-4">
        <b-tabs>
            <b-tab active>
                <template v-slot:title>
                    <span class="d-inline-block d-sm-none">
                        <i class="far fa-user"></i>
                    </span>
                    <span class="d-none d-sm-inline-block">General Configurations</span>
                </template>

                <div class="col-7 mt-5">
                    <div class="mb-4">
                        <label for="request_title">Location of UNICEF office</label>
                        <input class="form-control" id="request_title" type="text" v-model="state.officeLocation" @focus="state.officeLocationModal = true" />
                    </div>
                    <div class="mb-4">
                        <label for="request_title">Office Name</label>
                        <input class="form-control" id="request_title" type="text" v-model="state.officeName" />
                    </div>
                    <div class="mb-4">
                        <label for="request_title">Platform Name</label>
                        <input class="form-control" id="request_title" type="text" v-model="state.platformName" />
                    </div>
                    <div class="mb-4">
                        <label for="request_title">Management's Group Email</label>
                        <input class="form-control" id="request_title" type="text" v-model="state.mgtGroupEmail" />
                    </div>
                    <div class="mb-4">
                        <label for="request_title">UNICEF South Sudan All Staff Group Email</label>
                        <input class="form-control" id="request_title" type="text" v-model="state.allStaffGroupEmail" />
                    </div>
                    <div class="mb-4">
                        <label for="request_title">UNICEF South Sudan Consultant's Group Email</label>
                        <input class="form-control" id="request_title" type="text" v-model="state.consultantGroupEmail" />
                    </div>
                    <div class="mb-4">
                        <label for="request_title">BSC's Group Email</label>
                        <input class="form-control" id="request_title" type="text" v-model="state.bscGroupMail" />
                    </div>
                    <!-- using permissions for this -->
                    <!-- <div class="mb-4">
                        <label for="request_title">BSC's Petty Cash Custodian</label>
                        <input class="form-control" id="request_title" type="text" v-model="state.pettyCashCustodian" />
                    </div> -->
                    <div class="mb-4">
                        <label for="request_title">Maximum Petty Cash Amount </label>

                        <div class="d-flex flex-row gap-2">
                            <div class="col-2">
                                <select class="form-control" id="amount" :disabled="true">
                                    <option value="SSP" :selected="true">SSP</option>
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                </select>
                            </div>
                            <div class="col-10">
                                <Money3Component class="form-control" v-model="state.maximumPettyCash" v-bind="state.config"></Money3Component>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="request_title">Administration Transport Associate</label>
                        <input class="form-control" id="request_title" type="text" v-model="state.adminTransportAssociate" />
                    </div>
                    <div class="mb-4">
                        <label for="request_title">Send a reminder to 3PP to check their account balance for fund by UNICEF (BSC, GSSC) once every</label>
                        <input class="form-control" id="request_title" type="number" v-model="state.reminder3PP" />
                    </div>
                    <div class="mb-4">
                        <label for="request_title">Send a reminder to Staff with Availability Status,BSC etc Permissions to update key information once every</label>
                        <input class="form-control" id="request_title" type="number" v-model="state.staffReminder" />
                    </div>
                    <div class="mb-4">
                        <label for="request_title">VAT %age</label>
                        <input class="form-control" id="request_title" type="number" v-model="state.vat" />
                    </div>
                    <div class="mb-4">
                        <label for="request_title">Maximum duration a parking slot can be Booked for</label>
                        <input class="form-control" id="request_title" type="number" v-model="state.packingDuration" />
                    </div>
                    <div class="mb-4">
                        <label for="request_title">Maximum time staff can take to identify which of their calls are 'Personal' or 'Official'</label>
                        <input class="form-control" id="request_title" type="number" v-model="state.callTime" />
                    </div>
                    <!-- <div class="mb-4">
                        <label for="request_title">UNICEF Bank Accounts for reconciliation on Staff Bills (e.g Phone Bills)</label>
                        <input class="form-control" id="request_title" type="text" v-model="state.unicefBanks" />
                    </div> -->
                    <div class="mb-4">
                        <label for="request_title">Day of the month when Payroll is run</label>
                        <input class="form-control" id="request_title" type="number" v-model="state.payrollDay" />
                    </div>
                    <div class="mb-4">
                        <label for="request_title">UNICEF Low Value Threshold</label>
                        <div class="d-flex flex-row gap-2">
                            <div class="col-2">
                                <select class="form-control" id="invocurrency" v-model="state.invoiceCurrency">
                                    <option value="SSP" :selected="true">SSP</option>
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                </select>
                            </div>
                            <div class="col-10">
                                <Money3Component class="form-control" v-model="state.invoiceAmount" v-bind="state.config"></Money3Component>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="request_title">Interval between which successive dashboards change</label>
                        <input class="form-control" id="request_title" type="number" v-model="state.dashboardChangeTime" />
                    </div>
                    <div class="mb-4">
                        <label for="request_title">Average target response time to Support Requests</label>
                        <input class="form-control" id="request_title" type="number" v-model="state.supportResTime" />
                    </div>
                    <div class="mb-4">
                        <label for="request_title">Maximum number of records to show per list</label>
                        <input class="form-control" id="request_title" type="number" v-model="state.listItemsNumber" />
                    </div>
                    <div class="mb-4">
                        <label for="request_title">Deadline for receipt of Travel Plans</label>
                        <input class="form-control" id="request_title" type="text" v-model="state.travelPlanDeadline" />
                    </div>
                    <div class="mb-4">
                        <label for="request_title">Day of the month when Quarterly Asset is done for the Disposal Process</label>
                        <input class="form-control" id="request_title" type="number" v-model="state.disposalTime" />
                    </div>
                    <div class="mb-4">
                        <label for="request_title">Number of days after which Staff are reminded to update their details in Staff Register</label>
                        <input class="form-control" id="request_title" type="number" v-model="state.staffRegTime" />
                    </div>
                    <div class="mb-4">
                        <label for="request_title">Day of the month when Super Administrators are reminded to check on the status of the Call Tree</label>
                        <input class="form-control" id="request_title" type="text" v-model="state.callTreeTime" />
                    </div>
                    <div class="mb-4">
                        <label for="pesonal_id">Day of the month MTN numbers receive Airtime and Data</label>
                        <v-select v-model="state.mtnDate" :options="state.mtnAirtimeDataDate"></v-select>
                    </div>
                    <div class="mb-4">
                        <label for="pesonal_id">Day of the month ZAIN numbers receive Airtime and Data</label>
                        <v-select v-model="state.zainDate" :options="state.zainAirtimeDataDate"></v-select>
                    </div>
                    <div class="mb-4">
                        <label for="">Password Expiry Duration in Days</label>
                        <input type="number" class="form-control" v-model="state.password_expiry_days" />
                    </div>
                    <div class="mb-4">
                        <label for="">Specify the number of previous passwords to validate against when checking new passwords</label>
                        <input type="number" class="form-control" v-model="state.passwords_to_check" />
                    </div>
                </div>
                <div class="">
                    <b-button variant="primary" @click="saveGeneralConfigs">Save Changes</b-button>
                </div>
            </b-tab>
            <b-tab>
                <template v-slot:title>
                    <span class="d-inline-block d-sm-none">
                        <i class="far fa-user"></i>
                    </span>
                    <span class="d-none d-sm-inline-block">Districts in South Sudan</span>
                </template>
                <div>
                    <div class="d-flex flex-row justify-content-between col-12 mb-4 mt-5">
                        <div class="d-flex align-items-center invisible">
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
                                                <div>
                                                    <i class="mdi mdi-chevron-down" aria-expanded="false"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-row gap-3">
                            <div>
                                <b-button variant="primary" @click="state.addDistrictModal = true">Add District</b-button>
                            </div>
                        </div>
                    </div>
                    <div v-if="props.disticts.length > 0">
                        <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100" id="userList-table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">District</th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7', textAlign: 'end' }"><div class="me-4">Action</div></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(district, index) in props.disticts" :key="`${index}_${district.id}`">
                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div role="button">
                                            <div class="col-12 text-truncate">{{ district.name }}</div>
                                        </div>
                                    </td>

                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div class="d-flex justify-content-end">
                                            <div class="list-unstyled hstack gap-1 mb-0">
                                                <div class="invisible" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="View" @click="">
                                                    <div class="btn btn-sm btn-soft-primary"><i class="mdi mdi-eye-outline"></i></div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit" @click="editDistrict(index)">
                                                    <div href="#" class="btn btn-sm btn-soft-info"><i class="mdi mdi-pencil-outline"></i></div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Delete" @click="deteleDistrict(district)">
                                                    <b-button variant="soft-danger" class="btn-sm" @click=""><i class="mdi mdi-delete-outline"></i></b-button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center pt-5 mt-5 mb-5">District List is Empty</div>
                </div>
            </b-tab>
            <b-tab>
                <template v-slot:title>
                    <span class="d-inline-block d-sm-none">
                        <i class="far fa-user"></i>
                    </span>
                    <span class="d-none d-sm-inline-block">Banks in South Sudan</span>
                </template>
                <div>
                    <div class="d-flex flex-row justify-content-between col-12 mb-4 mt-5">
                        <div class="d-flex align-items-center invisible">
                            <div class="rounded-pill d-none d-lg-block" style="background-color: #f9f9f9; border: 0px">
                                <div class="input-group d-flex flex-row align-items-center">
                                    <div class="mt-1">
                                        <span class="" style="font-size: medium"><i class="bx bx-search-alt"></i></span>
                                    </div>

                                    <div class="hstack gap-3" role="button">
                                        <div class="vr" style="color: #dbdbdb"></div>
                                        <div class="btn-group dropbottomstart">
                                            <div data-bs-toggle="dropdown" class="d-flex flex-row gap-3">
                                                <div>
                                                    <i class="mdi mdi-chevron-down" aria-expanded="false"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-row gap-3">
                            <div>
                                <b-button variant="primary" @click="state.addBankModal = true">Add Bank</b-button>
                            </div>
                        </div>
                    </div>
                    <div v-if="props.banks.length > 0">
                        <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100" id="userList-table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Banks</th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7', textAlign: 'end' }"><div class="me-4">Action</div></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(bank, index) in props.banks" :key="`${index}_${bank.id}`">
                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div role="button">
                                            <div class="col-12 text-truncate">{{ bank.name }}</div>
                                        </div>
                                    </td>

                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div class="d-flex justify-content-end">
                                            <div class="list-unstyled hstack gap-1 mb-0">
                                                <div class="invisible" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="View" @click="">
                                                    <div class="btn btn-sm btn-soft-primary"><i class="mdi mdi-eye-outline"></i></div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit" @click="editBank(bank)">
                                                    <div href="#" class="btn btn-sm btn-soft-info"><i class="mdi mdi-pencil-outline"></i></div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Delete" @click="deleteBank(bank)">
                                                    <b-button variant="soft-danger" class="btn-sm" @click=""><i class="mdi mdi-delete-outline"></i></b-button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center pt-5 mt-5 mb-5">Bank List is Empty</div>
                </div>
            </b-tab>
            <b-tab>
                <template v-slot:title>
                    <span class="d-inline-block d-sm-none">
                        <i class="far fa-user"></i>
                    </span>
                    <span class="d-none d-sm-inline-block">Donors</span>
                </template>
                <div>
                    <div class="d-flex flex-row justify-content-between col-12 mb-4 mt-5">
                        <div class="d-flex align-items-center invisible">
                            <div class="rounded-pill d-none d-lg-block" style="background-color: #f9f9f9; border: 0px">
                                <div class="input-group d-flex flex-row align-items-center">
                                    <div class="mt-1">
                                        <span class="" style="font-size: medium"><i class="bx bx-search-alt"></i></span>
                                    </div>

                                    <div class="hstack gap-3" role="button">
                                        <div class="vr" style="color: #dbdbdb"></div>
                                        <div class="btn-group dropbottomstart">
                                            <div data-bs-toggle="dropdown" class="d-flex flex-row gap-3">
                                                <div>
                                                    <i class="mdi mdi-chevron-down" aria-expanded="false"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-row gap-3">
                            <div>
                                <b-button variant="primary" @click="state.addDonoeModal = true">Add Donor</b-button>
                            </div>
                        </div>
                    </div>
                    <div v-if="props.donors.length > 0">
                        <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100" id="userList-table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Donor</th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7', textAlign: 'end' }"><div class="me-4">Action</div></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(donor, index) in props.donors" :key="`${index}_${donor.id}`">
                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div role="button">
                                            <div class="col-12 text-truncate">{{ donor.donor_name }}</div>
                                        </div>
                                    </td>

                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div class="d-flex justify-content-end">
                                            <div class="list-unstyled hstack gap-1 mb-0">
                                                <!-- <div class="invisible" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="View" @click="">
                                                    <div class="btn btn-sm btn-soft-primary"><i class="mdi mdi-eye-outline"></i></div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit" @click="editBank(bank)">
                                                    <div href="#" class="btn btn-sm btn-soft-info"><i class="mdi mdi-pencil-outline"></i></div>
                                                </div> -->
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Delete" @click="deleteDonor(donor)">
                                                    <b-button variant="soft-danger" class="btn-sm" @click=""><i class="mdi mdi-delete-outline"></i></b-button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center pt-5 mt-5 mb-5">Donor List is Empty</div>
                </div>
            </b-tab>
        </b-tabs>
        <b-modal class="" v-model="state.addDistrictModal" size="lg" title="Add New District" title-class="font-18" centered hide-footer hide-title>
            <div class="mb-5 mt-3">
                <label>District Name:</label>
                <input class="form-control" type="text" v-model="state.newDistrictName" />
            </div>

            <div class="mb-5">
                <label>Drag Maker to get coordinates:</label>
                <div class="mt-3 mb-3">
                    {{ state.districtLat }}
                    {{ state.districtLng }}
                </div>

                <GoogleMap api-key="AIzaSyCBE7aRb0VJdRBlUnvjcQ_lnKWoAWuXUx8" :center="state.markCenter" :zoom="16" style="height: 400px">
                    <Marker :options="{ position: state.markCenter, draggable: true }" @dragend="showLocation" />
                </GoogleMap>
            </div>

            <div class="text-center mb-4">
                <b-button variant="primary" @click="addDistrict" :disabled="state.addDistrictLoader"
                    ><i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.addDistrictLoader"></i>{{ state.isEditDistrict ? "Save Changes" : "Add District" }}</b-button
                >
            </div>
        </b-modal>
        <b-modal class="" v-model="state.officeLocationModal" size="lg" title="Drag marker to select office location" title-class="font-18" centered hide-footer hide-title>
            <div class="mb-5">
                <div class="mt-3 mb-3">
                    {{ state.officeLocationLat }}
                    {{ state.officeLocationLng }}
                </div>

                <GoogleMap api-key="AIzaSyCBE7aRb0VJdRBlUnvjcQ_lnKWoAWuXUx8" :center="state.markCenter" :zoom="16" style="height: 400px">
                    <Marker :options="{ position: state.markCenter, draggable: true }" @dragend="selectOfficeLocation" />
                </GoogleMap>
            </div>

            <div class="text-center mb-4">
                <b-button variant="primary" @click="state.officeLocationModal = false" :disabled="state.addDistrictLoader"
                    ><i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.addDistrictLoader"></i>Select Location</b-button
                >
            </div>
        </b-modal>
        <b-modal class="" v-model="state.addBankModal" size="md" title="Add Bank" title-class="font-18" centered hide-footer hide-title>
            <div class="mb-5 mt-3">
                <label>Bank Name:</label>
                <input class="form-control" type="text" v-model="state.newBankName" />
            </div>

            <div class="text-center mb-4">
                <b-button variant="primary" @click="addBank" :disabled="state.addBankLoader"
                    ><i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.addBankLoader"></i>{{ state.isEditBank ? "Save Changes" : "Add Bank" }}</b-button
                >
            </div>
        </b-modal>
        <b-modal class="" v-model="state.addDonoeModal" size="md" title="Add Donor" title-class="font-18" centered hide-footer hide-title>
            <div class="mb-5 mt-3">
                <label>Donor Name</label>
                <v-select v-model="state.donorName" :options="state.donors" :taggable="true"></v-select>
            </div>

            <div class="text-center mb-4">
                <b-button variant="primary" @click="addDonor" :disabled="state.addDonorLoader"
                    ><i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.addDonorLoader"></i> Add Donor</b-button
                >
            </div>
        </b-modal>
    </div>
</template>
