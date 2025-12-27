<template>
    <div>
        <b-modal v-model="modalItemValue" hide-footer scrollable centered size="lg" title="Upload Mobile App" @close="closeModal">
            <div class="tailwind-classes">
                <Vueform ref="formInfo" v-model="formDetails" sync>
                    <TextElement
                        :disabled="isDisabled"
                        :floating="false"
                        name="version"
                        size="sm"
                        label="Version"
                        placeholder="version"
                        :columns="{
                            xs: { container: 12, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 12, label: 12, wrapper: 12 },
                            lg: { container: 12, label: 12, wrapper: 12 },
                        }"
                    >
                        <template #between="scope">
                            <input type="hidden" name="scope_1" :value="scope" />
                            <div v-for="error in v$['version'].$errors" :key="error.$uid + `version`">
                                <span class="text-danger">{{ error.$message }}</span>
                            </div>
                        </template>
                    </TextElement>
                    <EditorElement
                        :disabled="isDisabled"
                        placeholder="Comment"
                        name="comment"
                        label="Comment"
                        size="md"
                        :columns="{
                            xs: { container: 12, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 12, label: 12, wrapper: 12 },
                            lg: { container: 12, label: 12, wrapper: 12 },
                        }"
                    >
                        <template #between="scope">
                            <input type="hidden" name="scope_2" :value="scope" />
                            <div v-for="error in v$['comment'].$errors" :key="error.$uid + `comment`">
                                <span class="text-danger">{{ error.$message }}</span>
                            </div>
                        </template>
                    </EditorElement>

                    <FileElement
                        :disabled="isDisabled"
                        :auto="true"
                        :upload-temp-endpoint="uploadTempFile"
                        :remove-temp-endpoint="removeTempFile"
                        :clickable="true"
                        label="APK"
                        accept=".apk"
                        size="md"
                        :columns="{
                            xs: { container: 12, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 12, label: 12, wrapper: 12 },
                            lg: { container: 12, label: 12, wrapper: 12 },
                        }"
                        :drop="true"
                        name="apk"
                    >
                        <template #between="scope">
                            <input type="hidden" name="scope_3" :value="scope" />
                            <div v-for="error in v$['apk'].$errors" :key="error.$uid + `apk`">
                                <span class="text-danger">{{ error.$message }}</span>
                            </div>
                        </template>
                    </FileElement>
                </Vueform>
            </div>
            <div class="row d-flex justify-content-center my-3">
                <div class="col-md-6">
                    <b-button variant="primary" class="w-100" @click.prevent="uploadApp">Upload</b-button>
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
        mobileAppForm: Boolean,
        mobileAppFormEvent: {
            type: Function,
            default: () => {},
        },
    },
    emits: ["updated:mobileAppForm"],
    setup(props, ctx) {
        const propsValues = toRef(props);

        const formInfo = ref(null);
        const formDetails = ref({
            version: null,
            comment: null,
            apk: null,
        });
        const isDisabled = ref(false);

        const closeModal = () => {
            ctx.emit("updated:mobileAppForm", false);
            props.mobileAppFormEvent();
        };
        const modalItemValue = ref(false);

        const rules = computed(() => {
            return {
                version: {
                    required,
                },
                comment: {
                    required,
                },
                apk: {
                    required,
                },
            };
        });

        const v$ = useVuelidate(rules, formDetails);

        const uploadApp = async () => {
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

            await RequestHelper.postRequest("/api/mobile-api/upload-apk", formDetails.value, {}, props.session?.api_token)
                .then(({ data }) => {
                    Swal.close();
                    notify.toastSuccessMessage("Uploaded Successfully");
                    formDetails.value = {
                        version: null,
                        comment: null,
                        apk: null,
                    };
                    formInfo.value = null;
                    ctx.emit("updated:mobileAppForm", false);
                    props.mobileAppFormEvent();
                })
                .catch((err) => {
                    Swal.close();
                    notify.toastErrorMessage("Something went wrong!");
                });
        };
        watch(props, (newValue, oldValue) => {
            modalItemValue.value = newValue.mobileAppForm;
        });

        const uploadTempFile = async (file, el$) => {
            let url = "/api/sp/submit-temp-files";
            let output = null;
            let data = new FormData();
            data.append("file", file);
            data.append("file_name", "UNICEF_APP");
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
            let url = "/api/sp/delete-temp-files";
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
            modalItemValue.value = props.mobileAppForm;
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
            uploadApp,
            uploadTempFile,
            removeTempFile,
        };
    },
};
</script>

<style lang="scss" scoped></style>
