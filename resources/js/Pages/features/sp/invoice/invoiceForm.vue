<template>

    <Head title="Submit Invoice" />
    <Layout>
        <PageHeader :title="'Submit Invoice'" :items="items" />
        <div class="card">
            <div class="card-body">
                <div class="tailwind-classes">
                    <div class="container-fluid">
                        <div class="row d-flex justify-content-between">
                            <div class="col-xl-10 col-lg-10 col-md-12 form-group form-row p-3">
                                <Vueform ref="formInfo" v-model="formDetails" sync>
                                    <SelectElement :disabled="isDisabled" :floating="false"
                                        :info="`Incase your product/service is not on the predetermined list, select the nearest product/service to which you offer`"
                                        label="Type of Product/Service for which you are Invoicing
                                (optional)" name="type_of_service" label-prop="label" :native="false" size="md"
                                        :columns="{
                                            xs: { container: 12, label: 12, wrapper: 12 },
                                            sm: { container: 12, label: 12, wrapper: 12 },
                                            md: { container: 8, label: 12, wrapper: 12 },
                                            lg: { container: 8, label: 12, wrapper: 12 },
                                        }" :search="true" :track-by="['value', 'label']" :create="true"
                                        :append-new-option="true" :truncate="true" placeholder="Search Services"
                                        :items="getSpServices" delay="1000">
                                        <template #between="scope">
                                            <div v-for="error in v$['type_of_service'].$errors"
                                                :key="error.$uid + `type_of_service`">
                                                <span class="text-danger">{{ error.$message }}</span>
                                            </div>
                                        </template>
                                    </SelectElement>

                                    <EditorElement :disabled="isDisabled" name="service_description"
                                        :hide-tools="['attach']" label="Description"
                                        :info="`Providing a Product/Service Description helps with the processing of your invoice so be as detailed as possible. \n<b>Note</b> however that you will have an opportunity to also upload supporting documentation on the next page.`"
                                        size="md" :placeholder="'# Service Description'" :default="serviceDescription"
                                        :format-data="(data, f) => formatDescription(data, f)" :accept="[]" :columns="{
                                            xs: { container: 12, label: 12, wrapper: 12 },
                                            sm: { container: 12, label: 12, wrapper: 12 },
                                            md: { container: 8, label: 12, wrapper: 12 },
                                            lg: { container: 8, label: 12, wrapper: 12 },
                                        }">
                                        <template #between="scope">
                                            <input type="hidden" name="service_description_hidden" :value="scope" />
                                            <div v-for="error in v$['service_description'].$errors"
                                                :key="error.$uid + `service_description`">
                                                <span class="text-danger">{{ error.$message }}</span>
                                            </div>
                                        </template>
                                    </EditorElement>

                                    <RadiogroupElement :disabled="isDisabled" :columns="{
                                        xs: { container: 12, label: 12, wrapper: 12 },
                                        sm: { container: 12, label: 12, wrapper: 12 },
                                        md: { container: 8, label: 12, wrapper: 12 },
                                        lg: { container: 8, label: 12, wrapper: 12 },
                                    }" field-name="has_unicef_contract" id="has_unicef_contract"
                                        :info="`<div>By selecting &quot;Yes&quot;, you will proceed to provide the contract No./PO No. and SES/Good Reciept No.</div><div>Whereas, selecting &quot;No&quot;, means you will provide the &quot;Fund Commitment No.&quot;</div>`"
                                        label="Do you have a contract with UNICEF ?" size="lg"
                                        name="has_unicef_contract" :items="[
                                            { value: 'yes', label: 'Yes, I have a Contract/PO' },
                                            { value: 'no', label: `No, I don't have a Contract/PO` },
                                        ]">
                                        <template #between="scope">
                                            <div v-for="error in v$['has_unicef_contract'].$errors"
                                                :key="error.$uid + `has_unicef_contract`">
                                                <span class="text-danger">{{ error.$message }}</span>
                                            </div>
                                        </template>
                                    </RadiogroupElement>

                                    <ListElement :min="1" :initial="1" :disabled="isDisabled"
                                        :conditions="[['has_unicef_contract', 'yes']]"
                                        :add-text="`<i class='fas fa-plus'></i>`" label="Contract No. / PO No."
                                        name="contract_number" :columns="{
                                            xs: { container: 12, label: 12, wrapper: 12 },
                                            sm: { container: 12, label: 12, wrapper: 12 },
                                            md: { container: 8, label: 12, wrapper: 12 },
                                            lg: { container: 8, label: 12, wrapper: 12 },
                                        }">
                                        <template #default="{ index }">
                                            <ObjectElement :name="index">
                                                <TextElement :disabled="isDisabled" :floating="false" name="numbers"
                                                    placeholder="Contract No. / PO No." />
                                            </ObjectElement>
                                        </template>
                                        <template #between="scope">
                                            <div v-for="error in v$['contract_number'].$errors"
                                                :key="error.$uid + `contract_number`">
                                                <span class="text-danger">{{ error.$message }}</span>
                                            </div>
                                        </template>
                                    </ListElement>

                                    <ListElement :min="1" :initial="1" :disabled="isDisabled"
                                        :add-text="`<i class='fas fa-plus'></i>`" label="UNICEF Receiving Officer(s)"
                                        name="unicef_focal_person" :default="staffDefault" :columns="{
                                            xs: { container: 12, label: 12, wrapper: 12 },
                                            sm: { container: 12, label: 12, wrapper: 12 },
                                            md: { container: 12, label: 12, wrapper: 12 },
                                            lg: { container: 12, label: 12, wrapper: 12 },
                                        }"
                                        :info="`Please provide the name of the UNICEF Focal Person, if uncertain leave it blank.`">
                                        <template #default="{ index }">
                                            <SelectElement :disabled="isDisabled" :floating="false" class="my-2"
                                                :name="index" label-prop="label" :native="false" size="sm"
                                                :search="true" :track-by="['value', 'label']" :truncate="true"
                                                placeholder="Search UNICEF staff" :items="getStaffList" delay="1000"
                                                :columns="{
                                                    xs: { container: 12, label: 12, wrapper: 12 },
                                                    sm: { container: 12, label: 12, wrapper: 12 },
                                                    md: { container: 8, label: 12, wrapper: 12 },
                                                    lg: { container: 8, label: 12, wrapper: 12 },
                                                }" />
                                        </template>
                                        <template #between="scope">
                                            <div v-for="error in v$['unicef_focal_person'].$errors"
                                                :key="error.$uid + `unicef_focal_person`">
                                                <span class="text-danger">{{ error.$message }}</span>
                                            </div>
                                        </template>
                                    </ListElement>

                                    <TextElement disabled="true" label="Vendor Number" name="vendor_number" :columns="{
                                        xs: { container: 12, label: 12, wrapper: 12 },
                                        sm: { container: 12, label: 12, wrapper: 12 },
                                        md: { container: 8, label: 12, wrapper: 12 },
                                        lg: { container: 8, label: 12, wrapper: 12 },
                                    }">
                                        <template #between="scope">
                                            <div v-for="error in v$['vendor_number'].$errors"
                                                :key="error.$uid + `vendor_number`">
                                                <span class="text-danger">{{ error.$message }}</span>
                                            </div>
                                        </template>
                                    </TextElement>

                                    <TextElement :disabled="isDisabled" label="In-house Supplier Invoice Number"
                                        name="invoice_number" :columns="{
                                            xs: { container: 12, label: 12, wrapper: 12 },
                                            sm: { container: 12, label: 12, wrapper: 12 },
                                            md: { container: 8, label: 12, wrapper: 12 },
                                            lg: { container: 8, label: 12, wrapper: 12 },
                                        }">
                                        <template #between="scope">
                                            <div v-for="error in v$['invoice_number'].$errors"
                                                :key="error.$uid + `invoice_number`">
                                                <span class="text-danger">{{ error.$message }}</span>
                                            </div>
                                        </template>
                                    </TextElement>

                                    <DateElement :disabled="isDisabled" :floating="false" :columns="{
                                        xs: { container: 12, label: 12, wrapper: 12 },
                                        sm: { container: 12, label: 12, wrapper: 12 },
                                        md: { container: 8, label: 12, wrapper: 12 },
                                        lg: { container: 8, label: 12, wrapper: 12 },
                                    }" :label="`Invoice Date`" placeholder="Invoice Date"
                                        :max="new Date().toJSON().slice(0, 10)" id="invoice_date"
                                        display-format="MMMM DD, YYYY" value-format="YYYY-MM-DD" name="invoice_date">
                                        <template #addon-after>
                                            <i class="fa fa-calendar-alt"></i>
                                        </template>
                                        <template #between="scope">
                                            <div v-for="error in v$['invoice_date'].$errors"
                                                :key="error.$uid + `invoice_date`">
                                                <span class="text-danger">{{ error.$message }}</span>
                                            </div>
                                        </template>
                                    </DateElement>
                                    <div v-show="!dontShowVat" class="vf-col-12">
                                        <CheckboxElement :disabled="isDisabled" true-value="yes" false-value="no"
                                            :columns="{
                                                xs: { container: 12, label: 12, wrapper: 12 },
                                                sm: { container: 12, label: 12, wrapper: 12 },
                                                md: { container: 8, label: 12, wrapper: 12 },
                                                lg: { container: 8, label: 12, wrapper: 12 },
                                            }" field-name="is_vat_applied" id="is_vat_applied"
                                            :info="`<div>If VAT was applied, please upload the EFRIS Receipt</div>`"
                                            size="lg" name="is_vat_applied" label="Is VAT applied?" text="Yes">
                                            <!-- <template #between="scope">
                                            <div v-for="error in v$['is_vat_applied'].$errors"
                                                :key="error.$uid + `is_vat_applied`">
                                                <span class="text-danger">{{
                                                    error.$message }}</span>
                                            </div>
                                        </template> -->
                                        </CheckboxElement>
                                    </div>

                                    <div class="form-group vf-col-8">
                                        <label for="invoice_gross_amount"
                                            style="font-size: 16px; margin-bottom: 3px">Gross Amount</label>
                                        <div class="row d-flex justify-content-evenly">
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <SelectElement :disabled="isDisabled" name="invoice_currency"
                                                    label-prop="label" :native="false" size="md" :columns="{
                                                        xs: { container: 12, label: 12, wrapper: 12 },
                                                        sm: { container: 12, label: 12, wrapper: 12 },
                                                        md: { container: 12, label: 12, wrapper: 12 },
                                                        lg: { container: 12, label: 12, wrapper: 12 },
                                                    }" :search="true" :track-by="['value', 'label']" add-class="py-1"
                                                    :truncate="true" placeholder="Currency" :items="[
                                                        { value: 'SSP', label: 'SSP' },
                                                        { value: 'UGX', label: 'UGX' },
                                                        { value: 'MWK', label: 'MWK' },
                                                        { value: 'USD', label: 'USD' },
                                                        { value: 'EUR', label: 'EUR' },
                                                        { value: 'KES', label: 'KES' },
                                                    ]">
                                                    <template #between="scope">
                                                        <div v-for="error in v$['invoice_currency'].$errors"
                                                            :key="error.$uid + `invoice_currency`">
                                                            <span class="text-danger">{{ error.$message }}</span>
                                                        </div>
                                                    </template>
                                                </SelectElement>
                                            </div>
                                            <div class="col-md-8 col-sm-12">
                                                <input name="invoice_gross_amount" :disabled="isDisabled"
                                                    class="form-control p-2" v-maska data-maska="9,99#.##"
                                                    data-maska-tokens="9:[0-9]:repeated" data-maska-reversed type="text"
                                                    placeholder="Invoice Amount"
                                                    v-model="formDetailsExt.amounts.invoice_gross_amount" />
                                                <div>
                                                    <div v-for="error in v$['amounts']['invoice_gross_amount'].$errors"
                                                        :key="error.$uid + `invoice_gross_amount`">
                                                        <span class="text-danger">{{ error.$message }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <FileElement :disabled="isDisabled" :conditions="[['is_vat_applied', 'yes']]"
                                        :auto="true" :upload-temp-endpoint="uploadTempFileEFRIS"
                                        :remove-temp-endpoint="removeEFRISTempFile" :clickable="true" accept=".pdf"
                                        label="EFRIS Receipt" :file="{ rules: 'max:15360' }" size="md" :columns="{
                                            xs: { container: 12, label: 12, wrapper: 12 },
                                            sm: { container: 12, label: 12, wrapper: 12 },
                                            md: { container: 8, label: 12, wrapper: 12 },
                                            lg: { container: 8, label: 12, wrapper: 12 },
                                        }" :drop="true" name="efris_receipt">
                                        <template #between="scope">
                                            <div v-for="error in v$['efris_receipt'].$errors"
                                                :key="error.$uid + `efris_receipt`">
                                                <span class="text-danger">{{ error.$message }}</span>
                                            </div>
                                        </template>
                                    </FileElement>

                                    <FileElement :disabled="isDisabled" :auto="true"
                                        :upload-temp-endpoint="uploadTempFile" :remove-temp-endpoint="removeTempFile"
                                        :clickable="true" accept=".pdf" label="Invoice File"
                                        :file="{ rules: 'max:15360' }" size="md" :columns="{
                                            xs: { container: 12, label: 12, wrapper: 12 },
                                            sm: { container: 12, label: 12, wrapper: 12 },
                                            md: { container: 8, label: 12, wrapper: 12 },
                                            lg: { container: 8, label: 12, wrapper: 12 },
                                        }" :drop="true" name="invoice_file">
                                        <template #between="scope">
                                            <div v-for="error in v$['invoice_file'].$errors"
                                                :key="error.$uid + `invoice_file`">
                                                <span class="text-danger">{{ error.$message }}</span>
                                            </div>
                                        </template>
                                    </FileElement>
                                    <!-- .jpg,.png,.docx,.xls,.csv,.xlsx, -->
                                    <MultifileElement :disabled="isDisabled" :auto="true"
                                        :upload-temp-endpoint="uploadTempFile" :clickable="true"
                                        :remove-temp-endpoint="removeTempFile" accept=".pdf" label="Supporting Files"
                                        :file="{ rules: 'max:15360' }" size="md" :columns="{
                                            xs: { container: 12, label: 12, wrapper: 12 },
                                            sm: { container: 12, label: 12, wrapper: 12 },
                                            md: { container: 8, label: 12, wrapper: 12 },
                                            lg: { container: 8, label: 12, wrapper: 12 },
                                        }" :drop="true" name="supporting_documents">
                                        <template #between="scope">
                                            <div v-for="error in v$['supporting_documents'].$errors"
                                                :key="error.$uid + `supporting_documents`">
                                                <span class="text-danger">{{ error.$message }}</span>
                                            </div>
                                        </template>
                                    </MultifileElement>

                                    <div style="height: 20vh"></div>
                                </Vueform>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div v-if="pageAttrs.beneficiary_group != null">

                        <benefinary-payment-list :createList="createList" @generatePDFEvent="generatePDF"
                            :group="pageAttrs.beneficiary_group" :participants="pageAttrs.beneficiary_participants" />
                    </div>
                </div>
                <div class="row d-flex justify-content-center p-4 mx-auto my-4"
                    style="width: 100%; background: gainsboro">
                    <div v-if="step == 1" class="col-md-4 px-3">
                        <b-button @click.prevent="resetForm" variant="danger" class="w-100"> Cancel </b-button>
                    </div>
                    <div v-else class="col-md-4 px-3">
                        <b-button @click.prevent="backStep" variant="danger" class="w-100 font-size-12">Back </b-button>
                    </div>

                    <div class="col-md-4 px-3">
                        <b-button @click.prevent="saveDefault" variant="info" class="w-100"> Save & Proceed Later
                        </b-button>
                    </div>
                    <div class="col-md-4 px-3">
                        <b-button @click.prevent="proceedSubmission" variant="success" class="w-100"> {{ step == 1 ?
                            "Proceed & Preview"
                            : "Submit" }} </b-button>
                    </div>
                </div>
            </div>

        </div>
    </Layout>
</template>

<script>
import { ref, useAttrs, onMounted, computed } from "vue";
import { RequestHelper } from "@/mixins/helpers";
import { notify } from "@/mixins/notify";
import "@vueform/multiselect/themes/default.css";
import PageHeader from "@/Components/page-header.vue";
import useVueform from "@vueform/vueform";
import Vueform from "@vueform/vueform";
import Axios from "axios";
import { vMaska } from "maska";
import { required, email, minLength, maxLength, helpers } from "@vuelidate/validators";
import { useVuelidate } from "@vuelidate/core";
import Swal from "sweetalert2";
import moment from "moment";
import { useCurrencyStore } from "@/stores/currencyStore";
import { useInvoiceFormStore } from "@/stores/invoiceFormStore";
import Layout from "@/Layouts/verticalvendor.vue";
import { router, usePage } from "@inertiajs/vue3";
import { isArray } from "lodash";
import benefinaryPaymentList from "./pdfs-generated/benefinary-payment-list.vue"

export default {
    name: "invoice-form",
    mixins: [Vueform],
    directives: { maska: vMaska },
    components: {
        Layout,
        PageHeader,
        benefinaryPaymentList,
    },

    setup(props, ctx) {
        const attrs = useAttrs();
        const pageAttrs = useAttrs();
        const beneficiaryList = ref(null);

        const createList = ref(false)

        const generatePDF = (data) => {
            console.log({data});
            beneficiaryList.value = data;
        }

        const { vendor_number, user_id, id, email } = attrs.spProfile;
        const storeForm = useInvoiceFormStore();

        const details = usePage().props;

        const staffEmails = ref([]);

        const goToInvoiceList = () => {
            return router.get(route("sp.invoices"));
        };

        const step = ref(1);
        const formDetailsExt = ref({
            user_type: "sp",
            vendor_id: user_id,
            service_provider_id: id,
            amounts: { invoice_gross_amount: null },
        });

        const formDetails = ref({
            user_type: "sp",
            vendor_id: user_id,
            service_provider_id: id,
            type_of_service: null,
            service_description: null,
            has_unicef_contract: null,
            contract_number: [null],
            unicef_focal_person: [null],
            vendor_number,
            invoice_number: null,
            invoice_date: null,
            is_vat_applied: null,
            invoice_currency: "SSP",
            efris_receipt: null,
            invoice_currency: null,
            invoice_file: null,
            amounts: {
                invoice_gross_amount: null,
            },
        });
        const formDetailsConfirmation = ref({
            user_type: "sp",
            vendor_number,
            vendor_id: user_id,
            service_provider_id: id,
        });
        const formInfo = ref(null);
        const formInfoConfirmation = ref(null);
        const isDisabled = ref(false);

        const BSC_URL = import.meta.env.VITE_FINANCE_APP_URL;
        const dontShowVat = import.meta.env.VITE_COUNTRY_OFFICE == "South Sudan";

        const tryPost = async () => {
            let formData = new FormData();
            formData.append("test", "kksksksksk");
            storeForm.storeSupportingFile({ uuid: "01", file: 90002 });
            await RequestHelper.postRequest(`${BSC_URL}/api/auth-token/test`, formData, {}, attrs.auth.user.api_token)
                .then(({ data }) => {
                    console.log({ data });
                })
                .catch((err) => {
                    console.log({ err });
                });
        };

        // upload temp file
        const uploadTempFileEFRIS = async (file, el$) => {
            let url = BSC_URL + "/api/sp/v1/submit-efrisetemp-files";
            // let url = BSC_URL + '/api/auth-token/test';
            let output = null;
            let data = el$.form$.convertFormData({
                file,
            });

            await RequestHelper.postRequest(url, data, {}, attrs.auth.user.api_token)
                .then(({ data }) => {
                    Swal.fire({
                        html: `<p class="text-sm">Valid EFRIS Receipt FILE</p><p>File is Acceptable</p>`,
                        icon: "success",
                    });

                    output = data.results;
                })
                .catch((err) => {
                    const { message, error } = err.response.data;
                    console.error({ err, message, error });

                    Swal.fire({
                        html: `<p class="text-sm">Invalid EFRIS Receipt FILE!</p><p>UNICEF UGANDA TIN NUMBER: <br/><b>${message?.output?.errors?.unicefTinNumber}</b> NOT FOUND</p>`,
                        icon: "error",
                    });
                    output = null;
                });
            // console.log({output});
            Object.assign(formDetailsExt.value, {
                efrisNumber: output?.fileContent?.content?.efrisNumber,
                vatAmount: output?.fileContent?.content?.vatAmount,
                vatRealAmount: output?.fileContent?.content?.vatRealAmount,
                exciseDutyAmount: output?.fileContent?.content?.exciseDutyAmount,
                grossAmounts: output?.fileContent?.content?.grossAmounts,
                netAmount: output?.fileContent?.content?.netAmount,
                currency: output?.fileContent?.content?.currency,
            });

            Object.assign(formDetails.value, {
                invoice_currency: formDetailsExt.value["currency"],
                invoice_gross_amount: parseFloat(`${formDetailsExt.value["grossAmounts"]}`.replaceAll(/,/g, "")).toFixed(2),
                exise_duty_amount: parseFloat(`${formDetailsExt.value["exciseDutyAmount"]}`.replaceAll(/,/g, "")).toFixed(2),
                invoice_value_amount: parseFloat(`${formDetailsExt.value["netAmount"]}`.replaceAll(/,/g, "")).toFixed(2),
                efd_receipt_number: parseFloat(`${formDetailsExt.value["efrisNumber"]}`.replaceAll(/,/g, "")),
                vat_amount: parseFloat(`${formDetailsExt.value["vatAmount"]}`.replaceAll(/,/g, "")).toFixed(2),
            });

            Object.assign(formDetailsExt.value, {
                amounts: {
                    invoice_gross_amount: parseFloat(`${formDetailsExt.value["grossAmounts"]}`.replaceAll(/,/g, "")).toFixed(2),
                },
                invoice_currency: formDetailsExt.value["currency"],
                invoice_gross_amount: parseFloat(`${formDetailsExt.value["grossAmounts"]}`.replaceAll(/,/g, "")).toFixed(2),
                exise_duty_amount: parseFloat(`${formDetailsExt.value["exciseDutyAmount"]}`.replaceAll(/,/g, "")).toFixed(2),
                invoice_value_amount: parseFloat(`${formDetailsExt.value["netAmount"]}`.replaceAll(/,/g, "")).toFixed(2),
                efd_receipt_number: parseFloat(`${formDetailsExt.value["efrisNumber"]}`.replaceAll(/,/g, "")),
                vat_amount: parseFloat(`${formDetailsExt.value["vatAmount"]}`.replaceAll(/,/g, "")).toFixed(2),
            });
            return output;
        };
        const uploadTempFile = async (file, el$) => {
            let url = BSC_URL + "/api/sp/v1/submit-invoicetemp-files";
            let output = null;
            let data = new FormData();
            data.append("file", file);
            // let data = el$.form$.convertFormData({
            //     file,
            // });

            await RequestHelper.postRequest(url, data, {}, attrs.auth.user.api_token)
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
            let url = BSC_URL + "/api/sp/v1/delete-invoicetemp-files";
            let output = null;
            let data = el$.form$.convertFormData({
                tmp,
                originalName,
            });

            await RequestHelper.postRequest(url, data, {}, attrs.auth.user.api_token)
                .then(({ data }) => {
                    output = data;
                })
                .catch((err) => {
                    output = null;
                });
            return output;
        };

        const removeEFRISTempFile = async (file, el$) => {
            const { tmp, originalName } = file;
            let url = BSC_URL + "/api/sp/v1/delete-invoicetemp-files";
            let output = null;
            let data = el$.form$.convertFormData({
                tmp,
                originalName,
            });

            await RequestHelper.postRequest(url, data, {}, attrs.auth.user.api_token)
                .then(({ data }) => {
                    output = data;
                    Object.assign(formDetails.value, {
                        invoice_value_amount: null,
                        efd_receipt_number: null,
                        exise_duty_amount: null,
                        invoice_gross_amount: null,
                        vat_amount: null,
                        currency: "SSP",
                    });

                    Object.assign(formDetailsExt.value, {
                        amounts: {
                            invoice_gross_amount: null,
                        },
                    });
                })
                .catch((err) => {
                    output = null;
                });

            return output;
        };

        // form vaidators
        const rules = computed(() => {
            return {
                type_of_service: {},
                service_description: {},
                has_unicef_contract: {
                    required,
                },
                contract_number: {},
                unicef_focal_person: {},
                vendor_number: {
                    required,
                },
                invoice_number: {
                    required,
                },
                invoice_date: {
                    required,
                },

                // is_vat_applied: {
                //     required
                // },
                invoice_currency: {
                    required,
                },
                efris_receipt: {},
                supporting_documents: {},
                invoice_file: {
                    required,
                },
                amounts: {
                    invoice_gross_amount: {
                        required,
                    },
                },
            };
        });
        const v$ = useVuelidate(rules, formDetails);

        const getSpServices = async (query) => {
            console.log({ query });
            const BSCURL = import.meta.env.VITE_FINANCE_APP_URL;
            let list = [];

            await RequestHelper.getRequest(
                `${BSCURL}/api/bsc-invoices/sp/products/all`,
                {
                    q: `${query || ""}`,
                },
                attrs.auth.user.api_token
            ).then(({ data }) => {
                list = data.results.map((e) => {
                    e.value = e.commodity_title_en;
                    e.label = e.commodity_title_en;
                    return e;
                });
            });

            return list;
        };
        const staffItems = ref([]);
        const staffDefault = ref([]);
        const getStaffList = async (query) => {
            console.log({ query });
            const BSCURL = import.meta.env.VITE_FINANCE_APP_URL;
            let list = [];

            let emails = {};

            staffEmails.value.forEach((itemEmail) => {
                emails["emails[]"] = itemEmail;
            });

            await RequestHelper.getRequest(
                `${BSCURL}/api/staff/list`,
                {
                    q: `${query || ""}`,
                    ...emails,
                },
                attrs.auth.user.api_token
            ).then(({ data }) => {
                list = data.results.map((e) => {
                    e.value = e.email;
                    e.label = `${e.name}`;
                    return e;
                });
            });
            if (staffEmails.value.length > 0) {
                staffDefault.value = list;
            }
            return list;
        };
        const saveDefault = async () => {
            try {
                Object.assign(formDetails.value, { status: "draft" });
                Object.assign(formDetails.value, { invoice_gross_amount: parseFloat(`${formDetailsExt.value["grossAmounts"]}`.replaceAll(/,/g, "")) });
                // formDetails.value['vendor_id'] = id;
                await RequestHelper.postRequest(BSC_URL + "/api/sp/v1/create-invoice", formDetails.value, {}, attrs.auth.user.api_token)
                    .then(({ data }) => {
                        formDetailsConfirmation.value = formDetails.value;
                        step.value = 2;
                    })
                    .catch(({ data }) => {
                        console.log({ data });
                    });
            } catch (error) {
                throw error;
            }
        };
        const backStep = () => {
            isDisabled.value = false;
            step.value = 1;
            createList.value = false;
        };

        const resetForm = () => {
            isDisabled.value = false;
            createList.value = false;
            step.value = 1;
            formInfo.value.reset();
            formDetailsExt.value = {
                amounts: { invoice_gross_amount: null },
            };
            formDetails.value = {
                user_type: "sp",
                vendor_id: user_id,
                service_provider_id: id,
                type_of_service: null,
                service_description: null,
                has_unicef_contract: null,
                contract_number: [],
                unicef_focal_person: [],
                vendor_number,
                invoice_number: null,
                invoice_date: null,
                is_vat_applied: null,
                efris_receipt: null,
                invoice_currency: null,
                invoice_file: null,
                amounts: {
                    invoice_gross_amount: null,
                },
            };
        };
        const invoiceInfo = ref();
        const serviceDescription = ref("");
        const formatDescription = (d, p) => {
            let obj = { [d]: p || serviceDescription.value || "" };
            console.log(obj);
            return obj;
        };
        const getInvoiceDetails = async () => {
            try {
                Swal.fire({
                    text: "...loading",
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });
                if (details.id == undefined) {
                    Swal.close();
                    return;
                }
                const base_url = import.meta.env.VITE_FINANCE_APP_URL;

                await RequestHelper.getRequest(`${base_url}/api/bsc-invoices/invoices/details/${details.id}`, {}, details.user.api_token).then(async ({ data }) => {
                    const {
                        service_provider_id,
                        ip_profile_id,
                        service_provider_service_id,
                        payment_type,
                        service_name,
                        service_code,
                        service_description,
                        contract_number,
                        vendor_number,
                        unicef_focal_person,
                        unicef_focal_person_names,
                        service_entry_number,
                        fund_commitment_number,
                        invoice_number,
                        invoice_date,
                        tax_payer_number,
                        invoice_currency,
                        invoice_value_amount,
                        tourism_levy_amount,
                        exise_duty_amount,
                        vat_amount,
                        efd_receipt_number,
                        other_taxes_amount,
                        invoice_manager,
                        assigned_staff,
                        stamped_date,
                        accepted_date,
                        closed_date,
                        status,
                        is_draft,
                        service_provider_comments,
                        has_unicef_contract,
                        pv_date,
                        document_number,
                        bsc_request_id,
                        submitted_by,
                        credit_memos,
                        gross_amount,
                        gssc_submit_date,
                    } = data.results;
                    invoiceInfo.value = data.results;

                    if (isArray(unicef_focal_person)) {
                        staffEmails.value = unicef_focal_person;
                    }

                    serviceDescription.value = service_description;

                    await getSpServices(service_name);
                    await getStaffList();
                    Object.assign(formDetails.value, {
                        user_type: "sp",
                        vendor_id: user_id,
                        service_provider_id,
                        type_of_service: service_name,
                        ip_profile_id,
                        service_provider_service_id,
                        payment_type,
                        service_name,
                        service_code,
                        service_description,
                        contract_number,
                        vendor_number,
                        unicef_focal_person,
                        unicef_focal_person_names,
                        service_entry_number,
                        fund_commitment_number,
                        invoice_number,
                        invoice_date,
                        tax_payer_number,
                        invoice_currency,
                        invoice_value_amount,
                        tourism_levy_amount,
                        exise_duty_amount,
                        vat_amount,
                        efd_receipt_number: efd_receipt_number,
                        efris_receipt: null,
                        other_taxes_amount,
                        invoice_manager,
                        assigned_staff,
                        stamped_date,
                        accepted_date,
                        closed_date,
                        status,
                        is_draft,
                        service_provider_comments,
                        is_vat_applied: has_unicef_contract,
                        has_unicef_contract,
                        pv_date,
                        document_number,
                        bsc_request_id,
                        submitted_by,
                        credit_memos,
                        gross_amount,
                        gssc_submit_date,
                    });
                    Object.assign(formDetailsExt.value, {
                        amounts: {
                            invoice_gross_amount: parseFloat(gross_amount).toFixed(3),
                        },
                    });

                    formDetails.value["service_description"] = service_description;

                    Swal.close();
                });
            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Something went wrong!",
                });
            }
        };

        const proceedSubmission = async () => {
            try {
                let beneficiaryFile = null;
                if (pageAttrs.beneficiary_payment_id != null) {
                    createList.value = true;

                }





                Object.assign(formDetails.value, formDetailsExt.value);

                // validate
                let validInputs = false;
                let validAmounts = false;
                validInputs = await v$.value.$validate();
                validAmounts = await v$.value.amounts.$validate();
                if (!validInputs || !validAmounts) {
                    if (!validAmounts) {
                        notify.toastErrorMessage("Invalid Invoice Amount");
                    }

                    return notify.toastErrorMessage("Invalid Inputs");
                }

                Object.assign(formDetails.value, { invoice_gross_amount: parseFloat(`${formDetailsExt.value["amounts"]["invoice_gross_amount"]}`.replaceAll(/,/g, "")) });
                if (details.id != undefined && details.id != null) {
                    formDetails.value["invoice_id"] = details.id || "";
                    formDetails.value["invoice_status"] = invoiceInfo.value["status"] == "DRAFT" ? "DRAFT" : "RESUBMITTED";
                }

                if (beneficiaryList.value != null) {

                    beneficiaryFile = beneficiaryList.value;

                    formDetails.value['beneficiary_verified_file'] = beneficiaryFile;

                }
                formDetails.value['beneficiary_payment_id'] = pageAttrs.beneficiary_payment_id == null ? null : pageAttrs.beneficiary_payment_id;

                console.log({ beneficiaryFile });



                let url = BSC_URL + "/api/sp/v1/create-invoice";
                switch (step.value) {
                    case 2:
                        Object.assign(formDetails.value, { status: "draft" });
                        Swal.fire({
                            html: `<p>Proccessing confirmed Information, Please wait...</p>`,
                            allowOutsideClick: false,
                            showCancelButton: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                        });
                        url = BSC_URL + "/api/sp/v1/create-invoice";
                        break;

                    default:
                        Swal.fire({
                            html: `<p>Proccessing confirmed Information, Please wait...</p>`,
                            allowOutsideClick: false,
                            showCancelButton: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                        });
                        url = BSC_URL + "/api/data/serialize/object";
                        break;
                }

                await RequestHelper.postRequest(url, formDetails.value, {}, attrs.auth.user.api_token)
                    .then(({ data }) => {
                        storeForm.saveInvoiceFormDataState({ formDetails: data, formData: formDetails.value });
                        formDetailsConfirmation.value = formDetails.value;
                        isDisabled.value = true;
                        Swal.close();
                        if (step.value == 1) {
                            step.value = 2;
                            notify.toastInfoMessage("Confirm information");
                        } else if (step.value == 2) {
                            notify.toastSuccessMessage("Successfully Submitted!");

                            resetForm();

                            v$.value.$reset();
                            goToInvoiceList();
                        }
                    })
                    .catch(({ data }) => {
                        console.log({ data });
                        isDisabled.value = false;
                        step.value = 1;
                        Swal.close();
                        notify.toastErrorMessage("Something Failed!");
                    });
            } catch (error) {
                console.error(error);
                Swal.close();
                notify.toastErrorMessage("Something Failed!");
            }
        };

        onMounted(async () => {
            if (pageAttrs.beneficiary_payment_id != null) {
                createList.value = true;
            }else{
                createList.value = false;
            }
            await getInvoiceDetails();
        });

        return {
            serviceDescription,
            staffItems,
            invoiceInfo,
            details,
            goToInvoiceList,
            uploadTempFileEFRIS,
            uploadTempFile,
            removeTempFile,
            removeEFRISTempFile,
            v$,
            isDisabled,
            useVueform,
            proceedSubmission,
            backStep,
            resetForm,
            saveDefault,
            step,
            formDetails,
            formDetailsExt,
            formDetailsConfirmation,
            formInfo,
            formInfoConfirmation,
            getSpServices,
            attrs,
            getStaffList,
            tryPost,
            dontShowVat,
            formatDescription,

            // beneficiary information
            pageAttrs,
            beneficiaryList,
            generatePDF,
            createList,
        };
    },
    methods: {
        getFile() {
            console.log(this.pageAttrs);
        }
    }
};
</script>

<style lang="scss">
@import url("./../../../../../sass/_custom-vueform-style.scss");
</style>
