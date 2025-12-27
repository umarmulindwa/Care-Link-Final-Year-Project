<script>

import {Head, usePage} from "@inertiajs/vue3";
import HorizontalSupportLayout from "@/Layouts/HorizontalSupportLayout.vue";
import Layout from "@/Layouts/main.vue";
import AddReview from "@/Pages/TravelPlanner/AddReview.vue";
export default {
    name:"Reviews",
    props: ['code'],
    mixins:[],
    components:{Layout,HorizontalSupportLayout,Head,AddReview},
    data() {
        return {
            isLoggedIn:false,
        };
    },
    mounted() {
        this.isLoggedIn = !!usePage().props.auth.user;
    },
};
</script>

<template>
    <Head title="Travel Planner Reviews" />

    <div v-if="isLoggedIn">
        <Layout>
            <add-review :code="code" :logged-in="isLoggedIn"/>
        </Layout>
    </div>
    <div v-else>
        <HorizontalSupportLayout>
            <add-review :code="code" :logged-in="isLoggedIn"/>
        </HorizontalSupportLayout>
    </div>
</template>

