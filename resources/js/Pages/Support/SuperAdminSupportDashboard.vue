<script setup>
import Layout from "../../Layouts/main.vue";
import { reactive, onMounted, computed } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import SupportChat from "./SupportChat.vue";
import SystemVideos from "./SystemVideos.vue";

//Default Layout
defineOptions({ layout: Layout });
//props
const props = defineProps({
    generalRequests: Array,
    accountProfileRequests: Array,
    systemVideos: Array,
});
//data
const state = reactive({
    title: "Support Requests",
    items: [
        {
            text: "Email",
            href: "/",
        },
        {
            text: "Inbox",
            active: true,
        },
    ],
    uncleanAccountReponses: {
        chats: props.accountProfileRequests,
    },
    uncleanGeneralResponses: {
        chats: props.generalRequests,
    },
});

//methods
//mounted
onMounted(() => {
    document.body.classList.remove("side-bg");
});

//computed
const getcleanedAccountReponses = computed(() => {
    const checkSet = new Set();
    const cleanArray = [];
    state.uncleanAccountReponses.chats.forEach((request) => {
        if (checkSet.has(request.email)) {
            //get the index of the duplicate and append the current request
            const duplicateIndex = cleanArray.findIndex((req) => req.email === request.email);
            if (!cleanArray[duplicateIndex].hasOwnProperty("requestThread")) {
                cleanArray[duplicateIndex].requestThread = [];
            }
            cleanArray[duplicateIndex].requestThread.push(request);
        } else {
            //add email to set to track duplicates
            checkSet.add(request.email);
            //push new chat to a clean array
            cleanArray.push(request);
        }
    });
    console.log("accounts", cleanArray);
    return cleanArray;
});

const getcleanedGeneralReponses = computed(() => {
    const checkSet = new Set();
    const cleanArray = [];
    state.uncleanGeneralResponses.chats.forEach((request) => {
        if (checkSet.has(request.email)) {
            //get the index of the duplicate and append the current request
            const duplicateIndex = cleanArray.findIndex((req) => req.email === request.email);
            if (!cleanArray[duplicateIndex].hasOwnProperty("requestThread")) {
                cleanArray[duplicateIndex].requestThread = [];
            }
            cleanArray[duplicateIndex].requestThread.push(request);
        } else {
            //add email to set to track duplicates
            checkSet.add(request.email);
            //push new chat to a clean array
            cleanArray.push(request);
        }
    });
    console.log("general", cleanArray);

    return cleanArray;
});

</script>

<template>
    <Head title="Support Center" />
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center mb-5 mt-4">
                    <h4>{{ state.title }}</h4>
                </div>
            </div>
        </div>
        <div class="w-100 d-flex flex-row justify-content-between">
            <div class="d-flex flex-column gap-3" style="width: 10%">
                <div class="card" role="button" :class="state.title == 'Support Requests' ? 'bg-primary text-white' : 'text-muted'" @click="state.title = 'Support Requests'">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                        <div class="mt-2">
                            <i class="bx bx-support nav-icon d-block mb-2" style="font-size: large"></i>
                        </div>
                        <div class="mb-2 text-center">
                            <p class="fw-bold mb-0">Support Requests</p>
                        </div>
                        <b-badge
                            class="position-absolute top-0 start-100 translate-middle rounded-pill"
                            :variant="`${getcleanedGeneralReponses.length + getcleanedAccountReponses.length > 0 ? 'danger' : 'success'}`"
                            >{{ getcleanedGeneralReponses.length + getcleanedAccountReponses.length }}</b-badge
                        >
                    </div>
                </div>
                <div class="card" role="button" @click="state.title = 'Support Request Statistics'" :class="state.title == 'Support Request Statistics' ? 'bg-primary text-white' : 'text-muted'">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                        <div>
                            <i class="fas fa-chart-bar nav-icon d-block mb-2" style="font-size: large"></i>
                        </div>
                        <div class="text-center">
                            <p class="fw-bold mb-0">Request Statistics</p>
                        </div>
                    </div>
                </div>
                <div class="card" role="button" @click="state.title = 'FAQS'" :class="state.title == 'FAQS' ? 'bg-primary text-white' : 'text-muted'">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                        <div>
                            <i class="fas fa-question-circle nav-icon d-block mb-2" style="font-size: large"></i>
                        </div>
                        <div class="text-center">
                            <p class="fw-bold mb-2">FAQS</p>
                        </div>
                    </div>
                </div>
                <div class="card" role="button" @click="state.title = 'Sytem Videos'" :class="state.title == 'Sytem Videos' ? 'bg-primary text-white' : 'text-muted'">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="mt-2">
                            <i class="bx bxs-videos nav-icon d-block mb-2" style="font-size: large"></i>
                        </div>
                        <div class="mb-2 text-center">
                            <p class="fw-bold mb-0">Sytem Videos</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" style="width: 87%">
                <div class="card-body" id="staff_reg" v-if="state.title == 'Support Requests'">
                    <SupportChat
                        :allRequests="[...getcleanedAccountReponses, ...getcleanedGeneralReponses]"
                        :generalRequests="[...getcleanedGeneralReponses]"
                        :accountRequests="[...getcleanedAccountReponses]"
                    />
                </div>
                <div class="card-body" id="requestStats" v-else-if="state.title == 'Support Request Statistics'">222</div>
                <div class="card-body" id="faq" v-else-if="state.title == 'FAQS'">33</div>
                <div class="card-body" id="radio_call" v-else>
                    <SystemVideos :videos="props.systemVideos" :currentUrl="usePage().url"/>
                </div>
            </div>
        </div>
    </div>
</template>
