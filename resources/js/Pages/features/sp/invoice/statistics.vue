<template>
    <div>
        <Head title="Invoices" />
        <Layout>
            <PageHeader :title="'Invoices'" :items="items" />
            <div>
                <invoiceList />
            </div>
        </Layout>
    </div>
</template>

<script setup>
import PageHeader from "@/Components/page-header.vue";
import Layout from "@/Layouts/verticalvendor.vue";

import invoiceList from "./reports/invoice-list.vue";
const items = ["Dashboard", "Invoice list"];
</script>

<style lang="scss" scoped></style>
