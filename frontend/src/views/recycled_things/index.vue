<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input
        v-model="queryParams.q"
        placeholder="关键字"
        style="width: 200px;"
        @keyup.enter.native="handleFilter"
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
        @keyup.enter.native="handleFilter"
      >
        <el-option
          v-for="customer in customers.data"
          :key="customer.id"
          :label="customer.name"
          :value="customer.id"
        />
      </el-select>
      <el-input
        v-model="queryParams.recycled_user"
        placeholder="接收人"
        style="width: 200px;"
        @keyup.enter.native="handleFilter"
      />
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
        @change="dateChanged"
      />
    </div>
    <div class="filter-container">
      <el-button
        type="primary"
        icon="el-icon-search"
        @click="handleFilter"
      >
        搜索
      </el-button>
      <el-button
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-edit"
        @click="handleRecycle"
      >
        回收
      </el-button>
    </div>

    <el-table
      v-loading="listLoading"
      :data="tableData"
      border
      fit
      highlight-current-row
      style="width: 100%"
    >
      <el-table-column
        label="回收日期"
        width="160px"
        align="center"
      >
        <template slot-scope="scope">
          <el-tooltip
            v-if="scope.row._is_recycle"
            class="item"
            effect="dark"
            content="留空表示当前日期"
            placement="top-start"
          >
            <el-date-picker
              v-model="scope.row.created_at"
              clearable
              type="date"
              placeholder="选择日期"
              class="edit-input"
              size="small"
            />
          </el-tooltip>
          <el-link
            v-else
            type="primary"
            @click="handleEditRecycled(scope)"
          >
            <span>{{ scope.row.created_at }}</span>
          </el-link>
        </template>
      </el-table-column>

      <el-table-column
        label="客户"
        align="center"
        min-width="160px"
      >
        <template slot-scope="scope">
          <el-select
            v-if="scope.row._is_recycle"
            v-model="scope.row.customer_id"
            filterable
            remote
            default-first-option
            :remote-method="loadCustomers"
            :loading="customers.loading"
            class="edit-input"
            size="small"
            @keyup.enter.native="handleFilter"
          >
            <el-option
              v-for="customer in customers.data"
              :key="customer.id"
              :label="customer.name"
              :value="customer.id"
            />
          </el-select>
          <template v-else>
            <el-link
              type="primary"
              @click="handleEditRecycled(scope)"
            >
              <span v-if="scope.row.customer && scope.row.customer.data">{{ scope.row.customer.data.name }}</span>
              <span v-else>{{ scope.row.customer_id }}</span>
            </el-link>
          </template>
        </template>
      </el-table-column>

      <el-table-column
        label="回收类别"
        width="100px"
        align="center"
      >
        <template slot-scope="scope">
          <el-select
            v-if="scope.row._is_recycle"
            v-model="scope.row.recyclable_type"
            class="edit-input"
            size="small"
          >
            <el-option
              v-for="item in recyclable_types"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            />
          </el-select>
          <el-link
            v-else
            type="primary"
            @click="handleEditRecycled(scope)"
          >
            <span>{{ scope.row.recyclable_type | recyclableType }}</span>
          </el-link>
        </template>
      </el-table-column>

      <el-table-column
        label="数量"
        width="80px"
        align="center"
      >
        <template slot-scope="scope">
          <el-input
            v-if="scope.row._is_recycle"
            v-model="scope.row.amount"
            class="edit-input"
            size="small"
          />
          <el-link
            v-else
            type="primary"
            @click="handleEditRecycled(scope)"
          >
            <span>{{ scope.row.amount }}</span>
          </el-link>
        </template>
      </el-table-column>

      <el-table-column
        min-width="100px"
        label="回收人"
        align="center"
      >
        <template slot-scope="scope">
          <el-input
            v-if="scope.row._is_recycle"
            v-model="scope.row.recycled_user"
            class="edit-input"
            size="small"
          />
          <el-link
            v-else
            type="primary"
            @click="handleEditRecycled(scope)"
          >
            <span>{{ scope.row.recycled_user }}</span>
          </el-link>
        </template>
      </el-table-column>

      <el-table-column
        label="确认数量"
        width="130px"
        align="center"
      >
        <template slot-scope="scope">
          <el-input
            v-if="scope.row._is_confirm"
            v-model="scope.row.confirmed_amount"
            class="edit-input"
            size="small"
            @keyup.enter.native="confirmEdit(scope)"
          />
          <template v-else>
            <el-link
              v-if="scope.row.id"
              type="primary"
              @click="handleConfirm(scope)"
            >{{ scope.row.confirmed_amount ? scope.row.confirmed_amount : '确认数量' }}</el-link>
            <span v-else>{{ scope.row.confirmed_amount }}</span>
          </template>
        </template>
      </el-table-column>

      <el-table-column
        label="确认人"
        width="100px"
        align="center"
      >
        <template slot-scope="scope">
          <span v-if="scope.row.confirmed_user && scope.row.confirmed_user.data">
            {{ scope.row.confirmed_user.data.name }}
          </span>
          <span v-else>{{ scope.row.confirmed_user_id }}</span>
        </template>
      </el-table-column>

      <el-table-column
        label="确认日期"
        width="130px"
        align="center"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.confirmed_at }}</span>
        </template>
      </el-table-column>

      <el-table-column
        label="不合格数"
        width="100px"
        align="center"
      >
        <template slot-scope="scope">
          <el-tooltip
            v-if="scope.row.id"
            class="item"
            effect="dark"
            content="点击显示检测窗口"
            placement="top"
          >
            <el-link
              type="primary"
              @click="handleQC(scope)"
            >
              <span v-if="scope.row.qc_records && scope.row.qc_records.data">
                {{ scope.row.qc_records.data | count_bad }}
              </span>
              <span v-else>0</span>
            </el-link>
          </el-tooltip>
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        label="操作"
        width="220"
      >
        <template slot-scope="scope">
          <template v-if="scope.row._is_recycle || scope.row._is_confirm">
            <el-button
              type="success"
              size="small"
              icon="el-icon-circle-check-outline"
              @click="confirmEdit(scope)"
            >Ok</el-button>
            <el-button
              size="small"
              icon="el-icon-refresh"
              type="warning"
              @click="cancelEdit(scope)"
            >Cancel</el-button>
          </template>
          <template v-else>
            <el-button
              type="danger"
              icon="el-icon-delete"
              size="small"
              @click="handleDelete(scope)"
            >Delete</el-button>
          </template>
        </template>
      </el-table-column>
    </el-table>

    <div
      v-show="!listLoading"
      class="pagination-container"
    >
      <el-pagination
        :total="total"
        :current-page.sync="queryParams.page"
        :page-sizes="pageSizes"
        :page-size="queryParams.per_page"
        layout="total, sizes, prev, pager, next, jumper"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>

    <qc-records @qc-done="qcDone" />
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from 'vuex'
import { deepClone } from '@/utils'
import { recyclesApi, recycle, updateRecycled, confirm } from '@/api/erp'
import { RecycledThing } from '@/defines/models'
import { RECYCLABLE_TYPES } from '@/defines/consts'
import Pagination from '../mixins/Pagination'
import QcRecords from './qc_records'

export default {
  name: 'RecycledThings',
  filters: {
    count_bad(records) {
      let amount = 0
      for (const record of records) {
        amount += +record.bad_amount
      }
      return amount
    },
    recyclableType(data) {
      const el = RECYCLABLE_TYPES.find(el => {
        if (el.value === data) {
          return true
        }
      })

      return el.label
    }
  },
  components: {
    QcRecords
  },
  mixins: [Pagination],
  data() {
    return {
      api: recyclesApi,
      tableData: [],
      listLoading: false,
      queryParams: {
        customer_id: undefined,
        confirmed_user_id: undefined,
        recycled_user: undefined,
        created_at: undefined,
        include: 'customer,confirmed_user,qc_records',
        q: '',
        sort_by: 'id',
        order: 'desc'
      },
      recyclable_types: RECYCLABLE_TYPES,
      pickerDate: null,
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
    ])
  },
  watch: {
    pickerDate(val) {
      if (Array.isArray(val)) {
        this.queryParams.created_at = 'date:' + val.join(',')
      } else {
        this.queryParams.created_at = ''
      }
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    ...mapActions('erp/basedata', [
      'loadCustomers'
    ]),
    ...mapMutations('erp/qc_records', [
      'SET_VISIBLE',
      'SET_RECYCLED_THING',
      'SET_INDEX'
    ]),
    newObj() {
      return RecycledThing()
    },
    async fetchData() {
      this.listLoading = true
      const response = await this.api.list({ params: this.queryParams })
      this.tableData = response.data.map(value => {
        this.$set(value, '_is_recycle', false)
        this.$set(value, '_is_confirm', false)
        this.setOriginal(value)
        return value
      })
      this.paginate(response)
      this.listLoading = false
    },
    handleFilter() {
      this.queryParams.page = 1
      this.fetchData()
    },
    handleDelete(scope) {
      this.$confirm('此操作将永久删除该条目, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(async () => {
        await this.api.destroy(scope.row.id)
        this.tableData.splice(scope.$index, 1)
        this.$message({
          type: 'success',
          message: '删除成功!'
        })
      })
    },
    handleRecycle() {
      const row = this.newObj()
      this.$set(row, '_is_recycle', true) // is edit recycle
      this.$set(row, '_is_confirm', false)
      row._is_create = true
      this.tableData.unshift(row)
    },
    handleEditRecycled(scope) {
      if (scope.row.customer && scope.row.customer.data) {
        const hasCustomer = this.customers.data.some(customer => {
          return customer.id === scope.row.customer.data.id
        })
        if (!hasCustomer) {
          this.customers.data.push(scope.row.customer.data)
        }
      }

      scope.row._is_recycle = true
    },
    handleConfirm(scope) {
      scope.row._is_confirm = true
    },
    handleQC(scope) {
      this.SET_RECYCLED_THING(scope.row)
      this.SET_INDEX(scope.$index)
      this.SET_VISIBLE(true)
    },
    cancelEdit(scope) {
      if (scope.row._is_create) {
        this.tableData.splice(scope.$index, 1)
        return
      }
      this.restore(scope.row)
      scope.row._is_recycle = false
      scope.row._is_confirm = false
    },
    async confirmEdit(scope) {
      const validate = this.validateForm(scope.row)
      if (validate) {
        let response
        scope.row.include = this.queryParams.include // call loadRelByModel
        if (scope.row._is_create) {
          response = await recycle(scope.row)
        } else if (scope.row._is_recycle) {
          response = await updateRecycled(scope.row.id, scope.row)
        } else {
          response = await confirm(scope.row.id, scope.row)
        }
        const { data } = response
        this.$set(data, '_is_recycle', false) // is edit recycle
        this.$set(data, '_is_confirm', false)
        this.setOriginal(data)
        this.tableData.splice(scope.$index, 1, data)
      }
    },
    setOriginal(row) {
      // js 对象是引用传值，所以这里会直接修改原始值
      row._original = deepClone(row)
    },
    restore(row) {
      // 恢复
      const { _original } = row
      for (const key of Object.keys(_original)) {
        if (!key.startsWith('_')) {
          row[key] = _original[key]
        }
      }
    },
    validateForm(row) {
      if (row._is_recycle && (!row.customer_id || !row.amount || !row.recyclable_type)) {
        this.$message({
          message: '回收类型, 客户, 数量 必填',
          type: 'error'
        })
        return false
      } else if (row._is_confirm && !row.confirmed_amount) {
        this.$message({
          message: '确认数量 必填',
          type: 'error'
        })
        return false
      }
      return true
    },
    dateChanged() {
      this.handleFilter()
    },
    qcDone(records, index) {
      const row = this.tableData[index]
      row.qc_records.data = records
    }
  }
}
</script>

<style scoped>
.el-date-editor.el-input.edit-input {
  width: 100%;
}
</style>
