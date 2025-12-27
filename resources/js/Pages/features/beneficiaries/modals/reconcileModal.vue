<template>
    <div>
        <b-modal v-model="modalItemValue" hide-footer scrollable centered size="md" title="" @close="closeModal">
            <div class="text-center" style="font-size: 1.112rem;">
                <span class="bx bx-info-circle text-primary" style="font-size: 6rem;"></span>
                <br>
                <b>Data Summary</b>
                <p>Total: {{ summaryValues.total }} <em>members</em> <br><span class="text-success">Approved: {{
                        summaryValues.approved }} <em>members</em> {{ summaryValues.approvedPercent }}%</span></p>
            </div>

            <div>
                <div class="tailwind-classes">
                    <Vueform ref="formInfo" v-model="formDetails" sync>
                        <SelectElement :disabled="isDisabled" :floating="false" label="Financial Institute"
                            name="vendor" label-prop="label" :native="false" size="md" :columns="{
                                xs: { container: 12, label: 12, wrapper: 12 },
                                sm: { container: 12, label: 12, wrapper: 12 },
                                md: { container: 12, label: 12, wrapper: 12 },
                                lg: { container: 12, label: 12, wrapper: 12 },
                            }" :search="true" :track-by="['value', 'label']" :create="true"
                            :append-new-option="true" :truncate="true" placeholder="Search Financial Institute"
                            :items="getVendors" >
                            <template #between="scope">
                                <input type="hidden" name="scope_to" :value="scope" />
                                <div v-for="error in v$['vendor'].$errors"
                                    :key="error.$uid + `vendor`">
                                    <span class="text-danger">{{ error.$message }}</span>
                                </div>
                            </template>
                        </SelectElement>
                        <EditorElement :disabled="isDisabled" placeholder="Comment" name="comment"
                            label="Comment (Optional)" size="md" :columns="{
                                xs: { container: 12, label: 12, wrapper: 12 },
                                sm: { container: 12, label: 12, wrapper: 12 },
                                md: { container: 12, label: 12, wrapper: 12 },
                                lg: { container: 12, label: 12, wrapper: 12 },
                            }">
                            <template #between="scope">
                                <input type="hidden" name="scope_2" :value="scope" />
                                <div v-for="error in v$['comment'].$errors" :key="error.$uid + `comment`">
                                    <span class="text-danger">{{ error.$message }}</span>
                                </div>
                            </template>
                        </EditorElement>
                    </Vueform>

                </div>

            </div>
            <div class="row d-flex justify-content-center my-3">
                <div class="col-md-5">
                    <b-button @click.prevent="closeModal" variant="danger" class="w-100">Cancel</b-button>
                </div>
                <div class="col-md-5">
                    <b-button class="w-100" @click.prevent="approveList" variant="success">
                        Proceed and Approve
                    </b-button>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
import { computed } from "vue";
import { ref, toRef, watch, onMounted } from "vue";
import { useStore } from "vuex";
import { RequestHelper } from "@/mixins/helpers";
import { notify } from "@/mixins/notify";
import { useVuelidate } from "@vuelidate/core";
import { required } from "@vuelidate/validators";
import Vueform from "@vueform/vueform";
import useVueform from "@vueform/vueform";
import Swal from "sweetalert2";




export default {
    props: {
        summaryModal: Boolean,
        closeModalEvent: {
            type: Function,
            required: true
        },
        session: Object
    },
    mixins: [Vueform],
    emits: ["updated:summary"],
    setup(props, ctx) {
        const store = useStore();
        const propsValues = toRef(props);
        const modalItemValue = ref(false);

        const closeModal = () => {
            modalItemValue.value = false;
            props.closeModalEvent();
        }

        watch(props, (newValue, oldValue) => {
            modalItemValue.value = newValue.summaryModal;
        });

        const summaryValues = computed(() => {
            let total = store.getters["beneficiaryStore/getSummary"]['all'].length;
            let approved = store.getters["beneficiaryStore/getSummary"]['approved'].length;
            let approvedPercent = (approved / total) * 100;
            return {
                total,
                approved, approvedPercent
            };
        });

        const formInfo = ref(null);
        const formDetails = ref({
            vendor: null,
            comment: null,
        });

        const rules = computed(() => {
            return {
                vendor: {
                    required,
                },
                comment: {

                },
            };
        });
        const isDisabled = ref(false);

        const v$ = useVuelidate(rules, formDetails);

        const approveList =async () => {
            isDisabled.value = true;
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

            setTimeout(() => {
                Swal.close();
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: "List has been approved!",
                    showConfirmButton: false,
                    timer: 1500,
                });
                props.closeModalEvent();
                ctx.emit("updated:summary");
                formDetails.value.vendor = null;
                formDetails.value.comment = null;
                formInfo.value.$reset();
                formInfo.value.$resetErrors();
                formInfo.value.$resetValidation();
            }, 3000)

        };

      const  getVendors = async(search)=>{
        let items = [];
        await RequestHelper.getRequest("/api/sp/vendors",{
            q:search||'',
        },props.session.api_token).then(({data})=>{
            let result = data.results;
            if(result != null){
                result.forEach(element => {
                    items.push({
                        value: element.id,
                        label: element.text,
                        ...element
                    });
                });
            }
        })

        return items;

      }


        onMounted(() => {
            modalItemValue.value = props.summaryModal;
        });

        return {
            propsValues,
            modalItemValue,
            approveList,
            store,
            closeModal,
            summaryValues,
            formInfo,
            formDetails,
            rules,
            v$,
            getVendors
        }
    }
}
</script>

<style lang="scss" scoped></style>
