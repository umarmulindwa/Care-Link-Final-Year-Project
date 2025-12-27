<template>
  <a-table
  class="ant-table-striped"
   :row-class-name="(_record, index) => (index % 2 === 1 ? 'table-striped' : null)"
    :columns="columns"
    :row-key="record => record.login.uuid"
    :data-source="queryResult"
    :pagination="pagination"
    :loading="loading"
    @change="handleTableChange"

    :fixed="'top'"
  >
    <template #bodyCell="{ column, text }">
      <template v-if="column.dataIndex === 'name'">{{ text.first }} {{ text.last }}</template>
    </template>
  </a-table>
</template>
<script setup>
import { computed } from 'vue';
import { usePagination } from 'vue-request';
import axios from 'axios';
const columns = [
  {
    title: 'Name',
    dataIndex: 'name',
    sorter: true,
    width: '20%',
  },
  {
    title: 'Gender',
      dataIndex: 'gender',
      sorter: true,
    filters: [
      {
        text: 'Male',
        value: 'male',
      },
      {
        text: 'Female',
        value: 'female',
      },
    ],
    width: '20%',
  },
  {
    title: 'Email',
      dataIndex: 'email',
      sorter: true,
  },
];
const queryData = (params) => {
  return  axios.get('https://randomuser.me/api?noinfo', {
    params,
  });
};
const {
  data: dataSource,
  run,
  loading,
    current,
  totalPage,
  pageSize
} = usePagination(queryData, {
    defaultParams: [
        {
            gender: 'female',
        }
    ],
  formatResult: res => res.data.results,
  pagination: {
    currentKey: 'page',
    pageSizeKey: 'results',
  },
});

const itemsList = usePagination(queryData, {

  pagination: {
    currentKey: 'page',
    pageSizeKey: 'results',
  },
});

const queryResult = computed(() => {
    const { results } = dataSource.value != undefined? dataSource.value.data : {};
    return results || [];
});
const pagination = computed(() => ({
  total: 200,
  current: current.value,
  pageSize: pageSize.value,
}));
const handleTableChange = (pag, filters, sorter) => {
    run({
        defaultParams: [
        {
            gender: 'female',
        }
    ],
    results: pag.pageSize,
    page: pag?.current,
    sortField: sorter.field,
    sortOrder: sorter.order,
    ...filters,
  });
};
</script>
<style scoped>
.ant-table-striped :deep(.table-striped) td {
  background-color: #fafafa;
}

</style>
