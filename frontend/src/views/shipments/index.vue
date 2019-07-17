<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input
        v-model="queryParams.q"
        placeholder="关键字"
        style="width: 200px;"
        class="filter-item"
        @keyup.enter.native="handleFilter"
      />
      <el-input
        v-model="queryParams.customer_id"
        placeholder="客户"
        style="width: 200px;"
        class="filter-item"
        @keyup.enter.native="handleFilter"
      />
      <el-select
        v-model="queryParams.recyclable_type"
        clearable
        class="filter-item"
        style="width: 200px;"
        placeholder="回收类型"
        @change="handleFilter"
      >
        <el-option
          v-for="item in recyclable_types"
          :key="item.value"
          :label="item.label"
          :value="item.value"
        />
      </el-select>
      <el-button
        class="filter-item"
        type="primary"
        icon="el-icon-search"
        @click="handleFilter"
      >
        搜索
      </el-button>
      <el-button
        class="filter-item"
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-edit"
        @click="handleCreate"
      >
        添加
      </el-button>
    </div>

    <el-table
      v-loading="listLoading"
      :data="tableData"
      border
      fit
      highlight-current-row
      style="width: 100%;"
    >
      <el-table-column
        label="序号"
        prop="id"
        align="center"
        width="80"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>

      <el-table-column
        label="发货日期"
        width="130px"
        align="center"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.created_at }}</span>
        </template>
      </el-table-column>

      <el-table-column
        label="客户"
        align="center"
        min-width="110px"
      >
        <template slot-scope="{row}">
          <span v-if="row.customer">{{ row.customer.data.name }}</span>
          <span v-else>{{ row.customer_id }}</span>
        </template>
      </el-table-column>

      <el-table-column
        label="品名"
        width="110px"
        align="center"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.product_name }}</span>
        </template>
      </el-table-column>

      <el-table-column
        label="批次"
        width="110px"
        align="center"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.product_batch }}</span>
        </template>
      </el-table-column>

      <el-table-column
        label="规格"
        width="80px"
        align="center"
      >
        <template slot-scope="scope">
          <span style="color:red;">{{ scope.row.spec }}</span>
        </template>
      </el-table-column>

      <el-table-column
        label="回收类别"
        width="100px"
        align="center"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.recyclable_type | recyclableType }}</span>
        </template>
      </el-table-column>

      <el-table-column
        label="重量"
        align="center"
        width="120px"
      >
        <template slot-scope="scope">
          <span v-if="scope.row.weight">{{ scope.row.weight }}kg</span>
        </template>
      </el-table-column>

      <el-table-column
        label="数量"
        align="center"
        width="95"
      >
        <template slot-scope="{row}">
          <span v-if="row.amount" style="color:red;">{{ Number(row.amount) }}</span>
        </template>
      </el-table-column>

      <el-table-column
        label="发货人"
        width="100px"
        align="center"
      >
        <template slot-scope="{row}">
          <span>{{ row.created_user }}</span>
        </template>
      </el-table-column>

      <el-table-column
        label="操作"
        align="center"
        width="230"
        class-name="small-padding fixed-width"
      >
        <template slot-scope="scope">
          <el-button
            type="primary"
            size="mini"
            @click="handleUpdate(scope)"
          >
            编辑
          </el-button>

          <el-button
            size="mini"
            type="danger"
            @click="handleDelete(scope)"
          >
            删除
          </el-button>
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

    <data-form-dialog @action-done="actionDone" />
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { shipmentsApi } from '@/api/erp'
import { Shipment } from '@/defines/models'
import { recyclableType } from '@/filters'
import { RECYCLABLE_TYPES } from '@/defines/consts'
import DataList from '../mixins/DataList'
import Pagination from '../mixins/Pagination'
import DataFormDialog from './DataFormDialog'

export default {
  name: 'Shipment',
  components: { DataFormDialog },
  filters: { recyclableType },
  mixins: [DataList, Pagination],
  data() {
    return {
      api: shipmentsApi,
      recyclable_types: RECYCLABLE_TYPES,
      queryParams: {
        customer_id: undefined,
        created_user_id: undefined,
        created_at: undefined,
        recyclable_type: undefined,
        include: 'customer'
      }
    }
  },
  computed: {
    ...mapState('erp/shipment', {
      formDialog: state => state.formDialog
    })
  },
  methods: {
    ...mapActions('erp/shipment', [
      'resetFormDialog',
      'setFormDialog'
    ]),
    handleCreate() {
      const shipment = Shipment()
      shipment.include = 'customer'

      this.setFormDialog({
        action: 'create',
        formData: shipment,
        index: -1,
        visible: true
      })
    },
    handleUpdate(scope) {
      const shipment = scope.row
      shipment.include = 'customer'

      this.setFormDialog({
        action: 'update',
        formData: scope.row,
        index: scope.$index,
        visible: true
      })
    }
  }
}
</script>
