<script setup>
import { reactive } from "vue";
import FileUpload from "../../Components/FileUpload.vue";
import { usePage, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";


const state = reactive({
    addVideoModal: false,
    videoTitle: "",
    videoURL: "",
    videDescription: "",
    videoLocation: "",
    isURL: false,
    isVideo: true,
    videoFile: null,
    docName: "",
    showName: false,
    uploadLoader: false,
    videoLocations: [
        "Web: FACE Forms",
        "Web: BSC",
        "Mobile: 3PP",
        "Web: First Time Login",
        "Web: Implementing Partners",
        "Web: Service Providers",
        "Web: Administration",
        "Web: PPM",
        "Web: Supply",
        "Web: Phone Bill",
    ],
    isEditing: false,
    editVideoId: null,
});
const props = defineProps({
    videos: Array,
    currentUrl:String
});

function uploadFile(event) {
    state.videoFile = event.target.files[0];
    state.docName = event.target.files[0].name;
    state.showName = true;
}
function removeDoc() {
    state.docName = "";
    state.showName = false;
    state.videoFile = null;
}
function uploadSystemVideo() {
    state.uploadLoader = true;
    let formData = new FormData();

    formData.append("videoTitle", state.videoTitle);
    formData.append("videoURL", state.videoURL);
    formData.append("videDescription", state.videDescription);
    formData.append("videoLocation", state.videoLocation);
    formData.append("videoFile", state.videoFile);

    //if in editing mode
    if(state.isEditing){
    formData.append("id", state.editVideoId);
    }

    const config = {
        headers: {
            "content-type": "multipart/form-data",
            Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
        },
    };

    axios
        .post("/api/video/uploadSystemVideo", formData, config)
        .then((res) => {
            console.log(res);
            if (res.status == 200) {
                state.addVideoModal = false;
                state.uploadLoader = false;
                Swal.fire({
                    title: "Video Uploaded Successfully",
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
                    (state.videoTitle = ""), (state.videoURL = "");
                    state.videDescription = "";
                    state.videoLocation = "";
                    state.videoFile = null;
                    if(currentUrl == '/system_congifuration'){
                    router.visit('/system_congifuration',{},{preserveState:false, preserveScroll: false });

                    }else{
                    router.visit('/supportCenter',{},{preserveState:false, preserveScroll: false });

                    }
                });
            }
        })
        .catch((error) => {
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
                    (state.videoTitle = ""), (state.videoURL = "");
                    state.videDescription = "";
                    state.videoLocation = "";
                    state.videoFile = null;
                state.uploadLoader = false;


                    Swal.close();
                }
            });
        });
}

function editVideoDetail(index){
    const videoDetails = props.videos[index];
    state.isEditing=true
    state.editVideoId = videoDetails.id;
    state.videoTitle= videoDetails.video_title
    state.videoURL= videoDetails.embed_url
    state.videDescription= videoDetails.about_the_video
    state.videoLocation= videoDetails.location

    if(videoDetails.embed_url != null && videoDetails.embed_url!= ""){
        state.isURL=true
        state.isVideo= false
    }

    state.addVideoModal = true;

}
</script>
<template>
    <div class="text-end mb-5">
        <b-button variant="primary" @click="state.addVideoModal = true">
            <i class="bx bx-video-plus font-size-16 align-middle me-2"></i>
            Add New Video
        </b-button>
    </div>
    <div class="table-responsive">
        <input type="file" @change="onChange" hidden id="batchStaffUploadFile" accept=".xls ,.xlsx" />
        <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100" id="userList-table" v-if="props.videos.length > 0">
            <thead class="table-light">
                <tr>
                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Video Title</th>
                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="text-center">Location</th>
                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="text-center">Views</th>
                    <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="text-center">Uploaded By</th>
                    <th scope="col" :style="{ backgroundColor: '#eff2f7', textAlign: 'center' }">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(video, index) in props.videos" :key="`${index}_${video.video_title}`">
                    <td :style="{ backgroundColor: '#fff' }">
                        <div role="button">
                            <div class="col-11 text-truncate">{{ video.video_title }}</div>
                        </div>
                    </td>
                    <td :style="{ backgroundColor: '#fff', maxWidth: '20vw', wordWrap: 'break-word' }" class="col-2 text-center">
                        {{ video.location }}
                    </td>
                    <td :style="{ backgroundColor: '#fff' }" class="text-center">
                        {{ video.views.length }}
                    </td>
                    <td :style="{ backgroundColor: '#fff' }" class="text-center">
                        {{ video.uploaded_by }}
                    </td>
                    <td :style="{ backgroundColor: '#fff' }">
                        <div class="d-flex justify-content-end">
                            <div class="list-unstyled hstack gap-1 mb-0">
                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="View" @click="">
                                    <div class="btn btn-sm btn-soft-primary"><i class="mdi mdi-eye-outline"></i></div>
                                </div>
                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit" @click="editVideoDetail(index)">
                                    <div href="#" class="btn btn-sm btn-soft-info"><i class="mdi mdi-pencil-outline"></i></div>
                                </div>
                                <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Delete">
                                    <b-button variant="soft-danger" class="btn-sm" @click="listDeleteModal = !listDeleteModal"><i class="mdi mdi-delete-outline"></i></b-button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div v-else class="d-flex  align-items-center justify-content-center " style="height: 25em;">
            <div>Video List is Empty</div>
        </div>
        <b-modal class="" v-model="state.addVideoModal" size="lg" title="Add New Video" title-class="font-18" centered hide-footer hide-title>
            <div class="mb-4 mt-3 ">
                <label>Video Title</label>
                <input class="form-control" type="text" v-model="state.videoTitle" />
            </div>
            <div class="d-flex gap-5 border-bottom mb-4">
                <div
                    class="form-check form-check-info mb-3"
                    @click="
                        () => {
                            state.isURL = false;
                            state.isVideo = true;
                        }
                    "
                >
                    <input class="form-check-input" type="checkbox" id="videocheck" :checked="state.isVideo" />
                    <label class="form-check-label" for="videocheck"> Video </label>
                </div>
                <div
                    class="form-check form-check-info mb-3"
                    @click="
                        () => {
                            state.isVideo = false;
                            state.isURL = true;
                        }
                    "
                >
                    <input class="form-check-input" type="checkbox" id="urlcheck" :checked="state.isURL" />
                    <label class="form-check-label" for="urlcheck"> URL </label>
                </div>
            </div>
            <div class="mb-4" v-if="state.isVideo">
                <FileUpload
                    :idName="'videoFile'"
                    :fileType="'System Video'"
                    :showName="state.showName"
                    :name="state.docName"
                    :uploadFile="uploadFile"
                    @removeDoc="removeDoc()"
                    :accept="'.webm, .mpg, .mkv, .mpeg, .mpe, .mpv, .ogg, .mp4, .m4p, .m4v, .avi, .wmv, .mov'"
                />
            </div>
            <div class="mb-4" v-if="state.isURL">
                <label>Embed Video URL</label>
                <input class="form-control" type="text" v-model="state.videoURL" />
            </div>
            <div class="mb-4">
                <label>About the Video</label>
                <textarea class="form-control" :maxlength="100" rows="3" placeholder="" v-model="state.videDescription"></textarea>
            </div>
            <div class="mb-5 mt-3">
                <label>Video Location</label>
                <select class="form-control" id="request_type" v-model="state.videoLocation">
                    <option value="" disabled>Select</option>
                    <option :value="location" v-for="(location,index) in state.videoLocations" :key="index">{{ location }}</option>
                </select>
            </div>
            <div class="text-center mb-4">
                <b-button variant="primary" @click="uploadSystemVideo" :disabled="state.uploadLoader"
                    ><i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.uploadLoader"></i>Upload Video</b-button
                >
            </div>
        </b-modal>
    </div>
</template>
