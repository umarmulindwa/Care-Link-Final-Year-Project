<template>
    <div>
        <b-modal v-model="modalItemValue" hide-footer scrollable centered size="lg" title="Upload Attendance List"
            @close="closeModal">
            <div class="tailwind-classes">
                <Vueform ref="formInfo" v-model="formDetails" sync>



                    <FileElement :disabled="isDisabled" :auto="true" :upload-temp-endpoint="uploadTempFile"
                        :remove-temp-endpoint="removeTempFile" :clickable="true" label="Upload" accept=".xlsx" size="md"
                        :columns="{
                            xs: { container: 12, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 12, label: 12, wrapper: 12 },
                            lg: { container: 12, label: 12, wrapper: 12 },
                        }" :drop="true" name="file">
                        <template #between="scope">
                            <input type="hidden" name="scope_3" :value="scope" />
                            <div v-for="error in v$['file'].$errors" :key="error.$uid + `file`">
                                <span class="text-danger">{{ error.$message }}</span>
                            </div>
                        </template>
                    </FileElement>
                </Vueform>
            </div>
            <div class="row d-flex justify-content-end my-3">
                <div class="col-md-6">
                    <b-button variant="secondary" class="w-100" @click.prevent="">Cancel</b-button>
                </div>
                <div class="col-md-6">
                    <b-button variant="primary" class="w-100" @click.prevent="uploadFile">Proceed</b-button>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
import { ref, onMounted, computed, toRef, watch } from "vue";
import Vueform from "@vueform/vueform";
import useVueform from "@vueform/vueform";
import { useVuelidate } from "@vuelidate/core";
import { required } from "@vuelidate/validators";
import { notify } from "@/mixins/notify";
import { RequestHelper } from "@/mixins/helpers";
import Swal from "sweetalert2";
export default {
    mixins: [Vueform],
    props: {
        session: Object,
        groupId: Number,
        uploadBeneficiaryListForm: Boolean,
        uploadBeneficiaryListFormEvent: {
            type: Function,
            default: () => { },
        },
    },
    emits: ["updated:uploadBeneficiaryListForm"],
    setup(props, ctx) {
        const propsValues = toRef(props);
        const FINANCE_URL = import.meta.env.VITE_FINANCE_APP_URL;


        const formInfo = ref(null);
        const formDetails = ref({

            file: null,
        });
        const isDisabled = ref(false);

        const closeModal = () => {
            ctx.emit("updated:uploadBeneficiaryListForm", false);
            props.uploadBeneficiaryListFormEvent();
        };
        const modalItemValue = ref(false);

        const rules = computed(() => {
            return {

                file: {
                    required,
                },
            };
        });

        const v$ = useVuelidate(rules, formDetails);

        const uploadFile = async () => {
            let validInputs = await v$.value.$validate();
            if (!validInputs) {
                return notify.toastErrorMessage("Invalid inputs!");
            }

            Swal.fire({
                text: "Processing, please wait...",
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            await RequestHelper.postRequest(FINANCE_URL +"/api/beneficiaries/attendance/import-data", {
                group_id:props.groupId,
                ...formDetails.value}, {}, props.session?.api_token)
                .then(({ data }) => {
                    console.log({ data });
                    Swal.close();
                    notify.toastSuccessMessage("Uploaded Successfully");
                    formDetails.value = {
                        title: null,
                        comment: null,
                        file: null,
                    };
                    formInfo.value = null;
                    ctx.emit("updated:uploadBeneficiaryListForm", false);
                    window.location.reload();
                })
                .catch((err) => {
                    console.error({ err });

                    Swal.close();
                    notify.toastErrorMessage("Something went wrong!");
                });


        };
        watch(props, (newValue, oldValue) => {
            modalItemValue.value = newValue.uploadBeneficiaryListForm;
        });

        const uploadTempFile = async (file, el$) => {
            let url = FINANCE_URL +"/api/sp/v1/submit-invoicetemp-files";
            let output = null;
            let data = new FormData();
            data.append("file", file);
            data.append("file_name", "BENEFICIARY_LIST");
            const { api_token } = propsValues.value.session;

            await RequestHelper.postRequest(url, data, {}, api_token)
                .then(({ data }) => {
                    output = data.results;
                })
                .catch((err) => {
                    output = null;
                });
            return output;
        };
        // delete temp file
        const removeTempFile = async (file, el$) => {
            const { tmp, originalName } = file;
            let url = FINANCE_URL + "/api/sp/v1/delete-invoicetemp-files";
            let output = null;
            let data = el$.form$.convertFormData({
                tmp,
                originalName,
            });

            await RequestHelper.postRequest(url, data, {}, propsValues.value.session.api_token)
                .then(({ data }) => {
                    output = data;
                })
                .catch((err) => {
                    output = null;
                });
            return output;
        };
        onMounted(() => {
            modalItemValue.value = props.uploadBeneficiaryListForm;
        });
        return {
            useVueform,
            closeModal,
            propsValues,
            modalItemValue,
            v$,
            formInfo,
            formDetails,
            isDisabled,
            uploadFile,
            uploadTempFile,
            removeTempFile,
        };
    },
};
</script>

<style lang="scss" scoped></style>
