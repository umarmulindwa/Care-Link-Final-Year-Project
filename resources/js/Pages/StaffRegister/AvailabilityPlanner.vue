<script setup>
import { reactive, computed, onMounted } from "vue";
import axios from "axios";
import moment from "moment";
import { usePage, router } from "@inertiajs/vue3";
import { read, utils } from "xlsx";
import Swal from "sweetalert2";

const props = defineProps({
    ongoingLeaves: Array,
    upcomingLeaves: Array,
});
const state = reactive({
    testmodal: false,
    showLoader: false,
    archivedLeaves: [],
    batchLeaveUploadFile: null,
    batchUploadLeave: [],
    editAvailabilityModal: false,
    currentLeaveScheduleStart:"",
    currentLeaveScheduleEnd:"",
    currentLeaveScheduleDelegateto:"",
    currentLeaveScheduleType:"",
});

onMounted(() => {
    //getting Archive Leaves
    state.showLoader = true;
    const options = {
        headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
        },
    };
    axios
        .get("api/staff-register/getArchivedLeaves", options)
        .then((res) => {
            console.log(res);

            state.archivedLeaves = res.data.results;
            state.showLoader = false;
        })
        .catch((err) => {
            console.log("this is the error", err);
            state.showLoader = false;

            Swal.fire({
                title: "Can not fetch Archived Leaves.",
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
                    router.visit("/staff_register");
                }
            });
        });
});

function dateDiff(startDate, endDate) {
    const start = moment(startDate, "YYYY-MM-DD");
    const end = moment(endDate, "YYYY-MM-DD");
    const days = moment.duration(end.diff(start)).asDays();

    return days;
}

function dateFormat(date2format) {
    return moment(date2format).fromNow();
}
function dateFormat2(date2format) {
    return moment(date2format).format("Do MMM YYYY");
}
function getBatchFile() {
    document.getElementById("batchLeaveScheduleUpload").click();
}
function isValidDateFormat(dateString) {
    const format = "YYYY/MM/DD";
    return moment(dateString, format, true).isValid();
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
    state.batchLeaveUploadFile = event.target.files ? event.target.files[0] : null;
    //save staff if sheet passes validation
    const options = {
        headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
        },
    };

    if (state.batchLeaveUploadFile != null) {
        const leaveSheetArrayBuffer = await state.batchLeaveUploadFile.arrayBuffer();

        //parsing
        const wb = read(leaveSheetArrayBuffer);

        /* generate array of objects from first worksheet */
        const ws = wb.Sheets[wb.SheetNames[0]]; // get the first worksheet
        const data = utils.sheet_to_json(ws); // generate objects

        /* update state */
        state.batchUploadLeave = data;
        console.log(state.batchUploadLeave[0]);
        //validating data (current validation only checks form email and name)
        let isDataClear = true;
        for (let index = 0; index < state.batchUploadLeave.length; index++) {
            const obj = state.batchUploadLeave[index];

            if (
                !obj.hasOwnProperty("Employee email") ||
                !obj.hasOwnProperty("Employee name") ||
                !obj.hasOwnProperty("Leave Starts") ||
                !obj.hasOwnProperty("Leave Ends") ||
                !obj.hasOwnProperty("Officer In Charge Email") ||
                !obj.hasOwnProperty("Officer In Charge Name")
            ) {
                Swal.fire({
                    title: "Missing Field",
                    icon: "error",
                    html: `<p style="font-size: 14px">The leave schedule at <b>row ${index + 2}</b> has missing information,<br> all fields are required.</p>`,
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
        if (!isDataClear) {
            return;
        }

        //formating dates
        let invalidDate = false;
        const formattedDateData = state.batchUploadLeave.map((obj, index) => {
            if (moment(obj["Leave Starts"]).isValid() && moment(obj["Leave Ends"]).isValid() && isValidDateFormat(obj["Leave Starts"]) && isValidDateFormat(obj["Leave Ends"])) {
                return { ...obj, "Leave Starts": moment(obj["Leave Starts"]).format("YYYY-MM-DD HH:mm:ss"), "Leave Ends": moment(obj["Leave Ends"]).format("YYYY-MM-DD HH:mm:ss") };
            } else {
                Swal.fire({
                    title: "Invalid Date Detected",
                    icon: "error",
                    html: `<p style="font-size: 14px">There is an invalid date on <b>row ${index + 2}</b>, use date format <br/> yyyy/mm/dd ie. (2024/05/24).</p>`,
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
                invalidDate = true;
                return;
            }
        });

        if (invalidDate) {
            return;
        }
        // console.log("formatted", formattedDateData);
        axios
            .post("api/staff-register/batchUploadLeave", { dataArray: formattedDateData }, options)
            .then((res) => {
                console.log(res);
                if (res.data.results === "success") {
                    Swal.fire({
                        title: "Schedules Added Successfully ",
                        icon: "success",
                        html: `<p style="font-size: 14px">You have successfully added <b>${state.batchUploadLeave.length}</b> new leave schedules </p>`,
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
                        router.visit("/staff_register");
                    }
                });
            });
    }
}

function deleteSelectedSchedule(id) {
    Swal.close();

    const options = {
        headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
        },
    };

    Swal.fire({
        title: "Are you sure?",
        html: '<p style="font-size: 14px">You are about to delete this leave Schedule. Proceed?</p>',
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Proceed",
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Please wait...",
                allowOutsideClick: false,
                showCancelButton: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            let formData = new FormData();
            formData.append("leave_id", id);

            axios
                .post(`/api/staff-register/delete-leave`, formData, options)
                .then((res) => {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        html: "<p class='font-size: 13px'>You have successfully deleteted the leave schedules.</p>",
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        showCloseButton: false,
                        confirmButtonText: "Ok",
                        confirmButtonColor: "#32CD32",
                    }).then((result) => {
                        router.visit("/staff_register");
                        Swal.close();
                    });
                })
                .catch(function (error) {
                    console.log(error);
                    state.isProcessing = false;
                    Swal.fire({
                        title: "Something Went Wrong",
                        icon: "error",
                        html: `<p style="font-size: 14px">There is something that went wrong and it is not your fault. Please, reach out to ICT for help.</p>`,
                        showCancelButton: false,
                        focusConfirm: true,
                        confirmButtonText: "OK",
                        confirmButtonColor: "#43ad60",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        closeOnClickOutside: false,
                    }).then((result) => {
                        router.visit("/staff_register");
                        Swal.close();
                    });
                });
        }
    });
}

function editLeave(leave){
    state.currentLeaveScheduleStart=leave.start
    state.currentLeaveScheduleEnd=leave.end
    state.currentLeaveScheduleDelegateto=leave.delegated_to
    state.currentLeaveScheduleType=""
 state.editAvailabilityModal = true;
}
</script>
<template>
    <div>
        <div class="d-flex flex-row justify-content-between col-12 mb-4">
            <div></div>
            <div>
                <b-dropdown class="btn-group mb-2 mb-sm-0" variant="primary">
                    <template #button-content>
                        Upload Schedules
                        <i class="mdi mdi-dots-vertical ms-2"></i>
                    </template>
                    <b-dropdown-item>
                        <div class="d-flex flex-row gap-2 align-items-center" @click="getBatchFile">
                            <div><i class="mdi mdi-cloud-upload" style="font-size: medium"></i></div>
                            <div>Batch Upload</div>
                        </div>
                    </b-dropdown-item>
                    <b-dropdown-item>
                        <div class="d-flex flex-row gap-2 align-items-center">
                            <div><i class="mdi mdi-microsoft-excel" style="font-size: medium"></i></div>
                            <div>Download Template</div>
                        </div>
                    </b-dropdown-item>
                </b-dropdown>
                <input type="file" @change="onChange" hidden id="batchLeaveScheduleUpload" accept=".xls ,.xlsx" />
            </div>
        </div>

        <div>
            <b-tabs>
                <b-tab active>
                    <template v-slot:title>
                        <span class="d-inline-block d-sm-none">
                            <i class="far fa-user"></i>
                        </span>
                        <span class="d-none d-sm-inline-block ms-3 me-3">Upcoming</span>
                    </template>
                    <div class="mt-5">
                        <div v-if="props.upcomingLeaves.length > 0">
                            <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100" id="userList-table">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Name</th>
                                        <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2 text-center">Starts</th>
                                        <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2 text-center">Ends</th>
                                        <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2 text-center">Duration</th>
                                        <th scope="col" :style="{ backgroundColor: '#eff2f7', textAlign: 'end' }"><div class="me-4">Action</div></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(leave, index) in props.upcomingLeaves" :key="`${index}_${leave.id}`">
                                        <td :style="{ backgroundColor: '#fff' }">
                                            <div role="button">
                                                <div class="col-12 text-truncate text-center">{{ leave.name }}</div>
                                            </div>
                                        </td>
                                        <td :style="{ backgroundColor: '#fff' }">
                                            <div role="button">
                                                <div class="col-12 text-truncate text-center">{{ `${dateFormat2(leave.start)}(${dateFormat(leave.start)})` }}</div>
                                            </div>
                                        </td>
                                        <td :style="{ backgroundColor: '#fff' }">
                                            <div role="button">
                                                <div class="col-12 text-truncate text-center">{{ `${dateFormat2(leave.end)}` }}</div>
                                            </div>
                                        </td>
                                        <td :style="{ backgroundColor: '#fff' }">
                                            <div role="button">
                                                <div class="col-12 text-truncate text-center">{{ `${dateDiff(leave.start, leave.end)} Day(s)` }}</div>
                                            </div>
                                        </td>

                                        <td :style="{ backgroundColor: '#fff' }">
                                            <div class="d-flex justify-content-end">
                                                <div class="list-unstyled hstack gap-1 mb-0">
                                                    <div class="invisible" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="View" @click="">
                                                        <div class="btn btn-sm btn-soft-primary"><i class="mdi mdi-eye-outline"></i></div>
                                                    </div>
                                                    <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit" @click="editLeave(leave)">
                                                        <div href="#" class="btn btn-sm btn-soft-info"><i class="mdi mdi-pencil-outline"></i></div>
                                                    </div>
                                                    <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Delete" @click="deleteSelectedSchedule(leave.id)">
                                                        <b-button variant="soft-danger" class="btn-sm" @click=""><i class="mdi mdi-delete-outline"></i></b-button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center pt-5 mt-5 mb-5">There are no upcoming Leave Schedules.</div>
                    </div>
                </b-tab>
                <b-tab>
                    <template v-slot:title>
                        <span class="d-inline-block d-sm-none">
                            <i class="far fa-user"></i>
                        </span>
                        <span class="d-none d-sm-inline-block ms-3 me-3">On-going</span>
                    </template>
                    <div class="mt-5">
                        <div v-if="props.ongoingLeaves.length > 0">
                            <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100" id="userList-table">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Name</th>
                                        <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2 text-center">Started</th>
                                        <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2 text-center">Ends</th>
                                        <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2 text-center">Duration</th>
                                        <th scope="col" :style="{ backgroundColor: '#eff2f7', textAlign: 'end' }"><div class="me-4">Action</div></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(leave, index) in props.ongoingLeaves" :key="`${index}_${leave.id}`">
                                        <td :style="{ backgroundColor: '#fff' }">
                                            <div role="button">
                                                <div class="col-12 text-truncate text-center">{{ leave.name }}</div>
                                            </div>
                                        </td>
                                        <td :style="{ backgroundColor: '#fff' }">
                                            <div role="button">
                                                <div class="col-12 text-truncate text-center">{{ `${dateFormat2(leave.start)}(${dateFormat(leave.start)})` }}</div>
                                            </div>
                                        </td>
                                        <td :style="{ backgroundColor: '#fff' }">
                                            <div role="button">
                                                <div class="col-12 text-truncate text-center">{{ `${dateFormat2(leave.end)}` }}</div>
                                            </div>
                                        </td>
                                        <td :style="{ backgroundColor: '#fff' }">
                                            <div role="button">
                                                <div class="col-12 text-truncate text-center">{{ `${dateDiff(leave.start, leave.end)} Day(s)` }}</div>
                                            </div>
                                        </td>

                                        <td :style="{ backgroundColor: '#fff' }">
                                            <div class="d-flex justify-content-end">
                                                <div class="list-unstyled hstack gap-1 mb-0">
                                                    <div class="invisible" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="View" @click="">
                                                        <div class="btn btn-sm btn-soft-primary"><i class="mdi mdi-eye-outline"></i></div>
                                                    </div>
                                                    <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit" @click="editLeave(leave)">
                                                        <div href="#" class="btn btn-sm btn-soft-info"><i class="mdi mdi-pencil-outline"></i></div>
                                                    </div>
                                                    <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Delete" @click="deleteSelectedSchedule(leave.id)">
                                                        <b-button variant="soft-danger" class="btn-sm" @click=""><i class="mdi mdi-delete-outline"></i></b-button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center pt-5 mt-5 mb-5">There are no upcoming Leave Schedules.</div>
                    </div>
                </b-tab>
                <b-tab>
                    <template v-slot:title>
                        <div>
                            <span class="d-inline-block d-sm-none">
                                <i class="far fa-user"></i>
                            </span>
                            <span class="d-none d-sm-inline-block ps-3 pe-3">Archives</span>
                        </div>
                    </template>
                    <div class="mt-5">
                        <div v-if="state.showLoader == true" class="text-center pt-5"><i class="bx bx-loader bx-spin font-size-16 align-middle me-3" v-if="state.showLoader"></i> Please Wait ...</div>
                        <div v-else>
                            <div v-if="state.archivedLeaves.length > 0">
                                <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100" id="userList-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Name</th>
                                            <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2 text-center">Started</th>
                                            <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2 text-center">Ends</th>
                                            <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2 text-center">Duration</th>
                                            <!-- <th scope="col" :style="{ backgroundColor: '#eff2f7', textAlign: 'end' }"><div class="me-4">Action</div></th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(leave, index) in state.archivedLeaves" :key="`${index}_${leave.id}`">
                                            <td :style="{ backgroundColor: '#fff' }">
                                                <div role="button">
                                                    <div class="col-12 text-truncate text-center">{{ leave.name }}</div>
                                                </div>
                                            </td>
                                            <td :style="{ backgroundColor: '#fff' }">
                                                <div role="button">
                                                    <div class="col-12 text-truncate text-center">{{ `${dateFormat2(leave.start)}` }}</div>
                                                </div>
                                            </td>
                                            <td :style="{ backgroundColor: '#fff' }">
                                                <div role="button">
                                                    <div class="col-12 text-truncate text-center">{{ `${dateFormat2(leave.end)}` }}</div>
                                                </div>
                                            </td>
                                            <td :style="{ backgroundColor: '#fff' }">
                                                <div role="button">
                                                    <div class="col-12 text-truncate text-center">{{ `${dateDiff(leave.start, leave.end)} Day(s)` }}</div>
                                                </div>
                                            </td>
                                            <!-- 
                                            <td :style="{ backgroundColor: '#fff' }">
                                                <div class="d-flex justify-content-end">
                                                    <div class="list-unstyled hstack gap-1 mb-0">
                                                        <div class="invisible" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="View" @click="">
                                                            <div class="btn btn-sm btn-soft-primary"><i class="mdi mdi-eye-outline"></i></div>
                                                        </div>
                                                        <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit" @click="editLeave(leave)">
                                                            <div href="#" class="btn btn-sm btn-soft-info"><i class="mdi mdi-pencil-outline"></i></div>
                                                        </div>
                                                        <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Delete" @click="deletePillar(pillar)">
                                                            <b-button variant="soft-danger" class="btn-sm" @click=""><i class="mdi mdi-delete-outline"></i></b-button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td> -->
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else class="text-center pt-5 mt-5 mb-5">There are no archived Leave Schedules.</div>
                        </div>
                    </div>
                </b-tab>
            </b-tabs>
        </div>
        <b-modal v-model="state.editAvailabilityModal" id="modal-md" size="md" title-class="fs-4" body-class="p-3" hide-footer hide-header>
            <div>
                <div class="p-4">
                    <div class="text-center mb-4 fw-bold fs-5">Edit Schedule</div>
                    <div class="col-12 gap-3 mb-2">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="fromDate">From:</label>
                                <b-form-input id="fromDate" type="date" onkeydown="return false" v-model="state.currentLeaveScheduleStart"></b-form-input>
                                <div v-if="state.currentLeaveScheduleStart.length == 0" class="text-danger">
                                    <small>The start date is required</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="toDate">To:</label>
                                <b-form-input
                                    id="toDate"
                                    type="date"
                                    :min="moment(state.currentLeaveScheduleStart).format('YYYY-MM-DD')"
                                    onkeydown="return false"
                                    v-model="state.currentLeaveScheduleEnd"
                                ></b-form-input>
                                <div v-if="state.currentLeaveScheduleEnd == 0" class="text-danger">
                                    <small>The end data is required</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="name">Select Staff to Delegate roles</label>

                                <div class="mb-3 row">
                                    <div class="">
                                        <select class="form-control" v-model="state.currentLeaveScheduleDelegateto">
                                            <option value="" disabled>Select</option>
                                            <option v-for="(staff, ind) in staff" :key="`${staff.personal_id}_${ind}`" :value="staff.email">
                                                {{ staff.name }}
                                            </option>
                                        </select>
                                        <div v-if="state.currentLeaveScheduleDelegateto.length == 0" class="text-danger">
                                            <small>This field is required</small>
                                        </div>
                                    </div>
                                </div>
                                <!-- <Multiselect  :options="state.options" :multiple="true"></Multiselect> -->
                                <!-- <b-form-input id="date" value="2019-08-19" type="date"></b-form-input> -->
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="name">Select Leave Type</label>

                                <div class="mb-3 row">
                                    <div class="">
                                        <select class="form-control" v-model="state.currentLeaveScheduleType">
                                            <option value="" disabled>Select</option>
                                            <option value="Staff Retreat/Weekends">Staff Retreat/Weekends</option>
                                            <option value="Un Holiday">Un Holiday</option>
                                            <option value="Annual Leave">Annual Leave</option>
                                            <option value="Official Business">Official Business</option>
                                            <option value="Field Mission">Field Mission</option>
                                            <option value="Away">Away</option>
                                            <option value="Home Leave">Home Leave</option>
                                            <option value="Medical Leave">Medical Leave</option>
                                            <option value="Compassionate Leave">Compassionate Leave</option>
                                        </select>
                                        <div v-if="state.currentLeaveScheduleType.length == 0" class="text-danger">
                                            <small>This field is required</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-100 d-flex flex-row justify-content-end gap-3">
                        <div>
                            <b-button class="ms-1" variant="danger" @click="state.editAvailabilityModal = false">Cancel</b-button>
                        </div>
                        <div>
                            <b-button class="ms-1" variant="primary"
                                ><i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.showLoader" :disabled="state.showLoader"></i>Edit</b-button
                            >
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>
    </div>
</template>
