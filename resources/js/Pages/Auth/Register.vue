<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";

import logoDark from "../../../images/logo-blue.svg";
import logoLight from "../../../images/logo-blue.svg";
import InputError from "@/Components/InputError.vue";
import simplebar from "simplebar-vue";

import { onMounted, reactive } from "vue";

//data
const form = useForm({
    name: "",
    email: "",
    patientGender: "",
    patientLocation: "",
    patientContact: "",
    patientDOB: "",
    hospital: "",
    hospitalLocation: "",
    department: "",
    password: "",
    registerAs: "",
    password_confirmation: "",
    terms: true,
    registerAs: "Patient"
});

//data
const state = reactive({
    slide: 0,
    sliding: null,
    logoDark,
    logoLight,
    registerAs: "Patient"
});

const coutryOffice = import.meta.env.VITE_COUNTRY_OFFICE;

//methods

//lifecycle
onMounted(() => {
    document.body.classList.add("side-bg");
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),

    });
};


function onSlideStart(slide) {
    state.sliding = true;
}
function onSlideEnd(slide) {
    state.sliding = false;
}

function changeBtn(btnState) {
    state.registerAs = btnState
    form.registerAs = btnState
}

</script>

<template>

    <Head title="Register" />

    <div>
        <div class="container-fluid p-0">
            <div class="row g-0">
                <div class="col-xl-9">
                    <div class="auth-full-bg  p-4 h-100">
                        <div class="w-100">
                            <div class=""></div>
                            <div class="d-flex h-100 flex-column justify-content-center text-white">
                                <div class="p-4">
                                    <div class="">
                                        <div class="col-lg-6">
                                            <div class="">
                                                <div class="mb-4">
                                                    <h2 class="fw-light">Care Link Platform</h2>
                                                </div>
                                                <div class="mb-4">
                                                    <h4>Connected Care for Better Living.</h4>
                                                </div>
                                                <div class="mb-4">
                                                    <p>
                                                        This system is designed to support HIV patients and healthcare
                                                        providers through secure, private, and reliable digital care. It
                                                        helps patients stay on track with medication and clinic visits
                                                        through timely reminders, while enabling doctors to monitor
                                                        treatment progress and manage patient information efficiently.
                                                    </p>
                                                    <p>By improving communication, adherence, and follow-up care, the
                                                        system promotes healthier lives and better treatment
                                                        outcomes—while respecting confidentiality and dignity at every
                                                        step.</p>
                                                </div>
                                                <Link :href="route('login')">
                                                    <b-button pill variant="primary" class="border border-white"
                                                        style="background-color: rgba(34, 157, 235, 0.9)">
                                                        <div class="px-4">LOGIN</div>
                                                    </b-button>
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end col -->

                <div class="col-xl-3 bg-white">
                    <div class="auth-full-page-content p-md-5 p-4">
                        <div class="w-100">
                            <div class="d-flex flex-column h-100">
                                <div class="mb-4 mb-md-4">
                                    <Link :href="route('login')" class="d-block auth-logo">
                                        <img :src="logoDark" alt="" height="70" class="auth-logo-dark" />
                                        <img :src="logoLight" alt="" height="70" class="auth-logo-light" />
                                    </Link>
                                </div>
                                <div class="my-auto">
                                    <div>
                                        <h5 class="text-primary">Register account</h5>
                                    </div>

                                    <div class="mt-4">

                                        <div class="mb-3 mt-5">
                                            <div class="">
                                                <label>Register As</label>
                                            </div>
                                        </div>
                                        <div class=" d-flex flex-row justify-content-between">
                                            <div><b-button :pill="true"
                                                    :variant="state.registerAs == 'Patient' ? 'info' : 'light'"
                                                    @click="changeBtn('Patient')"><span
                                                        class="ms-4 me-4">Patient</span></b-button></div>
                                            <div><b-button :pill="true"
                                                    :variant="state.registerAs == 'Doctor' ? 'info' : 'light'"
                                                    @click="changeBtn('Doctor')"><span
                                                        class="ms-4 me-4">Doctor</span></b-button>
                                            </div>

                                        </div>
                                        <div v-if="form.errors.email"
                                            class="alert alert-danger alert-dismissible fade show mt-4 mb-4"
                                            role="alert">
                                            {{ form.errors.email }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        <div v-if="form.errors.password"
                                            class="alert alert-danger alert-dismissible fade show mt-4 mb-4"
                                            role="alert">
                                            {{ form.errors.password }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        <div v-if="form.errors.patientGender"
                                            class="alert alert-danger alert-dismissible fade show mt-4 mb-4"
                                            role="alert">
                                            {{ form.errors.patientGender }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        <div v-if="form.errors.patientLocation"
                                            class="alert alert-danger alert-dismissible fade show mt-4 mb-4"
                                            role="alert">
                                            {{ form.errors.patientLocation }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        <div v-if="form.errors.patientContact"
                                            class="alert alert-danger alert-dismissible fade show mt-4 mb-4"
                                            role="alert">
                                            {{ form.errors.patientContact }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        <div v-if="form.errors.patientDOB"
                                            class="alert alert-danger alert-dismissible fade show mt-4 mb-4"
                                            role="alert">
                                            {{ form.errors.patientDOB }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        <div v-if="form.errors.hospital"
                                            class="alert alert-danger alert-dismissible fade show mt-4 mb-4"
                                            role="alert">
                                            {{ form.errors.hospital }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        <div v-if="form.errors.hospitalLocation"
                                            class="alert alert-danger alert-dismissible fade show mt-4 mb-4"
                                            role="alert">
                                            {{ form.errors.hospitalLocation }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        <div v-if="form.errors.department"
                                            class="alert alert-danger alert-dismissible fade show mt-4 mb-4"
                                            role="alert">
                                            {{ form.errors.department }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        <form v-if="state.registerAs == 'Patient'">



                                            <simplebar style="max-height: 200px !important">
                                                <div class="mb-3 mt-5">
                                                    <label for="name">Full Name</label>
                                                    <input style="font-size: 13px" type="text" class="form-control"
                                                        id="name" placeholder="Name" required v-model="form.name" />
                                                    <InputError class="mt-2 text-danger mb-4"
                                                        :message="form.errors.name" />
                                                </div>

                                                <div class="mb-3">
                                                    <label for="email"> Email Address</label>
                                                    <input style="font-size: 13px" type="email" class="form-control"
                                                        id="email" required autocomplete="username"
                                                        placeholder="Enter email" v-model="form.email" />
                                                    <InputError class="mt-2 text-danger mb-4"
                                                        :message="form.errors.email" />
                                                </div>

                                                <div class="mb-3 mt-3">
                                                    <label for="name">Gender</label>
                                                    <input style="font-size: 13px" type="text" class="form-control"
                                                        id="name" placeholder="Gender" required
                                                        v-model="form.patientGender" />
                                                    <InputError class="mt-2 text-danger mb-4"
                                                        :message="form.errors.patientGender" />
                                                </div>
                                                <div class="mb-3 mt-3">
                                                    <label for="name">Location/Address</label>
                                                    <input style="font-size: 13px" type="text" class="form-control"
                                                        id="name" placeholder="Enter your Location" required
                                                        v-model="form.patientLocation" />
                                                    <InputError class="mt-2 text-danger mb-4"
                                                        :message="form.errors.patientLocation" />
                                                </div>
                                                <div class="mb-3 mt-3">
                                                    <label for="name">Phone Number</label>
                                                    <input style="font-size: 13px" type="text" class="form-control"
                                                        id="name" placeholder="Enter your Phone Number" required
                                                        v-model="form.patientContact" />
                                                    <InputError class="mt-2 text-danger mb-4"
                                                        :message="form.errors.patientContact" />
                                                </div>
                                                <div class="mb-3 mt-3">
                                                    <label for="name">Date of Birth</label>
                                                    <input style="font-size: 13px" type="date" class="form-control"
                                                        id="name" placeholder="DOB" required
                                                        v-model="form.patientDOB" />
                                                    <InputError class="mt-2 text-danger mb-4"
                                                        :message="form.errors.patientDOB" />
                                                </div>

                                                <div class="mb-3">
                                                    <label for="password">Password</label>
                                                    <input style="font-size: 13px" type="password" class="form-control"
                                                        id="password" placeholder="Enter password"
                                                        v-model="form.password" required autocomplete="new-password" />
                                                    <InputError class="mt-2 text-danger mb-4"
                                                        :message="form.errors.password" />
                                                </div>
                                                <div class="mb-3">
                                                    <label for="password_confirmation">Confirm Password</label>
                                                    <input style="font-size: 13px" type="password" class="form-control"
                                                        id="password_confirmation" v-model="form.password_confirmation"
                                                        placeholder="Enter password" required
                                                        autocomplete="new-password" />
                                                    <InputError class="mt-2 text-danger mb-4"
                                                        :message="form.errors.password_confirmation" />
                                                </div>
                                            </simplebar>
                                            <div>
                                                <p class="mb-0">
                                                    By registering you agree to the CareLink
                                                    <Link :href="route('terms')" class="text-primary">Terms of Use
                                                    </Link>
                                                </p>
                                            </div>

                                            <div class="mt-4 d-grid">
                                                <button class="btn btn-primary btn-block waves-effect waves-light"
                                                    type="submit" @click="submit" :disabled="form.processing">
                                                    <i class="bx bx-loader bx-spin font-size-16 align-middle me-2"
                                                        v-if="form.processing"></i>Register
                                                </button>
                                            </div>

                                        </form>
                                        <!-- </b-tab> -->
                                        <!-- <b-tab title="Doctor"> -->
                                        <form v-else>
                                            <div v-if="form.errors.email"
                                                class="alert alert-danger alert-dismissible fade show mt-4 mb-4"
                                                role="alert">
                                                {{ form.errors.email }}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div v-if="form.errors.password"
                                                class="alert alert-danger alert-dismissible fade show mt-4 mb-4"
                                                role="alert">
                                                {{ form.errors.password }}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                    aria-label="Close"></button>
                                            </div>
                                            <simplebar style="max-height: 200px !important">
                                                <div class="mb-3 mt-5">
                                                    <label for="name">Full Name</label>
                                                    <input style="font-size: 13px" type="text" class="form-control"
                                                        id="name" placeholder="Name" required v-model="form.name" />
                                                    <InputError class="mt-2 text-danger mb-4"
                                                        :message="form.errors.name" />
                                                </div>
                                                <div class="mb-3 mt-3">
                                                    <label for="name">Hospital</label>
                                                    <input style="font-size: 13px" type="text" class="form-control"
                                                        id="name" placeholder="Hospital Name" required
                                                        v-model="form.hospital" />
                                                    <InputError class="mt-2 text-danger mb-4"
                                                        :message="form.errors.hospital" />
                                                </div>
                                                <div class="mb-3 mt-3">
                                                    <label for="name">Hospital Location</label>
                                                    <input style="font-size: 13px" type="text" class="form-control"
                                                        id="name" placeholder="Hospital Location" required
                                                        v-model="form.hospitalLocation" />
                                                    <InputError class="mt-2 text-danger mb-4"
                                                        :message="form.errors.hospitalLocation" />
                                                </div>
                                                <div class="mb-3 mt-3">
                                                    <label for="name">Department</label>
                                                    <input style="font-size: 13px" type="text" class="form-control"
                                                        id="name" placeholder="Department" required
                                                        v-model="form.department" />
                                                    <InputError class="mt-2 text-danger mb-4"
                                                        :message="form.errors.department" />
                                                </div>

                                                <div class="mb-3">
                                                    <label for="email"> Email Address</label>
                                                    <input style="font-size: 13px" type="email" class="form-control"
                                                        id="email" required autocomplete="username"
                                                        placeholder="Enter email" v-model="form.email" />
                                                    <InputError class="mt-2 text-danger mb-4"
                                                        :message="form.errors.email" />
                                                </div>

                                                <div class="mb-3">
                                                    <label for="password">Password</label>
                                                    <input style="font-size: 13px" type="password" class="form-control"
                                                        id="password" placeholder="Enter password"
                                                        v-model="form.password" required autocomplete="new-password" />
                                                    <InputError class="mt-2 text-danger mb-4"
                                                        :message="form.errors.password" />
                                                </div>
                                                <div class="mb-3">
                                                    <label for="password_confirmation">Confirm Password</label>
                                                    <input style="font-size: 13px" type="password" class="form-control"
                                                        id="password_confirmation" v-model="form.password_confirmation"
                                                        placeholder="Enter password" required
                                                        autocomplete="new-password" />
                                                    <InputError class="mt-2 text-danger mb-4"
                                                        :message="form.errors.password_confirmation" />
                                                </div>
                                            </simplebar>
                                            <div>
                                                <p class="mb-0">
                                                    By registering you agree to the CareLink
                                                    <Link :href="route('terms')" class="text-primary">Terms
                                                        of Use
                                                    </Link>
                                                </p>
                                            </div>

                                            <div class="mt-4 d-grid">
                                                <button class="btn btn-primary btn-block waves-effect waves-light"
                                                    type="submit" @click="submit" :disabled="form.processing">
                                                    <i class="bx bx-loader bx-spin font-size-16 align-middle me-2"
                                                        v-if="form.processing"></i>Register
                                                </button>
                                            </div>

                                        </form>
                                        <!-- </b-tab> -->
                                        <!-- </b-tabs> -->




                                        <div class="mt-5 text-center">
                                            <p>
                                                Already have an account ?
                                                <Link :href="route('login')" class="fw-medium text-primary">Login
                                                </Link>
                                            </p>
                                        </div>

                                    </div>
                                </div>

                                <div class="mt-4 mt-md-5 text-center">
                                    <p>© {{ new Date().getFullYear() }} CARELINK Uganda.</p>
                                    <Link :href="route('supportRequest')">
                                        <div class="text-center text-primary" role="button">
                                            <span><i class="bx bx-support me-1"></i></span>Support
                                        </div>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container-fluid -->
    </div>
</template>

<style lang="scss" scoped>
:deep(.carousel-caption) {
    position: relative !important;
    right: 0;
    bottom: 1.25rem;
    left: 0;
    padding-top: 1.25rem;
    padding-bottom: 1.25rem;
    color: #495057;
    text-align: center;
}

:deep(.carousel-indicators li) {
    background-color: rgba(85, 110, 230, 0.25);
}

.carousel-item {
    background: none !important;
}
</style>
