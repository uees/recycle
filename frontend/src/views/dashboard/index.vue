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
        style="width: 100px;"
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

      <el-checkbox
        v-model="queryParams.includecustomers"
        class="filter-item"
      >展示客户</el-checkbox>

      <el-button
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-search"
        class="filter-item"
        @click="fetchData"
      >
        查看月度数据
      </el-button>

      <template v-if="user.roles.some(role => role.name === 'admin')">
        <el-button
          style="margin-left: 10px;"
          type="primary"
          class="filter-item"
          @click="handleMake"
        >
          更新数据
        </el-button>

        <el-button
          style="margin-left: 10px;"
          type="primary"
          class="filter-item"
          @click="handleMakeAll"
        >
          更新所有客户数据
        </el-button>
      </template>
    </div>

    <div class="filter-container">
      <el-date-picker
        v-model="pickerDate"
        :picker-options="pickerOptions"
        type="daterange"
        align="right"
        clearable
        value-format="yyyy-MM-dd"
        range-separator="至"
        start-placeholder="日期开始"
        end-placeholder="日期结束"
      />

      <el-button
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-search"
        @click="fetchRangeData"
      >
        查看所选范围数据
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
        label="年份"
        width="100"
      >
        <template slot-scope="{row}">
          <span v-if="row.year">{{ row.year }}年</span>
        </template>
      </el-table-column>

      <el-table-column
        label="月份"
        width="100"
      >
        <template slot-scope="{row}">
          <span v-if="row.month">{{ row.month }}月</span>
        </template>
      </el-table-column>

      <el-table-column
        label="客户"
        align="center"
        min-width="110px"
      >
        <template slot-scope="{row}">
          <span v-if="row.customer && row.customer.data">{{ row.customer.data.name }}</span>
          <span v-else-if="row.customer_id">{{ row.customer_id }}</span>
          <span v-else>月度统计</span>
        </template>
      </el-table-column>

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
import { mapActions, mapState, mapGetters } from 'vuex'
import { getRecycledStatistics, makeRecycledStatistics, getRecycledStatisticsRange, makeAllCustomersRecycledStatistics } from '@/api/erp'
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
        recyclable_type: 'bucket',
        include: 'customer',
        includecustomers: false
      },
      pickerDate: undefined,
      pickerDateQuery: undefined,
      pickerOptions: {
        shortcuts: [{
          text: '最近一周',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
            picker.$emit('pick', [start, end])
          }
        }, {
          text: '最近一个月',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
            picker.$emit('pick', [start, end])
          }
        }, {
          text: '最近三个月',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90)
            picker.$emit('pick', [start, end])
          }
        }]
      }
    }
  },
  computed: {
    ...mapState('erp/basedata', [
      'customers'
    ]),
    ...mapGetters([
      'user'
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
    },
    pickerDate(val) {
      if (Array.isArray(val)) {
        this.pickerDateQuery = 'date:' + val.join(',')
      } else {
        this.pickerDateQuery = undefined
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
    async fetchRangeData() {
      this.listLoading = true
      const { data } = await getRecycledStatisticsRange({
        date: this.pickerDateQuery,
        customer_id: this.queryParams.customer_id,
        recyclable_type: this.queryParams.recyclable_type
      })
      this.tableData = data
      this.listLoading = false
      return data
    },
    async handleMake() {
      if (!this.date) {
        this.$message({
          message: '请先选择月份',
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
    },
    async handleMakeAll() {
      if (!this.date) {
        this.$message({
          message: '请先选择月份',
          type: 'error'
        })
        return false
      }

      await makeAllCustomersRecycledStatistics(this.queryParams)

      this.$message({
        message: '生成统计数据完毕',
        type: 'success'
      })
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
