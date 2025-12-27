<template>
    <footer class="footer2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">{{ new Date().getFullYear() }} © UNICEF {{ coutryOffice }}.</div>
                <div class="col-sm-6">
                    <div class="text-sm-end d-none d-sm-block">Designed & Developed by Trail Analytics</div>
                </div>
            </div>
        </div>
    </footer>
    <!-- end footer -->
</template>
<script setup>
const coutryOffice = import.meta.env.VITE_COUNTRY_OFFICE;
</script>
