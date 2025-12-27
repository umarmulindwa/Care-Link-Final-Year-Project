<template>
    <!-- file upload -->

    <DropZone @drop.prevent="drop" @change="selectedFile" :is-multiple="true" />

    <ul class="list-group mt-2">
        <li class="list-group-item d-flex justify-content-between align-items-center" v-for="(file,index) in uploaded.reverse()" :key="index">
            {{ file.name }}
            <span class="text-primary">
                <span style="margin-right: 20px">{{ convertBytesToKBMB(file.size) }}</span>
                <button class="btn btn-default" @click.prevent="removeFile(index)">
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
    name: "document-upload",
    components:{
        DropZone,
    },
    data(){
        return {
            uploaded: [],
        }
    },

    methods: {
        removeFile(index){
            this.uploaded.splice(index,1)
            this.$emit('files',this.uploaded);
        },

        drop (e){
            let files  = e.dataTransfer.files;

            for(let x = 0; x < files.length; x++){
                this.uploaded.push(files[x]);
            }

            this.$emit('files',this.uploaded);

        },
        selectedFile(){
            let files = document.querySelector(".dropzoneFile").files;

            for(let x = 0; x < files.length; x++){
                this.uploaded.push(files[x]);
            }

            this.$emit('files',this.uploaded);
        },

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
    },

};

</script>


