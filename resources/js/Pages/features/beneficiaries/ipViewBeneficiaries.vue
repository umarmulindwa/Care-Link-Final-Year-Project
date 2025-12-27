<template>

    <Head :title="`Master List - Beneficiaries::${listTitle}`" />
    <Layout>
        <PageHeader :title="`Master List - Beneficiaries::${listTitle}`" :items="items">
            <div class="row d-flex justify-content-end mb-3">
                <div class="col-md-4">
                    <input class="form-control" @input.prevent="(e) => searchBeneficiaries(e)"
                        placeholder="Search..." />
                </div>
            </div>
        </PageHeader>
        <div class="row d-flex justify-content-center">
            <div class="col-12" style="min-height: 80vh;">
                <div class="card shadow-md">
                    <div class="card-body p-0">
                        <a-table :scroll="{ x: 2000, y: 500 }" :pagination="false" bordered class="ant-table-striped"
                            :columns="columns" size="5" :loading="state.loading"
                            :row-class-name="(_record, index) => (index % 2 === 1 ? 'table-striped' : null)"
                            :row-selection="{ selectedRowKeys: state.selectedRowKeys, onChange: onSelectChange }"
                            :data-source="dataList" @change="handleTableChange">
                            <template #bodyCell="{ column, text, record }">
                                <div v-if="column.dataIndex === 'title'">
                                    <span class="text-primary" @click="goto(record.id)">{{ record.title }}</span>
                                </div>
                                <div v-if="column.dataIndex === 'details'">
                                    <div>
                                        <b>Name:</b> {{ `${record.firstname || ''} ${record.surname || ''}
                                        ${record.othername || ''}` }}
                                    </div>
                                    <div>
                                        <b>Unique ID:</b> {{ `${record.uniq_id || ''}` }}
                                    </div>

                                    <div>
                                        <b>National ID:</b> {{ `${record.national_id || ''}` }}
                                    </div>
                                    <div>
                                        <b>Facility:</b> {{ `${record.health_facilityboma || ''}` }}
                                    </div>
                                    <div>
                                        <b>State:</b>{{ `${record.state || ''}` }}
                                    </div>
                                    <div>
                                        <b> Payment Status: </b> <a-tag
                                            :color="['FSP Verified'].includes(record.payment_status) ? 'green' : record.payment_status == 'Approved' ? 'geekblue' : 'volcano'">
                                            {{ record.payment_status }}
                                        </a-tag>
                                    </div>
                                    <div>
                                        <b>Added:</b> {{ `${formatters.formatJustDate(record.created_at)}` }}
                                    </div>
                                </div>



                                <div v-if="column.dataIndex === 'approved_date'">
                                    {{ formatters.formatDate(text) }}
                                </div>


                                <div v-if="column.dataIndex === 'amount_paid'">
                                    <div class="editable-cell">
                                        <div v-if="editableData[record.key]" class="editable-cell-input-wrapper">
                                            <!-- <a-input v-model:value="editableData[record.key].amount_paid"
                                                @pressEnter="save(record.key)" /> -->
                                            <!-- <check-outlined class="editable-cell-icon-check"
                                                @click="save(record.key)" /> -->
                                            <Input v-model:value="editableData[record.key].amount_paid"
                                                @pressEnter="save(record.key)"></Input>
                                        </div>
                                        <div v-else class="editable-cell-text-wrapper">
                                            {{ text || ' ' }}
                                            <!-- <edit-outlined class="editable-cell-icon" @click="edit(record.key)" /> -->
                                        </div>
                                    </div>
                                </div>

                                <div v-if="column.dataIndex === 'amount_to_pay'">
                                    <div class="editable-cell">
                                        <div v-if="editableData[record.key]" class="editable-cell-input-wrapper">
                                            <!-- <a-input v-model:value="editableData[record.key].amount_paid"
                                                @pressEnter="save(record.key)" /> -->
                                            <!-- <check-outlined class="editable-cell-icon-check"
                                                @click="save(record.key)" /> -->
                                            <Input v-model:value="editableData[record.key].amount_to_pay"
                                                @pressEnter="save(record.key)"></Input>
                                        </div>
                                        <div v-else class="editable-cell-text-wrapper">
                                            {{ text || ' ' }}
                                            <!-- <edit-outlined class="editable-cell-icon" @click="edit(record.key)" /> -->
                                        </div>
                                    </div>
                                </div>
                                <div v-if="column.dataIndex === 'planned_payment_date'">
                                    <div class="editable-cell">
                                        <div v-if="editableData[record.key]" class="editable-cell-input-wrapper">
                                            <!-- <a-input v-model:value="editableData[record.key].date_paid"
                                                @pressEnter="save(record.key)" /> -->
                                            <!-- <b-form-datepicker v-model="value" :min="min" :max="max" locale="en"></b-form-datepicker> -->
                                            <!-- <b-form-datepicker @pressEnter="save(record.key)" v-model="editableData[record.key].date_paid" class="mb-2"></b-form-datepicker> -->
                                            <!-- <check-outlined class="editable-cell-icon-check"
                                                @click="save(record.key)" /> -->
                                            <a-date-picker @pressEnter="save(record.key)"
                                                v-model:value="editableData[record.key].planned_payment_date"
                                                format="YYYY-MM-DD" />
                                        </div>
                                        <div v-else class="editable-cell-text-wrapper">
                                            {{ (text != '' && text != null ? formatters.formatJustDate(text) : text) ||
                                                ' '
                                            }}
                                            <!-- <edit-outlined class="editable-cell-icon" @click="edit(record.key)" /> -->
                                        </div>
                                    </div>
                                </div>

                                <div v-if="column.dataIndex === 'payment_date'">
                                    <div class="editable-cell">
                                        <div v-if="editableData[record.key]" class="editable-cell-input-wrapper">
                                            <!-- <a-input v-model:value="editableData[record.key].date_paid"
                                                @pressEnter="save(record.key)" /> -->
                                            <!-- <b-form-datepicker v-model="value" :min="min" :max="max" locale="en"></b-form-datepicker> -->
                                            <!-- <b-form-datepicker @pressEnter="save(record.key)" v-model="editableData[record.key].date_paid" class="mb-2"></b-form-datepicker> -->
                                            <!-- <check-outlined class="editable-cell-icon-check"
                                                @click="save(record.key)" /> -->
                                            <a-date-picker @pressEnter="save(record.key)"
                                                v-model:value="editableData[record.key].payment_date"
                                                format="YYYY-MM-DD" />
                                        </div>
                                        <div v-else class="editable-cell-text-wrapper">
                                            {{ (text != '' && text != null ? formatters.formatJustDate(text) : text) ||
                                                ' '
                                            }}
                                            <!-- <edit-outlined class="editable-cell-icon" @click="edit(record.key)" /> -->
                                        </div>
                                    </div>
                                </div>
                                <div v-if="column.dataIndex === 'phone_number'">
                                    <div class="editable-cell">
                                        <div v-if="editableData[record.key]" class="editable-cell-input-wrapper">
                                            <!-- <a-input v-model:value="editableData[record.key].phone"
                                                @pressEnter="save(record.key)" /> -->
                                            <Input @change="e => formatData(e, record.key, 'phone', column.dataIndex)"
                                                v-model:value="editableData[record.key].phone_number"
                                                @pressEnter="save(record.key)"></Input>
                                            <!-- <check-outlined class="editable-cell-icon-check"
                                                @click="save(record.key)" /> -->

                                        </div>
                                        <div v-else class="editable-cell-text-wrapper">
                                            {{ text || ' ' }}
                                            <!-- <edit-outlined class="editable-cell-icon" @click="edit(record.key)" /> -->
                                        </div>
                                    </div>
                                </div>

                                <template v-else-if="column.dataIndex === 'operation'">
                                    <div class="editable-row-operations"
                                        v-if="!['Invoiced to UNICEF', 'Acceptable'].includes(listStatus)">
                                        <span v-if="editableData[record.key]">
                                            <a-typography-link @click="save(record.key)"><span
                                                    style="font-size: 1.23rem;color:#039be5"><i
                                                        class='bx bxs-save'></i></span></a-typography-link>
                                            <a-popconfirm title="Sure to cancel?" @confirm="cancel(record.key)">
                                                <a><span style="font-size: 1.23rem;color:#e53935"><i
                                                            class='bx bx-window-close'></i></span></a>
                                            </a-popconfirm>
                                        </span>
                                        <span v-else>
                                            <a @click="edit(record.key)"><span style="font-size: 1.23rem;"><i
                                                        class='bx bxs-edit-alt'></i></span></a>
                                        </span>
                                    </div>
                                </template>
                            </template>
                        </a-table>

                    </div>
                    <div class="row d-flex justify-content-start my-2">
                        <div class="col-md-8">
                            <a-pagination v-model:current="currentPage" v-model:pageSize="pageSize" show-size-changer
                                @showSizeChange="onShowSizeChange" :total="customPagination.total"
                                @change="onChangePage" />
                        </div>

                    </div>
                    <div>
                        <div class="row d-flex justify-content-end my-2" v-if="!['Verified'].includes(listStatus)">

                            <div class="col-md-4">
                                <b-button-group class="w-100">
                                    <b-button variant="danger">Delete</b-button>
                                    <b-button variant="success" @click.prevent="updateList">Update</b-button>
                                </b-button-group>
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
import { cloneDeep, debounce } from "lodash-es";
import { EditOutlined } from "@ant-design/icons-vue";
import { InputNumber, Input } from "ant-design-vue"
import { notificationMethods } from "@/state/helpers";
import { notify } from "@/mixins/notify";
import moment from "moment";
// import {debounce} from "lodash-es";


const listTitle = ref("Hospital Health Workers");
const listStatus = ref(null);
const page = usePage();
const store = useStore();
const session = page.props.auth.user;
const items = ref([
    { href: "/", text: 'Home' },
    { href: route('beneficiary-payments'), text: 'Master List' },
    { active: true, text: 'Beneficiaries' },
]);

const FINANCE_URL = import.meta.env.VITE_FINANCE_APP_URL;

const currentPage = ref(1);
const pageSize = ref(20);
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
    state.loading = true;
    console.log('selectedRowKeys changed: ', selectedRowKeys);
    state.selectedRowKeys = selectedRowKeys;
    state.loading = false;
};


const columns = [

    {
        title: "Details",
        dataIndex: "details",
        key: "details",
        width: "15%",
        scopedSlots: { customRender: "details" },
        sorter: true,
        fixed: 'left',
    },



    {
        title: "Amount To Pay (SSP)",
        dataIndex: "amount_to_pay",
        key: "amount_to_pay",
        scopedSlots: { customRender: "amount_to_pay" },
        sorter: true,

    },
    {
        title: "planned payment date",
        dataIndex: "planned_payment_date",
        key: "planned_payment_date",
        sorter: true,
    },
    {
        title: "Amount Paid (SSP)",
        dataIndex: "amount_paid",
        key: "amount_paid",
        sorter: true,
    },
    {
        title: "payment date",
        dataIndex: "payment_date",
        key: "payment_date",
        sorter: true,
    },
    {
        title: "Phone Number",
        dataIndex: "phone_number",
        key: "phone_number",
        scopedSlots: { customRender: "phone_number" },
        sorter: true,
    },
    {
        title: "",
        dataIndex: "operation",
        key: "operation",
    },
];

const dataList = ref([])

const countList = computed(() => dataList.value.length);

const editableData = reactive({});
const customPagination = ref({});


const edit = key => {
    editableData[key] = cloneDeep(dataList.value.filter(item => key === item.key)[0]);
}


const save = key => {
    state.loading = true;
    Object.assign(dataList.value.filter(item => key === item.key)[0], editableData[key]);
    delete editableData[key];
    state.loading = false;
};
const numberFormat = /^-?\d*(\.\d*)?$/;
const formatData = (evt, key, cat, objKey) => {
    let value = evt.target.value;
    switch (cat) {
        case 'phone':
            let digits = value.replace(/\D/g, '');
            let match = digits.match(/^(\d{0,3})(\d{0,3})(\d{0,4})$/);
            let newValue = "";
            if (match) {
                const part1 = match[1] ? `(${match[1]}` : '';
                const part2 = match[2] ? `) ${match[2]}` : '';
                const part3 = match[3] ? `-${match[3]}` : '';
                newValue = `${part1}${part2}${part3}`.trim();
            } else {
                newValue = digits;
            }



            editableData[key][`${objKey}`] = newValue;
            console.log(newValue);


            break;

        case 'currency':
            value = value.replace(/[^0-9.]/g, '');
            // const matchInput = digits.match(/^(\d{0,3})(\d{0,3})(\d{0,4})$/);
            const parts = value.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            let newCurrency = parts.join('.').replaceAll(",", '');

            editableData[key][`${objKey}`] = newCurrency;
            console.log(newCurrency);


            break;

        default:
            break;
    }
    // editableData[key][`${objKey}`] = evt.target.value;

    // Object.assign(dataList.value.filter(item => key === item.key)[0], editableData[key]);
}

const cancel = key => {
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


const search = ref("");

const getUploadedBeneficiaries = async (params = {}) => {

    return RequestHelper.getRequest(`${FINANCE_URL}/api/beneficiaries/list-beneficiaries-groups/${pageAttrs.group_id}`, {
        search: search.value || "",
        ...params
    }, session?.api_token);
}

const updateList = async () => {
    state.loading = true;
    let updatedItems = dataList.value.filter(item => state.selectedRowKeys.includes(item.key));
    // store.dispatch("beneficiaryStore/storeApprovedParticipants", {
    //     all: dataList.value,
    //     approved: state.selectedRowKeys,
    //     group_id: pageAttrs.group_id
    // });

    let formInputs = {
        'updated_items': updatedItems,
        'selected_items_ids': state.selectedRowKeys,
    }

    if (state.selectedRowKeys.length === 0) {
        state.loading = false;
        return notify.toastErrorMessage("Please select rows to update !")
    }

    Swal.fire({
        title: "Processing, please wait...",
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        showConfirmButton: false,
        showCancelButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });



    await RequestHelper.postRequest(FINANCE_URL + `/api/beneficiaries/fsp-update-participants-payments/${pageAttrs.group_id}`, formInputs, {}, session?.api_token).then(({ data }) => {
        Swal.fire({
            title: "Updated Participants Payments",
            icon: "success",
            showConfirmButton: true,
            showCancelButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,

        });
        getUploadedBeneficiaries().finally(() => {
            notify.toastSuccessMessage("Participants Updated successfully");
        });

    }).catch(err => {
        console.error(err);
        Swal.fire({
            title: "Something went wrong",
            icon: "error",
            showConfirmButton: true,
            showCancelButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
        });
    })


    state.loading = false;


}

const fetchBeneficiaries = async (params = {}) => {
    state.loading = true;
    await getUploadedBeneficiaries({
        per_page: pageSize.value || 10,
        page: currentPage.value || 1,
        ...params,
    }).then((res) => {
        console.log({ res })
        listTitle.value = res.data.results.group.title;
        listStatus.value = res.data.results.group.status;
        dataList.value = res.data.results.participants.data.map(item => {
            item.key = item.id;
            item.planned_payment_date = item.planned_payment_date != null && item.planned_payment_date != "" ? moment(item.planned_payment_date) : "";
            item.payment_date = item.payment_date != null && item.payment_date != "" ? moment(item.payment_date) : "";
            return item;
        });

        let newPagination = { ...customPagination.value }
        newPagination.total = res.data.results.participants.total;
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

const handleTableChange = (pagination, filters, sorter) => {
    const pager = { ...pagination };
    pager.current = pagination.current;
    customPagination.value = pager;

    fetchBeneficiaries({ per_page: customPagination.value.pageSize, page: customPagination.current, sortField: sorter.field, sortOrder: sorter.order, ...filters })

}

const searchBeneficiaries =debounce((e) => {
    search.value = e.target.value;
    currentPage.value = 1;
    fetchBeneficiaries();
},300);

const onChangePage = pageNumber => {
    currentPage.value = pageNumber;

    fetchBeneficiaries();

}

const onShowSizeChange = (current, per_page) => {
    currentPage.value = current;
    pageSize.value = per_page;
    fetchBeneficiaries();

}






onMounted(() => {
    fetchBeneficiaries();
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
        font-size: 1.18rem;
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
