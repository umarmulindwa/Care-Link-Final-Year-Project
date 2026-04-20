<script setup>
import { onMounted, reactive } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import InstructionsModal from "./InstructionsModal.vue";
import HorizontalSupportLayout from "../../Layouts/HorizontalSupportLayout.vue";
import { VueEditor } from "vue3-editor";
import PdfIcon from "../../Components/icons/PdfIcon.vue";
import ExcelIcon from "../../Components/icons/ExcelIcon.vue";
import WordIcon from "../../Components/icons/WordIcon.vue";
import Swal from "sweetalert2";

const state = reactive({
    faqs: [
        {
            title: "What is HIV and how is it different from AIDS?",
            subTitle: "HIV (Human Immunodeficiency Virus) is a virus that attacks the body’s immune system, specifically the CD4 cells, which help fight infections. When HIV is not treated over a long period, it weakens the immune system to a very serious level. This advanced stage is called AIDS (Acquired Immunodeficiency Syndrome). Not everyone with HIV develops AIDS, especially if they start treatment early and take their medication consistently. With modern antiretroviral therapy (ART), people living with HIV can maintain strong immune systems and never progress to AIDS. Understanding this difference is important because HIV is manageable, and early treatment can prevent serious complications.",
            active: false,
        },
        {
            title: "How is HIV transmitted?",
            subTitle: "HIV is transmitted through specific body fluids that contain the virus in sufficient amounts. These include blood, semen, vaginal fluids, rectal fluids, and breast milk. The most common ways HIV spreads are through unprotected sexual contact with an infected person, sharing needles or syringes, and from mother to child during pregnancy, childbirth, or breastfeeding. It is important to understand that HIV cannot be transmitted through casual contact such as hugging, shaking hands, sharing food, using the same toilet, or mosquito bites. Knowing how HIV spreads helps individuals take the right preventive measures, such as using protection during sex and avoiding sharing sharp objects.",
            active: false,
        },
        {
            title: "How can I know if I have HIV?",
            subTitle: "The only reliable way to know if you have HIV is by taking an HIV test. Many people with HIV do not show symptoms in the early stages, which is why testing is very important even if you feel healthy. Some individuals may experience flu-like symptoms within a few weeks after infection, but these symptoms are not enough to confirm HIV. Testing can be done at hospitals, clinics, or through self-testing kits where available. Early diagnosis allows you to start treatment immediately, which helps protect your immune system and reduces the risk of transmitting the virus to others. Regular testing is especially important if you are at higher risk.",
            active: false,
        },
        {
            title: "Can HIV be treated or cured?",
            subTitle: "Currently, there is no cure for HIV, but it can be effectively controlled with treatment known as antiretroviral therapy (ART). ART involves taking medication daily as prescribed by a healthcare provider. These medicines reduce the amount of virus in the body, known as the viral load, to very low levels. When taken consistently, ART allows people living with HIV to live long and healthy lives, similar to those without HIV. It also prevents the virus from damaging the immune system further. Skipping medication can lead to drug resistance, making treatment less effective. Therefore, adherence to treatment is very important for long-term health and well-being.",
            active: false,
        },
        {
            title: "Can I transmit HIV if I am on treatment?",
            subTitle: "If a person living with HIV is on consistent treatment and achieves an undetectable viral load, they cannot transmit HIV through sexual contact. This concept is known as U=U, meaning “Undetectable equals Untransmittable.” An undetectable viral load means the level of HIV in the blood is so low that standard tests cannot detect it. However, reaching and maintaining this state requires strict adherence to medication and regular medical check-ups. It is still important to follow medical advice and attend clinic visits to ensure the viral load remains controlled. This breakthrough has significantly reduced stigma and allowed people living with HIV to have safer relationships.",
            active: false,
        },
        {
            title: "Can people living with HIV have normal relationships and children?",
            subTitle: "Yes, people living with HIV can have normal, healthy relationships and even have children who are HIV-negative. With proper treatment and medical guidance, the risk of transmitting HIV to a partner or baby can be reduced to very low levels. For couples where one partner is HIV-positive and the other is negative, safe practices and medical support ensure a healthy relationship. Pregnant women living with HIV can take medication to prevent transmission to their babies during pregnancy and delivery. Modern healthcare has made it possible for people living with HIV to plan families and live fulfilling emotional and social lives without significant limitations.",
            active: false,
        },
        {
            title: "What are the early symptoms of HIV?",
            subTitle: "Some people may experience early symptoms of HIV within 2 to 4 weeks after infection. These symptoms often resemble the flu and may include fever, sore throat, swollen lymph nodes, fatigue, rash, and muscle aches. However, not everyone experiences these symptoms, and they usually go away on their own. After this early stage, HIV may not cause noticeable symptoms for many years while still damaging the immune system. Because symptoms are not reliable indicators, testing is the only accurate way to confirm HIV infection. Recognizing early signs can encourage timely testing, but relying solely on symptoms can be misleading.",
            active: false,
        },
        {
            title: "What happens if I miss my HIV medication?",
            subTitle: "Missing HIV medication doses can have serious consequences. When medication is not taken consistently, the virus can begin to multiply again in the body, increasing the viral load. This can weaken the immune system and increase the risk of illness. More importantly, inconsistent use of medication can lead to drug resistance, where the virus no longer responds to the treatment being used. This makes it harder to control HIV and may require switching to more complex or expensive medications. If you miss a dose, it is important to follow your healthcare provider’s advice and try to maintain a strict routine to avoid future missed doses.",
            active: false,
        },
        {
            title: "How can I prevent HIV infection or transmission?",
            subTitle: "HIV prevention involves a combination of safe practices and medical strategies. Using condoms correctly and consistently during sexual activity significantly reduces the risk of infection. Avoiding the sharing of needles or sharp instruments is also very important. For people at higher risk, preventive medications such as PrEP (pre-exposure prophylaxis) can be taken to reduce the chances of getting HIV. In emergency situations, PEP (post-exposure prophylaxis) can be used within 72 hours after potential exposure. Regular testing and knowing your partner’s status also play a key role in prevention. Education and awareness are essential tools in reducing the spread of HIV.",
            active: false,
        },
        {
            title: "What is viral load and why is it important?",
            subTitle: "Viral load refers to the amount of HIV present in a person’s blood. It is an important measure used by healthcare providers to monitor how well HIV treatment is working. When a person takes their medication consistently, the viral load can drop to an undetectable level. This means the virus is effectively controlled and cannot be transmitted through sex. A high viral load, on the other hand, indicates that the virus is active and may be damaging the immune system. Regular viral load testing helps doctors adjust treatment if necessary and ensures that patients remain healthy while reducing the risk of transmission.",
            active: false,
        },
    ],
    showDetailModal: false,
    currentTitle: "",
    currentSubTile: "",
    currentIndex: 0,
    supportRequestModal: false,
    supportDescription: "",
    issueType: "",
    supportIssue: "",
    userName: "",
    userEmail: "",
    selectedSupportReportFile: [],
    selectedFileNames: [],
    showSubmissionLoader: false,
});

//mounted
onMounted(() => {
    //removing default blue bacground
    document.body.classList.remove("side-bg");
});

function showModal(index, faq) {
    state.currentTitle = faq.title;
    state.currentSubTile = faq.subTitle;
    state.currentIndex = index;
    state.showDetailModal = true;
}
function getSupportFiles(event) {
    state.selectedSupportReportFile.unshift(event.target.files[0]);
    state.selectedFileNames.unshift(event.target.files[0].name);
}

function deleteSelectedFile(index) {
    state.selectedSupportReportFile.splice(index, 1);
    state.selectedFileNames.splice(index, 1);
}
function getExtension(name) {
    // gets filename extension
    const regex = new RegExp("[^.]+$");
    const extension = name.match(regex);
    return extension;
}

function submitSupportRequest() {
    state.showSubmissionLoader = true;
    let formData = new FormData();

    state.selectedSupportReportFile.forEach((file, index) => {
        formData.append(`files[${index}]`, file);
    });
    formData.append("detailedDescription", state.supportDescription);
    formData.append("issueType", state.issueType);
    formData.append("issue", state.supportIssue);
    formData.append("name", usePage().props.auth.user ? usePage().props.auth.user.name : state.userName);
    formData.append("email", usePage().props.auth.user ? usePage().props.auth.user.email : state.userEmail);

    const config = {
        headers: {
            "content-type": "multipart/form-data",
            //Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
        },
    };

    axios
        .post("/api/support/submitRequest", formData, config)
        .then((res) => {
            console.log(res);
            if (res.status == 200) {
                state.supportRequestModal = false;
                state.showSubmissionLoader = false;
                Swal.fire({
                    html: "<p>Support Request Submitted</p>",
                    icon: "success",
                    showCloseButton: false,
                    showCancelButton: false,
                    focusConfirm: true,
                    confirmButtonText: "OK",
                    confirmButtonColor: "#43ad60",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    closeOnClickOutside: false,
                }).then((result) => {
                    state.supportDescription = "";
                    state.issueType = "";
                    state.supportIssue = "";
                    state.userName = "";
                    state.userEmail = "";
                    state.selectedSupportReportFile = [];
                    state.selectedFileNames = [];
                    state.showSubmissionLoader = false;
                });
            }
        })
        .catch((error) => {
            app.isProcessing = false;
            console.log({ error });
            Swal.fire({
                title: "Something Went Wrong",
                icon: "error",
                html: `<p style="font-size: 14px">There is something that went wrong and it is not your fault. Please, reach out to ICT for help.</p>`,
                showCloseButton: false,
                showCancelButton: false,
                focusConfirm: true,
                confirmButtonText: "OK",
                confirmButtonColor: "#43ad60",
                allowOutsideClick: false,
                allowEscapeKey: false,
                closeOnClickOutside: false,
            }).then((result) => {
                if (result.value) {
                    state.supportDescription = "";
                    state.issueType = "";
                    state.supportIssue = "";
                    state.userName = "";
                    state.userEmail = "";
                    state.selectedSupportReportFile = [];
                    state.selectedFileNames = [];
                    state.showSubmissionLoader = false;

                    Swal.close();
                }
            });
        });
}
</script>
<template>
    <Head title="Support" />
    <div>
        <HorizontalSupportLayout>
            <h2 class="mb-3 text-center">Frequently Asked Questions?</h2>
            <div class="text-center">Begin by going over the Frequently Asked Questions below, or send us a Support Request</div>
            <div class="mt-5 ps-2 pe-2 position-relative">
                <div role="button" class="card mb-4" v-for="(faq, index) in state.faqs" :key="index">
                    <div class="card-body" role="button" @mouseenter="faq.active = true" @mouseleave="faq.active = false" @click="showModal(index, faq)">
                        <div :class="faq.active ? 'text-primary' : ''">
                            <h5>{{ faq.title }}</h5>
                        </div>
                        <div>{{ faq.subTitle.length > 100 ? faq.subTitle.slice(0, 100) + '...' : faq.subTitle }}</div>
                    </div>
                </div>
            </div>
            <InstructionsModal
                :subTitle="state.currentSubTile"
                :showModal="state.showDetailModal"
                :title="state.currentTitle"
                :faqIndex="state.currentIndex"
                @closeClicked="state.showDetailModal = false"
            />
        </HorizontalSupportLayout>

        <div class="d-flex justify-content-end mb-5" style="width: 96%; position: fixed; bottom: 0px; margin-left: auto">
            <div role="button" class="avatar-lg rounded-circle bg-success text-white d-flex flex-column justify-content-center align-items-center" style="" @click="state.supportRequestModal = true">
                <div>
                    <i class="bx bx-support" style="font-size: large"></i>
                </div>
                <div class="">Write to Us</div>
            </div>
        </div>
        <b-modal v-model="state.supportRequestModal" size="lg" title="WHAT DIFFICULTY ARE YOU FACING?" title-class="font-18" centered hide-footer hide-title>
            <div class="mb-4 mt-3">
                <label>What is this Issue About</label>
                <select class="form-control" id="request_type" v-model="state.issueType">
                    <option value="" disabled>Select</option>
                    <option value="Account and Profile">Account and Profile</option>
                    <option value="General Support">General Support</option>
                </select>
            </div>
            <div v-if="!usePage().props.auth.user">
                <div class="mb-4">
                    <label>Your Name:</label>
                    <input class="form-control" type="text" v-model="state.userName" />
                </div>

                <div class="mb-4">
                    <label>Email:</label>
                    <input class="form-control" type="email" v-model="state.userEmail" />
                </div>
            </div>
            <div class="mb-4">
                <label>Issue</label>
                <textarea class="form-control" :maxlength="100" rows="1" placeholder="" v-model="state.supportIssue"></textarea>
            </div>
            <div class="mb-4">
                <label for="request_description">Detailed Description</label>
                <VueEditor v-model="state.supportDescription" id="request_description"></VueEditor>
            </div>
            <div class="mb-5">
                <label>Attach any relevant documents</label>

                <div @dragenter.prevent="toggleActive" @dragleave.prevent="toggleActive" @dragover.prevent @drop.prevent="toggleActive" :class="{ 'active-dropzone': state.active }" class="dropzone">
                    <div class="mx-auto">
                        <i class="bx bxs-cloud-upload" style="font-size: 5em; color: #b5b5b5"></i>
                    </div>
                    <div class="fw-bold text-muted text-center">DRAG & DROP</div>
                    <label for="dropzoneFile">Select Files</label>
                    <input type="file" id="dropzoneFile" class="dropzoneFile btn btn-primary" multiple @change="getSupportFiles" />
                </div>
            </div>
            <div v-if="state.selectedFileNames.length > 0" class="mb-5">
                <div role="button" class="" v-for="(doc, index) in state.selectedFileNames" v-bind:key="'file__' + doc + index">
                    <div class="d-flex gap-2 mb-3 align-items-center justify-content-between col-6">
                        <div class="d-flex gap-3 align-items-center">
                            <div>
                                <div v-if="getExtension(doc) == 'xls' || getExtension(doc) == 'xlsx'">
                                    <ExcelIcon :height="'33px'" :width="'33px'" />
                                </div>
                                <div v-else-if="getExtension(doc) == 'doc' || getExtension(doc) == 'docx'">
                                    <WordIcon :height="'33px'" :width="'33px'" />
                                </div>
                                <div v-else-if="getExtension(doc) == 'pdf'">
                                    <PdfIcon :height="'33px'" :width="'33px'" />
                                </div>
                                <div
                                    v-else-if="
                                        getExtension(doc) == 'png' ||
                                        getExtension(doc) == 'jpg' ||
                                        getExtension(doc) == 'jpeg' ||
                                        getExtension(doc) == 'webp' ||
                                        getExtension(doc) == 'gif' ||
                                        getExtension(doc) == 'svg'
                                    "
                                >
                                    <div :style="{ 'border-color': '#6a1b9a', color: '#6a1b9a' }">
                                        <i class="fa fa-camera" style="font-size: x-large"></i>
                                    </div>
                                </div>
                                <div v-else-if="getExtension(doc) == 'wav'">
                                    <div :style="{ 'border-color': '#6a1b9a', color: '#6a1b9a' }">
                                        <i class="fa fa-microphone" style="font-size: x-large"></i>
                                    </div>
                                </div>
                                <div v-else>
                                    <div :style="{ 'border-color': '#6a1b9a', color: '#6a1b9a' }">
                                        <i class="bx bx-file" style="font-size: x-large"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <div>
                                    <div class="" style="font-size: 13px; color: #777777">
                                        <p class="text mb-0 mt-0">{{ doc }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <i class="bx bxs-x-circle text-danger fs-5 mt-2 ms-5" role="button" @click="deleteSelectedFile(index)"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mb-3">
                <b-button variant="primary" @click="submitSupportRequest" :disabled="state.showSubmissionLoader">
                    <i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.showSubmissionLoader"></i>Submit Request
                </b-button>
            </div>
        </b-modal>
    </div>
</template>
<style scoped lang="scss">
.dropzone {
    // width: 400px;
    height: 100px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    row-gap: 16px;
    border: 2px dashed #3b86fe6f;
    background-color: #fff;
    transition: 0.3s ease all;
    label {
        padding: 8px 12px;
        border-radius: 4px;
        color: #fff;
        background-color: #3b85fe;
        transition: 0.3s ease all;
    }
    input {
        display: none;
    }
}
.active-dropzone {
    color: #fff;
    border-color: #fff;
    background-color: #3b85fe;
    label {
        background-color: #fff;
        border-radius: 4px;
        color: #3b85fe;
    }
}
</style>
