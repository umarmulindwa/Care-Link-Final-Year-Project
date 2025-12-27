<script setup>
import { ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import ActionMessage from "@/Components/ActionMessage.vue";
import FormSection from "@/Components/FormSection.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import singleInput from "@/Pages/StaffRegister/NewStaff/SingleInput.vue";

const props = defineProps({
    user: Object,
});

const userProfile = props.user.staff_profile;

const form = useForm({
    _method: "PUT",
    name: userProfile.name,
    email: userProfile.email,
    personalId: userProfile.personal_id,
    indexNo:userProfile.index_number,
    gender:userProfile.gender,
    maritalStatus:userProfile.marital_status,
    phoneNumber:userProfile.phone,
    whatsappNo:userProfile.phone_whatsapp,
    address:userProfile.address,
    position:userProfile.position_text,
    positionID:userProfile.position_id,
    section:userProfile.section,
    pillar:userProfile.pillar,
    organisationUnit:userProfile.organisation_unit,
    grade:userProfile.grade,
    reportTo:userProfile.reports_to,
    category:userProfile.category,
    appointmentType:userProfile.appointment_type,
    dateJoinedGlobal:userProfile.date_began_working_with_unicef,
    dateJoinedCountry:userProfile.date_began_working_with_unicef_country_level,
    contractEndDate:userProfile.date_contract_end,
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);
const singleInputID = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route("user-profile-information.update"), {
        errorBag: "updateProfileInformation",
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (!photo) return;

    singleInputID.value.singleStaff.photo = photo;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route("current-user-photo.destroy"), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>

<template>
    <form>
        <!-- Profile Photo -->
        <div v-if="$page.props.jetstream.managesProfilePhotos" class="mb-4">
            <!-- Profile Photo File Input -->

            <label for="photo">Profile Picture</label>
            <input ref="photoInput" type="file" class="invisible" @change="updatePhotoPreview" />

            <!-- Current Profile Photo -->
            <div v-show="!photoPreview" class="mt-2 mb-4">
                <img :src="user.profile_photo_url" :alt="user.name" class="rounded-circle avatar-md object-fit-cover" />
            </div>

            <!-- New Profile Photo Preview -->
            <div v-show="photoPreview" class="mt-2 mb-4">
                <img class="rounded-circle avatar-md object-fit-cover" :alt="user.name" :src="photoPreview" />
            </div>

            <div class="d-flex flex-row col-md-6 gap-3">
                <div>
                    <b-button variant="primary" @click="selectNewPhoto">Change Profile Picture</b-button>
                </div>

                <div v-if="user.profile_photo_path" type="button" class="mt-2 text-danger" @click.prevent="deletePhoto"><i class="fas fa-trash-alt"></i></div>
            </div>

            <InputError :message="form.errors.photo" class="mt-2" />
        </div>

        <!-- staff profile details  -->

        <single-input :embed="userProfile" @updated="form.recentlySuccessful = true" ref="singleInputID">
            <template #email>
                <!-- Email Verification-->
                <div class="alert alert-primary alert-dismissible fade show" role="alert" v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                    <div class="mb-4">
                        <div class="mb-2">Your email address is unverified.</div>

                        <Link :href="route('verification.send')" method="post" @click.prevent="sendEmailVerification">
                            <b-button variant="primary" @click="sendEmailVerification">Click here to re-send the verification email.</b-button>
                        </Link>
                    </div>

                    <div v-show="verificationLinkSent" class="mt-2 mb-4 text-success">A new verification link has been sent to your email address.</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </template>
        </single-input>

        <div class="mt-2"></div>

    </form>
</template>
