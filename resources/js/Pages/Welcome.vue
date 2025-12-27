<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

import profileImg from "../../images/profile-img.png";
import logo from "../../images/logo.svg";
import InputError from '@/Components/InputError.vue';


//props
defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    canResetPassword: Boolean,
    status: String,
});

//data
const form = useForm({
    email: '',
    password: '',
    remember: false,
});
const coutryOffice = import.meta.env.VITE_COUNTRY_OFFICE;

//methods

//submiting login credentials
const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

</script>

<template>
    <Head title="Welcome" />

    <div>
        <div class="account-pages my-5 pt-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card overflow-hidden">
                            <div class="bg-soft bg-primary">
                                <div class="row">
                                    <div class="col-7">
                                        <div class="text-primary p-4">
                                            <h5 class="text-primary">Welcome Back !</h5>
                                            <p>Sign in to continue.</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img :src="profileImg" alt class="img-fluid" />
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div>
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img :src="logo" alt class="rounded-circle" height="34" />
                                        </span>
                                    </div>
                                </div>
                                <div class="p-2">

                                    <b-alert v-if="status" variant="danger" class="mt-3" dismissible>{{ status }}</b-alert>
                                    <b-form class="p-2" @submit.prevent="submit">
                                        <slot />
                                        <b-form-group id="input-group-1" label="Email" label-for="input-1" class="mb-3">
                                            <b-form-input id="input-1" name="email" v-model="form.email" type="email"
                                                placeholder="Enter email" required autofocus
                                                autocomplete="username"></b-form-input>
                                            <InputError class="mt-2" :message="form.errors.email" />
                                        </b-form-group>

                                        <b-form-group id="input-group-2" label="Password" label-for="input-2" class="mb-3">
                                            <b-form-input id="input-2" v-model="form.password" name="password"
                                                type="password" placeholder="Enter password" required
                                                autocomplete="current-password"></b-form-input>
                                            <InputError class="mt-2" :message="form.errors.password" />
                                        </b-form-group>

                                        <b-form-checkbox id="customControlInline" name="remember" value="accepted"
                                            v-model:checked="form.remember" unchecked-value="not_accepted">
                                            Remember me
                                        </b-form-checkbox>

                                        <div class="mt-3 d-grid">
                                            <b-button type="submit" variant="primary" class="btn-block"
                                                :disabled="form.processing">Log In</b-button>
                                        </div>
                                        <div class="mt-4 text-center">
                                            <h5 class="font-size-14 mb-3">Sign in with</h5>

                                            <ul class="list-inline">
                                                <li class="list-inline-item">
                                                    <a href="javascript: void(0);"
                                                        class="social-list-item bg-primary text-white border-primary">
                                                        <i class="mdi mdi-facebook"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <a href="javascript: void(0);"
                                                        class="social-list-item bg-info text-white border-info">
                                                        <i class="mdi mdi-twitter"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <a href="javascript: void(0);"
                                                        class="social-list-item bg-danger text-white border-danger">
                                                        <i class="mdi mdi-google"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="mt-4 text-center">
                                            <Link :href="route('password.request')" class="text-muted">
                                            <i class="mdi mdi-lock mr-1"></i> Forgot your password?
                                            </Link>
                                        </div>
                                    </b-form>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <p>
                                Don't have an account ?
                                <Link :href="route('register')" class="fw-medium text-primary">Signup now</Link>
                            </p>
                            <p>
                                © {{ new Date().getFullYear() }} UNICEF {{ coutryOffice }}. Crafted by Trail Analytics.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<!-- <style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}
@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}
</style> -->
