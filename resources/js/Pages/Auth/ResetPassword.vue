<script setup>
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import logoDark from "../../../images/logo-blue.svg";
import logoLight from "../../../images/logo-blue.svg";
import { onMounted } from "vue";

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: "",
    password_confirmation: "",
});

const coutryOffice = import.meta.env.VITE_COUNTRY_OFFICE;

//lifecycle
onMounted(() => {
    document.body.classList.add("side-bg");
});

const submit = () => {
    form.post(route("password.update"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head title="Forgot Password" />

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

                <div class="col-xl-3">
                    <div class="auth-full-page-content p-md-5 p-4 bg-white">
                        <div class="w-100">
                            <div class="d-flex flex-column h-100">
                                <div class="mb-4 mb-md-5">
                                    <router-link to="/" class="d-block auth-logo">
                                        <img :src="logoDark" alt="" height="30" class="auth-logo-dark" />
                                        <img :src="logoLight" alt="" height="30" class="auth-logo-light" />
                                    </router-link>
                                </div>
                                <div class="my-auto">
                                    <div>
                                        <h5 class="text-primary">Reset Password</h5>
                                        <p>Enter your new credentials!</p>
                                    </div>

                                    <div class="mt-4">
                                        <b-alert v-model="isResetError" class="mb-4" variant="danger" dismissible>{{ error }}</b-alert>
                                        <b-alert v-model="tryingToReset" class="mb-4" variant="success" dismissible>{{ status }}</b-alert>

                                        <form @submit.prevent="submit">
                                            <slot />
                                            <div class="mb-3">
                                                <label for="email">Email</label>
                                                <input
                                                    style="font-size: 13px"
                                                    type="email"
                                                    name="email"
                                                    v-model="form.email"
                                                    class="form-control"
                                                    id="email"
                                                    placeholder="Enter email"
                                                    autocomplete="username"
                                                />
                                                <InputError class="mt-2 text-danger" :message="form.errors.email" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="password">Password</label>
                                                <input
                                                    style="font-size: 13px"
                                                    name="email"
                                                    v-model="form.password"
                                                    class="form-control"
                                                    id="password"
                                                    type="password"
                                                    required
                                                    autofocus
                                                    autocomplete="new-password"
                                                />
                                                <InputError class="mt-2 text-danger" :message="form.errors.password" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="password_confirmation">Confirm Password</label>
                                                <input
                                                    style="font-size: 13px"
                                                    name="email"
                                                    v-model="form.password_confirmation"
                                                    class="form-control"
                                                    id="password_confirmation"
                                                    type="password"
                                                    required
                                                    autocomplete="new-password"
                                                />
                                                <InputError class="mt-2 text-danger" :message="form.errors.password_confirmation" />
                                            </div>
                                            <div class="col-12 text-end">
                                                <button class="btn btn-primary w-md waves-effect waves-light" type="submit" :disabled="form.processing">
                                                    <i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="form.processing"></i>{{ form.processing ? "Please wait" : "Reset Password" }}
                                                </button>
                                            </div>
                                            <!-- <div class="mb-3">
                                                <label for="useremail">Email</label>
                                                <input type="email" class="form-control" id="useremail" placeholder="Enter email" v-model="form.email" />
                                                <InputError class="mt-2 text-danger mb-4" :message="form.errors.email" />
                                            </div>

                                            <div class="form-group row mb-0">
                                                <div v-if="form.recentlySuccessful" class="alert alert-success text-center mb-4" role="alert">Reset Email has been sent.</div>

                                                <div class="col-12 text-end">
                                                    <button class="btn btn-primary w-md waves-effect waves-light" type="submit" :disabled="form.processing">
                                                        <i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="form.processing"></i>Reset
                                                    </button>
                                                </div>
                                            </div> -->
                                        </form>
                                        <div class="mt-5 text-center">
                                            <p>
                                                Remember It ?
                                                <Link :href="route('login')" class="fw-medium text-primary"> Sign In here </Link>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 mt-md-5 text-center">
                                    <p>© {{ new Date().getFullYear() }} UNICEF {{ coutryOffice }}.</p>
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
