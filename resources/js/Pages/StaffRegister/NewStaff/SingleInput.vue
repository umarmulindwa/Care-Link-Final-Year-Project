<template>

<!--    Didnt create a separate component bse of time constraints :: TODO-->

    <div v-if="embed">
        <div class="row">
            <div class="col-md-9">
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="pesonal_id">PERSONNEL ID</label>
                        <input class="form-control" id="pesonal_id" type="text" v-model="singleStaff.personal_id" placeholder="ID" :disabled="editMode"/>
                        <!--                    <span class="text-danger" v-if="missingValue(singleStaff.personal_id)">PERSONNEL ID required</span>-->
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="index_number">INDEX NUMBER</label>
                        <input class="form-control" id="index_number" type="text" v-model="singleStaff.index_number" placeholder="NUMBER" :disabled="editMode"/>
<!--                        <span class="text-danger" v-if="missingValue(singleStaff.index_number)">INDEX NUMBER is required</span>-->
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-10">
                        <label for="staff_names">NAME</label>
                        <input class="form-control" id="staff_names" type="text" v-model="singleStaff.name" placeholder="First and Last Names" :disabled="editMode"/>
<!--                        <span class="text-danger" v-if="missingValue(singleStaff.name)">NAME is required</span>-->
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-10">
                        <label class="" for="autoSizingSelect">GENDER</label>
                        <div style="display: flex; align-items: center;padding-top:3px;">
                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="gender" id="gender_male" v-model="singleStaff.gender" value="Male" :disabled="editMode">
                                <label class="form-check-label" for="gender_male">
                                    Male
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="gender_female" v-model="singleStaff.gender" value="Female" :disabled="editMode">
                                <label class="form-check-label" for="gender_female">
                                    Female
                                </label>
                            </div>
                        </div>
<!--                        <span class="text-danger" v-if="missingValue(singleStaff.gender)">GENDER is required</span>-->
                    </div>
                </div>
<!--                <div class="row mb-4">-->
<!--                    <div class="col-md-5">-->
<!--                        <label class="" for="autoSizingSelect">MARITAL STATUS</label>-->
<!--                        <select class="form-select form-control" id="" v-model="singleStaff.marital_status">-->
<!--                            <option disabled value="">Select</option>-->
<!--                            <option value="Single">Single</option>-->
<!--                            <option value="Married">Married</option>-->
<!--                            <option value="Widowed">Widowed</option>-->
<!--                            <option value="Other">Other</option>-->
<!--                        </select>-->
<!--                        <span class="text-danger" v-if="missingValue(singleStaff.marital_status)">MARITAL STATUS is required</span>-->
<!--                    </div>-->
<!--                </div>-->
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label class="" for="autoSizingSelect">STAFF TYPE</label>
                        <select class="form-select form-control" id="" v-model="singleStaff.staff_type" :disabled="editMode">
                            <option disabled value="">Select</option>
                            <option value="Official">Official</option>
                            <option value="Volunteer">Volunteer</option>
                            <option value="Intern">Intern</option>
                        </select>
<!--                        <span class="text-danger" v-if="missingValue(singleStaff.staff_type)">STAFF TYPE is required</span>-->
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-8">
                        <label for="staff_email">EMAIL</label>
                        <input class="form-control" id="staff_email" type="email" v-model="singleStaff.email" placeholder="Email" :disabled="editMode"/>
                        <span class="text-danger" v-if="missingValue(singleStaff.email)">EMAIL is required</span>
                        <slot name="email"></slot>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="phone_number">PHONE NUMBER</label>
                        <div class="">
                            <input class="form-control" id="phone-1" type="text" v-model="singleStaff.phone" />
                            <br><span id="error-msg-phone-1" class="error-hide text-danger"></span>
                        </div>
                        <span class="text-danger" v-if="missingValue(singleStaff.phone)">PHONE NUMBER is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <div class="">
                            <label for="whatsapp">WHATSAPP NUMBER</label>
                            <input class="form-control" id="phone-2" type="text" v-model="singleStaff.phone_whatsapp" />
                            <br><span id="error-msg-phone-2" class="error-hide text-danger"></span>
                        </div>
                        <span class="text-danger" v-if="missingValue(singleStaff.phone_whatsapp)">WHATSAPP NUMBER is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <div class="">
                            <label for="whatsapp">ALLOCATED PHONE NUMBER</label>
                            <input class="form-control" id="phone-3" type="text" v-model="singleStaff.allocated_phone_number" />
                            <br><span id="error-msg-phone-3" class="error-hide text-danger"></span>
                        </div>
                        <span class="text-danger" v-if="missingValue(singleStaff.allocated_phone_number)">ALLOCATED PHONE NUMBER is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-10">
                        <label for="address">ADDRESS</label>
                        <div class="row">
                            <div class="col-10">
                    <textarea v-if="Object.keys(locationData).length > 0"
                              class="form-control" rows="2"
                              v-model="locationData.addr">
                    </textarea>
                                <textarea v-else
                                          style="resize: none"
                                          class="form-control"
                                          rows="2"
                                          v-model="singleStaff.address">
                    </textarea>
                            </div>
                            <div class="col-2">
                                <div class="d-flex justify-content-center">
                                    <div @click="openMap = !openMap" class="location-picker-icon small mt-2">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="text-danger" v-if="missingValue(singleStaff.address)">ADDRESS is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="position">POSITION</label>
                        <v-select v-model="singleStaff.position_text" :options="positions" taggable :disabled="editMode"></v-select>
<!--                        <span class="text-danger" v-if="missingValue(singleStaff.position_text)">POSITION is required</span>-->
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="positionId">POSITION ID</label>
                        <input class="form-control" id="positionId" type="text" v-model="singleStaff.position_id" placeholder="Position ID" :disabled="editMode"/>
<!--                        <span class="text-danger" v-if="missingValue(singleStaff.position_id)">POSITION ID is required</span>-->
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="section">SECTION</label>
                        <v-select v-model="singleStaff.section" label="name" :reduce="section => section.id" :options="sections" :disabled="editMode"></v-select>
<!--                        <span class="text-danger" v-if="missingValue(singleStaff.section)">SECTION is required</span>-->
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="pillar">PILLAR</label>
                        <v-select v-model="singleStaff.pillar" label="name" :reduce="pillar => pillar.id" :options="pillars" :disabled="editMode"></v-select>
<!--                        <span class="text-danger" v-if="missingValue(singleStaff.pillar)">PILLAR is required</span>-->
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="org_unit">ORGANISATION UNIT</label>
                        <v-select v-model="singleStaff.organisation_unit" label="name" :reduce="unit => unit.id" :options="organisation_units" :disabled="editMode"></v-select>
                        <span class="text-danger" v-if="missingValue(singleStaff.organisation_unit)">ORGANISATION UNIT is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="report_to">REPORTS TO</label>
                        <v-select v-model="singleStaff.reports_to" label="name" :reduce="st => st.position_id" :options="staff" :disabled="editMode"></v-select>
                        <!--                    <span class="text-danger" v-if="missingValue(singleStaff.reports_to)">REPORTS TO is required</span>-->
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="staff_names">CATEGORY</label>
                        <v-select v-model="singleStaff.category" label="name" :reduce="category => category.id" :options="categories" :disabled="editMode"></v-select>
<!--                        <span class="text-danger" v-if="missingValue(singleStaff.category)">CATEGORY is required</span>-->
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="appointment_type">APPOINTMENT TYPE</label>
                        <v-select v-model="singleStaff.appointment_type" label="name" :reduce="types => types.id" :options="appointment_types" :disabled="editMode"></v-select>
<!--                        <span class="text-danger" v-if="missingValue(singleStaff.appointment_type)">APPOINTMENT TYPE is required</span>-->
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="date_joined_global">DATE JOINED UNICEF (GLOBAL)</label>
                        <input class="form-control" id="date_joined_global" type="date" v-model="singleStaff.date_began_working_with_unicef" :disabled="editMode"/>
<!--                        <span class="text-danger" v-if="missingValue(singleStaff.date_began_working_with_unicef)">DATE JOINED UNICEF (GLOBAL) is required</span>-->
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="date_joined_country">DATE JOINED UNICEF (COUNTRY OFFICE)</label>
                        <input class="form-control" id="date_joined_country" type="date" v-model="singleStaff.date_began_working_with_unicef_country_level" :disabled="editMode"/>
<!--                        <span class="text-danger" v-if="missingValue(singleStaff.date_began_working_with_unicef_country_level)">DATE JOINED UNICEF (COUNTRY OFFICE) is required</span>-->
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="contract_end">CONTRACT END DATE</label>
                        <input class="form-control" id="contract_end" type="date" v-model="singleStaff.date_contract_end" :disabled="editMode"/>
<!--                        <span class="text-danger" v-if="missingValue(singleStaff.date_contract_end)">CONTRACT END DATE is required</span>-->
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="contract_end">BLOOD GROUP (Optional)</label>
                        <select class="form-select form-control" id="" v-model="singleStaff.blood_group" :disabled="editMode">
                            <option disabled value="">Select</option>
                            <template v-for="(group,index) in blood_groups" :key="`b_${index}`">
                                <option :value="group">{{ group }}</option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="contract_end">P/NUMBER (Optional)</label>
                        <input class="form-control"  type="text" v-model="singleStaff.p_number" placeholder="Number" :disabled="editMode"/>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-2">
                        <label for="contract_end">RADIO TYPE</label>
                        <select class="form-select form-control" id="" v-model="singleStaff.radio_type" :disabled="editMode">
                            <option disabled value="">Select</option>
                            <template v-for="(type,index) in radio_types" :key="`b_${index}`">
                                <option :value="type">{{ type }}</option>
                            </template>
                        </select>
<!--                        <span class="text-danger" v-if="missingValue(singleStaff.radio_type)">RADIO TYPE is required</span>-->
                    </div>
                    <div class="col-md-3">
                        <label for="contract_end">CALL SIGN</label>
                        <input class="form-control"  type="text" v-model="singleStaff.call_sign" placeholder="Number" :disabled="editMode"/>
<!--                        <span class="text-danger" v-if="missingValue(singleStaff.call_sign)">CALL SIGN is required</span>-->
                    </div>
                </div>
                <div class="row mb-4 align-items-center">
                    <div class="col-3">
                        <b>Identification & VISA's</b>
                    </div>
                    <div class="col-9">
                        <hr class="m-0" style="height:1px; background-color:#6c757d; border:none;" />
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="contract_end"><span style="text-transform: uppercase !important;">UN ID</span> Card</label>
                        <input class="form-control"  type="text" v-model="singleStaff.identity.un_id" placeholder="Number"/>
                        <span class="text-danger" v-if="missingValue(singleStaff.identity.un_id)">UN ID is required</span>
                    </div>
                    <div class="col-md-4">
                        <label for="contract_end">EXPIRY</label>
                        <input class="form-control"  type="date" v-model="singleStaff.identity.un_id_expiry" placeholder="Number"/>
                        <span class="text-danger" v-if="missingValue(singleStaff.identity.un_id_expiry)">EXPIRY is required</span>
                    </div>
                    <div class="col-md-4">
                        <label for="">UPLOAD</label>
                        <div class="row">
                            <div class="col-md-6">
                                <small>Front <span class="bx bx-check text-success" v-if="singleStaff.identity.front_un_id_proof || temporal.front_un_id_proof"></span></small>
                                <label for="file-upload" style="cursor: pointer; color: blue; text-decoration: underline;">
                                    click to upload
                                </label>
                                <input id="file-upload"
                                       type="file"
                                       class="form-control-sm"
                                       @change="(e) => singleStaff.identity.front_un_id_proof = e.target.files[0]"
                                       accept="image/jpeg, image/png, image/gif"
                                       style="display: none;"/>
                            </div>
                            <div class="col-md-6">
                                <small>Back <span class="bx bx-check text-success" v-if="singleStaff.identity.back_un_id_proof || temporal.back_un_id_proof"></span></small>
                                <label for="file-upload-back" style="cursor: pointer; color: blue; text-decoration: underline;">
                                    click to upload
                                </label>
                                <input id="file-upload-back"
                                       type="file"
                                       class="form-control-sm"
                                       @change="(e) => singleStaff.identity.back_un_id_proof = e.target.files[0]"
                                       accept="image/jpeg, image/png, image/gif"
                                       style="display: none;"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="contract_end"><span style="text-transform: uppercase !important;">UNLP</span> (Optional)</label>
                        <input class="form-control"  type="text" v-model="singleStaff.identity.diplomatic_passport" placeholder="Number"/>
                        <span class="text-danger" v-if="missingValue(singleStaff.identity.diplomatic_passport)">DIPLOMATIC PASSPORT (Optional) is required</span>
                    </div>
                    <div class="col-md-4">
                        <label for="contract_end">EXPIRY</label>
                        <input class="form-control"  type="date" v-model="singleStaff.identity.diplomatic_passport_expiry" placeholder="Number"/>
                        <span class="text-danger" v-if="missingValue(singleStaff.identity.diplomatic_passport_expiry)">EXPIRY is required</span>
                    </div>
                    <div class="col-md-4">
                        <label for="">UPLOAD</label>
                        <div class="row">
                            <div class="col-md-6">
                                <small>
                                    Front
                                    <span class="bx bx-check text-success" v-if="singleStaff.identity.front_diplomatic_passport_proof || temporal.front_diplomatic_passport_proof"></span>
                                </small>
                                <label for="file-upload-front-diplomatic" style="cursor: pointer; color: blue; text-decoration: underline;">
                                    click to upload
                                </label>
                                <input id="file-upload-front-diplomatic"
                                       type="file"
                                       class="form-control"
                                       @change="(e) => singleStaff.identity.front_diplomatic_passport_proof = e.target.files[0]"
                                       accept="image/jpeg, image/png, image/gif"
                                       style="display: none;"/>
                            </div>
                            <div class="col-md-6">
                                <small>
                                    Back
                                    <span class="bx bx-check text-success" v-if="singleStaff.identity.back_diplomatic_passport_proof || temporal.back_diplomatic_passport_proof"></span>
                                </small>
                                <label for="file-upload-back-diplomatic" style="cursor: pointer; color: blue; text-decoration: underline;">
                                    click to upload
                                </label>
                                <input id="file-upload-back-diplomatic"
                                       type="file"
                                       class="form-control"
                                       @change="(e) => singleStaff.identity.back_diplomatic_passport_proof = e.target.files[0]"
                                       accept="image/jpeg, image/png, image/gif"
                                       style="display: none;"/>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="contract_end">NATIONAL PASSPORT</label>
                        <input class="form-control"  type="text" v-model="singleStaff.identity.national_passport" placeholder="Number"/>
                        <span class="text-danger" v-if="missingValue(singleStaff.identity.national_passport)">NATIONAL PASSPORT is required</span>
                    </div>
                    <div class="col-md-4">
                        <label for="contract_end">EXPIRY</label>
                        <input class="form-control"  type="date" v-model="singleStaff.identity.national_passport_expiry" placeholder="Number"/>
                        <span class="text-danger" v-if="missingValue(singleStaff.identity.national_passport_expiry)">EXPIRY is required</span>
                    </div>
                    <div class="col-md-4">
                        <label for="">UPLOAD</label>
                        <div class="row">
                            <div class="col-md-6">
                                <small>
                                    Front
                                    <span class="bx bx-check text-success" v-if="singleStaff.identity.front_national_passport_proof || temporal.front_national_passport_proof"></span>
                                </small>
                                <label for="file-upload-front-national" style="cursor: pointer; color: blue; text-decoration: underline;">
                                    click to upload
                                </label>
                                <input id="file-upload-front-national"
                                       type="file"
                                       class="form-control"
                                       @change="(e) => singleStaff.identity.front_national_passport_proof = e.target.files[0]"
                                       accept="image/jpeg, image/png, image/gif"
                                       style="display: none;"/>
                            </div>
                            <div class="col-md-6">
                                <small>
                                    Back
                                    <span class="bx bx-check text-success" v-if="singleStaff.identity.back_national_passport_proof || temporal.back_national_passport_proof"></span>
                                </small>
                                <label for="file-upload-back-national" style="cursor: pointer; color: blue; text-decoration: underline;">
                                    click to upload
                                </label>
                                <input id="file-upload-back-national"
                                       type="file"
                                       class="form-control"
                                       @change="(e) => singleStaff.identity.back_national_passport_proof = e.target.files[0]"
                                       accept="image/jpeg, image/png, image/gif"
                                       style="display: none;"/>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label for="contract_end">VISA (Optional)</label>
                        <input class="form-control"  type="text" v-model="singleStaff.identity.visa" placeholder="Number"/>
                        <!--                    <span class="text-danger" v-if="missingValue(singleStaff.identity.visa)">VISA (Optional) is required</span>-->
                    </div>
                    <div class="col-md-3">
                        <label for="contract_end">EXPIRY</label>
                        <input class="form-control"  type="date" v-model="singleStaff.identity.visa_expiry" placeholder="Number"/>
                        <!--                    <span class="text-danger" v-if="missingValue(singleStaff.identity.visa_expiry)">EXPIRY is required</span>-->
                    </div>
                    <div class="col-md-2">
                        <label for="contract_end">TYPE</label>
                        <select class="form-select form-control" id="" v-model="singleStaff.identity.visa_type">
                            <option disabled value="">Select</option>
                            <template v-for="(type,index) in visa_types" :key="`b_${index}`">
                                <option :value="type">{{ type }}</option>
                            </template>
                        </select>
                        <!--                    <span class="text-danger" v-if="missingValue(singleStaff.personal_id)">TYPE is required</span>-->
                    </div>
                    <div class="col-md-4">
                        <label for="">UPLOAD</label>
                        <div class="row">
                            <div class="col-md-6">
                                <small>
                                    Front
                                    <span class="bx bx-check text-success" v-if="singleStaff.identity.front_visa_proof || temporal.front_visa_proof"></span>
                                </small>
                                <label for="file-upload-front-visa" style="cursor: pointer; color: blue; text-decoration: underline;">
                                    click to upload
                                </label>
                                <input id="file-upload-front-visa"
                                       type="file"
                                       class="form-control"
                                       @change="(e) => singleStaff.identity.front_visa_proof = e.target.files[0]"
                                       accept="image/jpeg, image/png, image/gif"
                                       style="display: none;"/>
                            </div>
                            <div class="col-md-6">
                                <small>
                                    Back
                                    <span class="bx bx-check text-success" v-if="singleStaff.identity.back_visa_proof || temporal.back_visa_proof"></span>
                                </small>
                                <label for="file-upload-back-visa" style="cursor: pointer; color: blue; text-decoration: underline;">
                                    click to upload
                                </label>
                                <input id="file-upload-back-visa"
                                       type="file"
                                       class="form-control"
                                       @change="(e) => singleStaff.identity.back_visa_proof = e.target.files[0]"
                                       accept="image/jpeg, image/png, image/gif"
                                       style="display: none;"/>
                            </div>
                        </div>


                    </div>
                </div>

                <div class="row mb-4 align-items-center">
                    <div class="col-5">
                        <b>Airtime & Data Allocations (Optional)</b>
                    </div>
                    <div class="col-7">
                        <hr class="m-0" style="height:1px; background-color:#6c757d; border:none;" />
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="appointment_type">PACKAGE OPTED FOR</label>
                        <v-select v-model="singleStaff.airtime_data.packageType" label="name" :options="packageOptions"></v-select>
                    </div>
                </div>

                <b-button variant="primary" @click="addSingleStaff"><i class="bx bx-loader bx-spin font-size-16 align-middle" v-if="processing"></i>{{ editMode? 'Save Changes':'Save' }}</b-button>
            </div>
        </div>
    </div>
    <div v-else>
        <b-modal v-model="open_single_input_modal" scrollable size="lg" title="Register UNICEF Staff" title-class="font-18" centered @ok="handleOk" @hidden="resetForm">
            <div class="mb-4">
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="pesonal_id">PERSONNEL ID</label>
                        <input class="form-control" id="pesonal_id" type="text" v-model="singleStaff.personal_id" placeholder="ID"/>
                        <!--                    <span class="text-danger" v-if="missingValue(singleStaff.personal_id)">PERSONNEL ID required</span>-->
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="index_number">INDEX NUMBER</label>
                        <input class="form-control" id="index_number" type="text" v-model="singleStaff.index_number" placeholder="NUMBER"/>
                        <span class="text-danger" v-if="missingValue(singleStaff.index_number)">INDEX NUMBER is required</span>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-10">
                        <label for="staff_names">NAME</label>
                        <input class="form-control" id="staff_names" type="text" v-model="singleStaff.name" placeholder="First and Last Names"/>
                        <span class="text-danger" v-if="missingValue(singleStaff.name)">NAME is required</span>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-10">
                        <label class="" for="autoSizingSelect">GENDER</label>
                        <div style="display: flex; align-items: center;padding-top:3px;">
                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="gender" id="gender_male" v-model="singleStaff.gender" value="Male">
                                <label class="form-check-label" for="gender_male">
                                    Male
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="gender_female" v-model="singleStaff.gender" value="Female">
                                <label class="form-check-label" for="gender_female">
                                    Female
                                </label>
                            </div>
                        </div>
                        <span class="text-danger" v-if="missingValue(singleStaff.gender)">GENDER is required</span>
                    </div>
                </div>
<!--                <div class="row mb-4">-->
<!--                    <div class="col-md-5">-->
<!--                        <label class="" for="autoSizingSelect">MARITAL STATUS</label>-->
<!--                        <select class="form-select form-control" id="" v-model="singleStaff.marital_status">-->
<!--                            <option disabled value="">Select</option>-->
<!--                            <option value="Single">Single</option>-->
<!--                            <option value="Married">Married</option>-->
<!--                            <option value="Widowed">Widowed</option>-->
<!--                            <option value="Other">Other</option>-->
<!--                        </select>-->
<!--                        <span class="text-danger" v-if="missingValue(singleStaff.marital_status)">MARITAL STATUS is required</span>-->
<!--                    </div>-->
<!--                </div>-->
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label class="" for="autoSizingSelect">STAFF TYPE</label>
                        <select class="form-select form-control" id="" v-model="singleStaff.staff_type">
                            <option disabled value="">Select</option>
                            <option value="Official">Official</option>
                            <option value="Volunteer">Volunteer</option>
                            <option value="Intern">Intern</option>
                        </select>
                        <span class="text-danger" v-if="missingValue(singleStaff.staff_type)">STAFF TYPE is required</span>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-8">
                        <label for="staff_email">EMAIL</label>
                        <input class="form-control" id="staff_email" type="email" v-model="singleStaff.email" placeholder="Email" :disabled="editMode"/>
                        <span class="text-danger" v-if="missingValue(singleStaff.email)">EMAIL is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="phone_number">PHONE NUMBER</label>
                        <div class="">
                            <input class="form-control" id="phone-1" type="text" v-model="singleStaff.phone" />
                            <br><span id="error-msg-phone-1" class="error-hide text-danger"></span>
                        </div>
                        <span class="text-danger" v-if="missingValue(singleStaff.phone)">PHONE NUMBER is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="whatsapp">WHATSAPP NUMBER</label>
                        <input class="form-control" id="phone-2" type="text" v-model="singleStaff.phone_whatsapp" />
                        <br><span id="error-msg-phone-2" class="error-hide text-danger"></span>
                    </div>
                    <span class="text-danger" v-if="missingValue(singleStaff.phone_whatsapp)">WHATSAPP NUMBER is required</span>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="whatsapp">ALLOCATED PHONE NUMBER</label>
                        <input class="form-control" id="phone-3" type="text" v-model="singleStaff.allocated_phone_number" />
                        <br><span id="error-msg-phone-3" class="error-hide text-danger"></span>
                    </div>
                    <span class="text-danger" v-if="missingValue(singleStaff.allocated_phone_number)">ALLOCATED PHONE NUMBER is required</span>
                </div>
                <div class="row mb-4">
                    <div class="col-md-10">
                        <label for="address">ADDRESS</label>
                        <div class="row">
                            <div class="col-10">
                        <textarea v-if="Object.keys(locationData).length > 0"
                                  class="form-control" rows="2"
                                  v-model="locationData.addr">
                        </textarea>
                                <textarea v-else
                                          style="resize: none"
                                          class="form-control"
                                          rows="2"
                                          v-model="singleStaff.address">
                        </textarea>
                            </div>
                            <div class="col-2">
                                <div class="d-flex justify-content-center">
                                    <div @click="openMap = !openMap" class="location-picker-icon small mt-2">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="text-danger" v-if="missingValue(singleStaff.address)">ADDRESS is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="position">POSITION</label>
                        <v-select v-model="singleStaff.position_text" :options="positions" taggable></v-select>
                        <span class="text-danger" v-if="missingValue(singleStaff.position_text)">POSITION is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="positionId">POSITION ID</label>
                        <input class="form-control" id="positionId" type="text" v-model="singleStaff.position_id" placeholder="Position ID"/>
                        <span class="text-danger" v-if="missingValue(singleStaff.position_id)">POSITION ID is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="section">SECTION</label>
                        <v-select v-model="singleStaff.section" label="name" :reduce="section => section.id" :options="sections"></v-select>
                        <span class="text-danger" v-if="missingValue(singleStaff.section)">SECTION is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="pillar">PILLAR</label>
                        <v-select v-model="singleStaff.pillar" label="name" :reduce="pillar => pillar.id" :options="pillars"></v-select>
                        <span class="text-danger" v-if="missingValue(singleStaff.pillar)">PILLAR is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="org_unit">ORGANISATION UNIT</label>
                        <v-select v-model="singleStaff.organisation_unit" label="name" :reduce="unit => unit.id" :options="organisation_units"></v-select>
                        <span class="text-danger" v-if="missingValue(singleStaff.organisation_unit)">ORGANISATION UNIT is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="report_to">REPORTS TO</label>
                        <v-select v-model="singleStaff.reports_to" label="name" :reduce="st => st.position_id" :options="staff"></v-select>
                        <!--                    <span class="text-danger" v-if="missingValue(singleStaff.reports_to)">REPORTS TO is required</span>-->
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="staff_names">CATEGORY</label>
                        <v-select v-model="singleStaff.category" label="name" :reduce="category => category.id" :options="categories"></v-select>
                        <span class="text-danger" v-if="missingValue(singleStaff.category)">CATEGORY is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="appointment_type">APPOINTMENT TYPE</label>
                        <v-select v-model="singleStaff.appointment_type" label="name" :reduce="types => types.id" :options="appointment_types"></v-select>
                        <span class="text-danger" v-if="missingValue(singleStaff.appointment_type)">APPOINTMENT TYPE is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="date_joined_global">DATE JOINED UNICEF (GLOBAL)</label>
                        <input class="form-control" id="date_joined_global" type="date" v-model="singleStaff.date_began_working_with_unicef" />
                        <span class="text-danger" v-if="missingValue(singleStaff.date_began_working_with_unicef)">DATE JOINED UNICEF (GLOBAL) is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="date_joined_country">DATE JOINED UNICEF (COUNTRY OFFICE)</label>
                        <input class="form-control" id="date_joined_country" type="date" v-model="singleStaff.date_began_working_with_unicef_country_level" />
                        <span class="text-danger" v-if="missingValue(singleStaff.date_began_working_with_unicef_country_level)">DATE JOINED UNICEF (COUNTRY OFFICE) is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="contract_end">CONTRACT END DATE</label>
                        <input class="form-control" id="contract_end" type="date" v-model="singleStaff.date_contract_end" />
                        <span class="text-danger" v-if="missingValue(singleStaff.date_contract_end)">CONTRACT END DATE is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="contract_end">BLOOD GROUP (Optional)</label>
                        <select class="form-select form-control" id="" v-model="singleStaff.blood_group">
                            <option disabled value="">Select</option>
                            <template v-for="(group,index) in blood_groups" :key="`b_${index}`">
                                <option :value="group">{{ group }}</option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="contract_end">P/NUMBER (Optional)</label>
                        <input class="form-control"  type="text" v-model="singleStaff.p_number" placeholder="Number"/>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-2">
                        <label for="contract_end">RADIO TYPE</label>
                        <select class="form-select form-control" id="" v-model="singleStaff.radio_type">
                            <option disabled value="">Select</option>
                            <template v-for="(type,index) in radio_types" :key="`b_${index}`">
                                <option :value="type">{{ type }}</option>
                            </template>
                        </select>
                        <span class="text-danger" v-if="missingValue(singleStaff.radio_type)">RADIO TYPE is required</span>
                    </div>
                    <div class="col-md-3">
                        <label for="contract_end">CALL SIGN</label>
                        <input class="form-control"  type="text" v-model="singleStaff.call_sign" placeholder="Number"/>
                        <span class="text-danger" v-if="missingValue(singleStaff.call_sign)">CALL SIGN is required</span>
                    </div>
                </div>
                <div class="row mb-4 align-items-center">
                    <div class="col-3">
                        <b>Identification & VISA's</b>
                    </div>
                    <div class="col-9">
                        <hr class="m-0" style="height:1px; background-color:#6c757d; border:none;" />
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="contract_end">UN ID Card</label>
                        <input class="form-control"  type="text" v-model="singleStaff.identity.un_id" placeholder="Number"/>
                        <span class="text-danger" v-if="missingValue(singleStaff.identity.un_id)">UN ID is required</span>
                    </div>
                    <div class="col-md-4">
                        <label for="contract_end">EXPIRY</label>
                        <input class="form-control"  type="date" v-model="singleStaff.identity.un_id_expiry" placeholder="Number"/>
                        <span class="text-danger" v-if="missingValue(singleStaff.identity.un_id_expiry)">EXPIRY is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="contract_end">UNLP (Optional)</label>
                        <input class="form-control"  type="text" v-model="singleStaff.identity.diplomatic_passport" placeholder="Number"/>
                        <span class="text-danger" v-if="missingValue(singleStaff.identity.diplomatic_passport)">DIPLOMATIC PASSPORT (Optional) is required</span>
                    </div>
                    <div class="col-md-4">
                        <label for="contract_end">EXPIRY</label>
                        <input class="form-control"  type="date" v-model="singleStaff.identity.diplomatic_passport_expiry" placeholder="Number"/>
                        <span class="text-danger" v-if="missingValue(singleStaff.identity.diplomatic_passport_expiry)">EXPIRY is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="contract_end">NATIONAL PASSPORT</label>
                        <input class="form-control"  type="text" v-model="singleStaff.identity.national_passport" placeholder="Number"/>
                        <span class="text-danger" v-if="missingValue(singleStaff.identity.national_passport)">NATIONAL PASSPORT is required</span>
                    </div>
                    <div class="col-md-4">
                        <label for="contract_end">EXPIRY</label>
                        <input class="form-control"  type="date" v-model="singleStaff.identity.national_passport_expiry" placeholder="Number"/>
                        <span class="text-danger" v-if="missingValue(singleStaff.identity.national_passport_expiry)">EXPIRY is required</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label for="contract_end">VISA (Optional)</label>
                        <input class="form-control"  type="text" v-model="singleStaff.identity.visa" placeholder="Number"/>
                        <!--                    <span class="text-danger" v-if="missingValue(singleStaff.identity.visa)">VISA (Optional) is required</span>-->
                    </div>
                    <div class="col-md-3">
                        <label for="contract_end">EXPIRY</label>
                        <input class="form-control"  type="date" v-model="singleStaff.identity.visa_expiry" placeholder="Number"/>
                        <!--                    <span class="text-danger" v-if="missingValue(singleStaff.identity.visa_expiry)">EXPIRY is required</span>-->
                    </div>
                    <div class="col-md-2">
                        <label for="contract_end">TYPE</label>
                        <select class="form-select form-control" id="" v-model="singleStaff.identity.visa_type">
                            <option disabled value="">Select</option>
                            <template v-for="(type,index) in visa_types" :key="`b_${index}`">
                                <option :value="type">{{ type }}</option>
                            </template>
                        </select>
                        <!--                    <span class="text-danger" v-if="missingValue(singleStaff.personal_id)">TYPE is required</span>-->
                    </div>
                </div>
            </div>

            <template #ok>
                <b-button variant="primary" class="pe-5 ps-5" @click="addSingleStaff"><i class="bx bx-loader bx-spin font-size-16 align-middle" v-if="processing"></i>{{ editMode? 'Save Changes':'Save' }}</b-button>
            </template>
        </b-modal>
    </div>

    <map-location
        title="Select Location"
        @clicked="onLocationSelected"
        :show="openMap"
        v-cloak
    />

</template>
<script>

import axios from "axios";
import vSelect from "vue-select";
import {router, usePage} from "@inertiajs/vue3";
import Swal from "sweetalert2";
import StaffTitles from "@/Components/StaticData/StaffTitles.js";
import mapLocation from "@/mixins/MapLocation.vue";
import PhoneUtil from "@/mixins/PhoneUtil.js";
import { Money3Component } from "v-money3";

export default {
    name: 'SingleInput',
    mixins:[PhoneUtil],
    components:{vSelect,mapLocation,Money3Component},
    props:['embed','checkUser'],
    data(){
        return {
            open_single_input_modal: false,
            open_incomplete_info_modal:false,

            openMap:false,

            blood_groups:['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+','AB-'],
            radio_types: ['VH', 'VHF','HF'],
            visa_types: ['Single Entry','Multiple Entry'],
            packageOptions: ["Airtime only", "Data only", "Data and Airtime"],
            dataOptions:['3GB Data','5GB Data','10GB Data','15GB Data'],

            processing:false,
            formIsInvalid:false,

            locationData:{},

            singleStaff: {
                phone: "",
                personal_id: "",
                index_number: "",
                marital_status: "",
                name: "",
                gender: "",
                staff_type: "",
                email: "",
                phone_whatsapp: "",
                allocated_phone_number:"",
                position_id: "",
                address: "",
                lat:"",
                lng:"",
                position_text: "",
                section: "",
                pillar: "",
                organisation_unit: "",
                reports_to: "",
                category: "",
                appointment_type: "",
                date_began_working_with_unicef: "",
                date_began_working_with_unicef_country_level: "",
                date_contract_end: "",
                blood_group:"",
                p_number:"",
                radio_type:"",
                call_sign:"",
                identity:{
                    un_id:"",
                    un_id_expiry:"",
                    front_un_id_proof:"",
                    back_un_id_proof:"",

                    //DIPLOMATIC PASSPORT IS NOW UNLP
                    diplomatic_passport:"",
                    diplomatic_passport_expiry:"",
                    front_diplomatic_passport_proof:"",
                    back_diplomatic_passport_proof:"",

                    national_passport:"",
                    national_passport_expiry:"",
                    front_national_passport_proof:"",
                    back_national_passport_proof:"",

                    visa:"",
                    visa_expiry:"",
                    visa_type:"",
                    front_visa_proof:"",
                    back_visa_proof:"",
                },
                airtime_data:{
                    category:"",
                    isp:"",
                    allocations:"",
                    dataPlan:"",
                    airtimeAllocations:"",
                    packageType:"",
                },
                photo: null,
            },

            temporal:{
                front_un_id_proof:"",
                back_un_id_proof:"",

                front_diplomatic_passport_proof:"",
                back_diplomatic_passport_proof:"",

                front_national_passport_proof:"",
                back_national_passport_proof:"",

                front_visa_proof:"",
                back_visa_proof:"",

            },

            excluded: [
                'blood_group',
                'p_number',
                'airtime_data',
                'visa',
                'visa_expiry',
                'visa_type',
                'reports_to',

                'lat',
                'lng',
                'photo',

                'front_un_id_proof',
                'front_diplomatic_passport_proof',
                'front_national_passport_proof',
                'front_visa_proof',

                'back_un_id_proof',
                'back_diplomatic_passport_proof',
                'back_national_passport_proof',
                'back_visa_proof',
            ],

            editMode: false,
            cleanState: null,

            sections: [],
            organisation_units:[],
            pillars:[],
            categories:[],
            airtime_categories:[],
            staff_types:[],
            positions:StaffTitles,
            appointment_types:[],
            staff:[],
            dollar_rate:1561.427,

            money3Config: {
                masked: false,
                prefix: "",
                suffix: "",
                thousands: ",",
                decimal: ".",
                precision: 2,
                disableNegative: false,
                disabled: false,
                min: null,
                max: null,
                allowBlank: false,
                minimumNumberOfCharacters: 0,
                shouldRound: true,
                focusOnRight: false,
            },
        }
    },

    async mounted() {
        // await this.loadDetails();
        this.initPhone("phone-1");
        this.initPhone("phone-2");
        this.initPhone("phone-3");

        if (this.$props.embed) {
            this.customStyle();
            this.excluded.push(
                'personal_id',
                'index_number',
                'marital_status',
                'gender',
                'staff_type',
                'position_text',
                'position_id',
                'section',
                'pillar',
                'organisation_unit',
                'reports_to',
                'category',
                'appointment_type',
                'date_began_working_with_unicef',
                'date_began_working_with_unicef_country_level',
                'date_contract_end',
                'radio_type',
                'call_sign',
            );

            await this.editStaff(this.$props.embed);
        }
    },

    methods: {
        handleOk(bvModalEvent) {
            bvModalEvent.preventDefault()
            this.addSingleStaff();
        },

        addSingleStaff() {
            this.formIsInvalid = this.validateForm(this.singleStaff,this.excluded);

            if(this.formIsInvalid) return this.errorModal("Required information is missing in some fields.");

            let msg = 'You are about to submit this form. This staff will be informed once their account is created. Sure to proceed?';

            if(this.editMode){
                msg = 'You are about to submit changes for staff details, sure to proceed?';
            }

            this.confirmModal(msg).then((result) => {
                if(result.isConfirmed){
                    const options = {
                        headers: {
                            "Content-Type": "multipart/form-data",
                            Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
                        },
                    };

                    Swal.fire({
                        title: "Please wait...",
                        allowOutsideClick: false,
                        showCancelButton: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });

                    this.processing = true;

                    axios.post("/api/staff-register/addStaff", {...this.singleStaff}, options)
                        .then(async (res) => {
                            this.processing = false;
                            if(this.embed || !this.editMode){
                                await this.submitAirTimeData(options);
                            }

                            this.processing = false;
                            this.open_single_input_modal = false;
                            this.successModal();

                        })
                        .catch((err) => {
                            this.processing = false;

                            this.errorModal(err.message === "Request failed with status code 422"
                                ? "The email has already been taken."
                                : `<p style="font-size: 14px">There is something that went wrong and it is not your fault. Please, reach out to ICT for help.</p>`);

                        });
                }
            })
        },

        submitAirTimeData(options){
            // https://airtime.staging.ssddigitalops.org/api/addSingleNumber
            let url = import.meta.env.VITE_AIRTIMEDATA_URL;

            if(this.editMode){
                url += '/api/updatePlans';
            }else{
                url += '/api/addSingleNumber';
            }

            let formData = {
                selectedStaffName: this.singleStaff.name,
                selectedStaffEmail: this.singleStaff.email,
                phoneNumber: this.editMode?
                    this.singleStaff.airtime_data.phoneNumber :
                    this.singleStaff.allocated_phone_number,
                staffTitle: this.singleStaff.position_text,
                ...this.singleStaff.airtime_data,
            }

            axios.post(url, {...formData }, options);
        },

        async loadDetails() {
            const options = {
                headers: {
                    "Content-Type": "multipart/form-data",
                    Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
                },
            };

            await axios.get("/api/staff-register/loadAddStaffDetails", options)
                .then((res) => {
                    const details = res.data.results;

                    this.sections = details.sections;
                    this.pillars = details.pillars;
                    this.organisation_units = details.organisationUnits;
                    this.staff = details.staff;
                    this.categories = details.categories;
                    this.airtime_categories = details.airtime_category;
                    this.appointment_types = details.appointmentType;
                    this.dollar_rate = details.dollar_rate;

                })
                .catch((err) => {
                    console.log("this is the error", err);
                });
        },

        onLocationSelected(value){
            this.locationData = value;
            this.singleStaff.address = value.addr || '';
            this.singleStaff.lat = value.lat || '';
            this.singleStaff.lng = value.lng || '';
        },

        missingValue(item){
            return !this.isValid(item) && this.formIsInvalid;
        },

        checkAllocationValue(){
            return (this.singleStaff.airtime_data.allocations * this.dollar_rate) < this.singleStaff.airtime_data.airtimeAllocations;
        },

        isValid(value){
            return value !== null && value !== undefined && value !== '';
        },

        validateForm(form,except_columns) {
            return this.hasEmptyValue(form, except_columns);
        },

        hasEmptyValue(obj, except) {
            for (const key in obj) {
                if (obj.hasOwnProperty(key)) {
                    const value = obj[key];

                    //for objects defined in the except array
                    if (except.includes(key)) {
                        continue;
                    }

                    if (typeof value === 'object' && value !== null) {
                        if (this.hasEmptyValue(value, except)) {
                            return true;
                        }
                    } else {
                        if (!this.isValid(value)) {
                            return true;
                        }
                    }
                }
            }
            return false;
        },

        confirmModal(msg){
            return Swal.fire({
                title: "Confirmation",
                icon: "info",
                html: `<p style="font-size: 14px">${msg}</p>`,
                showCloseButton: false,
                showCancelButton: true,
                focusConfirm: true,
                confirmButtonText: "Ok,proceed",
                confirmButtonColor: "#43ad60",
                allowOutsideClick: false,
                allowEscapeKey: false,
                closeOnClickOutside: false,
            });
        },

        successModal(){
            if(this.editMode){
                Swal.fire({
                    title: "Great",
                    html: `<p style="font-size: 14px">You have updated staff details successfully!</p>`,
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
                        if(this.$props.embed){
                            this.$emit('updated');
                        }else{
                            router.visit("/staff_register");
                        }
                    }
                });
            }else{
                Swal.fire({
                    title: "New Staff Registered",
                    icon: "success",
                    html: `<p style="font-size: 14px">A temporary password will be sent to the registered Email shortly.</p>`,
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
                        if(this.$props.embed){
                            this.$emit('created');
                        }else{
                            router.visit("/staff_register");
                        }
                    }
                });
            }
        },

        errorModal(msg){
            return Swal.fire({
                title: "Oops",
                icon: "error",
                html: `<p style="font-size: 14px">${msg}</p>`,
                showCloseButton: false,
                showCancelButton: false,
                focusConfirm: true,
                confirmButtonText: "OK",
                confirmButtonColor: "#43ad60",
                allowOutsideClick: false,
                allowEscapeKey: false,
                closeOnClickOutside: false,
            })
        },

        async editStaff(staff,showModal = true) {
            this.open_single_input_modal = showModal;
            await this.loadDetails();
            this.editMode = true;
            this.cleanState = this.singleStaff;
            const section = this.sections.find(section => section.id == staff.section)?.id;
            const category = this.categories.find(category => category.id == staff.category)?.id;
            const filteredStaff = this.filterObject(staff, this.singleStaff);

            const identity = staff.staff_identity ? {...staff.staff_identity} : {
                un_id: "",
                un_id_expiry: "",
                front_un_id_proof:"",
                back_un_id_proof:"",

                diplomatic_passport: "",
                diplomatic_passport_expiry: "",
                front_diplomatic_passport_proof:"",
                back_diplomatic_passport_proof:"",

                national_passport: "",
                national_passport_expiry: "",
                front_national_passport_proof:"",
                back_national_passport_proof:"",

                visa: "",
                visa_expiry: "",
                visa_type: "",
                front_visa_proof:"",
                back_visa_proof:"",
            };

            const airtime_data = staff.unicef_phone_number? {...staff.unicef_phone_number} : {
                phoneType: "",
                phoneNumber: "",
            }

            this.singleStaff = {
                id: staff.id,
                ...filteredStaff,
                section,
                category,
                identity,
                airtime_data,
                photo: null,
            }

            this.temporal = {
                front_un_id_proof:staff.staff_identity.staff_files.find((file) => file.file_name === 'front un id'),
                back_un_id_proof:staff.staff_identity.staff_files.find((file) => file.file_name === 'back un id'),
                front_diplomatic_passport_proof:staff.staff_identity.staff_files.find((file) => file.file_name === 'front diplomatic'),
                back_diplomatic_passport_proof:staff.staff_identity.staff_files.find((file) => file.file_name === 'back diplomatic'),
                front_national_passport_proof:staff.staff_identity.staff_files.find((file) => file.file_name === 'front national'),
                back_national_passport_proof:staff.staff_identity.staff_files.find((file) => file.file_name === 'back national'),
                front_visa_proof:staff.staff_identity.staff_files.find((file) => file.file_name === 'front visa'),
                back_visa_proof:staff.staff_identity.staff_files.find((file) => file.file_name === 'back visa'),
            };
        },

        filterObject(source, template) {
            const result = {};
            for (const key in template) {
                if (Object.prototype.hasOwnProperty.call(source, key)) {
                    if (typeof template[key] === 'object' && template[key] !== null && !Array.isArray(template[key])) {
                        result[key] = this.filterObject(source[key], template[key]);
                    } else {
                        result[key] = source[key];
                    }
                }
            }
            return result;
        },

        resetForm(){
            if(this.editMode){
                this.editMode = false;
                this.singleStaff = this.cleanState;
            }
        },

        customStyle(){
            //change case 'capitalize'
            document.querySelectorAll('label').forEach(label => {
                let text = label.textContent.toLowerCase().split(' ');
                for (let i = 0; i < text.length; i++) {
                    text[i] = text[i].charAt(0).toUpperCase() + text[i].slice(1);
                }
                label.textContent = text.join(' ');
            });

            //change font-size to match
            document.querySelectorAll('.form-control').forEach(element => {
                element.classList.add('font-size-13');
            });
        }

    },

    watch:{
        'singleStaff.airtime_data.category': function(newV, oldV) {
            if(!this.editMode){
                let usd = this.airtime_categories.find(category => category.category === newV)?.usd_allocations;
                this.singleStaff.airtime_data.allocations = usd ? usd * this.dollar_rate : 0;
            }
        }
    }

}
</script>

<style scoped>
.text-danger {
    text-transform: lowercase;
}

.staff_identity{
    border:1px solid grey;
    border-radius: 2px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.staff_identity-img{
    width: 100%;
}
</style>

