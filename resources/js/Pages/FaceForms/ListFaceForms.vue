<template>

    <Head :title="title" />
    <Layout>
        <PageHeader :title="title" :items="items">
            <div class="row d-flex justify-content-end mb-3">
                <div class="col-md-4">
                    <input class="form-control" placeholder="Search..."
                        @input.prevent="(e) => searchBeneficiaries(e)" />
                </div>
                <div class="col-md-4">
                    <b-button-group class="w-100">
                        <b-button variant="success" @click.prevent="goto('create-face')">Create Face Form</b-button>
                        <b-dropdown variant="success">
                            <template #button-content>
                                <b-icon icon="filter" />
                            </template>
                            <b-dropdown-menu>
                                <b-dropdown-item>
                                    <a @click.prevent="downloadAttendanceTemplate" rel="noopener noreferrer">
                                        <img src="/images/icons/icon.excel.2.png" style="height: 20px;width: 20px;"
                                            class="img-fluid" alt="" />
                                        Extract
                                    </a>
                                </b-dropdown-item>
                            </b-dropdown-menu>
                        </b-dropdown>
                    </b-button-group>
                </div>
            </div>
        </PageHeader>
        <div>
            <div class="row">
                <div class="col-md-12">
                    <b-card>
                        <b-card-text>
                            <a-table :scroll="{ y: 500 }" :pagination="pagination" @change="handleTableChange" bordered
                                class="ant-table-striped" :columns="columns" size="5" :loading="state.loading"
                                :row-class-name="(_record, index) => (index % 2 === 1 ? 'table-striped' : null)"
                                :row-selection="{ selectedRowKeys: state.selectedRowKeys, onChange: onSelectChange }"
                                :data-source="dataSources.data">
                                <template #bodyCell="{ column, text, record }">
                                    <div v-if="column.dataIndex == 'reference_no'">

                                        <a href="#" @click="reviewItem(record.id)">
                                            <div>
                                                <b> {{ text }} </b>
                                            </div>

                                        </a>
                                        <div>
                                                {{ record.title }}
                                            </div>
                                            <div><b>Type: </b>{{ `${record.face_type} (${record.request_report})` }}</div>
                                            <div><b>Report Period: </b>{{ `${record.start_period}  to ${record.end_period}` }}</div>

                                    </div>
                                    <div  v-if="column.dataIndex == 'face_date'">
                                        <div>{{ formatters.formatJustDate(text) }}</div>
                                    </div>
                                    <div  v-if="column.dataIndex == 'face_recieved_date'">
                                        <div>{{ formatters.formatDate(text) }}</div>
                                    </div>
                                    <div  v-if="column.dataIndex == 'status'">
                                        <div>
                                            <a-tag
                                        :color="['pending review'].includes(record.status) ? 'green' : record.status == 'Approved' ? 'volcano' :  'geekblue'">
                                        With UNICEF
                                    </a-tag>
                                        </div>
                                    </div>
                                </template>
                            </a-table>
                        </b-card-text>
                    </b-card>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import Layout from "@/Layouts/verticalvendor.vue";
import PageHeader from "@/Components/page-header.vue";
import { reactive, ref, onMounted, computed } from "vue"
import { debounce } from "lodash";
import Swal from "sweetalert2";
import { RequestHelper,formatters } from "@/mixins/helpers";

const title = "Face Form::List";

const items = [
    {
        href: '/',
        text: "Home"
    },
    {
        active: true,

        text: "Face Forms"
    }
]

const goto = (routeName) => {
    router.visit(route(routeName));
}

const columns = [
    {
        title: "#",
        dataIndex: "reference_no",
        key: "reference_no",
        sorter: true,
         width:"30%"
    },

    {
        title: "FACE Date",
        dataIndex: "face_date",
        key: "face_date",
        sorter: true,

    },
    {
        title: "Submitted",
        dataIndex: "face_recieved_date",
        key: "face_recieved_date",
        sorter: true,
    },
    {
        title: "Status",
        dataIndex: "status",
        key: "status",
        sorter: true,
    },
    {
        title: "",
        dataIndex: "action",
        key: "action",
    },
]


const FINANCE_URL = import.meta.env.VITE_BSC_REQUESTS_APP_URL;

const dataSources = ref({ data: [] });

const reviewItem = (itemId) => {
    router.visit(route("", itemId))
}

const session = usePage().props.auth.user;

function downloadAttendanceTemplate() {

}

const state = reactive({
    selectedRowKeys: [],
    loading: false,
});


const onSelectChange = selectedRowKeys => {
    state.loading = true;
    state.selectedRowKeys = selectedRowKeys;
    state.loading = false;
};

const customPagination = ref({
    current_page: 1,
    per_page: 100,
    total: 0
});

const pagination = computed(() => ({
    total: customPagination.value.total || 0,
    current: customPagination.value.current_page,
    pageSize: customPagination.value.per_page,
    position: ['bottomRight'],
    showSizeChanger: true,
    pageSizeOptions: ['15', '25', '100', '200', customPagination.value.total],
    onShowSizeChange: (pagCurr, pagSize) => {
        Object.assign(customPagination.value, {
            current_page: pagCurr,
            per_page: pagSize,
        });
    }
}));

const searchBeneficiaries = debounce(async (e) => {
    const valItem = e.target.value || '';
    await getMyFaces({ search: valItem });

}, 300);

const queryData = (params = {}) => {
    console.log({ params });
    return RequestHelper.getRequest(`${FINANCE_URL}/api/faces/ip-faces`, {
        search: params.search || '',
        ...params
    }, session?.api_token);
}

const getMyFaces = async (params = {}) => {
    state.loading = true;
    await queryData({
        per_page: customPagination.value.per_page || 50,
        page: customPagination.value.current_page || 1,
        ...params,
    }).then(({ data }) => {
        console.log({ data });
        const { faces } = data.results;

        dataSources.value.data = faces.data.map((item) => {
            item.key = item.id;

            return item;
        });

        let newPagination = { ...customPagination.value }
        newPagination.total = faces.total;
        customPagination.value = newPagination;
    }).catch(err => {
        console.log(err);
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Something went wrong!",
        });
    })
    state.selectedRowKeys = [];
    state.loading = false;
}

const handleTableChange = (pag, filters, sorter) => {
    Object.assign(customPagination.value, {
        current_page: pag.current,
        per_page: pag?.pageSize,
    });
    getMyFaces({ per_page: pag.pageSize, page: pag?.current, sortField: sorter.field, sortOrder: sorter.order, ...filters })
}

onMounted(() => {
    getMyFaces({ per_page: 20 });
});

</script>

<style lang="scss" scoped>
.ant-table-striped :deep(.table-striped) td {
    background-color: #eceff1;
}
</style>
