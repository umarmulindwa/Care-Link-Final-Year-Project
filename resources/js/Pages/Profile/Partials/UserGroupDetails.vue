<script setup>
const props = defineProps({
    user: Object,
});
</script>
<template>
    <div class="mt-5">
        <div class="col-md-12 mb-4">
            <div class="table-responsive mb-5">
                <table class="col-12 table table-striped">
                    <tbody>
                        <tr>
                            <th :style="{ backgroundColor: '#eff2f7' }">Staff name :</th>
                            <td :style="{ backgroundColor: '#eff2f7' }">{{ props.user.name }}</td>
                        </tr>
                        <tr>
                            <th :style="{ backgroundColor: '#eff2f7' }">Section :</th>
                            <td :style="{ backgroundColor: '#eff2f7' }">{{ props.user?.staffProfile?.sectionname }}</td>
                        </tr>
                        <tr>
                            <th :style="{ backgroundColor: '#eff2f7' }">Index :</th>
                            <td :style="{ backgroundColor: '#eff2f7' }">{{ props.user?.staffProfile?.index_number }}</td>
                        </tr>
                        <tr>
                            <th :style="{ backgroundColor: '#eff2f7' }">Tile :</th>
                            <td :style="{ backgroundColor: '#eff2f7' }">{{ props.user?.staffProfile?.position_text }}</td>
                        </tr>
                        <tr>
                            <th :style="{ backgroundColor: '#eff2f7' }">Group :</th>
                            <td :style="{ backgroundColor: '#eff2f7' }">{{ props.user?.staffProfile?.groupDetails?.group.group_number }}</td>
                        </tr>
                        <tr>
                            <th :style="{ backgroundColor: '#eff2f7' }">Group Leader :</th>
                            <td :style="{ backgroundColor: '#eff2f7' }">{{ props.user?.staffProfile?.groupDetails?.groupLeader }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pt-5" v-if="props.user?.staffProfile?.groupDetails?.otherGroupMembers != null">
            <h4 class="card-title mb-4">Group Members</h4>
            <div class="col-md-12 mb-4">
                <div class="table-responsive mb-5">
                    <table class="col-12 table table-striped">
                        <tbody>
                            <tr v-for="(groupMember,index) in props.user?.staffProfile?.groupDetails?.otherGroupMembers" :key="`${index}_${groupMember.name}`">
                                <th :style="{ backgroundColor: '#eff2f7' }">{{ groupMember.name }}</th>
                                <td :style="{ backgroundColor: '#eff2f7' }" class="">{{ groupMember.gender }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
