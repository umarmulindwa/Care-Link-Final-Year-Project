<script setup>
const props = defineProps({
    faqIndex: Number,
    title: String,
    subTitle: String,
    showModal: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["closeClicked"]);

function modalClosed() {
    emit("closeClicked");
}
</script>

<template>
    <div>
        <b-modal @close="modalClosed" v-model="props.showModal" size="lg" :title="props.title" title-class="font-18" centered hide-footer hide-title>
            <div>
                <div class="fw-bold">Issue</div>
                <p>{{ props.subTitle }}</p>
            </div>
            <!-- Modal body -->
            <div v-if="props.faqIndex == 0">
                <div>this is the comment from the first comment</div>
            </div>
        </b-modal>
    </div>
</template>
