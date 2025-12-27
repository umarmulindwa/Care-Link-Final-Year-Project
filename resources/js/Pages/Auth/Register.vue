<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";

import logoDark from "../../../images/logo-blue.svg";
import logoLight from "../../../images/logo-blue.svg";
import InputError from "@/Components/InputError.vue";

import { onMounted, reactive } from "vue";

//data
const form = useForm({
    name: "",
    email: "",
    vendor_number: "",
    password: "",
    password_confirmation: "",
    terms: true,
});

//data
const state = reactive({
    slide: 0,
    sliding: null,
    logoDark,
    logoLight,
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

// export default {
//     data() {
//         return {
//             slide: 0,
//             sliding: null,
//             logoDark,
//             logoLight,
//         };
//     },
//     components: {},
//     methods: {
//         onSlideStart(slide) {
//             this.sliding = true;
//         },
//         onSlideEnd(slide) {
//             this.sliding = false;
//         },
//     },
//     mounted() {
//         document.body.classList.add("auth-body-bg");
//     },
// };
</script>

<template>
    <Head title="Register" />

    <div>
        <div class="container-fluid p-0">
            <div class="row g-0">
                <div class="col-xl-9">
                    <div class="auth-full-bg pt-lg-5 p-4 h-100">
                        <div class="w-100">
                            <div class=""></div>
                            <div class="d-flex h-100 flex-column justify-content-center text-white">
                                <div class="p-4">
                                    <div class="">
                                        <div class="col-lg-6">
                                            <div class="">
                                                <div class="mb-4">
                                                    <h2 class="fw-light">{{ coutryOffice }} Digital Operations</h2>
                                                </div>
                                                <div class="mb-4">
                                                    <h4>Enhancing Productivity</h4>
                                                </div>
                                                <div class="mb-4">
                                                    <p>
                                                        We are passionate about efficiency hence have established this platform to limit the need to deliver physical invoices to the UNICEF offices,
                                                        ease the submission of FACE Forms and more.
                                                    </p>
                                                    <p>Yes, that means you need an account (if you are not staff) to use this platform, but worry not, that too has also been simplified.</p>
                                                </div>
                                                <Link :href="route('login')">
                                                    <b-button pill variant="primary" class="border border-white" style="background-color: rgba(34, 157, 235, 0.9)">
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
                                <div class="mb-4 mb-md-5">
                                    <Link :href="route('login')" class="d-block auth-logo">
                                        <img :src="logoDark" alt="" height="30" class="auth-logo-dark" />
                                        <img :src="logoLight" alt="" height="30" class="auth-logo-light" />
                                    </Link>
                                </div>
                                <div class="my-auto">
                                    <div>
                                        <h5 class="text-primary">Register account</h5>
                                        <p class="text-muted">Register as a Service Provider.</p>
                                    </div>

                                    <div class="mt-4">
                                        <form>
                                            <div v-if="form.errors.email" class="alert alert-danger alert-dismissible fade show mt-4 mb-4" role="alert">
                                                {{ form.errors.email }}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                            <div v-if="form.errors.password" class="alert alert-danger alert-dismissible fade show mt-4 mb-4" role="alert">
                                                {{ form.errors.password }}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                            <div class="mb-3">
                                                <label for="name">Name</label>
                                                <input style="font-size: 13px" type="text" class="form-control" id="name" placeholder="Name" required v-model="form.name" />
                                                <InputError class="mt-2 text-danger mb-4" :message="form.errors.name" />
                                            </div>

                                            <div class="mb-3">
                                                <label for="email"> Email Address</label>
                                                <input
                                                    style="font-size: 13px"
                                                    type="email"
                                                    class="form-control"
                                                    id="email"
                                                    required
                                                    autocomplete="username"
                                                    placeholder="Enter email"
                                                    v-model="form.email"
                                                />
                                                <InputError class="mt-2 text-danger mb-4" :message="form.errors.email" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="vendor_number"> Vendor Number</label>
                                                <input
                                                    style="font-size: 13px"
                                                    type="text"
                                                    class="form-control"
                                                    id="vendor_number"
                                                    required
                                                    autocomplete="vendor_number"
                                                    placeholder="Enter vendor number"
                                                    v-model="form.vendor_number"
                                                />
                                                <InputError class="mt-2 text-danger mb-4" :message="form.errors.vendor_number" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="password">Password</label>
                                                <input
                                                    style="font-size: 13px"
                                                    type="password"
                                                    class="form-control"
                                                    id="password"
                                                    placeholder="Enter password"
                                                    v-model="form.password"
                                                    required
                                                    autocomplete="new-password"
                                                />
                                                <InputError class="mt-2 text-danger mb-4" :message="form.errors.password" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="password_confirmation">Confirm Password</label>
                                                <input
                                                    style="font-size: 13px"
                                                    type="password"
                                                    class="form-control"
                                                    id="password_confirmation"
                                                    v-model="form.password_confirmation"
                                                    placeholder="Enter password"
                                                    required
                                                    autocomplete="new-password"
                                                />
                                                <InputError class="mt-2 text-danger mb-4" :message="form.errors.password_confirmation" />
                                            </div>

                                            <div>
                                                <p class="mb-0">
                                                    By registering you agree to the UNICEF DOP
                                                    <Link :href="route('terms')" class="text-primary">Terms of Use</Link>
                                                </p>
                                            </div>

                                            <div class="mt-4 d-grid">
                                                <button class="btn btn-primary btn-block waves-effect waves-light" type="submit" @click="submit" :disabled="form.processing">
                                                    <i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="form.processing"></i>Register
                                                </button>
                                            </div>

                                            <!-- <form>
                                            <div class="mb-3">
                                                <label for="spusername">Service Provider Name</label>
                                                <input type="text" class="form-control" id="spusername" placeholder="Name" />
                                            </div>

                                            <div class="mb-3">
                                                <label for="spuseremail">Service Provider Email Address</label>
                                                <input type="email" class="form-control" id="spuseremail" placeholder="Enter email" />
                                            </div>

                                            <div class="mb-3">
                                                <label for="spuseremail">Vendor Number</label>
                                                <input type="email" class="form-control" id="spuseremail" placeholder="Enter email" />
                                            </div>

                                            <div class="mb-3">
                                                <label for="spuserpassword">Password</label>
                                                <input type="password" class="form-control" id="spuserpassword" placeholder="Enter password" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="password_confirmation">Confirm Password</label>
                                                <input
                                                    type="password"
                                                    class="form-control"

                                                    id="password_confirmation"
                                                    placeholder="Enter password"
                                                    required
                                                    autocomplete="new-password"
                                                />
                                                <InputError class="mt-2" :message="form.errors.password_confirmation" />
                                            </div>

                                            <div>
                                                <p class="mb-0">
                                                    By registering you agree to the UNICEF DOP
                                                    <a href="#" class="text-primary">Terms of Use</a>
                                                </p>
                                            </div>

                                            <div class="mt-4 d-grid">
                                                <button class="btn btn-primary btn-block waves-effect waves-light" type="submit">Register</button>
                                            </div>
                                        </form> -->

                                            <div class="mt-5 text-center">
                                                <p>
                                                    Already have an account ?
                                                    <Link :href="route('login')" class="fw-medium text-primary">Login</Link>
                                                </p>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="mt-4 mt-md-5 text-center">
                                    <p>© {{ new Date().getFullYear() }} UNICEF {{ coutryOffice }}.</p>
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
