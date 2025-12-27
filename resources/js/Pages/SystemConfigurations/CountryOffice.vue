<script setup>
import Swal from "sweetalert2";
import { reactive, onMounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { GoogleMap, Marker, Polygon } from "vue3-google-map";
import { showLoader } from "@/mixins/helpers";
import axios from "axios";
import vSelect from "vue-select";
import mapLocation from "@/mixins/MapLocation.vue";

const props = defineProps({
    pillars: Array,
    sections: Array,
    organisationUnit: Array,
    grade: Array,
    categories: Array,
    appointmentType: Array,
    rooms: Array,
    field_offices: Array,
    staff: Array,
});
const state = reactive({
    //pillars
    addPillarModal: false,
    addPillarLoader: false,
    isEditPillar: false,
    pillarId: null,
    newPillarName: "",

    //sections
    addSectionModal: false,
    addSectionLoader: false,
    isEditSection: false,
    sectionId: null,
    newSectionName: "",

    //organisation unit
    addOrganisationUnitModal: false,
    addOrganisationUnitLoader: false,
    isEditOrganisationUnit: false,
    organisationUnitId: null,
    newOrganisationUnitName: "",

    //grades
    addGradeModal: false,
    addGradeLoader: false,
    isEditGrade: false,
    gradeId: null,
    newGradeName: "",

    //categories
    addCategoryModal: false,
    addCategoryLoader: false,
    isEditCategory: false,
    categoryId: null,
    newCategoryName: "",

    //Appointments Types
    addAppointmentModal: false,
    addAppointmentLoader: false,
    isEditAppointment: false,
    appointmentId: null,
    newAppointmentName: "",

    //Room
    addRoomsModal: false,
    addRoomLoader: false,
    isEditRoom: false,
    roomId: null,
    newRoomName: "",

    newRoomLocation: "Juba, South Sudan",
    roomLocationModal: false,
    roomLocationLat: "",
    roomLocationLng: "",
    newRoomLocationCoords: "",
    roommapLoader: false,
    markCenter: { lat: 4.851259724661479, lng: 31.60444601876833 },

    //Field Office
    addFieldOfficeModal: false,
    isEditFieldOffice: false,
    fieldOfficeId: null,
    newFieldOfficeName: "",
    newFieldOfficeLocation: {
        addr: '',
        lat: '',
        lng: ''
    },
    newFieldOfficeChief: null,
    addFieldOfficeLoader: false,
    fieldOfficeHQ: 0,
    openMap: false,

    locationData: {},
});

async function selectRoomLocation(coord, index) {
    const { latLng } = coord;
    state.roomLocationLat = latLng.lat();
    state.roomLocationLng = latLng.lng();
    state.newRoomLocationCoords = `${state.roomLocationLat} , ${state.roomLocationLng}`;
    state.roommapLoader = true;
    //getting the name of the place
    const response =
        await fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${state.roomLocationLat},${state.roomLocationLng}&result_type=locality&key=AIzaSyCBE7aRb0VJdRBlUnvjcQ_lnKWoAWuXUx8
`);
    const place = await response.json();
    if (place.results.length > 0) {
        state.newRoomLocation = place.results[0].formatted_address;
        state.roommapLoader = false;
    } else {
        state.newRoomLocation = place.plus_code.compound_code;
        state.roommapLoader = false;
    }
}

function addnewRoom() {
    state.addRoomsModal = true;
    const options = {
        "content-type": "multipart/form-data",
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
    };

    let formdata = new FormData();
    formdata.append("id", state.roomId);
    formdata.append("room", state.newRoomName);
    formdata.append("roomOfficeLocation", state.newRoomLocation);
    formdata.append("roomOfficeLocationCoords", state.newRoomLocationCoords);

    axios
        .post("api/system-configuration/addNewRoom", formdata, options)
        .then((res) => {
            state.addRoomLoader = false;
            state.addRoomsModal = false;

            if (res.data.results === "success") {
                Swal.fire({
                    title: `${state.isEditRoom ? "Updated Successfully" : "Added Successfully."}`,
                    icon: "success",
                    html: `<p style="font-size: 14px">${state.isEditRoom ? "Your changes have been saved successfully" : "A new room has been added successfully."}</p>`,
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
            state.addRoomLoader = false;
            state.addRoomsModal = false;

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

function editRoom(room) {
    state.isEditRoom = true;
    state.newRoomName = room.name;
    state.roomId = room.id;
    state.addRoomsModal = true;
}

function deleteRoom(room) {
    Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to delete <strong>${room.name}</strong> from the room list.</p>`,
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
            formdata.append("id", room.id);

            axios
                .post("api/system-configuration/deleteRoom", formdata, options)
                .then((res) => {
                    if (res.data.results === "success") {
                        Swal.fire({
                            title: `Deleted Successfully.`,
                            icon: "success",
                            html: `<p style="font-size: 14px">The category has been deleted successfully</p>`,
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
function addnewAppointmenType() {
    state.addAppointmentModal = true;
    const options = {
        "content-type": "multipart/form-data",
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
    };

    let formdata = new FormData();
    formdata.append("id", state.appointmentId);
    formdata.append("appointment", state.newAppointmentName);

    axios
        .post("api/system-configuration/addNewAppointmentType", formdata, options)
        .then((res) => {
            state.addAppointmentLoader = false;
            state.addAppointmentModal = false;

            if (res.data.results === "success") {
                Swal.fire({
                    title: `${state.isEditAppointment ? "Updated Successfully" : "Added Successfully."}`,
                    icon: "success",
                    html: `<p style="font-size: 14px">${state.isEditAppointment ? "Your changes have been saved successfully" : "A new appointment type has been added successfully."}</p>`,
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
            state.addCategoryLoader = false;
            state.addCategoryModal = false;

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

function editAppointment(category) {
    state.isEditAppointment = true;
    state.newAppointmentName = category.name;
    state.appointmentId = category.id;
    state.addAppointmentModal = true;
}

function deleteAppointment(category) {
    Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to delete <strong>${category.name}</strong> from the category list.</p>`,
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
            formdata.append("id", category.id);

            axios
                .post("api/system-configuration/deleteAppointmentType", formdata, options)
                .then((res) => {
                    if (res.data.results === "success") {
                        Swal.fire({
                            title: `Deleted Successfully.`,
                            icon: "success",
                            html: `<p style="font-size: 14px">The category has been deleted successfully</p>`,
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
function addnewCategoryName() {
    state.addCategoryLoader = true;
    const options = {
        "content-type": "multipart/form-data",
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
    };

    let formdata = new FormData();
    formdata.append("id", state.categoryId);
    formdata.append("category", state.newCategoryName);

    axios
        .post("api/system-configuration/addNewCategory", formdata, options)
        .then((res) => {
            state.addCategoryLoader = false;
            state.addCategoryModal = false;

            if (res.data.results === "success") {
                Swal.fire({
                    title: `${state.isEditCategory ? "Updated Successfully" : "Added Successfully."}`,
                    icon: "success",
                    html: `<p style="font-size: 14px">${state.isEditCategory ? "Your changes have been saved successfully" : "A new category has been added successfully."}</p>`,
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
            state.addCategoryLoader = false;
            state.addCategoryModal = false;

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

function editCategory(category) {
    state.isEditCategory = true;
    state.newCategoryName = category.name;
    state.categoryId = category.id;
    state.addCategoryModal = true;
}

function deleteCategory(category) {
    Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to delete <strong>${category.name}</strong> from the category list.</p>`,
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
            formdata.append("id", category.id);

            axios
                .post("api/system-configuration/deleteCategory", formdata, options)
                .then((res) => {
                    if (res.data.results === "success") {
                        Swal.fire({
                            title: `Deleted Successfully.`,
                            icon: "success",
                            html: `<p style="font-size: 14px">The category has been deleted successfully</p>`,
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
function addGrade() {
    state.addGradeLoader = true;
    const options = {
        "content-type": "multipart/form-data",
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
    };

    let formdata = new FormData();
    formdata.append("id", state.gradeId);
    formdata.append("grade", state.newGradeName);

    axios
        .post("api/system-configuration/addNewGrade", formdata, options)
        .then((res) => {
            state.addGradeLoader = false;
            state.addGradeModal = false;

            if (res.data.results === "success") {
                Swal.fire({
                    title: `${state.isEditGrade ? "Updated Successfully" : "Added Successfully."}`,
                    icon: "success",
                    html: `<p style="font-size: 14px">${state.isEditGrade ? "Your changes have been saved successfully" : "A new grade has been added successfully."}</p>`,
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
            state.addSectionModal = false;
            state.addSectionLoader = false;

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

function editGrade(grade) {
    state.isEditGrade = true;
    state.newGradeName = grade.name;
    state.gradeId = grade.id;
    state.addGradeModal = true;
}

function deleteGrade(grade) {
    Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to delete <strong>${grade.name}</strong> from the grade list.</p>`,
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
            formdata.append("id", grade.id);

            axios
                .post("api/system-configuration/deleteGrade", formdata, options)
                .then((res) => {
                    if (res.data.results === "success") {
                        Swal.fire({
                            title: `Deleted Successfully.`,
                            icon: "success",
                            html: `<p style="font-size: 14px">The grade has been deleted successfully</p>`,
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

function addOrganisationUnit() {
    state.addOrganisationUnitLoader = true;
    const options = {
        "content-type": "multipart/form-data",
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
    };

    let formdata = new FormData();
    formdata.append("id", state.organisationUnitId);
    formdata.append("unit", state.newOrganisationUnitName);

    axios
        .post("api/system-configuration/addNewOrganisaitionUnit", formdata, options)
        .then((res) => {
            state.addOrganisationUnitLoader = false;
            state.addOrganisationUnitModal = false;

            if (res.data.results === "success") {
                Swal.fire({
                    title: `${state.isEditOrganisationUnit ? "Updated Successfully" : "Added Successfully."}`,
                    icon: "success",
                    html: `<p style="font-size: 14px">${state.isEditOrganisationUnit ? "Your changes have been saved successfully" : "A new organisation unit has been added successfully."}</p>`,
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
            state.addSectionModal = false;
            state.addSectionLoader = false;

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

function editUnit(organisationUnit) {
    state.isEditOrganisationUnit = true;
    state.newOrganisationUnitName = organisationUnit.name;
    state.organisationUnitId = organisationUnit.id;
    state.addOrganisationUnitModal = true;
}

function deleteUnit(organisationUnit) {
    Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to delete <strong>${organisationUnit.name}</strong> from the organisation unit list.</p>`,
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
            formdata.append("id", organisationUnit.id);

            axios
                .post("api/system-configuration/deleteOrganisationUnit", formdata, options)
                .then((res) => {
                    if (res.data.results === "success") {
                        Swal.fire({
                            title: `Deleted Successfully.`,
                            icon: "success",
                            html: `<p style="font-size: 14px">The organisation unit has been deleted successfully</p>`,
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
//............................

function addSection() {
    state.addSectionLoader = true;
    const options = {
        "content-type": "multipart/form-data",
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
    };

    let formdata = new FormData();
    formdata.append("id", state.sectionId);
    formdata.append("section", state.newSectionName);

    axios
        .post("api/system-configuration/addNewSection", formdata, options)
        .then((res) => {
            state.addSectionLoader = false;
            state.addSectionModal = false;

            if (res.data.results === "success") {
                Swal.fire({
                    title: `${state.isEditSection ? "Updated Successfully" : "Added Successfully."}`,
                    icon: "success",
                    html: `<p style="font-size: 14px">${state.isEditSection ? "Your changes have been saved successfully" : "A new section has been added successfully."}</p>`,
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
            state.addSectionModal = false;
            state.addSectionLoader = false;

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

function editSection(section) {
    state.isEditSection = true;
    state.newSectionName = section.name;
    state.sectionId = section.id;
    state.addSectionModal = true;
}

function deleteSection(section) {
    Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to delete <strong>${section.name}</strong> from the section list.</p>`,
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
            formdata.append("id", section.id);

            axios
                .post("api/system-configuration/deleteSection", formdata, options)
                .then((res) => {
                    if (res.data.results === "success") {
                        Swal.fire({
                            title: `Deleted Successfully.`,
                            icon: "success",
                            html: `<p style="font-size: 14px">The section has been deleted successfully</p>`,
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
function addPillar() {
    state.addPillarLoader = true;
    const options = {
        "content-type": "multipart/form-data",
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
    };

    let formdata = new FormData();
    formdata.append("id", state.pillarId);
    formdata.append("pillar", state.newPillarName);

    axios
        .post("api/system-configuration/addNewPillar", formdata, options)
        .then((res) => {
            state.addPillarLoader = false;
            state.addPillarModal = false;

            if (res.data.results === "success") {
                Swal.fire({
                    title: `${state.isEditPillar ? "Updated Successfully" : "Added Successfully."}`,
                    icon: "success",
                    html: `<p style="font-size: 14px">${state.isEditPillar ? "Your changes have been saved successfully" : "A new pillar has been added successfully."}</p>`,
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

function editPillar(pillar) {
    state.isEditPillar = true;
    state.newPillarName = pillar.name;
    state.pillarId = pillar.id;
    state.addPillarModal = true;
}

function deletePillar(pillar) {
    Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to delete <strong>${pillar.name}</strong> from the pillar list.</p>`,
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
            formdata.append("id", pillar.id);

            axios
                .post("api/system-configuration/deletePillar", formdata, options)
                .then((res) => {
                    if (res.data.results === "success") {
                        Swal.fire({
                            title: `Deleted Successfully.`,
                            icon: "success",
                            html: `<p style="font-size: 14px">The pillar has been deleted successfully</p>`,
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

// field office methods
function addNewFieldOffice() {
    state.addFieldOfficeLoader = true;

    const options = {
        "content-type": "multipart/form-data",
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
    };

    let formData = new FormData();
    formData.append("id", state.fieldOfficeId || '');
    formData.append("name", state.newFieldOfficeName);
    formData.append("location", state.newFieldOfficeLocation ? JSON.stringify(state.newFieldOfficeLocation) : '');
    formData.append("is_headquarters", state.fieldOfficeHQ ? 1 : 0);
    formData.append('field_chief', state.newFieldOfficeChief ? state.newFieldOfficeChief.id : '')

    axios
        .post("api/system-configuration/addNewFieldOffice", formData, options)
        .then((res) => {
            state.addFieldOfficeLoader = false;
            state.addFieldOfficeModal = false;
            state.locationData = {};
            state.newFieldOfficeChief = null;

            if (res.data.results === "success") {
                Swal.fire({
                    title: `${state.isEditFieldOffice ? "Updated Successfully" : "Added Successfully."}`,
                    icon: "success",
                    html: `<p style="font-size: 14px">${state.isEditRoom ? "Your changes have been saved successfully" : "A new field office has been added successfully."}</p>`,
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
            state.addFieldOfficeLoader = false;
            state.addFieldOfficeModal = false;

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

function editFieldOffice(office) {
    state.isEditFieldOffice = true;
    state.newFieldOfficeName = office.name;
    state.fieldOfficeId = office.id;
    state.fieldOfficeHQ = office.is_headquarters;
    state.newFieldOfficeLocation = office.location ? parseLocation(office.location) : {
        addr: '',
        lat: '',
        lng: ''
    };
    state.addFieldOfficeModal = true;
}

function deleteFieldOffice(office) {
    Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to delete <strong>${office.name}</strong> from the field office list. Note that permissions associated with this field office will also be dropped</p>`,
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

            let formData = new FormData();
            formData.append("id", office.id);

            axios
                .post("api/system-configuration/deleteFieldOffice", formData, options)
                .then((res) => {
                    if (res.data.results === "success") {
                        Swal.fire({
                            title: `Deleted Successfully.`,
                            icon: "success",
                            html: `<p style="font-size: 14px">The field office has been deleted successfully</p>`,
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

function field_office_hq_exists() {
    return props.field_offices.some(office => office.is_headquarters);
}

function onLocationSelected(value) {
    state.locationData = value;
    state.newFieldOfficeLocation = value;
}

function parseLocation(location) {
    return JSON.parse(location);
}

function getFieldOfficeChiefNames(office) {
    return office.field_chiefs.map((chief) => chief.name);
}

const editUNDay = (item) => {

}
const deleteUNDay = (item) => {

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
                    <span class="d-none d-sm-inline-block" title="Pillars">Pillars</span>
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
                                <b-button variant="primary" @click="state.addPillarModal = true">Add Pillar</b-button>
                            </div>
                        </div>
                    </div>
                    <div v-if="props.pillars.length > 0">
                        <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100"
                            id="userList-table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Pillars</th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7', textAlign: 'end' }">
                                        <div class="me-4">Action</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(pillar, index) in props.pillars" :key="`${index}_${pillar.id}`">
                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div role="button">
                                            <div class="col-12 text-truncate">{{ pillar.name }}</div>
                                        </div>
                                    </td>

                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div class="d-flex justify-content-end">
                                            <div class="list-unstyled hstack gap-1 mb-0">
                                                <div class="invisible" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    aria-label="View" >
                                                    <div class="btn btn-sm btn-soft-primary"><i
                                                            class="mdi mdi-eye-outline"></i></div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit"
                                                    @click="editPillar(pillar)">
                                                    <div href="#" class="btn btn-sm btn-soft-info"><i
                                                            class="mdi mdi-pencil-outline"></i></div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top"
                                                    aria-label="Delete" @click="deletePillar(pillar)">
                                                    <b-button variant="soft-danger" class="btn-sm" ><i
                                                            class="mdi mdi-delete-outline"></i></b-button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center pt-5 mt-5 mb-5">No pillars Registered</div>
                </div>
            </b-tab>
            <b-tab>
                <template v-slot:title>
                    <span class="d-inline-block d-sm-none">
                        <i class="far fa-user"></i>
                    </span>
                    <span class="d-none d-sm-inline-block" title="Sections">Sections</span>
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
                                <b-button variant="primary" @click="state.addSectionModal = true">Add Section</b-button>
                            </div>
                        </div>
                    </div>
                    <div v-if="props.sections.length > 0">
                        <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100"
                            id="userList-table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Sections</th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7', textAlign: 'end' }">
                                        <div class="me-4">Action</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(section, index) in props.sections" :key="`${index}_${section.id}`">
                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div role="button">
                                            <div class="col-12 text-truncate">{{ section.name }}</div>
                                        </div>
                                    </td>

                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div class="d-flex justify-content-end">
                                            <div class="list-unstyled hstack gap-1 mb-0">
                                                <div class="invisible" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    aria-label="View" >
                                                    <div class="btn btn-sm btn-soft-primary"><i
                                                            class="mdi mdi-eye-outline"></i>
                                                    </div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit"
                                                    @click="editSection(section)">
                                                    <div href="#" class="btn btn-sm btn-soft-info"><i
                                                            class="mdi mdi-pencil-outline"></i></div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top"
                                                    aria-label="Delete" @click="deleteSection(section)">
                                                    <b-button variant="soft-danger" class="btn-sm" ><i
                                                            class="mdi mdi-delete-outline"></i></b-button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center pt-5 mt-5 mb-5">Section List is Empty</div>
                </div>
            </b-tab>
            <b-tab>
                <template v-slot:title>
                    <span class="d-inline-block d-sm-none">
                        <i class="far fa-user"></i>
                    </span>
                    <span class="d-none d-sm-inline-block" title="Organisation Units">Org. Units</span>
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
                                <b-button variant="primary" @click="state.addOrganisationUnitModal = true">Add
                                    Organisation
                                    Unit</b-button>
                            </div>
                        </div>
                    </div>
                    <div v-if="props.organisationUnit.length > 0">
                        <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100"
                            id="userList-table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Organisation
                                        Units</th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7', textAlign: 'end' }">
                                        <div class="me-4">Action</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(organisationUnit, index) in props.organisationUnit"
                                    :key="`${index}_${organisationUnit.id}`">
                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div role="button">
                                            <div class="col-12 text-truncate">{{ organisationUnit.name }}</div>
                                        </div>
                                    </td>

                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div class="d-flex justify-content-end">
                                            <div class="list-unstyled hstack gap-1 mb-0">
                                                <div class="invisible" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    aria-label="View" >
                                                    <div class="btn btn-sm btn-soft-primary"><i
                                                            class="mdi mdi-eye-outline"></i>
                                                    </div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit"
                                                    @click="editUnit(organisationUnit)">
                                                    <div href="#" class="btn btn-sm btn-soft-info"><i
                                                            class="mdi mdi-pencil-outline"></i></div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top"
                                                    aria-label="Delete" @click="deleteUnit(organisationUnit)">
                                                    <b-button variant="soft-danger" class="btn-sm" ><i
                                                            class="mdi mdi-delete-outline"></i></b-button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center pt-5 mt-5 mb-5">No Registered Units</div>
                </div>
            </b-tab>
            <b-tab>
                <template v-slot:title>
                    <span class="d-inline-block d-sm-none">
                        <i class="far fa-user"></i>
                    </span>
                    <span class="d-none d-sm-inline-block" title="Grades">Grades</span>
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
                                <b-button variant="primary" @click="state.addGradeModal = true">Add Grade</b-button>
                            </div>
                        </div>
                    </div>
                    <div v-if="props.grade.length > 0">
                        <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100"
                            id="userList-table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Grades</th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7', textAlign: 'end' }">
                                        <div class="me-4">Action</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(grade, index) in props.grade" :key="`${index}_${grade.id}`">
                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div role="button">
                                            <div class="col-12 text-truncate">{{ grade.name }}</div>
                                        </div>
                                    </td>

                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div class="d-flex justify-content-end">
                                            <div class="list-unstyled hstack gap-1 mb-0">
                                                <div class="invisible" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    aria-label="View" >
                                                    <div class="btn btn-sm btn-soft-primary"><i
                                                            class="mdi mdi-eye-outline"></i>
                                                    </div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit"
                                                    @click="editGrade(grade)">
                                                    <div href="#" class="btn btn-sm btn-soft-info"><i
                                                            class="mdi mdi-pencil-outline"></i></div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top"
                                                    aria-label="Delete" @click="deleteGrade(grade)">
                                                    <b-button variant="soft-danger" class="btn-sm" ><i
                                                            class="mdi mdi-delete-outline"></i></b-button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center pt-5 mt-5 mb-5">Grade List is Empty</div>
                </div>
            </b-tab>
            <b-tab>
                <template v-slot:title>
                    <span class="d-inline-block d-sm-none">
                        <i class="far fa-user"></i>
                    </span>
                    <span class="d-none d-sm-inline-block" title="Categories">Categories</span>
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
                                <b-button variant="primary" @click="state.addCategoryModal = true">Add
                                    Category</b-button>
                            </div>
                        </div>
                    </div>
                    <div v-if="props.categories.length > 0">
                        <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100"
                            id="userList-table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Categories
                                    </th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7', textAlign: 'end' }">
                                        <div class="me-4">Action</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(category, index) in props.categories" :key="`${index}_${category.id}`">
                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div role="button">
                                            <div class="col-12 text-truncate">{{ category.name }}</div>
                                        </div>
                                    </td>

                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div class="d-flex justify-content-end">
                                            <div class="list-unstyled hstack gap-1 mb-0">
                                                <div class="invisible" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    aria-label="View" >
                                                    <div class="btn btn-sm btn-soft-primary"><i
                                                            class="mdi mdi-eye-outline"></i>
                                                    </div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit"
                                                    @click="editCategory(category)">
                                                    <div href="#" class="btn btn-sm btn-soft-info"><i
                                                            class="mdi mdi-pencil-outline"></i></div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top"
                                                    aria-label="Delete" @click="deleteCategory(category)">
                                                    <b-button variant="soft-danger" class="btn-sm" ><i
                                                            class="mdi mdi-delete-outline"></i></b-button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center pt-5 mt-5 mb-5">Category List is Empty</div>
                </div>
            </b-tab>
            <b-tab>
                <template v-slot:title>
                    <span class="d-inline-block d-sm-none">
                        <i class="far fa-user"></i>
                    </span>
                    <span class="d-none d-sm-inline-block" title="Appointment Types">Appt. Types</span>
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
                                <b-button variant="primary" @click="state.addAppointmentModal = true">Add Appointment
                                    Type</b-button>
                            </div>
                        </div>
                    </div>
                    <div v-if="props.appointmentType.length > 0">
                        <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100"
                            id="userList-table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Appointment
                                        Types</th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7', textAlign: 'end' }">
                                        <div class="me-4">Action</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(appointment, index) in props.appointmentType"
                                    :key="`${index}_${appointment.id}`">
                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div role="button">
                                            <div class="col-12 text-truncate">{{ appointment.name }}</div>
                                        </div>
                                    </td>

                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div class="d-flex justify-content-end">
                                            <div class="list-unstyled hstack gap-1 mb-0">
                                                <div class="invisible" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    aria-label="View" >
                                                    <div class="btn btn-sm btn-soft-primary"><i
                                                            class="mdi mdi-eye-outline"></i>
                                                    </div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit"
                                                    @click="editAppointment(appointment)">
                                                    <div href="#" class="btn btn-sm btn-soft-info"><i
                                                            class="mdi mdi-pencil-outline"></i></div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top"
                                                    aria-label="Delete" @click="deleteAppointment(appointment)">
                                                    <b-button variant="soft-danger" class="btn-sm" ><i
                                                            class="mdi mdi-delete-outline"></i></b-button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center pt-5 mt-5 mb-5">Appointment Type List is Empty</div>
                </div>
            </b-tab>
            <b-tab>
                <template v-slot:title>
                    <span class="d-inline-block d-sm-none">
                        <i class="far fa-user"></i>
                    </span>
                    <span class="d-none d-sm-inline-block" title="Rooms">Rooms</span>
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
                                <b-button variant="primary" @click="state.addRoomsModal = true">Add Room</b-button>
                            </div>
                        </div>
                    </div>
                    <div v-if="props.rooms.length > 0">
                        <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100"
                            id="userList-table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Rooms</th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Location</th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7', textAlign: 'end' }">
                                        <div class="me-4">Action</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(room, index) in props.rooms" :key="`${index}_${room.id}`">
                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div role="button">
                                            <div class="col-12 text-truncate">{{ room.name }}</div>
                                        </div>
                                    </td>
                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div role="button">
                                            <div class="col-12 text-truncate">{{ room.office_location }}</div>
                                        </div>
                                    </td>

                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div class="d-flex justify-content-end">
                                            <div class="list-unstyled hstack gap-1 mb-0">
                                                <div class="invisible" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    aria-label="View" >
                                                    <div class="btn btn-sm btn-soft-primary"><i
                                                            class="mdi mdi-eye-outline"></i>
                                                    </div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit"
                                                    @click="editRoom(room)">
                                                    <div href="#" class="btn btn-sm btn-soft-info"><i
                                                            class="mdi mdi-pencil-outline"></i></div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top"
                                                    aria-label="Delete" @click="deleteRoom(room)">
                                                    <b-button variant="soft-danger" class="btn-sm" ><i
                                                            class="mdi mdi-delete-outline"></i></b-button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center pt-5 mt-5 mb-5">No Registered Rooms</div>
                </div>
            </b-tab>

            <b-tab>
                <template v-slot:title>
                    <span class="d-inline-block d-sm-none">
                        <i class="far fa-user"></i>
                    </span>
                    <span class="d-none d-sm-inline-block" title="Field Offices">Field Off.</span>
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
                                <b-button variant="primary" @click="state.addFieldOfficeModal = true">Add Field
                                    Office</b-button>
                            </div>
                        </div>
                    </div>
                    <div v-if="props.field_offices.length > 0" class="table-responsive">
                        <table class="table align-middle table-striped dt-responsive w-100" id="userList-table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" style="width:30%">Field
                                        Office</th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" style="width:30%">Location
                                    </th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" style="width:25%">Chief</th>
                                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" style="width:10%">
                                        <div class="text-center">Action</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!field_office_hq_exists()">
                                    <td colspan="4" class="text-center text-danger">No headquarters is defined. This may
                                        cause
                                        unpredictable behavior</td>
                                </tr>
                                <tr v-for="(office, index) in props.field_offices" :key="`${index}_${office.id}`">
                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div role="button">
                                            <div class="col-12 text-truncate">
                                                {{ office.name }}
                                                <span v-if="office.is_headquarters"
                                                    class="text-success">(HeadQuarters)</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div role="button">
                                            <div class="col-12 text-truncate">
                                                <span v-if="office.location">
                                                    {{ parseLocation(office.location).addr }}
                                                    <span v-if="parseLocation(office.location).lat">( {{
                                                        `${parseLocation(office.location).lat},${parseLocation(office.location).lng}`
                                                    }} )</span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div role="button">
                                            <div class="col-12 text-truncate">{{
                                                getFieldOfficeChiefNames(office).join(',') }}</div>
                                        </div>
                                    </td>

                                    <td :style="{ backgroundColor: '#fff' }">
                                        <div class="d-flex justify-content-end">
                                            <div class="list-unstyled hstack gap-1 mb-0">
                                                <div class="invisible" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    aria-label="View" >
                                                    <div class="btn btn-sm btn-soft-primary"><i
                                                            class="mdi mdi-eye-outline"></i>
                                                    </div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit"
                                                    @click="editFieldOffice(office)">
                                                    <div href="#" class="btn btn-sm btn-soft-info"><i
                                                            class="mdi mdi-pencil-outline"></i></div>
                                                </div>
                                                <div data-bs-toggle="tooltip" data-bs-placement="top"
                                                    aria-label="Delete" @click="deleteFieldOffice(office)">
                                                    <b-button variant="soft-danger" class="btn-sm" ><i
                                                            class="mdi mdi-delete-outline"></i></b-button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center pt-5 mt-5 mb-5">No Registered Rooms</div>
                </div>
            </b-tab>
            <b-tab>
                <template v-slot:title>
                    <span class="d-inline-block d-sm-none">
                        <i class="far fa-user"></i>
                    </span>
                    <span class="d-none d-sm-inline-block" title="Field Offices">UN Days</span>
                </template>
                <div class="row d-flex justify-content-end">
                    <div class="col-md-4">
                        <b-button-group class="w-100">
                            <b-button variant="primary" @click.prevent="">Add Date</b-button>
                            <b-dropdown variant="primary">
                                <template #button-content>
                                    <b-icon icon="filter" />
                                </template>
                                <b-dropdown-menu>
                                    <b-dropdown-item>
                                        <a @click.prevent="" rel="noopener noreferrer">
                                            Extract
                                        </a>
                                    </b-dropdown-item>
                                </b-dropdown-menu>
                            </b-dropdown>
                        </b-button-group>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle table-striped dt-responsive w-100">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" :style="{ backgroundColor: '#eff2f7' }" style="width:30%">Date</th>
                                <th scope="col" :style="{ backgroundColor: '#eff2f7' }" style="width:30%">Title</th>
                                <th scope="col" :style="{ backgroundColor: '#eff2f7' }" style="width:25%">Updated At
                                </th>
                                <th scope="col" :style="{ backgroundColor: '#eff2f7' }" style="width:10%">
                                    <div class="text-center">Action</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr :key="day" v-for="day in 8">
                                <td>Date</td>
                                <td>Title</td>
                                <td>Updated At</td>
                                <td :style="{ backgroundColor: '#fff' }">
                                    <div class="d-flex justify-content-end">
                                        <div class="list-unstyled hstack gap-1 mb-0">
                                            <div class="invisible" data-bs-toggle="tooltip" data-bs-placement="top"
                                                aria-label="View" >
                                                <div class="btn btn-sm btn-soft-primary"><i
                                                        class="mdi mdi-eye-outline"></i></div>
                                            </div>
                                            <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit"
                                                @click="editUNDay(day)">
                                                <div href="#" class="btn btn-sm btn-soft-info"><i
                                                        class="mdi mdi-pencil-outline"></i></div>
                                            </div>
                                            <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Delete"
                                                @click="deleteUNDay(day)">
                                                <b-button variant="soft-danger" class="btn-sm" ><i
                                                        class="mdi mdi-delete-outline"></i></b-button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </b-tab>
        </b-tabs>
        <b-modal class="" v-model="state.addPillarModal" size="md" title="Add Pillar" title-class="font-18" centered
            hide-footer hide-title>
            <div class="mb-5 mt-3">
                <label>Pillar:</label>
                <input class="form-control" type="text" v-model="state.newPillarName" />
            </div>

            <div class="text-center mb-4">
                <b-button variant="primary" @click="addPillar" :disabled="state.addPillarLoader"><i
                        class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.addPillarLoader"></i>{{
                            state.isEditPillar ? "Save Changes" : "Add Pillar" }}</b-button>
            </div>
        </b-modal>
        <b-modal class="" v-model="state.addSectionModal" size="md" title="Add Section" title-class="font-18" centered
            hide-footer hide-title>
            <div class="mb-5 mt-3">
                <label>Section:</label>
                <input class="form-control" type="text" v-model="state.newSectionName" />
            </div>

            <div class="text-center mb-4">
                <b-button variant="primary" @click="addSection" :disabled="state.addSectionLoader"><i
                        class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.addSectionLoader"></i>{{
                            state.isEditSection ? "Save Changes" : "Add Section" }}</b-button>
            </div>
        </b-modal>
        <b-modal class="" v-model="state.addOrganisationUnitModal" size="md" title="Add Organisation Unit"
            title-class="font-18" centered hide-footer hide-title>
            <div class="mb-5 mt-3">
                <label>Organisation Unit:</label>
                <input class="form-control" type="text" v-model="state.newOrganisationUnitName" />
            </div>

            <div class="text-center mb-4">
                <b-button variant="primary" @click="addOrganisationUnit" :disabled="state.addOrganisationUnitLoader"><i
                        class="bx bx-loader bx-spin font-size-16 align-middle me-2"
                        v-if="state.addOrganisationUnitLoader"></i>{{ state.isEditOrganisationUnit ? "Save Changes" :
                            `Add
                    Organisation Unit` }}</b-button>
            </div>
        </b-modal>
        <b-modal class="" v-model="state.addGradeModal" size="md" title="Add Grade" title-class="font-18" centered
            hide-footer hide-title>
            <div class="mb-5 mt-3">
                <label>Grade:</label>
                <input class="form-control" type="text" v-model="state.newGradeName" />
            </div>

            <div class="text-center mb-4">
                <b-button variant="primary" @click="addGrade" :disabled="state.addGradeLoader"><i
                        class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.addGradeLoader"></i>{{
                            state.isEditGrade ? "Save Changes" : "Add Grade" }}</b-button>
            </div>
        </b-modal>
        <b-modal class="" v-model="state.addCategoryModal" size="md" title="Add Category" title-class="font-18" centered
            hide-footer hide-title>
            <div class="mb-5 mt-3">
                <label>Category:</label>
                <input class="form-control" type="text" v-model="state.newCategoryName" />
            </div>

            <div class="text-center mb-4">
                <b-button variant="primary" @click="addnewCategoryName" :disabled="state.addCategoryLoader"><i
                        class="bx bx-loader bx-spin font-size-16 align-middle me-2"
                        v-if="state.addCategoryLoader"></i>{{
                            state.isEditCategory ? "Save Changes" : "Add Category" }}</b-button>
            </div>
        </b-modal>
        <b-modal class="" v-model="state.addAppointmentModal" size="md" title="Add Appointment Type"
            title-class="font-18" centered hide-footer hide-title>
            <div class="mb-5 mt-3">
                <label>Appointment Type:</label>
                <input class="form-control" type="text" v-model="state.newAppointmentName" />
            </div>

            <div class="text-center mb-4">
                <b-button variant="primary" @click="addnewAppointmenType" :disabled="state.addAppointmentLoader"><i
                        class="bx bx-loader bx-spin font-size-16 align-middle me-2"
                        v-if="state.addAppointmentLoader"></i>{{
                            state.isEditAppointment ? "Save Changes" : "Add Appointment Type" }}</b-button>
            </div>
        </b-modal>
        <b-modal class="" v-model="state.addRoomsModal" size="md" title="Add Room" title-class="font-18" centered
            hide-footer hide-title>
            <div class="mb-4 mt-4">
                <label>Room:</label>
                <input class="form-control" type="text" v-model="state.newRoomName" />
            </div>
            <div class="mb-5">
                <label>Location:</label>
                <input class="form-control" type="text" v-model="state.newRoomLocation"
                    @focus="state.roomLocationModal = true" />
            </div>

            <div class="text-center mb-4">
                <b-button variant="primary" @click="addnewRoom"
                    :disabled="state.addRoomLoader || state.roommapLoader"><i
                        class="bx bx-loader bx-spin font-size-16 align-middle me-2"
                        v-if="state.addRoomLoader || state.roommapLoader"></i>{{ state.isEditRoom ? "Save Changes" :
                            "Add Room"
                    }}</b-button>
            </div>
        </b-modal>
        <b-modal class="" v-model="state.roomLocationModal" size="lg" title="Drag marker to select office location"
            title-class="font-18" centered hide-footer hide-title>
            <div class="mb-5">
                <div class="mt-3 mb-3">
                    {{ state.roomLocationLat }}
                    {{ state.roomLocationLng }}
                </div>

                <GoogleMap api-key="AIzaSyCBE7aRb0VJdRBlUnvjcQ_lnKWoAWuXUx8" :center="state.markCenter" :zoom="16"
                    style="height: 400px">
                    <Marker :options="{ position: state.markCenter, draggable: true }" @dragend="selectRoomLocation" />
                </GoogleMap>
            </div>

            <div class="text-center mb-4">
                <b-button variant="primary" @click="state.roomLocationModal = false">Select Location</b-button>
            </div>
        </b-modal>


        <!--        Field Offices Modals-->
        <b-modal class="" v-model="state.addFieldOfficeModal" size="md" title="Add Field Office" scrollable
            title-class="font-18" centered hide-footer hide-title>
            <div class="mb-3 mt-3">
                <label>Field Office:</label>
                <input class="form-control" type="text" v-model="state.newFieldOfficeName" />
            </div>
            <div class="mb-3 mt-3">
                <label>Location:</label>
                <div class="row">
                    <div class="col-10">
                        <textarea v-if="Object.keys(state.locationData).length > 0" class="form-control" rows="2"
                            v-model="state.locationData.addr">
                </textarea>
                        <textarea v-else style="resize: none" class="form-control" rows="2"
                            v-model="state.newFieldOfficeLocation.addr">
                </textarea>
                    </div>
                    <div class="col-2">
                        <div class="d-flex justify-content-center">
                            <div @click="state.openMap = !state.openMap" class="location-picker-icon small mt-2">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3 mt-3">
                <label>Field Office Chief:</label>
                <v-select v-model="state.newFieldOfficeChief" label="name" :options="staff"></v-select>
            </div>

            <div class="mb-3">
                <div class="form-group">
                    <input type="checkbox" class="form-check-input me-2 border-primary" v-model="state.fieldOfficeHQ"
                        :checked="state.fieldOfficeHQ">
                    <label class="form-check-label">Mark as headquarters</label>
                </div>
            </div>

            <div class="text-center mb-4">
                <b-button variant="primary" @click="addNewFieldOffice" :disabled="state.addFieldOfficeLoader"><i
                        class="bx bx-loader bx-spin font-size-16 align-middle me-2"
                        v-if="state.addFieldOfficeLoader"></i>{{
                            state.isEditRoom ? "Save Changes" : "Save Field Office" }}</b-button>
            </div>
        </b-modal>

        <map-location title="Select Location" @clicked="onLocationSelected" :show="state.openMap" v-cloak />
    </div>
</template>
