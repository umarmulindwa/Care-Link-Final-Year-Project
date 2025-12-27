<script>

import {Head, useForm, usePage} from "@inertiajs/vue3";
import HorizontalSupportLayout from "@/Layouts/HorizontalSupportLayout.vue";
import {VueEditor} from "vue3-editor";
import axios from "axios";
import moment from "moment";

export default {
    name:"Reviews",
    props: ['code','loggedIn'],
    mixins:[],
    components:{VueEditor,HorizontalSupportLayout,Head},
    data() {
        return {
            url: import.meta.env.VITE_ADMIN_TRAVEL_PLANNER_URL,

            token: usePage().props.auth.user?.api_token,

            errorMessage: "",
            error: false,

            loading: true,
            registering: false,

            open_otp_modal: false,

            emailForm:{
                email_address:""
            },

            email_sent: false,
            token_sent:false,

            otpForm:useForm({
                email:'',
                token:'',
            }),

            reviews:[],

            trip:null,

            reviewComment:"",
            reviewStars:5,

            defaultToolbar: [
                [
                    {
                        header: [false, 1, 2, 3, 4, 5, 6],
                    },
                ],
                ["bold", "italic", "underline", "strike"],
            ],
        };
    },
    mounted() {
        this.getTripDetails();
    },
    methods: {
        disburseEmail(){
            this.registering = true;

            let formData = new FormData();
            formData.append('email',this.trip?.user.email);

            axios.post('/send-otp',formData)
                .then((response) => {
                    this.token_sent = true;
                    this.otpForm.email = this.trip?.user.email;
                    this.registering = false;
                })
                .catch((error) => {
                    this.registering = false;
                    console.log(error)
                });
        },

        confirmIdentity(){
            this.registering = true;

            axios.post("/confirm-identity-with-otp", this.otpForm)
                .then((response) => {
                   this.token = response.data;
                   this.open_otp_modal = false;
                    this.registering = false;
                }).catch((error) => {
                    this.registering = false;
                    if (error.response && error.response.data && error.response.data.errors) {
                        const errors = error.response.data.errors;
                        this.errorMessage = Object.values(errors).flat().join(', ');
                    } else {
                        this.errorMessage = error.message || 'An unknown error occurred.';
                    }
                });
        },

        formatDate(date,format) {
            return moment(date).format(format || 'DD MMMM,YYYY');
        },

        getTripDetails(dont_check = false){
            this.loading = true;
            axios.get(`${this.url}/api/get-trip-details/${this.code}`, {
                // headers: {
                //     "Accept": "application/json",
                //     "Authorization": `Bearer ${usePage().props.auth.user.api_token}`
                // }
            }).then((response) => {
                this.trip = response.data.trip;
                if(this.trip){
                    this.reviews = this.trip.reviews;

                    if(!dont_check){
                        if(this.trip.user.email !== usePage().props.auth.user?.email || !this.loggedIn){
                            this.open_otp_modal = true;
                        }
                    }

                }

                this.loading = false;

            }).catch((e) => {
                console.log('error',e);
            })

        },

        submitReview(){
            if(this.reviewComment.length < 3){
                return;
            }
            let formData = new FormData();
            formData.append('field_trip_id',this.trip.id);
            formData.append('review',this.reviewComment);
            formData.append('review_by',this.trip.user.email);
            formData.append('stars',this.reviewStars);


            this.loading = true;

            axios.post(`${this.url}/api/save-review`,formData, {
                headers: {
                    "Accept": "application/json",
                    "Authorization": `Bearer ${this.token}`
                }
            }).then((response) => {
                this.loading = false;
                this.getTripDetails(true);
                this.reviewComment = '';
            }).catch((e) => {
                console.log('error',e);
            })
        },

    },

}
</script>

<template>
    <div>
        <h2 class="mb-3 text-center">Travel Planner Reviews</h2>
        <div class="text-center">Reference No. <b>{{ code }}</b></div>
        <div class="mt-5 ps-2 pe-2 position-relative">
            <template v-if="loading && !trip">
                <div class="row mb-5">
                    <div class="col-12 text-center">
                        <span class="spinner spinner-border"></span>
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="card">
                    <div class="card-body">
                        <div class="row" v-if="trip">
                            <div class="col-7">

                                <div class="row">
                                    <div class="col-12">
                                        <p class="mb-0">Travel Type</p>
                                        <p class="text-muted">{{ trip.trip_type }}</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <p class="mb-0">Travel Scope</p>
                                        <p class="text-muted">{{ trip.travel_scope }}</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <p class="mb-0">Pickup <span class="text-primary fas fa-map-marker-alt"></span></p>
                                        <span><a href="#" @click.prevent="">{{ trip.pickup_address}}</a></span><br>
                                        <small>On {{ formatDate(trip.event_start_date) }} at {{ trip.event_start_time}}
                                            <span v-if="trip.trip_type !== 'In-city'">({{trip.departure_to_arrival}} before event start time, {{trip.distance_kms}} away)</span>
                                        </small>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <p class="mb-0">{{ trip.trip_type === 'In-city'? 'Drop-off':'Destination'}} <span class=" text-primary fas fa-map-marker-alt"></span></p>
                                        <span><a href="#" @click.prevent="">{{trip.destination_and_distance}} </a></span><br>
                                        <small v-if="trip.trip_type !== 'In-city'">For {{ trip.event_period}} (from {{ formatDate(trip.event_start_date) }} {{trip.event_start_time}}
                                            to {{ formatDate(trip.event_end_date) }} {{trip.event_end_time}} ) </small>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="row" v-else>
                            <div class="col-md-12 text-center">
                                <p>It seems this trip does not exist!</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="button" class="card mb-4">
                    <div class="card-body" role="button">
                        <div class="star-rating d-flex justify-content-center">
                            <span class="star mx-1" :class="reviewStars > 0?'selected':''" @click="reviewStars = 1">&#9733;</span>
                            <span class="star mx-1" :class="reviewStars > 1?'selected':''" @click="reviewStars = 2">&#9733;</span>
                            <span class="star mx-1" :class="reviewStars > 2?'selected':''" @click="reviewStars = 3">&#9733;</span>
                            <span class="star mx-1" :class="reviewStars > 3?'selected':''" @click="reviewStars = 4">&#9733;</span>
                            <span class="star mx-1" :class="reviewStars > 4?'selected':''" @click="reviewStars = 5">&#9733;</span>
                        </div>

                        <VueEditor v-model="reviewComment" :editorToolbar="defaultToolbar" placeholder="Describe your experience..." ></VueEditor>

                        <div class="mt-4">
                            <button class="btn btn-primary" :disabled="loading || !trip || reviewComment.length < 3" @click.prevent="submitReview()">
                                SUBMIT REVIEW
                            </button>
                        </div>
                    </div>
                </div>
                <div role="button" class="card mb-4" v-for="(review, index) in reviews" :key="index">
                    <div class="card-body" role="button">
                        <div class="">
                            <h5><b>{{ review.sentiment }}</b></h5>
                            <div class="star-rating-sm d-flex" style="margin-top:-5px;">
                                <span class="star mx-1" :class="review.stars > 0?'selected':''">&#9733;</span>
                                <span class="star mx-1" :class="review.stars > 1?'selected':''">&#9733;</span>
                                <span class="star mx-1" :class="review.stars > 2?'selected':''">&#9733;</span>
                                <span class="star mx-1" :class="review.stars > 3?'selected':''">&#9733;</span>
                                <span class="star mx-1" :class="review.stars > 4?'selected':''">&#9733;</span>
                            </div>
                        </div>
                        <div v-html="review.review"></div>
                        <div class="mt-2 text-success">
                            <span class="bx bx-time">{{ formatDate(review.created_at,'dddd,DD MMMM,YYYY HH:mm') }}</span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        <b-modal v-model="open_otp_modal" no-close-on-backdrop centered hide-header hide-footer>
            <template v-if="token_sent">
                <form action="" @submit.prevent="confirmIdentity">
                    <div class="row">
                        <div class="col-12 text-center">
                            <span class="bx bx-mail-send font-size-24"></span>
                            <p>An email with a one-time-password (OTP) has been sent to <b>{{ trip?.user.email }}</b><br>Please check your inbox and enter the token below: </p>

                            <input type="number" class="otp-input" v-model="otpForm.token" required>

                            <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show mt-4 mb-4" role="alert">
                                {{ errorMessage }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6 offset-md-3">
                            <button class="btn btn-primary w-100" type="submit" :disabled="registering">
                                <span class="spinner spinner-border spinner-border-sm" v-if="registering"></span>
                                VERIFY
                            </button>
                        </div>
                    </div>
                </form>
            </template>
            <template v-else>
                <div class="row">
                    <div class="col-12 text-center">
                        <img src="/images/icon.lock.png" style="width:80px;" alt="">
                        <template v-if="loggedIn">
                            <p>You are not logged in as {{ trip?.user.name }}, the account associated with this trip.</p>
                        </template>
                        <template v-else>
                            <p>You are currently not logged in.</p>
                        </template>
                        <p>To verify your identity, we will send a one-time password (OTP) to <b>{{ trip?.user.email }}</b>.</p>
                    </div>
                </div>
                <form action=""  @submit.prevent="disburseEmail">
                    <div class="row mt-4">
                        <div class="col-md-6 offset-md-3">
                            <button class="btn btn-primary w-100" type="submit" :disabled="registering">
                                <span class="spinner spinner-border spinner-border-sm" v-if="registering"></span>
                                SEND OTP
                            </button>
                        </div>
                    </div>
                </form>
            </template>

        </b-modal>
    </div>
</template>

<style lang="scss" scoped>
h2 {
    width: 100%;
    text-align: center;
    font-size: 1rem;
}

h2 span {
    background: #fff;
    padding: 0 10px;
}

.larger-fullstop {
    position: relative;
    top: -0.1em;
    font-size: 1.5em;
}

.otp-input {
    font-size: 2em;
    font-family: monospace;
    padding: 10px;
    border: 2px solid transparent;
    border-radius: 5px;
    width: 200px;
    text-align: center;
    box-sizing: border-box;
    outline: none;
    background-color: #f8f9fa;
}

/* Style for the input when focused */
.otp-input:focus {
    border-color: #007bff;
}

/* styles.css */
.star-rating {
    font-size: 2rem;
    color: #d3d3d3;
}

.star-rating-sm {
    font-size: 1rem;
    color: #d3d3d3;
}

.star-rating-text{
    font-size:1rem;
    color:#black;
}

.star {
    cursor: pointer;
    transition: color 0.2s;
}

.star:hover,
.star.selected {
    color: #ffd700;
}

</style>
