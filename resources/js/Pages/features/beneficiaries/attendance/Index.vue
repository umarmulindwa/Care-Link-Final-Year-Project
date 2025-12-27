<template>

    <Head :title="`Attendance::${'List'}`" />
    <Layout>
        <PageHeader :title="`Attendance::${'List'}`" :items="items">
            <div class="row d-flex justify-content-end mb-3">

                <div class="col-md-2">
                    <input type="search" class="form-control p-2" placeholder="search..." name="search"
                        @input="e => searchBeneficiaries(e)">
                </div>
                <div class="col-md-2">
                    <b-form-select v-model="selectedMonth" :options="months" placeholder="Select Month"
                        @change="changeMonth"></b-form-select>
                </div>
                <div class="col-md-3">
                    <b-button-group class="w-100">

                        <b-button @click.prevent="openUploadBeneficiaryListForm" variant="success">Upload
                            List</b-button>
                        <b-dropdown variant="success">
                            <template #button-content>
                                <b-icon icon="filter" />
                            </template>
                            <b-dropdown-menu>
                                <b-dropdown-item>
                                    <a @click.prevent="downloadAttendanceTemplate" rel="noopener noreferrer">
                                        <img src="/images/icons/icon.excel.2.png" style="height: 20px;width: 20px;"
                                            class="img-fluid" alt="" />
                                        Download Template
                                    </a>
                                </b-dropdown-item>
                            </b-dropdown-menu>
                        </b-dropdown>
                    </b-button-group>
                </div>
            </div>
        </PageHeader>
        <div class="row d-flex justify-content-center">
            <div class="col-12" style="min-height: 80vh;">
                <div class="card shadow-md">
                    <div class="card-body p-0">
                        <a-table :scroll="{ x: 3500, y: 500 }" :pagination="false" bordered class="ant-table-striped"
                            :columns="columns" size="5" :loading="state.loading"
                            :row-class-name="(_record, index) => (index % 2 === 1 ? 'table-striped' : 'table-striped2')"
                            :row-selection="{ selectedRowKeys: state.selectedRowKeys, onChange: onSelectChange }"
                            :data-source="dataList">
                            <template #bodyCell="{ column, text, record }">
                                <div v-if="column.dataIndex === 'details'">
                                    <div><b>Name:</b> {{ `${record.beneficiary.surname || ''}
                                        ${record.beneficiary.firstname || ''} ${record.beneficiary.othername || ''}` }}
                                    </div>
                                    <div><b>Facility:</b> {{ `${record.beneficiary.health_facilityboma || ''}
                                        ${record.beneficiary.firstname || ''} ${record.beneficiary.othername || ''}` }}
                                    </div>
                                    <div><b>Phone:</b> {{ `${record.beneficiary.phone_number || ''}` }}</div>
                                    <div><b>National ID:</b> {{ `${record.beneficiary.national_id || ''}` }}</div>
                                </div>

                                <template v-for="num in numberOfDays" :key="`day_m_${num}`">
                                    <div v-if="column.dataIndex === `day_${num}`">
                                        <a-checkbox :checked="text"></a-checkbox>
                                    </div>
                                </template>



                            </template>

                        </a-table>
                    </div>
                    <div>
                        <div class="row d-flex justify-content-start my-2">
                            <div class="col-md-8">
                                <a-pagination v-model:current="currentPage" v-model:pageSize="pageSize"
                                    show-size-changer @showSizeChange="onShowSizeChange" :total="customPagination.total"
                                    @change="onChangePage" />
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </Layout>

</template>

<script setup>
import Layout from "@/Layouts/verticalvendor.vue";
import PageHeader from "@/Components/page-header.vue";
import { usePage, Head, router } from "@inertiajs/vue3";
import { ref, useAttrs, onMounted, reactive, computed } from "vue";
import { RequestHelper, formatters, getDownloadedFile } from "@/mixins/helpers";
import Swal from "sweetalert2";
import moment from "moment";
import { debounce } from "lodash-es";

const FINANCE_URL = import.meta.env.VITE_FINANCE_APP_URL;

const session = usePage().props.auth.user;
const groupId = usePage().props.group_id;

const currentPage = ref(1);
const pageSize = ref(20);
const customPagination = ref({});
const search = ref("");




const selectedMonth = ref('select Month');
const filterList = debounce((e) => {

    getAttendance(e.target.value);
}, 1000)


const downloadAttendanceTemplate = async () => {
    Swal.fire({
        text: "Processing, please wait...",
        didOpen: () => {
            Swal.showLoading();
        },
    });
    await RequestHelper.downloadFile(FINANCE_URL + `/api/beneficiaries/attendance/download-template?group_id=${groupId}`, {}, session.api_token).then(({ data }) => {
        getDownloadedFile(data, `attendance-${moment((new Date()).toDateString()).format('Y-MMM')}-${Math.random()}.xlsx`)
    });
    Swal.close();
}




const changeMonth = (value) => {
    selectedMonth.value = value;
}

const state = reactive({
    selectedRowKeys: [],
    // Check here to configure the default column
    loading: false,
});

const onSelectChange = selectedRowKeys => {
    state.loading = true;
    state.selectedRowKeys = selectedRowKeys;
    state.loading = false;
};



const items = [
    { href: route('dashboard'), text: 'Home' },
    { href: route('beneficiary-payments'), text: 'Payments' },
    { active: true, text: 'Attendance' },
];



const dataList = ref([]);
const startTime = ref(null);
const endTime = ref(null);


const columns = ref([]);

const numberOfDays = ref(0);

const generateColumns = async (startStr, endStr) => {
    columns.value = [
        {
            title: "Uniq ID",
            dataIndex: "uniq_id",
            key: "uniq_id",
            sorter: true,
            width: "4%",
            fixed: 'left',
        },
        {
            title: "Beneficiary",
            dataIndex: "details",
            key: "details",
            width: "7%",
            sorter: true,
            fixed: 'left',
        },
        {
            title: "Amount To Pay",
            dataIndex: "amount_to_pay",
            key: "amount_to_pay",
            scopedSlots: { customRender: "amount_to_pay" },
            sorter: true,
            width: "4.5%",
            fixed: 'left',
        },
    ]

    // Create the start date for the month
    let startDate = new Date(startStr);
    // Create the end date for the month
    let endDate = new Date(endStr); // 0 here sets the date to the last day of the previous month

    // Loop through from start date to end date
    let currentDate = startDate;

    var i = 0;
    while (currentDate <= endDate) {
        columns.value.push({
            title: moment(new Date(currentDate)).format('DD-MMM-YY'),
            key: `day_${parseInt(moment(new Date(currentDate)).format('DD'))}`,
            dataIndex: `day_${parseInt(moment(new Date(currentDate)).format('DD'))}`,
            formatter: (value, row) => {
                const day = parseInt(moment(new Date(row.date)).format('DD'));
                return row.attendance[day] || '';
            }
        });
        currentDate.setDate(currentDate.getDate() + 1);
        i++;
    }
    numberOfDays.value = i;
}

const months = computed(() => {
    let endDate = new Date(endTime.value || (new Date).toDateString());
    let months = [];

    for (let i = 0; i < 3; i++) {
        let monthDate = new Date(endDate);
        monthDate.setMonth(endDate.getMonth() - i);
        let monthName = monthDate.toLocaleString('default', { month: 'long' });

        months.push(monthName);
    }

    months.push('select Month');

    return months.reverse();
})

const getAttendance = async (params={}) => {
    state.loading = true;
    return RequestHelper.getRequest(FINANCE_URL + `/api/beneficiaries/attendance/details/${groupId}/list`, {
        q: search.value || '',
        ...params
    }, session.api_token);


}


const fetchAttendance = async (params = {}) => {
    state.loading = true;
    await getAttendance({
        per_page: pageSize.value || 10,
        page: currentPage.value || 1,
        ...params,
    }).then(({ data }) => {
        const { attendance, startDate, endDate, groupId } = data.results;
        dataList.value = attendance.data.map((e) => {
            e.key = e.id;
            return e;
        });
        startTime.value = startDate;
        endTime.value = endDate;

        let newPagination = { ...customPagination.value }
        newPagination.total = attendance.total;
        customPagination.value = newPagination;


    }).catch(error => {
        console.log(error.message);
    });

    await generateColumns(startTime.value, endTime.value);
    selectedMonth.value = (new Date(endTime.value)).toLocaleString('default', { month: 'long' })

    state.loading = false;
    state.selectedRowKeys = [];
    state.loading = false;
}

const handleTableChange = (pagination, filters, sorter) => {
    const pager = { ...pagination };
    pager.current = pagination.current;
    customPagination.value = pager;

    fetchAttendance({ per_page: customPagination.value.pageSize, page: customPagination.current, sortField: sorter.field, sortOrder: sorter.order, ...filters })

}

const searchBeneficiaries = (e) => {
    search.value = e.target.value;
    currentPage.value = 1;
    fetchAttendance();
}

const onChangePage = pageNumber => {
    currentPage.value = pageNumber;

    fetchAttendance();

}

const onShowSizeChange = (current, per_page) => {
    currentPage.value = current;
    pageSize.value = per_page;
    fetchAttendance();

}

onMounted(() => {
    fetchAttendance();
});

</script>

<style lang="scss" scoped>
.ant-table-striped :deep(.table-striped) td {
    background-color: #e0e0e0;
}

.ant-table-striped :deep(.table-striped2) td {
    background-color: #eeeeee;
}
</style>
