<template>
    <Head title="Submit Invoice" />
    <Layout>
        <!-- <PageHeader :title="'Submit Invoice'" /> -->
        <div class="tailwind-classes">
            <div class="container">

                <div class="card card-body mb-6">
                    <span class="text-xl font-light text-center">Submit Invoice</span>
                    <Vueform v-show="step == 1" ref="formInfo" v-model="formDatails" class="mb-4" sync>
                        <!-- <Vueform endpoint="/api/sp/test-submit-form" method="post" :prepare="proceedSubmission" v-model="formDatails" class="mb-4"> -->

                        <RadiogroupElement :columns="{
                            xs: { container: 8, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 8, label: 12, wrapper: 12 },
                            lg: { container: 8, label: 12, wrapper: 12 }
                        }" field-name="choose_lang" id="choose_lang" label="Choose Your language" size="lg"
                            name="choose_lang" :items="[
                                { value: 'Vue.js', label: 'Vue.js' },
                                { value: 'React', label: 'React' },
                                { value: 'AngularJS', label: 'AngularJS' },
                            ]" />

                        <SelectElement label="Select field" name="select_lang" label-prop="label" :native="false" size="sm"
                            :columns="{
                                xs: { container: 8, label: 12, wrapper: 12 },
                                sm: { container: 12, label: 12, wrapper: 12 },
                                md: { container: 8, label: 12, wrapper: 12 },
                                lg: { container: 8, label: 12, wrapper: 12 }
                            }" :search="true" :track-by="['value', 'label']" :create="true" :append-new-option="true"
                            :truncate="true" placeholder="Search Items" :items="[

                                { value: 'Vue.js', label: 'Vue.js' },
                                { value: 'React', label: 'React' },
                                { value: 'AngularJS', label: 'AngularJS' },
                            ]" />

                        <DateElement :columns="{
                            xs: { container: 8, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 8, label: 12, wrapper: 12 },
                            lg: { container: 8, label: 12, wrapper: 12 }
                        }" :label="`Event Date`" placeholder="Event Date" :min="new Date().toJSON().slice(0, 10)"
                            id="event_date" display-format="MMMM DD, YYYY" value-format="YYYY-MM-DD" name="event_date">
                            <template #addon-after>
                                <i class='fa fa-calendar-alt'></i>
                            </template>
                        </DateElement>

                        <FileElement :auto="true" :upload-temp-endpoint="uploadTempFile"
                            :remove-temp-endpoint="removeTempFile" :clickable="true" accept=".pdf"
                            label="Invoice File" :file="{ rules: 'max:15360' }" size="md" :columns="{
                                xs: { container: 8, label: 12, wrapper: 12 },
                                sm: { container: 12, label: 12, wrapper: 12 },
                                md: { container: 8, label: 12, wrapper: 12 },
                                lg: { container: 8, label: 12, wrapper: 12 }
                            }" :drop="true" name="invoice" />
                        <MultifileElement :auto="true" upload-temp-endpoint="/api/sp/submit-temp-files/" :clickable="true"
                            remove-temp-endpoint="/api/sp/delete-temp-files/" accept=".jpg,.png,.docx,.xls,.csv,.xlsx,.pdf"
                            label="Supporting Files" :file="{ rules: 'max:15360' }" size="md" :columns="{
                                xs: { container: 8, label: 12, wrapper: 12 },
                                sm: { container: 12, label: 12, wrapper: 12 },
                                md: { container: 8, label: 12, wrapper: 12 },
                                lg: { container: 8, label: 12, wrapper: 12 }
                            }" :drop="true" name="supporting_documents" />
                        <TEditorElement name="t_editor" label="Description" size="md" :columns="{
                            xs: { container: 8, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 8, label: 12, wrapper: 12 },
                            lg: { container: 8, label: 12, wrapper: 12 }
                        }" />
                        <TTextElement label="First Name" name="first_name" :columns="{
                            xs: { container: 8, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 8, label: 12, wrapper: 12 },
                            lg: { container: 8, label: 12, wrapper: 12 }
                        }">
                            <template #addon-after>
                                <i class='fa fa-pen'></i>
                            </template>
                        </TTextElement>
                        <div class="form-group vf-col-8">
                            <label for="cost" style="font-size: 16px; margin-bottom: 3px;">Cost</label>
                            <div class="row d-flex justify-content-evenly">
                                <div class="col-3">
                                    <SelectElement name="select_currency" label-prop="label" :native="false" size="md"
                                        :columns="{
                                            xs: { container: 12, label: 12, wrapper: 12 },
                                            sm: { container: 12, label: 12, wrapper: 12 },
                                            md: { container: 12, label: 12, wrapper: 12 },
                                            lg: { container: 12, label: 12, wrapper: 12 }
                                        }" :search="true" :track-by="['value', 'label']" add-class="py-1"
                                        :truncate="true" placeholder="Currency" :items="[

                                            { value: 'MWK', label: 'MWK' },
                                            { value: 'UGX', label: 'UGX' },
                                            { value: 'USD', label: 'USD' },
                                            { value: 'TZH', label: 'TZH' },
                                            { value: 'KSH', label: 'KSH' },
                                        ]" />
                                </div>
                                <div class="col-9">

                                    <input class="form-control p-2" v-maska data-maska="9 99#.##"
                                        data-maska-tokens="9:[0-9]:repeated" data-maska-reversed type="text"
                                        placeholder="Money Input" v-model="formDatailsExt.cost">
                                </div>
                            </div>

                        </div>
                        <!-- <HiddenElement name="hidden_cost" :modal="formDatails.cost" /> -->
                        <TTextareaElement label="Enter Text" placeholder="Enter Text" rows="5" name="t_description" size="md"
                            :columns="{
                                xs: { container: 8, label: 12, wrapper: 12 },
                                sm: { container: 12, label: 12, wrapper: 12 },
                                md: { container: 8, label: 12, wrapper: 12 },
                                lg: { container: 8, label: 12, wrapper: 12 }
                            }" />
                        <CheckboxElement label="I accept" info="test tip" info-position="right" size="lg" false-value="no"
                            true-value="yes" name="accept" text="I accept the Terms of Use" :columns="{
                                xs: { container: 8, label: 12, wrapper: 12 },
                                sm: { container: 12, label: 12, wrapper: 12 },
                                md: { container: 8, label: 12, wrapper: 12 },
                                lg: { container: 8, label: 12, wrapper: 12 }
                            }">

                        </CheckboxElement>

                        <ListElement label="Todo Items" :columns="{
                            xs: { container: 8, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 8, label: 12, wrapper: 12 },
                            lg: { container: 8, label: 12, wrapper: 12 }
                        }" name="todos">
                            <template #default="{ index }">
                                <TextElement :name="index" placeholder="To-do (inline)" />
                            </template>
                        </ListElement>
                        <ObjectElement name="object" :columns="{
                            xs: { container: 12, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 8, label: 12, wrapper: 12 },
                            lg: { container: 8, label: 12, wrapper: 12 }
                        }">
                            <TextElement name="first_name" placeholder="First name" :columns="{
                                default: 12,
                                sm: 6
                            }" />
                            <TextElement name="last_name" placeholder="Last name" :columns="{
                                default: 12,
                                sm: 6
                            }" />
                        </ObjectElement>
                        <ListElement label="This and that" name="list" :columns="{
                            xs: { container: 12, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 8, label: 12, wrapper: 12 },
                            lg: { container: 8, label: 12, wrapper: 12 }
                        }">
                            <template #default="{ index }">
                                <ObjectElement :name="index" :label="`To-do #${index + 1}`
                                    ">
                                    <TextElement name="todo" placeholder="To-do" />
                                    <CheckboxElement size="lg" name="ready" text="Ready" add-class="-mt-2" />
                                </ObjectElement>
                            </template>
                        </ListElement>
                        <!-- <ButtonElement  :button-class="['font-semibold p-2']" button-label="Submit" name="button" size="md"
                            submits /> -->
                        <div style="height: 10vh;"></div>
                    </Vueform>
                    <Vueform v-if="step == 2" ref="formInfoConfirmation" v-model="formDatailsConfirmation" class="mb-4"
                        sync>
                        <!-- <Vueform endpoint="/api/sp/test-submit-form" method="post" :prepare="proceedSubmission" v-model="formDatails" class="mb-4"> -->

                        <RadiogroupElement disabled="true" :columns="{
                            xs: { container: 8, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 8, label: 12, wrapper: 12 },
                            lg: { container: 8, label: 12, wrapper: 12 }
                        }" field-name="choose_lang" id="choose_lang" label="Choose Your language" size="lg"
                            name="choose_lang" :items="[
                                { value: 'Vue.js', label: 'Vue.js' },
                                { value: 'React', label: 'React' },
                                { value: 'AngularJS', label: 'AngularJS' },
                            ]" />

                        <SelectElement disabled="true" label="Select field" name="select_lang" label-prop="label"
                            :native="false" size="sm" :columns="{
                                xs: { container: 8, label: 12, wrapper: 12 },
                                sm: { container: 12, label: 12, wrapper: 12 },
                                md: { container: 8, label: 12, wrapper: 12 },
                                lg: { container: 8, label: 12, wrapper: 12 }
                            }" :search="true" :track-by="['value', 'label']" :create="true" :append-new-option="true"
                            :truncate="true" placeholder="Search Items" :items="[

                                { value: 'Vue.js', label: 'Vue.js' },
                                { value: 'React', label: 'React' },
                                { value: 'AngularJS', label: 'AngularJS' },
                            ]" />

                        <DateElement disabled="true" :columns="{
                            xs: { container: 8, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 8, label: 12, wrapper: 12 },
                            lg: { container: 8, label: 12, wrapper: 12 }
                        }" :label="`Event Date`" placeholder="Event Date" :min="new Date().toJSON().slice(0, 10)"
                            id="event_date" display-format="MMMM DD, YYYY" value-format="YYYY-MM-DD" name="event_date">
                            <template #addon-after>
                                <i class='fa fa-calendar-alt'></i>
                            </template>
                        </DateElement>

                        <FileElement disabled="true" :auto="true" upload-temp-endpoint="/api/sp/submit-temp-files/"
                            remove-temp-endpoint="/api/sp/delete-temp-files/" :clickable="true" accept=".pdf"
                            label="Invoice File" :file="{ rules: 'max:15360' }" size="md" :columns="{
                                xs: { container: 8, label: 12, wrapper: 12 },
                                sm: { container: 12, label: 12, wrapper: 12 },
                                md: { container: 8, label: 12, wrapper: 12 },
                                lg: { container: 8, label: 12, wrapper: 12 }
                            }" :drop="true" name="invoice" />
                        <MultifileElement disabled="true" :auto="true" upload-temp-endpoint="/api/sp/submit-temp-files/"
                            :clickable="true" remove-temp-endpoint="/api/sp/delete-temp-files/"
                            accept=".jpg,.png,.docx,.xls,.csv,.xlsx,.pdf" label="Supporting Files"
                            :file="{ rules: 'max:15360' }" size="md" :columns="{
                                xs: { container: 8, label: 12, wrapper: 12 },
                                sm: { container: 12, label: 12, wrapper: 12 },
                                md: { container: 8, label: 12, wrapper: 12 },
                                lg: { container: 8, label: 12, wrapper: 12 }
                            }" :drop="true" name="supporting_documents" />
                        <TEditorElement disabled="true" name="t_editor" size="md" :columns="{
                            xs: { container: 8, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 8, label: 12, wrapper: 12 },
                            lg: { container: 8, label: 12, wrapper: 12 }
                        }" />
                        <TTextElement disabled="true" label="First Name" name="first_name" :columns="{
                            xs: { container: 8, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 8, label: 12, wrapper: 12 },
                            lg: { container: 8, label: 12, wrapper: 12 }
                        }">
                            <template #addon-after>
                                <i class='fa fa-pen'></i>
                            </template>
                        </TTextElement>
                        <div class="form-group vf-col-8">
                            <label for="cost" style="font-size: 16px; margin-bottom: 3px;">Cost</label>
                            <div class="row d-flex justify-content-evenly">
                                <div class="col-3">
                                    <SelectElement disabled="true" name="select_currency" label-prop="label" :native="false"
                                        size="md" :columns="{
                                            xs: { container: 12, label: 12, wrapper: 12 },
                                            sm: { container: 12, label: 12, wrapper: 12 },
                                            md: { container: 12, label: 12, wrapper: 12 },
                                            lg: { container: 12, label: 12, wrapper: 12 }
                                        }" :search="true" :track-by="['value', 'label']" add-class="py-1"
                                        :truncate="true" placeholder="Currency" :items="[

                                            { value: 'MWK', label: 'MWK' },
                                            { value: 'UGX', label: 'UGX' },
                                            { value: 'USD', label: 'USD' },
                                            { value: 'TZH', label: 'TZH' },
                                            { value: 'KSH', label: 'KSH' },
                                        ]" />
                                </div>
                                <div class="col-9">

                                    <input disabled="true" class="form-control p-2" v-maska data-maska="9 99#.##"
                                        data-maska-tokens="9:[0-9]:repeated" data-maska-reversed type="text"
                                        placeholder="Money Input" v-model="formDatailsExt.cost">
                                </div>
                            </div>

                        </div>
                        <!-- <HiddenElement name="hidden_cost" :modal="formDatails.cost" /> -->
                        <TTextareaElement disabled="true" label="Enter Text" placeholder="Enter Text" rows="5"
                            name="t_editor" size="md" :columns="{
                                xs: { container: 8, label: 12, wrapper: 12 },
                                sm: { container: 12, label: 12, wrapper: 12 },
                                md: { container: 8, label: 12, wrapper: 12 },
                                lg: { container: 8, label: 12, wrapper: 12 }
                            }" />
                        <CheckboxElement disabled="true" label="I accept" info="test tip" info-position="right" size="lg"
                            false-value="no" true-value="yes" name="accept" text="I accept the Terms of Use" :columns="{
                                xs: { container: 12, label: 12, wrapper: 12 },
                                sm: { container: 12, label: 12, wrapper: 12 },
                                md: { container: 8, label: 12, wrapper: 12 },
                                lg: { container: 8, label: 12, wrapper: 12 }
                            }">

                        </CheckboxElement>

                        <ListElement disabled="true" label="Todo Items" :columns="{
                            xs: { container: 12, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 8, label: 12, wrapper: 12 },
                            lg: { container: 8, label: 12, wrapper: 12 }
                        }" name="todos">
                            <template #default="{ index }">
                                <TextElement disabled="true" :name="index" placeholder="To-do (inline)" />
                            </template>
                        </ListElement>
                        <ObjectElement disabled="true" name="object" :columns="{
                            xs: { container: 12, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 8, label: 12, wrapper: 8 },
                            lg: { container: 8, label: 12, wrapper: 8 }
                        }">
                            <TextElement disabled="true" name="first_name" placeholder="First name"  />
                            <TextElement disabled="true" name="last_name" placeholder="Last name"  />
                        </ObjectElement>
                        <ListElement disabled="true" label="This and that" name="list" :columns="{
                            xs: { container: 12, label: 12, wrapper: 12 },
                            sm: { container: 12, label: 12, wrapper: 12 },
                            md: { container: 8, label: 12, wrapper: 8 },
                            lg: { container: 8, label: 12, wrapper: 8 }
                        }">
                            <template #default="{ index }">
                                <ObjectElement disabled="true" :name="index" :label="`To-do #${index + 1}`
                                    " >
                                    <TextElement disabled="true" name="todo" placeholder="To-do"  />
                                    <CheckboxElement disabled="true" size="lg" name="ready" text="Ready"
                                        add-class="-mt-2"  />
                                </ObjectElement>
                            </template>
                        </ListElement>
                        <!-- <ButtonElement  :button-class="['font-semibold p-2']" button-label="Submit" name="button" size="md"
                            submits /> -->
                        <div style="height: 10vh;"></div>
                    </Vueform>


                    <div class="row d-flex justify-around p-4 mx-auto" style="width:100%;background: gainsboro;">
                        <button @click.prevent="resetForm" v-if="step == 1"  type="button"
                            class="col-md-3 col-sm-12 my-2 p-2 focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Cancel</button>
                        <button @click.prevent="backStep" v-else  type="button"
                            class="col-md-3 col-sm-12 my-2  p-2 text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">Back</button>
                        <button @click.prevent="saveDefault" style="" type="button"
                            class="col-md-3 col-sm-12 my-2 p-2 text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">Save
                            & Proceed Later</button>

                        <button @click.prevent="proceedSubmission"  type="button"
                            class="col-md-4 my-2 p-2 text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">{{step== 1?'Proceed':'Submit'}}</button>
                    </div>

                </div>
            </div>
        </div>
    </Layout>
</template>
<!-- <script setup>
import { ref } from "vue";
const sr = ref("")
</script> -->
<script >
import { vMaska } from "maska"
import { ref,useAttrs } from "vue";
import imageDefault from "@/../images/brands/dribbble.png"
import Layout from '@/Layouts/verticalmain.vue';
import PageHeader from '@/Components/page-header.vue';

import {RequestHelper} from "@/mixins/helpers";
import {notify} from "@/mixins/notify";


import useVueform from '@vueform/vueform';
import Vueform from '@vueform/vueform';
import Axios from "axios";



// let {RadiogroupElement} = useVueform;
// const data = [useVueform];

export default {
    name: 'test-form',
    mixins: [Vueform],
    directives: { maska: vMaska },
    components: {
        Layout,
        PageHeader
    },
    data() {
        return {
            choose_lang: null,

        }
    },

    setup() {
        const attrs = useAttrs();
        const step = ref(1);
        const formDatailsExt = ref({
            cost: "1289000"
        })
        const formDatails = ref({
            accept: 'yes',

        });
        const formDatailsConfirmation = ref({});
        const formInfo = ref(null);
        const formInfoConfirmation = ref(null);
        // upload temp file
        const uploadTempFile = async (file,el$) =>{
            console.log({file,el$});
            let url = '/api/sp/submit-temp-files/';
            let output = null;
            let data = el$.form$.convertFormData({
                file,
            });

            await RequestHelper.postRequest(url,data,{},attrs.auth.user.api_token).then(({data})=>{
                output = data;
            }).catch(err=>{
                output = null;
            });
            return output;
        }
        // delete temp file
        const removeTempFile = async(file,el$)=>{
            const {tmp,originalName} = file;
            let url = '/api/sp/delete-temp-files/';
            let output = null;
            let data = el$.form$.convertFormData({
                tmp,originalName
            });
            console.log({tmp,originalName});

            await RequestHelper.postRequest(url,data,{},attrs.auth.user.api_token).then(({data})=>{
                output = data;
            }).catch(err=>{
                output = null;
            });
            return output;
        }
        const saveData = async () => {
            console.log(formDatails);
        }

        const saveDefault = () => {

            try {


                Object.assign(formDatails.value, { status: 'draft' })
                Object.assign(formDatails.value, { cost: `${formDatailsExt.value['cost']}`.replaceAll(' ', '') })
                Axios.post("/api/sp/test-submit-form", formDatails.value, {
                    headers: {
                        "Accept": "application/json",
                        'Content-Type': "multipart/form-data; charset=utf-8; boundary=" + Math.random().toString().substr(2),
                    }
                }).then(({ data }) => {
                    formDatailsConfirmation.value = formDatails.value;
                    step.value = 2
                }).catch(({ data }) => {
                    console.log({ data })
                });
            } catch (error) {
                throw error
            }

        }
        const backStep = () => {
            step.value = 1;
        }

        const resetForm = () => {
            step.value = 1;
            formInfo.value.reset();
            formDatails.value = {};
        }


        const proceedSubmission = async () => {


            try {

                Object.assign(formDatails.value, { status: 'submitted' })
                Object.assign(formDatails.value, { cost: `${formDatailsExt.value['cost']}`.replaceAll(' ', '') })
                Axios.post("/api/sp/test-submit-form", formDatails.value, {
                    headers: {
                        "Accept": "application/json",
                        'Content-Type': "multipart/form-data; charset=utf-8; boundary=" + Math.random().toString().substr(2),
                    }
                }).then(({ data }) => {
                    formDatailsConfirmation.value = formDatails.value;
                    step.value = 2
                }).catch(({ data }) => {
                    console.log({ data })
                })
            } catch (error) {
                throw error
            }
        }

        const getInputs = (input, n) => {
            // Remove all non-numeric characters using a regular expression
            const numericText = input.replace(/[^0-9.]/g, '');

            // Convert the numeric text to a number
            const numericValue = parseFloat(numericText);

            // Check if the input was a valid number
            let formattedCurrency = numericValue.toFixed(2);
            if (isNaN(numericValue)) {
                formattedCurrency = null;
                return 'Invalid input';
            }

            // Format the number as currency with two decimal places

            formDatails.value[n] = formattedCurrency;
            return formattedCurrency;
        }

        const formattedCurrency = (currencyValue) => new Intl.NumberFormat('en-US', {
            style: 'currency',
            // currency: ''
        }).format(currencyValue);

        return {
            useVueform, saveData, getInputs, formattedCurrency, formDatails, formDatailsConfirmation, formInfoConfirmation, proceedSubmission, saveDefault, formInfo, formDatailsExt, imageDefault, step, backStep,
            resetForm,
            uploadTempFile,removeTempFile
        };
    }
}
</script>


<style lang="scss">
@import url("./../../../../../sass/_custom-vueform-style.scss");
</style>
