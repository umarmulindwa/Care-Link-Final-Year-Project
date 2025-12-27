<template>
    <div>
        <b-modal v-model="modalItemValue" hide-footer hide-header no-close-on-esc no-close-on-backdrop scrollable
            centered size="lg" title="Upload Mobile App" @close="closeModal">
            <div class="row d-flex justify-content-evenly py-4">
                <div class="col-md-4">
                    <img src="/images/icons/phone.3.png" class="img-fluid" />
                </div>
                <div class="col-md-8">
                    <div>
                        <span class="font-size-24" style="font-weight: 300; color: #283593">Bringing
                            Functionality</span><br />
                        <span class="font-size-20" style="font-weight: 500; color: #283593">Closer to You</span>
                    </div>
                    <div class="my-4 font-size-16">
                        <p>
                            Select your Platform of choice, <br />
                            Download and install the App, <br />Login and Follow the instructions
                        </p>
                    </div>
                    <div class="d-flex flex-row">
                        <div class="m-1 link-pointer" @click.prevent="downloadFile" style="width: 150px">
                            <img src="/images/icons/store.google.png" height="150" class="img-fluid"
                                style="height: 50px" />
                        </div>
                        <!-- <div class="m-1 link-pointer" @click.prevent="downloadFile" style="width: 150px">
                            <img src="/images/icons/store.app.png" height="150" class="img-fluid w-100"
                                style="height: 50px" />
                        </div> -->
                    </div>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
import { RequestHelper } from "@/mixins/helpers";
import Swal from "sweetalert2";
import { ref, onMounted, computed, toRef, watch } from "vue";

export default {
    props: {
        user: {
            type: Object,
            default: {}
        },
        options: {
            type: Object,
            default: {}
        },
        downloadMobileAppModal: Boolean,
        downloadMobileEvent: {
            type: Function,
            default: () => { },
        },
    },

    emits: [""],
    setup(props, ctx) {
        const modalItemValue = ref(false);

        const closeModal = () => {
            ctx.emit("updated:downloadMobileAppModal", false);
            props.downloadMobileEvent();
        };

        const downloadFile = async () => {
            Swal.fire({
                text: "File downloading...",
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            if (props.options.page == 'login_page') {
                // window.open("https://play.google.com/store/apps/details?id=com.app", "_blank");
                await RequestHelper.getRequest("/api/get-latest-download-data", {},null).then(({ data }) => {
                    const { file_path } = data.results;
                    window.location.href = file_path;
                    Swal.close();
                });
            } else {
                // window.open("https://play.google.com/store/apps/details?id=com.app", "_blank");
                await RequestHelper.getRequest("/api/mobile-api/get-latest-download-data", {}, props.user?.api_token).then(({ data }) => {
                    const { file_path } = data.results;
                    window.location.href = file_path;
                    Swal.close();
                });
            }

        };

        onMounted(() => {
            modalItemValue.value = props.downloadMobileAppModal;
        });

        watch(props, (newValue, oldValue) => {
            modalItemValue.value = newValue.downloadMobileAppModal;
        });

        return {
            closeModal,
            modalItemValue,
            downloadFile,
        };
    },
};
</script>

<style lang="scss" scoped></style>
