<template>

    <Head :title="`BENEFICIARIES PAYMENTS::${listTitle}`" />
    <Layout>
        <PageHeader :title="`BENEFICIARIES PAYMENTS::${listTitle}`" :items="items">
            <div class="row d-flex justify-content-end mb-3">
                <div class="col-md-4">
                    <input class="form-control" placeholder="Search..." />
                </div>
            </div>
        </PageHeader>
        <div class="row d-flex justify-content-center">
            <div class="col-12" style="min-height: 80vh;">
                <div class="card shadow-md">
                    <div class="card-body p-0">
                        <div>
                            <a-table :columns="columns" :data-source="dataSource" bordered>
                                <template #bodyCell="{ column, text, record }">
                                    <template v-if="['name', 'age', 'address'].includes(column.dataIndex)">
                                        <div>
                                            <a-input v-if="editableData[record.key]"
                                                v-model:value="editableData[record.key][column.dataIndex]"
                                                style="margin: -5px 0" />
                                            <template v-else>
                                                {{ text }}
                                            </template>
                                        </div>
                                    </template>
                                    <template v-else-if="column.dataIndex === 'operation'">
                                        <div class="editable-row-operations">
                                            <span v-if="editableData[record.key]">
                                                <a-typography-link @click="save(record.key)">Save</a-typography-link>
                                                <a-popconfirm title="Sure to cancel?" @confirm="cancel(record.key)">
                                                    <a>Cancel</a>
                                                </a-popconfirm>
                                            </span>
                                            <span v-else>
                                                <a @click="edit(record.key)">Edit</a>
                                            </span>
                                        </div>
                                    </template>
                                </template>
                            </a-table>
                        </div>


                        <div>
                            <div class="row d-flex justify-content-end my-2">

                                <div class="col-md-4">
                                    <b-button-group class="w-100">
                                        <b-button variant="danger">Reject</b-button>
                                        <b-button variant="success" @click.prevent="updateList">Update</b-button>
                                    </b-button-group>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <uploadFormModal :uploadBeneficiaryListForm="uploadBeneficiaryListForm"
                    :uploadBeneficiaryListFormEvent="closeUploadBeneficiaryListForm" :session="session" />
            </div>
        </div>

    </Layout>
</template>

<script setup>
import { ref, useAttrs, onMounted, computed, reactive } from "vue";
import { usePage, router, Head } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import Layout from "@/Layouts/verticalvendor.vue";
import PageHeader from "@/Components/page-header.vue";
import { RequestHelper, formatters } from "@/mixins/helpers";
import uploadFormModal from "@/Pages/features/beneficiaries/modals/uploadForm.vue"
import { useStore } from "vuex";
import { cloneDeep } from "lodash-es"

const listTitle = ref("Hospital Health Workers");
const page = usePage();
const store = useStore();
const session = page.props.auth.user;
const items = ref([
    { href: "/", text: 'Home' },
    { active: true, text: 'Payments' },
]);

const state = reactive({
    selectedRowKeys: [],
    // Check here to configure the default column
    loading: false,
});

const pageAttrs = useAttrs();

const uploadBeneficiaryListForm = ref(false);

const openUploadBeneficiaryListForm = () => {
    uploadBeneficiaryListForm.value = true;
}
const closeUploadBeneficiaryListForm = () => {
    uploadBeneficiaryListForm.value = false;
}

const onSelectChange = selectedRowKeys => {
    console.log('selectedRowKeys changed: ', selectedRowKeys);
    state.selectedRowKeys = selectedRowKeys;
};

const updateList = () => {
    summaryModal.value = true;
    store.dispatch("beneficiaryStore/storeApprovedParticipants", {
        all: dataList.value,
        approved: state.selectedRowKeys,
        group_id: pageAttrs.group_id
    });
}

const columns = [
  {
    title: 'name',
    dataIndex: 'name',
    width: '25%',
  },
  {
    title: 'age',
    dataIndex: 'age',
    width: '15%',
  },
  {
    title: 'address',
    dataIndex: 'address',
    width: '40%',
  },
  {
    title: 'operation',
    dataIndex: 'operation',
  },
];

const dataList = ref([])

const countList = computed(() => dataList.value.length);

// const editableData = reactive({});

// const edit = key => {
//     editableData[key] = cloneDeep(dataList.value.filter(item => key === item.key)[0]);
// }



const data = [];
for (let i = 0; i < 100; i++) {
  data.push({
    key: i.toString(),
    name: `Edrward ${i}`,
    age: 32,
    address: `London Park no. ${i}`,
  });
}
const dataSource = ref(data);
const editableData = reactive({});
const edit = key => {
  editableData[key] = cloneDeep(dataSource.value.filter(item => key === item.key)[0]);
};
//const save = key => {
//  Object.assign(dataSource.value.filter(item => key === item.key)[0], editableData[key]);
//  delete editableData[key];
//};
const cancel = key => {
  delete editableData[key];
};


const save = key => {
    Object.assign(dataList.value.filter(item => key === item.key)[0], editableData[key]);
    delete editableData[key];
};

const generateRandomDate = (start, end) => {
    const startDate = new Date(start);
    const endDate = new Date(end);
    const randomTimestamp = startDate.getTime() + Math.random() * (endDate.getTime() - startDate.getTime());
    return new Date(randomTimestamp);
};


function goto(recordId) {
    router.get(route('beneficiary-reconcile-review', recordId))
}



const FINANCE_URL = import.meta.env.VITE_FINANCE_APP_URL;

const getApprovedBeneficiaryGroups = async () => {
    await RequestHelper.getRequest(`${FINANCE_URL}/api/beneficiaries/approved-beneficiary-payments/${pageAttrs.group_id}`, {}, session?.api_token).then(({ data }) => {
        listTitle.value = data.results.group.title;
        dataList.value = data.results.participants.map(item => {
            item.key = item.id;
            return item;
        });
    }).catch(err => {
        console.log(err);
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Something went wrong!",
        });
    })
}

onMounted(() => {
    getApprovedBeneficiaryGroups();
})






</script>

<style lang="scss" scoped>
.ant-table-striped :deep(.table-striped) td {
    background-color: #eceff1;
}

.editable-row-operations a {
  margin-right: 8px;
}

.editable-cell {
    position: relative;

    .editable-cell-input-wrapper,
    .editable-cell-text-wrapper {
        padding-right: 24px;
    }

    .editable-cell-text-wrapper {
        padding: 5px 24px 5px 5px;
    }

    .editable-cell-icon,
    .editable-cell-icon-check {
        position: absolute;
        right: 0;
        width: 20px;
        cursor: pointer;
    }

    .editable-cell-icon {
        margin-top: 4px;
        display: none;
    }

    .editable-cell-icon-check {
        line-height: 28px;
    }

    .editable-cell-icon:hover,
    .editable-cell-icon-check:hover {
        color: #108ee9;
    }

    .editable-add-btn {
        margin-bottom: 8px;
    }
}

.editable-cell:hover .editable-cell-icon {
    display: inline-block;
}
</style>
