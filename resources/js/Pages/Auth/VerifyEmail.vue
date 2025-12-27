<script setup>
import logoDark from "../../../images/logo-blue.svg";
import logoLight from "../../../images/logo-blue.svg";
import { Head, Link, useForm, usePage,router } from "@inertiajs/vue3";
import { onMounted, reactive, computed } from "vue";

const props = defineProps({
    status: String,
});

const form = useForm({});

//data
const state = reactive({
    slide: 0,
    sliding: null,
    logoDark,
    logoLight,

});

const coutryOffice = import.meta.env.VITE_COUNTRY_OFFICE;

const verificationLinkSent = computed(() => props.status === "verification-link-sent");
//lifecycle
onMounted(() => {
    document.body.classList.add("side-bg");
    router.reload();
});

const submit = () => {
    form.post(route("verification.send"));
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
//     props:{
//       status: String,
//     },
//     components: { Head, Link },
//     computed(){
//       function verificationLinkSent(){

//       }
//     },
//     methods: {
//         submit() {
//             form.post(route("verification.send"));
//         },
//         onSlideStart(slide) {
//             this.sliding = true;
//         },
//         onSlideEnd(slide) {
//             this.sliding = false;
//         },
//     },
//     mounted() {
//         document.body.classList.add("side-bg");
//     },
// };
</script>

<template>
    <Head title="Email Verification" />

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
                                    <Link :href="route('login')" class="d-block auth-logo">
                                        <img :src="logoDark" alt="" height="30" class="auth-logo-dark" />
                                        <img :src="logoLight" alt="" height="30" class="auth-logo-light" />
                                    </Link>
                                </div>
                                <div class="my-auto">
                                    <div class="text-center">
                                        <div class="avatar-md mx-auto">
                                            <div class="avatar-title rounded-circle bg-light">
                                                <i class="bx bxs-envelope h1 mb-0 text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="p-2 mt-4">
                                            <h4>Verify your email </h4>

                                            <p v-if="verificationLinkSent" variant="success" class="mt-3" dismissible>
                                                A new verification link has been sent to the email address you provided in your profile settings.
                                            </p>
                                            <p v-else>
                                                We have sent you a verification email
                                                <span class="fw-semibold">{{ usePage().props.auth.user.email }}</span
                                                >, Please check it
                                            </p>
                                            <!-- <div class="mt-4">
                                                <router-link to="/" class="btn btn-success w-md">Verify email</router-link>
                                            </div> -->
                                            <p>
                                                Have issues with your email ?
                                                <Link :href="route('profile.show')" class="fw-medium text-primary"> Edit Profile </Link>
                                            </p>
                                            <form @submit.prevent="submit">
                                                <div class="mt-4">
                                                    <button type="submit" class="btn btn-success w-md" :disabled="form.processing">Resend Verification Email</button>
                                                </div>
                                            </form>
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
