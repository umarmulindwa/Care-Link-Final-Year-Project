<script setup>
import icondata from "../../../images/icondata.png";

const props = defineProps(["searchResults"]);
</script>
<template>
    <div>
        <div v-if="typeof props.searchResults === 'string'">
            <div class="text-center mt-5 mb-5">
                <div class="mb-4"><img :src="icondata" :height="80" /></div>
                <div>{{ props.searchResults }}</div>
            </div>
        </div>
        <div v-else>
            <div v-if="props.searchResults.length > 0">
                <div class="mb-4">{{ props.searchResults.length }} result{{ props.searchResults.length > 1 ? "s" : "" }} found</div>
                <div class="">
                    <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100" id="userList-table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">USER</th>
                                <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="text-center">SECTION/IP/SP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(list, index) in props.searchResults" :key="index + list.email + list.name">
                                <td :style="{ backgroundColor: '#fff' }">
                                    <div class="text-primary" role="button">
                                        {{ list.name }}
                                    </div>
                                    <div>
                                        {{ list.email }}
                                    </div>
                                </td>
                                <td :style="{ backgroundColor: '#fff' }" class="text-center">
                                    <div>
                                        {{ list.sectionname }}
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="text-center mt-5 mb-5" v-else>
                <div class="mb-4"><img :src="icondata" :height="80" /></div>
                <div>No Results found within Users</div>
            </div>
        </div>
    </div>
</template>
