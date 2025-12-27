<template>

    <Head :title="title" />
    <Layout>
        <PageHeader :title="title" :items="items">

        </PageHeader>

        <div>
            <div class="row d-flex justify-content-start">
                <div class="col-md-8">
                    <b-card>
                        <b-card-text>
                            <div class="row justify-content-start">
                                <div class="col-md-9">
                                    <div class="tailwind-classes">

                                        <div class="container-fluid">
                                            <div class="row d-flex justify-content-between">
                                                <div class="col-xl-10 col-lg-10 col-md-12 form-group form-row p-3">
                                                    <Vueform ref="formInfo" v-model="formDetails" sync>
                                                        <TextElement :disabled="isDisabled" size="sm" :columns="{
                                                            xs: { container: 12, label: 12, wrapper: 12 },
                                                            sm: { container: 12, label: 12, wrapper: 12 },
                                                            md: { container: 12, label: 12, wrapper: 12 },
                                                            lg: { container: 12, label: 12, wrapper: 12 },
                                                        }" label="Title" placeholder="# Face Title" name="title"
                                                            :floating="false">
                                                            <template #between="scope">
                                                                <div v-for="error in v$['title'].$errors"
                                                                    :key="error.$uid + `title`">
                                                                    <span class="text-danger">{{ error.$message
                                                                        }}</span>
                                                                </div>
                                                            </template>
                                                        </TextElement>

                                                        <TextElement size="sm" :columns="{
                                                            xs: { container: 12, label: 12, wrapper: 12 },
                                                            sm: { container: 12, label: 12, wrapper: 12 },
                                                            md: { container: 12, label: 12, wrapper: 12 },
                                                            lg: { container: 12, label: 12, wrapper: 12 },
                                                        }" label="Vendor Number" placeholder="# IP Number" name="ip_no" :disabled="true"
                                                            :floating="false">

                                                        </TextElement>
                                                        <SelectElement :disabled="isDisabled" name="face_type"
                                                            label-prop="label" label="Type of Request" :floating="false"
                                                            :native="false" size="sm" :columns="{
                                                                xs: { container: 12, label: 12, wrapper: 12 },
                                                                sm: { container: 12, label: 12, wrapper: 12 },
                                                                md: { container: 12, label: 12, wrapper: 12 },
                                                                lg: { container: 12, label: 12, wrapper: 12 },
                                                            }" :search="true" :track-by="['value', 'label']"
                                                            add-class="py-1" :truncate="true" placeholder="Select"
                                                            :items="[
                                                                { value: 'Direct Cash Transfer', label: 'Direct Cash Transfer' },
                                                                { value: 'Direct Payment', label: 'Direct Payment' },
                                                                { value: 'Reimbursement', label: 'Reimbursement' },
                                                            ]">
                                                            <template #between="scope">
                                                                <div v-for="error in v$['face_type'].$errors"
                                                                    :key="error.$uid + `face_type`">
                                                                    <span class="text-danger">{{ error.$message
                                                                        }}</span>
                                                                </div>
                                                            </template>
                                                        </SelectElement>

                                                        <SelectElement :disabled="isDisabled"
                                                            label="Is this a Requesting or Reporting Form?"
                                                            name="request_report" label-prop="label" :floating="false"
                                                            :native="false" size="sm" :columns="{
                                                                xs: { container: 12, label: 12, wrapper: 12 },
                                                                sm: { container: 12, label: 12, wrapper: 12 },
                                                                md: { container: 12, label: 12, wrapper: 12 },
                                                                lg: { container: 12, label: 12, wrapper: 12 },
                                                            }" :search="true" :track-by="['value', 'label']"
                                                            add-class="py-1" :truncate="true" placeholder="Select"
                                                            :items="[
                                                                { value: 'requesting', label: 'Requesting' },
                                                                { value: 'reporting', label: 'Reporting' },
                                                            ]">
                                                            <template #between="scope">
                                                                <div v-for="error in v$['request_report'].$errors"
                                                                    :key="error.$uid + `request_report`">
                                                                    <span class="text-danger">{{ error.$message
                                                                        }}</span>
                                                                </div>
                                                            </template>
                                                        </SelectElement>

                                                        <SelectElement :disabled="isDisabled"
                                                            label="UNICEF Program Associate" name="pa_email"
                                                            :floating="false" label-prop="label" :native="false"
                                                            size="sm" :columns="{
                                                                xs: { container: 12, label: 12, wrapper: 12 },
                                                                sm: { container: 12, label: 12, wrapper: 12 },
                                                                md: { container: 12, label: 12, wrapper: 12 },
                                                                lg: { container: 12, label: 12, wrapper: 12 },
                                                            }" :search="true" :track-by="['value', 'label']"
                                                            add-class="py-1" :truncate="true"
                                                            placeholder="Select UNICEF Program Associate"
                                                            :items="getUnicefStaff" delay="300">
                                                            <template #between="scope">
                                                                <div v-for="error in v$['pa_email'].$errors"
                                                                    :key="error.$uid + `pa_email`">
                                                                    <span class="text-danger">{{ error.$message
                                                                        }}</span>
                                                                </div>
                                                            </template>
                                                        </SelectElement>


                                                        <div class="vf-col-8">
                                                            <DateElement class="vf-my-3" label="FACE Date"
                                                                :disabled="isDisabled" :floating="false" :columns="{
                                                                    xs: { container: 6, label: 12, wrapper: 12 },
                                                                    sm: { container: 6, label: 12, wrapper: 12 },
                                                                    md: { container: 6, label: 12, wrapper: 12 },
                                                                    lg: { container: 6, label: 12, wrapper: 12 },
                                                                }" size="sm" :date="true" :time="false" :hour24="false"
                                                                placeholder="# Face Date"
                                                                :max="new Date().toJSON().slice(0, 10)"
                                                                name="face_date">
                                                                <template #addon-before>
                                                                    <i class="fa fa-clock"></i>
                                                                </template>
                                                                <template #between="scope">
                                                                    <!-- <div v-for="error in v$['invoice_date'].$errors" :key="error.$uid + `invoice_date`">
                                                                <span class="text-danger">{{ error.$message }}</span>
                                                            </div>
                                                            -->
                                                                </template>
                                                            </DateElement>
                                                        </div>


                                                        <div class="vf-col-6">
                                                            <b>Period</b><br>
                                                            <DateElement class="vf-my-3" label="From"
                                                                :disabled="isDisabled" :floating="false" :columns="{
                                                                    xs: { container: 6, label: 12, wrapper: 12 },
                                                                    sm: { container: 6, label: 12, wrapper: 12 },
                                                                    md: { container: 6, label: 12, wrapper: 12 },
                                                                    lg: { container: 6, label: 12, wrapper: 12 },
                                                                }" size="sm" :date="true" :time="false" :hour24="false"
                                                                placeholder="# PERIOD FROM"
                                                                :max="new Date().toJSON().slice(0, 10)"
                                                                name="start_period">
                                                                <template #addon-before>
                                                                    <i class="fa fa-clock"></i>
                                                                </template>
                                                                <template #between="scope">
                                                                    <div v-for="error in v$['start_period'].$errors"
                                                                        :key="error.$uid + `start_period`">
                                                                        <span class="text-danger">{{ error.$message
                                                                            }}</span>
                                                                    </div>

                                                                </template>
                                                            </DateElement>

                                                        </div>

                                                        <div class="vf-col-6">
                                                            <b></b><br>
                                                            <DateElement class="vf-my-3" label="To"
                                                                :disabled="isDisabled" :floating="false" :columns="{
                                                                    xs: { container: 6, label: 12, wrapper: 12 },
                                                                    sm: { container: 6, label: 12, wrapper: 12 },
                                                                    md: { container: 6, label: 12, wrapper: 12 },
                                                                    lg: { container: 6, label: 12, wrapper: 12 },
                                                                }" size="sm" :date="true" :time="false" :hour24="false"
                                                                placeholder="# PERIOD TO"
                                                                :min="formDetails.start_period" name="end_period">
                                                                <template #addon-before>
                                                                    <i class="fa fa-clock"></i>
                                                                </template>
                                                                <template #between="scope">
                                                                    <div v-for="error in v$['end_period'].$errors"
                                                                        :key="error.$uid + `end_period`">
                                                                        <span class="text-danger">{{ error.$message
                                                                            }}</span>
                                                                    </div>

                                                                </template>
                                                            </DateElement>

                                                        </div>


                                                        <div class="vf-col-12">
                                                            <CheckboxElement :floating="false" :disabled="isDisabled"
                                                                :true-value="1" :false-value="0" :columns="{
                                                                    xs: { container: 12, label: 12, wrapper: 12 },
                                                                    sm: { container: 12, label: 12, wrapper: 12 },
                                                                    md: { container: 12, label: 12, wrapper: 12 },
                                                                    lg: { container: 12, label: 12, wrapper: 12 },
                                                                }" field-name="i_have_scanned_all_documents_one_file"
                                                                id="i_have_scanned_all_documents_one_file"
                                                                :info="`<div>Ensure that the FACE FORM is signed</div>`"
                                                                size="sm" name="i_have_scanned_all_documents_one_file"
                                                                label=""
                                                                text="I have scanned all documents into one file">

                                                            </CheckboxElement>
                                                        </div>

                                                        <FileElement :disabled="isDisabled" :auto="true"
                                                            :upload-temp-endpoint="uploadTempFile"
                                                            :remove-temp-endpoint="removeTempFile" :clickable="true"
                                                            accept=".pdf"
                                                            :info="`<div>Ensure that the FACE FORM is signed</div>`"
                                                            label="Upload FACE Form (Signed File)"
                                                            :file="{ rules: 'max:15360' }" size="sm" :columns="{
                                                                xs: { container: 12, label: 12, wrapper: 12 },
                                                                sm: { container: 12, label: 12, wrapper: 12 },
                                                                md: { container: 12, label: 12, wrapper: 12 },
                                                                lg: { container: 12, label: 12, wrapper: 12 },
                                                            }" :drop="true" name="face_file">
                                                            <template #between="scope">
                                                                <div v-for="error in v$['face_file'].$errors"
                                                                    :key="error.$uid + `face_file`">
                                                                    <span class="text-danger">{{ error.$message
                                                                        }}</span>
                                                                </div>
                                                            </template>
                                                        </FileElement>

                                                        <FileElement :disabled="isDisabled"
                                                            :conditions="[['face_type', 'Direct Payment'], ['request_report', 'reporting']]"
                                                            :auto="true" :upload-temp-endpoint="uploadTempFile"
                                                            :remove-temp-endpoint="removeTempFile" :clickable="true"
                                                            label="Upload Expenditure Report"
                                                            :file="{ rules: 'max:15360' }" size="sm" :columns="{
                                                                xs: { container: 12, label: 12, wrapper: 12 },
                                                                sm: { container: 12, label: 12, wrapper: 12 },
                                                                md: { container: 12, label: 12, wrapper: 12 },
                                                                lg: { container: 12, label: 12, wrapper: 12 },
                                                            }" :drop="true" name="expenditure_report">

                                                        </FileElement>

                                                        <FileElement :disabled="isDisabled"
                                                            :conditions="[['face_type', 'Direct Payment'], ['request_report', 'reporting']]"
                                                            :auto="true" :upload-temp-endpoint="uploadTempFile"
                                                            :remove-temp-endpoint="removeTempFile" :clickable="true"
                                                            label="Upload Invoice" :file="{ rules: 'max:15360' }"
                                                            size="sm" :columns="{
                                                                xs: { container: 12, label: 12, wrapper: 12 },
                                                                sm: { container: 12, label: 12, wrapper: 12 },
                                                                md: { container: 12, label: 12, wrapper: 12 },
                                                                lg: { container: 12, label: 12, wrapper: 12 },
                                                            }" :drop="true" name="invoice">
                                                            <!-- <template #between="scope">
                                                                <div v-for="error in v$['ice_budget'].$errors"
                                                                    :key="error.$uid + `ice_budget`">
                                                                    <span class="text-danger">{{ error.$message
                                                                        }}</span>
                                                                </div>
                                                            </template> -->
                                                        </FileElement>

                                                        <FileElement :disabled="isDisabled"
                                                            :conditions="[['face_type', 'Direct Payment'], ['request_report', 'reporting']]"
                                                            :auto="true" :upload-temp-endpoint="uploadTempFile"
                                                            :remove-temp-endpoint="removeTempFile" :clickable="true"
                                                            label="Contract" :file="{ rules: 'max:15360' }" size="sm"
                                                            :columns="{
                                                                xs: { container: 12, label: 12, wrapper: 12 },
                                                                sm: { container: 12, label: 12, wrapper: 12 },
                                                                md: { container: 12, label: 12, wrapper: 12 },
                                                                lg: { container: 12, label: 12, wrapper: 12 },
                                                            }" :drop="true" name="contract">
                                                            <!-- <template #between="scope">
                                                                <div v-for="error in v$['ice_budget'].$errors"
                                                                    :key="error.$uid + `ice_budget`">
                                                                    <span class="text-danger">{{ error.$message
                                                                        }}</span>
                                                                </div>
                                                            </template> -->
                                                        </FileElement>

                                                        <FileElement :disabled="isDisabled"
                                                            :conditions="[['face_type', 'Direct Payment'], ['request_report', 'reporting']]"
                                                            :auto="true" :upload-temp-endpoint="uploadTempFile"
                                                            :remove-temp-endpoint="removeTempFile" :clickable="true"
                                                            label="Request Letter from the Ministry"
                                                            :file="{ rules: 'max:15360' }" size="sm" :columns="{
                                                                xs: { container: 12, label: 12, wrapper: 12 },
                                                                sm: { container: 12, label: 12, wrapper: 12 },
                                                                md: { container: 12, label: 12, wrapper: 12 },
                                                                lg: { container: 12, label: 12, wrapper: 12 },
                                                            }" :drop="true"
                                                            description="(Applies for a 'Government Partner')"
                                                            name="request_letter_govt_partner">

                                                        </FileElement>



                                                        <FileElement :disabled="isDisabled"
                                                            :conditions="[['face_type', 'Direct Payment'], ['request_report', 'requesting']]"
                                                            :auto="true" :upload-temp-endpoint="uploadTempFile"
                                                            :remove-temp-endpoint="removeTempFile" :clickable="true"
                                                            label="ICE/Budget Breakdown" :file="{ rules: 'max:15360' }"
                                                            size="sm" :columns="{
                                                                xs: { container: 12, label: 12, wrapper: 12 },
                                                                sm: { container: 12, label: 12, wrapper: 12 },
                                                                md: { container: 12, label: 12, wrapper: 12 },
                                                                lg: { container: 12, label: 12, wrapper: 12 },
                                                            }" :drop="true" name="ice_budget_breakdown">
                                                            <!-- <template #between="scope">
                                                                <div v-for="error in v$['file_b'].$errors"
                                                                    :key="error.$uid + `file_b`">
                                                                    <span class="text-danger">{{ error.$message
                                                                        }}</span>
                                                                </div>
                                                            </template> -->
                                                        </FileElement>

                                                        <FileElement :disabled="isDisabled"
                                                            :conditions="[['face_type', 'Direct Payment'], ['request_report', 'requesting']]"
                                                            :auto="true" :upload-temp-endpoint="uploadTempFile"
                                                            :remove-temp-endpoint="removeTempFile" :clickable="true"
                                                            label="Request Letter from the Ministry"
                                                            :file="{ rules: 'max:15360' }" size="sm" :columns="{
                                                                xs: { container: 12, label: 12, wrapper: 12 },
                                                                sm: { container: 12, label: 12, wrapper: 12 },
                                                                md: { container: 12, label: 12, wrapper: 12 },
                                                                lg: { container: 12, label: 12, wrapper: 12 },
                                                            }" :drop="true"
                                                            description="(Applies for a 'Government Partner')"
                                                            name="request_letter_govt_partner">
                                                            <!-- <template #between="scope">
                                                                <div v-for="error in v$['file_b'].$errors"
                                                                    :key="error.$uid + `file_b`">
                                                                    <span class="text-danger">{{ error.$message
                                                                        }}</span>
                                                                </div>
                                                            </template> -->
                                                        </FileElement>




                                                        <FileElement :disabled="isDisabled"
                                                            :conditions="[['face_type', 'Direct Cash Transfer'], ['request_report', 'reporting']]"
                                                            :auto="true" :upload-temp-endpoint="uploadTempFile"
                                                            :remove-temp-endpoint="removeTempFile" :clickable="true"
                                                            label="Expenditure Report" :file="{ rules: 'max:15360' }"
                                                            size="sm" :columns="{
                                                                xs: { container: 12, label: 12, wrapper: 12 },
                                                                sm: { container: 12, label: 12, wrapper: 12 },
                                                                md: { container: 12, label: 12, wrapper: 12 },
                                                                lg: { container: 12, label: 12, wrapper: 12 },
                                                            }" :drop="true" name="expenditure_report">
                                                            <!-- <template #between="scope">
                                                                <div v-for="error in v$['file_b'].$errors"
                                                                    :key="error.$uid + `file_b`">
                                                                    <span class="text-danger">{{ error.$message
                                                                        }}</span>
                                                                </div>
                                                            </template> -->
                                                        </FileElement>

                                                        <FileElement :disabled="isDisabled"
                                                            :conditions="[['face_type', 'Direct Cash Transfer'], ['request_report', 'reporting']]"
                                                            :auto="true" :upload-temp-endpoint="uploadTempFile"
                                                            :remove-temp-endpoint="removeTempFile" :clickable="true"
                                                            label="Cumulative Report" :file="{ rules: 'max:15360' }"
                                                            size="sm" :columns="{
                                                                xs: { container: 12, label: 12, wrapper: 12 },
                                                                sm: { container: 12, label: 12, wrapper: 12 },
                                                                md: { container: 12, label: 12, wrapper: 12 },
                                                                lg: { container: 12, label: 12, wrapper: 12 },
                                                            }" :drop="true" name="cumulative_report">
                                                            <!-- <template #between="scope">
                                                                <div v-for="error in v$['file_b'].$errors"
                                                                    :key="error.$uid + `file_b`">
                                                                    <span class="text-danger">{{ error.$message
                                                                        }}</span>
                                                                </div>
                                                            </template> -->
                                                        </FileElement>

                                                        <FileElement :disabled="isDisabled"
                                                            :conditions="[['face_type', 'Direct Cash Transfer'], ['request_report', 'requesting']]"
                                                            :auto="true" :upload-temp-endpoint="uploadTempFile"
                                                            :remove-temp-endpoint="removeTempFile" :clickable="true"
                                                            label="ICE/Budget Breakdown" :file="{ rules: 'max:15360' }"
                                                            size="sm" :columns="{
                                                                xs: { container: 12, label: 12, wrapper: 12 },
                                                                sm: { container: 12, label: 12, wrapper: 12 },
                                                                md: { container: 12, label: 12, wrapper: 12 },
                                                                lg: { container: 12, label: 12, wrapper: 12 },
                                                            }" :drop="true" name="ice_budget_breakdown">
                                                            <!-- <template #between="scope">
                                                                <div v-for="error in v$['file_b'].$errors"
                                                                    :key="error.$uid + `file_b`">
                                                                    <span class="text-danger">{{ error.$message
                                                                        }}</span>
                                                                </div>
                                                            </template> -->
                                                        </FileElement>

                                                        <!--
                                                        dp
                                                            reporting
                                                                face_form (signed)
                                                                expenditure_report
                                                                invoice
                                                                contract
                                                                request_letter_govt_partner
                                                            requesting
                                                                face_form (signed)
                                                                ice_budget_breakdown
                                                                request_letter_govt_partner

                                                        dct
                                                            reporting
                                                                face_form (signed)
                                                                expenditure_report
                                                                cumulative_report
                                                                is_narrative_report_submitted
                                                            requesting
                                                                face_form (signed)
                                                                ice_budget_breakdown
                                                        Reimbursement ✔
                                                            reporting
                                                                face_form (signed)
                                                                expenditure report
                                                                cumulative_report
                                                            requesting
                                                                face_form (signed)
                                                                ice_budget_breakdown
                                                        -->

                                                        <FileElement :disabled="isDisabled"
                                                            :conditions="[['face_type', 'reimbursement'], ['request_report', 'requesting']]"
                                                            :auto="true" :upload-temp-endpoint="uploadTempFile"
                                                            :remove-temp-endpoint="removeTempFile" :clickable="true"
                                                            label="ICE/Budget Report" :file="{ rules: 'max:15360' }"
                                                            size="sm" :columns="{
                                                                xs: { container: 12, label: 12, wrapper: 12 },
                                                                sm: { container: 12, label: 12, wrapper: 12 },
                                                                md: { container: 12, label: 12, wrapper: 12 },
                                                                lg: { container: 12, label: 12, wrapper: 12 },
                                                            }" :drop="true" name="ice_budget_breakdown">
                                                            <!-- <template #between="scope">
                                                                <div v-for="error in v$['file_b'].$errors"
                                                                    :key="error.$uid + `file_b`">
                                                                    <span class="text-danger">{{ error.$message
                                                                        }}</span>
                                                                </div>
                                                            </template> -->
                                                        </FileElement>



                                                        <FileElement :disabled="isDisabled"
                                                            :conditions="[['face_type', 'reimbursement'], ['request_report', 'reporting']]"
                                                            :auto="true" :upload-temp-endpoint="uploadTempFile"
                                                            :remove-temp-endpoint="removeTempFile" :clickable="true"
                                                            label="Expenditure Report" :file="{ rules: 'max:15360' }"
                                                            size="sm" :columns="{
                                                                xs: { container: 12, label: 12, wrapper: 12 },
                                                                sm: { container: 12, label: 12, wrapper: 12 },
                                                                md: { container: 12, label: 12, wrapper: 12 },
                                                                lg: { container: 12, label: 12, wrapper: 12 },
                                                            }" :drop="true" name="expenditure_report">
                                                            <!-- <template #between="scope">
                                                                <div v-for="error in v$['file_b'].$errors"
                                                                    :key="error.$uid + `file_b`">
                                                                    <span class="text-danger">{{ error.$message
                                                                        }}</span>
                                                                </div>
                                                            </template>
                                                            -->
                                                        </FileElement>

                                                        <FileElement :disabled="isDisabled"
                                                            :conditions="[['face_type', 'reimbursement'], ['request_report', 'reporting']]"
                                                            :auto="true" :upload-temp-endpoint="uploadTempFile"
                                                            :remove-temp-endpoint="removeTempFile" :clickable="true"
                                                            label="Cumulative Report" :file="{ rules: 'max:15360' }"
                                                            size="sm" :columns="{
                                                                xs: { container: 12, label: 12, wrapper: 12 },
                                                                sm: { container: 12, label: 12, wrapper: 12 },
                                                                md: { container: 12, label: 12, wrapper: 12 },
                                                                lg: { container: 12, label: 12, wrapper: 12 },
                                                            }" :drop="true" name="cumulative_report">
                                                            <!-- <template #between="scope">
                                                                <div v-for="error in v$['file_b'].$errors"
                                                                    :key="error.$uid + `file_b`">
                                                                    <span class="text-danger">{{ error.$message
                                                                        }}</span>
                                                                </div>
                                                            </template>
                                                            -->
                                                        </FileElement>
                                                        <div class="vf-col-12">
                                                            <CheckboxElement :floating="false" :disabled="isDisabled"
                                                                :conditions="[['face_type', 'Direct Cash Transfer'], ['request_report', 'reporting']]"
                                                                :true-value="1" :false-value="0" :columns="{
                                                                    xs: { container: 12, label: 12, wrapper: 12 },
                                                                    sm: { container: 12, label: 12, wrapper: 12 },
                                                                    md: { container: 12, label: 12, wrapper: 12 },
                                                                    lg: { container: 12, label: 12, wrapper: 12 },
                                                                }" field-name="is_narrative_report_submitted"
                                                                id="is_narrative_report_submitted" size="sm"
                                                                name="is_narrative_report_submitted"
                                                                text="Report submitted in Narrative System">

                                                            </CheckboxElement>
                                                        </div>
                                                    </Vueform>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-3 bg-slate-500">
                                    <div style="height: 100% !important;"></div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-end">
                                <div class="col-md-4 mx-2">
                                    <b-button variant="danger" class="w-100"
                                        @click.prevent="resetFaceForm">Cancel</b-button>
                                </div>

                                <div class="col-md-4 mx-2">
                                    <b-button variant="primary" class="w-100" @click.prevent="submitFaceForm">Submit
                                        FACE FORM</b-button>
                                </div>

                            </div>

                        </b-card-text>
                    </b-card>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body" style="background-color: #ccc;text-transform: capitalize;">
                            <div class="features">
                                <div class="type">Direct payment</div>
                                <div class="sub-type">reporting</div>
                                <div class="sub-type-item">face form (signed)</div>
                                <div class="sub-type-item">expenditure report</div>
                                <div class="sub-type-item">invoice</div>
                                <div class="sub-type-item">contract</div>
                                <div class="sub-type-item">request letter Government partner</div>
                                <div class="sub-type">requesting</div>
                                <div class="sub-type-item">face form (signed)</div>
                                <div class="sub-type-item">ice/budget breakdown</div>
                                <div class="sub-type-item">request letter Government partner</div>

                                <div class="type">Direct Cash Transfer</div>
                                <div class="sub-type">reporting</div>
                                <div class="sub-type-item">face form (signed)</div>
                                <div class="sub-type-item">expenditure report</div>
                                <div class="sub-type-item">cumulative report</div>
                                <div class="sub-type-item">Confirm whether narrative report is submitted to the
                                    Narrative System</div>
                                <div class="sub-type">requesting</div>
                                <div class="sub-type-item">face form (signed)</div>
                                <div class="sub-type-item">ice/budget breakdown</div>

                                <div class="type">Reimbursement</div>
                                <div class="sub-type">reporting</div>
                                <div class="sub-type-item">face form (signed)</div>
                                <div class="sub-type-item">expenditure report</div>
                                <div class="sub-type-item">cumulative report</div>
                                <div class="sub-type">requesting</div>
                                <div class="sub-type-item">face form (signed)</div>
                                <div class="sub-type-item">ice/budget breakdown</div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import Layout from "@/Layouts/verticalvendor.vue";
import PageHeader from "@/Components/page-header.vue";
import { reactive, toRefs, ref, onMounted, computed } from "vue"
import { debounce } from "lodash";
import { vMaska } from "maska";
import { required, email, minLength, maxLength, helpers } from "@vuelidate/validators";
import { useVuelidate } from "@vuelidate/core";
import Swal from "sweetalert2";
import { RequestHelper } from "@/mixins/helpers";
import { notify } from "@/mixins/notify";
import Vueform, { TextElement } from "@vueform/vueform";

export default {
    components: { Layout, PageHeader },
    mixins: [Vueform],
    setup(props, ctx) {
        const title = "Face Form::Create New";
        const items = [
            {
                href: '/',
                text: "Home"
            },
            {
                href: route('list-faces'),
                text: 'LISTS'
            },
            {
                active: true,

                text: "Create FACE FORM"
            }
        ]
        const searchBeneficiaries = debounce((e) => {
            const valItem = e.target.value || '';


        }, 300);
        const session = usePage().props.auth.user;
        const profile = usePage().props.profile;
        const getUnicefStaff = async (staff) => {
            let list = [];
            await RequestHelper.getRequest('/api/staff-register/list-pa-staff', { q: staff || '' }, session?.api_token).then(({ data }) => {
                console.log({ data });
                data.results.data.map((elem, ky) => {
                    list.push({
                        value: elem.email,
                        label: `${elem.name}`,
                    });
                });
            }).catch(err => {
                console.error({ err });
            });
            console.log(list);
            return list;
        }
        const FINANCE_URL = import.meta.env.VITE_BSC_REQUESTS_APP_URL;
        const state = reactive({
            count: 0,

        })

        const goto = (routeName) => {
            router.visit(route(routeName));
        }

        const isDisabled = ref(false);
        const formInfo = ref(null);
        const formDetails = ref({
            title: null,
            ip_no:profile?.vendor_number,
            face_type: null,
            face_date: null,
            request_report: null,
            unicef_program_associate: null,
            i_have_scanned_all_documents_one_file: null,
            start_period: null,
            end_period: null,
            face_file: null,
            ice_budget_breakdown: null,
            narrative_report: null,
            cumulative_report: null,
            expenditure_report: null,
            contract: null,
            invoice: null,
            request_letter_govt_partner: null,
            narrative_report: null,
            is_narrative_report_submitted: null,
            supporting_files: null,
            pa_email: null
        });

        const rules = computed(() => {
            return {
                title: {
                    required
                },
                face_type: {
                    required
                },
                request_report: {
                    required
                },
                pa_email: {
                    required
                },
                i_have_scanned_all_documents_one_file: {

                },
                start_period: {
                    required
                },
                end_period: {
                    required
                },
                face_file: {
                    required
                },

            }
        })

        const uploadTempFile = async (file, el$) => {
            let url = FINANCE_URL + "/api/face-files/submit-files";
            let output = null;
            let data = new FormData();
            data.append("file", file);
            // let data = el$.form$.convertFormData({
            //     file,
            // });

            await RequestHelper.postRequest(url, data, {}, session?.api_token)
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
            let url = FINANCE_URL + "/api/face-files/delete-files";
            let output = null;
            let data = el$.form$.convertFormData({
                tmp,
                originalName,
            });

            await RequestHelper.postRequest(url, data, {}, session?.api_token)
                .then(({ data }) => {
                    output = data;
                })
                .catch((err) => {
                    output = null;
                });
            return output;
        };



        const v$ = useVuelidate(rules, formDetails);
        const step= ref(1);
        const submitFaceForm = async () => {
            Swal.fire({
                text:"Processing, Please Wait...",
                didOpen:()=>{
                    Swal.showLoading();
                }
            });
            const formValid = await v$.value.$validate();

            if(!formValid){
                Swal.fire({
                        icon:'warning',
                        text:'Invalid Inputs'
                    });
                return;
            }

            if(step.value == 1){
                Swal.close();
                notify.toastInfoMessage('Confirm Details')
                isDisabled.value = true;
                step.value = 2
            }

            else if(step.value == 2){
                let formData = {};

                Object.assign(formData,formDetails.value);

                await RequestHelper.postRequest(FINANCE_URL + '/api/faces/ip-submit',formData,{},session?.api_token).then(({data})=>{
                    Swal.fire({
                        icon:"success",
                        text:'Submitted Successfully'
                    });
                    router.visit(route('list-faces'));
                }).catch(err=>{
                    Swal.fire({
                        icon:"error",
                        text:'Oops...'
                    });
                })


            }


        }

        const resetFaceForm = async () => {
            isDisabled.value = false;
            step.value = 1;
        }

        return {
            ...toRefs(state),
            searchBeneficiaries,
            goto,
            items,
            title,
            formDetails,
            isDisabled,
            uploadTempFile,
            removeTempFile,
            v$,
            formInfo,
            getUnicefStaff,
            submitFaceForm,
            resetFaceForm

        }
    }
}
</script>

<style lang="scss" scoped>
.bg-slate-500 {
    background-color: #ccc;
    height: 100%;
}

.features {
    text-transform: capitalize;
    font-size: 12px;


    .type {
        font-weight: 700;
        text-transform: uppercase;
        margin-left: 20px;
        margin-top: 15px;

    }

    .sub-type {
        font-weight: 600;
        margin-left: 40px;
        margin-top: 10px;
    }

    .sub-type-item {
        margin-left: 60px;
        margin-top: 3px;
    }
}
</style>
