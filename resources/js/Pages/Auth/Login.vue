<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";

import logoDark from "../../../images/logo-blue.svg";
import logoLight from "../../../images/logo-blue.svg";
import { onMounted, reactive, onBeforeMount, ref } from "vue";
import InputError from "@/Components/InputError.vue";
import { setLayout } from "../../Composables/Layout.js";
import { computed } from "vue";
import getMobileAppModal from "../modals/getMobileAppModal.vue";

//props
const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    canResetPassword: Boolean,
    status: String,
});

//data
const state = reactive({
    slide: 0,
    sliding: null,
    logoDark,
    logoLight,
    accountActive: false,
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const coutryOffice = computed(() => {
    return import.meta.env.VITE_COUNTRY_OFFICE;
});

//methods

onMounted(() => {
    document.body.classList.add("side-bg");
    //getting account status
    const requestParams = new URLSearchParams(window.location.search);
    const isAccountActive = requestParams.get("accountActive");
    state.accountActive = isAccountActive;
});
//submiting login credentials
const submit = () => {
    form.transform((data) => ({
        ...data,
        remember: form.remember ? "on" : "",
    })).post(route("login"), {
        onFinish: () => {
            form.reset("password");
            //getting account status
            const requestParams = new URLSearchParams(window.location.search);
            const isAccountActive = requestParams.get("accountActive");
            state.accountActive = isAccountActive;
        },
    });
};


const downloadMobileAppModal = ref(false);
const downloadMobileApp = () => {
    downloadMobileAppModal.value = true;
};
const downloadMobileEvent = () => {
    downloadMobileAppModal.value = !downloadMobileAppModal.value;
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

    <Head title="Login" />

    <div>
        <div class="w-100 p-0">
            <div class="row g-0">
                <div class="col-xl-9">
                    <div class="auth-full-bg pt-lg-5 p-4 h-100">
                        <div class="w-100">
                            <div></div>
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
                                                        We are passionate about efficiency hence have established this
                                                        platform to limit the need to deliver physical invoices to the
                                                        UNICEF offices,
                                                        ease the submission of FACE Forms and more.
                                                    </p>
                                                    <p>Yes, that means you need an account (if you are not staff) to use
                                                        this platform, but worry not, that too has also been simplified.
                                                    </p>
                                                </div>
                                                <div class="d-flex">

                                                </div>

                                                <div class="row d-flex justify-content-start">
                                                    <div class="col-md-6 d-flex justify-content-start">
                                                        <Link :href="route('register')">
                                                        <b-button pill variant="primary" class="border border-white"
                                                            style="background-color: rgba(34, 157, 235, 0.9)">
                                                            <div class="px-4">SIGN UP</div>
                                                        </b-button>
                                                        </Link>

                                                        <b-button pill variant="primary" cla @click="downloadMobileApp"
                                                            class="border border-white mx-2"
                                                            style="background-color: rgba(34, 157, 235, 0.9)">
                                                            <div class="px-2"><span class="bx bx-mobile-alt"></span> Mobile
                                                            </div>
                                                        </b-button>
                                                    </div>

                                                </div>
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
                                        <h5 class="text-primary">Welcome Back !</h5>
                                        <p class="text-muted">Sign in to continue to your Care Link Portal</p>
                                    </div>

                                    <div class="mt-4">
                                        <form @submit.prevent="submit">
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
                                            <div v-if="state.accountActive == false"
                                                class="alert alert-danger alert-dismissible fade show mt-4 mb-4"
                                                role="alert">
                                                Your account is verified but yet to be activated by UNICEF. You will be
                                                notified once this process is complete.
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                    aria-label="Close"></button>
                                            </div>

                                            <div class="mb-3">
                                                <label for="email">Email</label>
                                                <input style="font-size: 13px" class="form-control" id="email"
                                                    type="email" autocomplete="username" v-model="form.email" required
                                                    autofocus placeholder="Enter email" />
                                                <InputError class="mt-2 text-danger mb-4"
                                                    :message="form.errors.email" />
                                            </div>

                                            <div class="mb-3">
                                                <div class="float-end">
                                                    <Link :href="route('password.request')" class="text-muted"> <i
                                                        class="mdi mdi-lock mr-1"></i> Forgot your password? </Link>
                                                </div>
                                                <label for="userpassword">Password</label>
                                                <input type="password" class="form-control" id="userpassword"
                                                    autocomplete="current-password" v-model="form.password" required
                                                    placeholder="Enter password" style="font-size: 13px" />
                                                <InputError class="mt-2 text-danger mb-4"
                                                    :message="form.errors.password" />
                                            </div>

                                            <b-form-checkbox id="customControlInline" name="remember" value="accepted"
                                                v-model:checked="form.remember" unchecked-value="not_accepted">
                                                Remember me
                                            </b-form-checkbox>
                                            <div class="mt-3 d-grid">
                                                <button class="btn btn-primary btn-block waves-effect waves-light"
                                                    type="submit" :disabled="form.processing">
                                                    <i class="bx bx-loader bx-spin font-size-16 align-middle me-2"
                                                        v-if="form.processing"></i>Log In
                                                </button>
                                            </div>
                                        </form>
                                        <div class="mt-5 text-center">
                                            <p>
                                                Don't have an account ?
                                                <Link :href="route('register')" class="fw-medium text-primary">Signup
                                                now </Link>
                                            </p>
                                        </div>
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
            <getMobileAppModal :options="{ page: 'login_page' }" v-model:downloadMobileEvent="downloadMobileEvent"
                :downloadMobileAppModal="downloadMobileAppModal" />
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
