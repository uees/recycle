<template>
  <div class="app-container">
    <div class="filter-container">
      <el-date-picker
        v-model="date"
        clearable
        type="month"
        class="filter-item"
        style="width: 200px;"
        placeholder="选择月份"
        @change="fetchData"
      />
      <el-select
        v-model="queryParams.customer_id"
        filterable
        clearable
        remote
        default-first-option
        placeholder="客户"
        :remote-method="loadCustomers"
        :loading="customers.loading"
        class="filter-item"
        style="width: 200px;"
        @keyup.enter.native="fetchData"
      >
        <el-option
          v-for="customer in customers.data"
          :key="customer.id"
          :label="customer.name"
          :value="customer.id"
        />
      </el-select>

      <el-select
        v-model="queryParams.recyclable_type"
        clearable
        class="filter-item"
        style="width: 200px;"
        placeholder="回收类型"
        @change="fetchData"
      >
        <el-option
          v-for="item in recyclable_types"
          :key="item.value"
          :label="item.label"
          :value="item.value"
        />
      </el-select>

      <el-button
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-search"
        class="filter-item"
        @click="fetchData"
      >
        查看
      </el-button>

      <el-button
        style="margin-left: 10px;"
        type="primary"
        class="filter-item"
        @click="handleMake"
      >
        生成统计数据
      </el-button>
    </div>

    <panel-group
      :total-statistics="tableData"
      :recyclable-type="queryParams.recyclable_type"
    />

    <el-table
      v-loading="listLoading"
      :data="tableData"
      border
      style="width: 100%"
    >
      <el-table-column
        prop="year"
        label="年份"
        width="100"
      />
      <el-table-column
        prop="month"
        label="月份"
        width="100"
      />
      <el-table-column
        label="可回收类型"
        width="120"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.recyclable_type | recyclableTypeLabel }}</span>
        </template>
      </el-table-column>
      <el-table-column
        prop="entering_warehouse_amount"
        label="生产用量"
      />
      <el-table-column
        prop="shipment_amount"
        label="发货量"
      />
      <el-table-column
        prop="recycled_amount"
        label="回收量"
      />
      <el-table-column
        prop="bad_amount"
        label="回收不良量"
      />
      <el-table-column
        prop="good_amount"
        label="有效回收量"
      />
    </el-table>
  </div>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { getRecycledStatistics, makeRecycledStatistics } from '@/api/erp'
import { RECYCLABLE_TYPES } from '@/defines/consts'
import { recyclableType as recyclableTypeLabel } from '@/filters'
import PanelGroup from './PanelGroup'

export default {
  name: 'Dashboard',
  filters: {
    recyclableTypeLabel
  },
  components: {
    PanelGroup
  },
  data() {
    return {
      tableData: [],
      date: undefined,
      listLoading: false,
      recyclable_types: RECYCLABLE_TYPES,
      queryParams: {
        year: undefined,
        month: undefined,
        customer_id: undefined,
        recyclable_type: undefined
      }
    }
  },
  computed: {
    ...mapState('erp/basedata', [
      'customers'
    ])
  },
  watch: {
    date(val) { // 把 date 关联到查询参数
      if (this.date) {
        this.queryParams.year = this.date.getFullYear()
        this.queryParams.month = this.date.getMonth() + 1
      } else {
        this.queryParams.year = undefined
        this.queryParams.month = undefined
      }
    }
  },
  mounted() {
    this.fetchData()
  },
  methods: {
    ...mapActions('erp/basedata', [
      'loadCustomers'
    ]),
    async fetchData() {
      this.listLoading = true
      const { data } = await getRecycledStatistics(this.queryParams)
      this.tableData = data
      this.listLoading = false
      return data
    },
    async handleMake() {
      if (!this.date) {
        this.$message({
          message: '请先选择日期',
          type: 'error'
        })
        return false
      }
      this.listLoading = true
      const { data } = await makeRecycledStatistics(this.queryParams)
      this.tableData = data
      this.listLoading = false

      this.$message({
        message: '生成统计数据完毕',
        type: 'success'
      })

      return data
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
