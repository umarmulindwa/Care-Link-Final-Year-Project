<template>
    <div>
        <b-modal v-model="propItems.requestVehicleForPrivateUse" hide-footer scrollable centered size="md" title="Request vehicle for Private Use" @close="closeModal">
            <div class="tailwind-classes">
                <Vueform ref="formInfo" v-model="formDetails" sync>
                    <DatesElement
                        mode="range"
                        :columns="{
                            xs: { container: 12, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 12, label: 12, wrapper: 12 },
                            lg: { container: 12, label: 12, wrapper: 12 },
                        }"
                        :floating="false"
                        size="md"
                        placeholder="Duration"
                        name="duration"
                        label="Duration"
                        :min="new Date().toJSON().slice(0, 10)"
                    >
                        <template #between="scope">
                            <div v-for="error in v$['duration'].$errors" :key="error.$uid + `duration`">
                                <span class="text-danger">{{ error.$message }}</span>
                            </div>
                        </template>
                    </DatesElement>

                    <EditorElement
                        :disabled="isDisabled"
                        placeholder="Reasons"
                        name="reasons"
                        label="Reasons"
                        size="md"
                        :columns="{
                            xs: { container: 12, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 12, label: 12, wrapper: 12 },
                            lg: { container: 12, label: 12, wrapper: 12 },
                        }"
                    >
                        <template #between="scope">
                            <div v-for="error in v$['reasons'].$errors" :key="error.$uid + `reasons`">
                                <span class="text-danger">{{ error.$message }}</span>
                            </div>
                        </template>
                    </EditorElement>
                </Vueform>
            </div>
            <div class="row d-flex justify-content-center my-3">
                <div class="col-md-6">
                    <b-button variant="primary" class="w-100" @click.prevent="requestVehicle">Submit</b-button>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
import { onMounted, ref, toRef, computed, watch } from "vue";
import Vueform from "@vueform/vueform";
import useVueform from "@vueform/vueform";
import { notify } from "@/mixins/notify";
import { RequestHelper } from "@/mixins/helpers";
import Swal from "sweetalert2";
import { vMaska } from "maska";
import { useStore } from "vuex";
import { useVuelidate } from "@vuelidate/core";
import { required } from "@vuelidate/validators";

export default {
    name: "request-vehicle-for-private-use-modal",
    mixins: [Vueform],
    directives: { maska: vMaska },
    props: {
        requestVehicleForPrivateUse: {
            type: Boolean,
            required: true,
        },
        session: {
            type: Object,
            required: false,
        },
    },
    emits: ["close-request-vehicle-modal"],
    setup(props, ctx) {
        const propItems = toRef(props);

        const formInfo = ref(null);
        const formDetails = ref({
            reasons: null,
            duration: [],
        });

        const rules = computed(() => {
            return {
                duration: {
                    required,
                },

                reasons: {
                    required,
                },
            };
        });

        const v$ = useVuelidate(rules, formDetails);
        const closeModal = () => {
            formInfo.value.reset();
            ctx.emit("close-request-vehicle-modal");
        };

        const vehicle_manager_url = import.meta.env.VITE_ADMIN_VEHICLE_MANAGER_URL;

        const requestVehicle = async () => {
            let data = formDetails.value;
            var isFormValid = await v$.value.$validate();
            if (!isFormValid) {
                Swal.fire({
                    icon: "error",
                    html: `<p>Invalid Inputs</p>`,
                });
                return;
            }

            Swal.fire({
                title: "Please wait...",
                allowOutsideClick: false,
                showCancelButton: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            data["start_date"] = formDetails.value.duration[0];
            data["end_date"] = formDetails.value.duration.length > 1 ? formDetails.value.duration[1] : formDetails.value.duration[0];
            data["user_email"] = propItems.value.session.email;

            RequestHelper.postRequest(vehicle_manager_url + "/api/private-use/pre-handover/make-request", data, {}, propItems.value.session.apiToken)
                .then(({ data }) => {
                    console.log(data);
                    Swal.close();
                })
                .catch((error) => {
                    console.error(error);
                    Swal.fire({
                        icon: "error",
                        title: "Some wrong happened?",
                    });
                });
        };

        return {
            propItems,
            requestVehicle,
            formDetails,
            formInfo,
            v$,
            closeModal,
        };
    },
};
</script>

<style lang="scss" scoped></style>
