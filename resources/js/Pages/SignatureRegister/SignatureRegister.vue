<script setup>
import Vue3Signature from "vue3-signature";
import { ref, reactive, onMounted, watch } from "vue";
import Swal from "sweetalert2";
import { usePage } from "@inertiajs/vue3";
import axios from "axios";

//event emitted once a signature has been saved
const emit = defineEmits(["signatureSaved",'closedModal']);

const useSignature = ref(null);
const state = reactive({
    count: 0,
    option: {
        penColor: "rgb(0, 0, 0)",
        backgroundColor: "rgb(255,255,255,0)",
    },
    disabled: false,
    savedSignature: null,
    uploadedSignature: null,
});

onMounted(() => {
    if (usePage().url != "/dashboard?impersonation=1") {
        getUserSignature();
    }

    const closeButton = document.getElementById('closeSignatureModal');

    if (closeButton) {
        closeButton.addEventListener('click', () => {
           emit('closedModal');
        });
    }
    // console.log("thisss", window.history.length);
});

const clear = () => {
    useSignature.value.clear();
};

const undo = () => {
    useSignature.value.undo();
};

const save = (t) => {
    const isCanvasEmpty = useSignature.value.isEmpty();

    if (isCanvasEmpty) {
        Swal.fire({
            icon: "warning",
            title: "No Signature Detected.",
            html: "<p class='font-size: 13px'>Please draw the signature you want to register in the box.</p>",
            showConfirmButton: true,
            allowOutsideClick: false,
            showCloseButton: false,
            confirmButtonText: "Ok",
            confirmButtonColor: "#32CD32",
        });
    } else {
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

        const options = {
            headers: {
                "Content-Type": "multipart/form-data",
                Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
            },
        };
        const signatureData = useSignature.value.save(t);
        formData.append("signature", signatureData);
        formData.append("userId", usePage().props.auth.user.id);

        axios
            .post(`/api/staff-register/saveUserSignature`, formData, options)
            .then((res) => {
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    html: "<p class='font-size: 13px'>Signature registered successfully.</p>",
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    showCloseButton: false,
                    confirmButtonText: "Ok",
                    confirmButtonColor: "#32CD32",
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById("closeSignatureModal").click();
                        Swal.close();
                        //emit signature saved event
                        emit("signatureSaved");
                        emit('closedModal');
                    }
                });
            })
            .catch(function (error) {
                console.log(error);
                state.isProcessing = false;
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
                        router.visit("/");

                        Swal.close();
                    }
                });
            });
    }
};
function uploadSignature(e) {
    state.uploadedSignature = e.target.files[0];
    const objectURL = URL.createObjectURL(state.uploadedSignature);
    useSignature.value.fromDataURL(`${objectURL}`);
}

function saveUploadedSignature() {
    document.getElementById("offlineSignedEquipmentReq").click();
}

async function getUserSignature() {
    const options = {
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
    };
    await axios
        .get(`/api/staff-register/get-userSignature`, options)
        .then((res) => {
            if (res.data.results != null) {
                state.savedSignature = res.data.results?.image;
                useSignature.value.fromDataURL(`${state.savedSignature}`);
                emit('closedModal');
            } else {
                //show modal if user has no signature
                document.getElementById("showSignatureManager").click();
            }
        })
        .catch((error) => {
            console.log(error);
        });
}
</script>
<template>
    <div class="modal fade" id="signatureRegister" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="signatureRegisterLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="signatureRegisterLabel">Register your signature by signing in the field below.</h6>
                    <button type="button" id="closeSignatureModal" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{ isCanvasEmpty }}
                <div id="signature-pad" class="modal-body d-flex align-items-center justify-content-center">
                    <Vue3Signature
                        ref="useSignature"
                        :defaultUrl="state.savedSignature"
                        :sigOption="state.option"
                        :h="'300px'"
                        :w="'700px'"
                        :disabled="state.disabled"
                        class="example border mt-5 mb-5"
                    ></Vue3Signature>
                </div>

                <div class="mx-auto d-flex flex-row gap-3 justisy-content-right align-items-center mb-4">
                    <div class="" @click="saveUploadedSignature" role="button">
                        <i class="mdi mdi-cloud-upload" style="color: #0160a0; font-size: 30px"></i>
                        <input id="offlineSignedEquipmentReq" type="file" @change="uploadSignature" ref="offlineSignedEquipmentReqCash" accept=".png" hidden />
                    </div>
                    <div>
                        <b-button variant="danger" @click="clear">Clear</b-button>
                    </div>
                    <div>
                        <b-button variant="warning" @click="undo">Undo</b-button>
                    </div>
                    <div>
                        <b-button variant="success" @click="save" :disabled="isEmpty">Register</b-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
