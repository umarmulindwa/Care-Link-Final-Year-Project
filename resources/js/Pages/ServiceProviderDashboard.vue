<script setup>
import { Head, Link, router, usePage } from "@inertiajs/vue3";

import Layout from "../Layouts/verticalvendor.vue";
import PageHeader from "@/Components/page-header.vue";
import spIndex from "@/Pages/features/sp/index.vue";

import { useStore } from "vuex";

import { onMounted } from "vue";

//props
const props = defineProps({
    user: Object,
});

//store
const store = useStore();

const BSC_URL = import.meta.env.VITE_FINANCE_APP_URL;

//mounted
onMounted(() => {
    //ading user details to the store
    store.commit("LoggedInUser/storeLoggedInUserDetails", props.user);
    store.commit("LoggedInUser/storeCurrentUrl", usePage().url);
    document.body.classList.remove("side-bg");
});

//methods
const logout = () => {
    router.post(route("logout"));
};
</script>

<template>
    <Head title="Dashboard" />

    <!-- <h3>Dashboard</h3> -->

    <!-- <form @submit.prevent="logout">
        <b-button type="submit" variant="primary" class="btn-block" >Log Out</b-button>
    </form> -->
    <Layout>
        <!-- <PageHeader :title="'Dashboard'" :items="items" /> -->
        <div><spIndex :user="props.user" /></div>
    </Layout>
</template>
