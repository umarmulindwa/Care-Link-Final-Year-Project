<template>
    <!-- file upload -->

    <DropZone @drop.prevent="drop" @change="selectedFile" />

    <ul class="list-group mt-2">
        <li class="list-group-item d-flex justify-content-between align-items-center" v-if="uploaded" :style="`background-image: ${uploaded}`">
            {{ uploaded.name }}
            <span class="text-primary">
                <span style="margin-right: 20px">{{ convertBytesToKBMB(uploaded.size) }}</span>
                <button class="btn btn-default" @click.prevent="uploaded = null">
                    <i class="bx bx-trash text-danger"></i>
                </button>
            </span>
        </li>
    </ul>
</template>

<script>

import {ref} from "vue";
import DropZone from "@/Components/widgets/dropZone.vue";

export default {
    name: "single-upload",
    components:{
        DropZone,
    },
    data(){
        return {
            uploaded: null,
        }
    },

    methods: {

        convertBytesToKBMB(bytes) {
            if (bytes < 1000) {
                return bytes + ' B';
            } else if (bytes < 1000000) {
                const kilobytes = (bytes / 1000).toFixed(2);
                return kilobytes + ' KB';
            } else {
                const megabytes = (bytes / 1000000).toFixed(2);
                return megabytes + ' MB';
            }
        },


        drop (e){
            let files  = e.dataTransfer.files;

            for(let x = 0; x < files.length; x++){
                this.uploaded = files[x];
            }

            this.$emit('files',this.uploaded);

        },
        selectedFile(){
            let files = document.querySelector(".dropzoneFile").files;

            for(let x = 0; x < files.length; x++){
                this.uploaded = files[x];
            }

            this.$emit('files',this.uploaded);
        },
    },

};

</script>


