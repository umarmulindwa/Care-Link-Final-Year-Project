<script setup></script>
<template>
     <div class="p-4">
        <div class="d-flex flex-row justify-content-between col-12 mb-4 ">
            <div class="d-flex align-items-center invisible">
                <div class="rounded-pill d-none d-lg-block" style="background-color: #f9f9f9; border: 0px">
                    <div class="input-group d-flex flex-row align-items-center">
                        <div class="mt-1">
                            <span class="" style="font-size: medium"><i class="bx bx-search-alt"></i></span>
                        </div>

                        <div class="hstack gap-3" role="button">
                            <div class="vr" style="color: #dbdbdb"></div>
                            <div class="btn-group dropbottomstart">
                                <div data-bs-toggle="dropdown" class="d-flex flex-row gap-3">
                                    <div>
                                        <i class="mdi mdi-chevron-down" aria-expanded="false"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-row gap-3">
              
                <div>
                    <b-button variant="primary">Upload Latest Codes</b-button>
                </div>
            </div>
        </div>
        <div class="text-center pt-5 mt-5 mb-5">List is Empty</div>
    </div>
</template>
