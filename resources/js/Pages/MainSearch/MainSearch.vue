<script setup>
import Layout from "../../Layouts/main.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import PageHeader from "@/Components/page-header.vue";
import icondata from "../../../images/icondata.png";
import UserSearch from "./UserSearch.vue";

const props = defineProps(["searchWithIn", "searchText", "searchResults"]);

</script>
<template>
    <Head title="Search" />
    <Layout>
        <PageHeader :title="`Search :: ${props.searchWithIn} :: ${props.searchText} `" :items="items" />

        <div class="card" style="">
            <div class="card-body">
                <div v-if="props.searchWithIn === 'Users'">
                    <UserSearch :searchResults="props.searchResults" />
                </div>

                <div v-else class="text-center mt-5 mb-5">
                    <div class="mb-4"><img :src="icondata" :height="80" /></div>

                    <div>No Results found within {{ props.searchWithIn }}</div>
                </div>
            </div>
        </div>
    </Layout>
</template>
