<template>
    <Head title="System Dashboards"></Head>
    <Layout :pages="['dashboards']">
        <div style="margin-top:-15px;"></div>
        <div class="row">
            <div class="col-md-12">
                <h5><b>DASHBOARDS</b></h5>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <Carousel
                    :autoplay="dashboard_change_intervals"
                    :wrap-around="true">
                    <Slide v-for="(item,index) in items" :key="index">
                        <div class="card w-100 shadow-md mt-2">
                            <div class="card-body">
                                <div class="carousel__item">
                                    <component :is="item.component" :data="item.data"></component>
                                </div>
                            </div>
                        </div>
                    </Slide>
                    <template #addons>
<!--                        <Navigation />-->
                        <Pagination />
                    </template>
                </Carousel>
            </div>
        </div>
    </Layout>
</template>

<script>

import Layout from "@/Layouts/main.vue";
import { Head} from "@inertiajs/vue3";
import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel'
import driverTripsToday from "@/Pages/Dashboards/travelPlanner/drivers/today.vue";
import driverTripsTomorrow from "@/Pages/Dashboards/travelPlanner/drivers/tomorrow.vue";
import driverTripsPending from "@/Pages/Dashboards/travelPlanner/drivers/all_pending_trips.vue";

export default {
    name:"system-dashboards",
    props:['dashboard_change_intervals'],
    components:{
        Layout,
        Head,
        Carousel,
        Slide,
        Pagination,
        Navigation,
    },
    data() {
        return {
            items:[
                {
                    component: driverTripsToday,
                    data: '',
                },
                {
                    component: driverTripsTomorrow,
                    data: '',
                },
                {
                    component: driverTripsPending,
                    data: '',
                },
            ],
        }
    },

    created(){
        document.body.classList.add("second-nav-disabled");
    },
}
</script>

