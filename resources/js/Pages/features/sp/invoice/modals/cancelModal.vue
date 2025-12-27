<template>
    <div>
        <b-modal v-model="modalItemValue" hide-footer scrollable centered size="lg" title="Recall Invoice" @close="closeModal">
            <div class="row d-flex justify-content-center">
                <div class="col-8 text-center">
                    <p>On recalling an invoice, UNICEF will be instructed to stop processing the invoice</p>
                </div>
                <div class="col-md-12">
                    <div class="tailwind-classes">
                        <Vueform ref="formInfo" v-model="formDetails" sync>
                            <EditorElement
                                :disabled="isDisabled"
                                placeholder="reason for recalling the invoices"
                                name="reason"
                                label="Reason"
                                size="md"
                                :columns="{
                                    xs: { container: 12, label: 12, wrapper: 12 },
                                    sm: { container: 12, label: 12, wrapper: 12 },
                                    md: { container: 12, label: 12, wrapper: 12 },
                                    lg: { container: 12, label: 12, wrapper: 12 },
                                }"
                            >
                                <template #between="scope">
                                    <input type="hidden" :value="scope" />
                                    <div v-for="error in v$['reason'].$errors" :key="error.$uid + `reason`">
                                        <span class="text-danger">{{ error.$message }}</span>
                                    </div>
                                </template>
                            </EditorElement>
                        </Vueform>
                    </div>
                </div>
                <div class="col-md-10 my-3">
                    <b-button class="w-100" variant="danger" @click="count += 1"> Recall Invoice </b-button>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
import { reactive, toRefs, ref, useAttrs, onMounted, computed } from "vue";
import useVueform from "@vueform/vueform";
import Vueform from "@vueform/vueform";
import { useVuelidate } from "@vuelidate/core";
import { router, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { RequestHelper } from "@/mixins/helpers";
import { useStore } from "vuex";

export default {
    mixins: [Vueform],
    props: {
        cancelModal: Boolean,
        cancelModalEvent: {
            type: Function,
            default: () => {},
        },
    },
    setup(props, ctx) {
        const state = reactive({
            count: 0,
        });
        const store = useStore();
        const modalItemValue = ref(false);
        const isDisabled = ref(false);
        const closeModal = () => {
            props.cancelModalEvent();
        };

        const formInfo = ref(null);
        const formDetails = ref({
            reason: null,
        });

        const BSC_URL = import.meta.env.VITE_FINANCE_APP_URL;

        const rules = computed(() => {
            return {
                reason: {
                    required: true,
                },
            };
        });

        const v$ = useVuelidate(rules, formDetails);

        // onMounted(() => {
        //     modalItemValue.value = props.cancelModal;
        // });

        store.watch(
            (state, getters) => {
                modalItemValue.value = getters["modalStore/showCancelInvoiceModal"];
                return getters["modalStore/showCancelInvoiceModal"];
            },
            () => {}
        );

        // store.watch(
        //     (state, getters) => {
        //         vehicleInfo.value = getters["vehicleStore/vehicleDetails"];
        //         return getters["vehicleStore/vehicleDetails"];
        //     },
        //     () => {
        //         if (vehicleInfo.value != null) {
        //             vehicleInfo.value = vehicleInfo.value;
        //             const { license_plate, current_mileage, date_of_purchase, vehicle_type_id } = vehicleInfo.value;
        //             currentMileage.value = current_mileage;
        //             Object.assign(formDetails.value, {
        //                 license_plate: license_plate,
        //                 current_mileage: current_mileage,
        //                 date_of_purchase: date_of_purchase,
        //                 vehicle_type_id: vehicle_type_id,
        //             });
        //         } else {
        //             vehicleInfo.value = null;
        //             currentMileage.value = 0;
        //         }
        //         console.log("value changes detected via vuex watch");
        //     }
        // );

        return {
            ...toRefs(state),
            store,
            modalItemValue,
            closeModal,
            v$,
            BSC_URL,
            formInfo,
            formDetails,
        };
    },
};
</script>

<style lang="scss" scoped></style>
