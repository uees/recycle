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
        v-model="queryParams.salesman"
        placeholder="业务员"
        style="width: 200px;"
        class="filter-item"
        @keyup.enter.native="handleFilter"
      />
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
        sortable="custom"
        align="center"
        width="80"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>

      <el-table-column
        label="公司名称"
        min-width="250px"
        align="center"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.name }}</span>
        </template>
      </el-table-column>

      <el-table-column
        label="公司地址"
        min-width="350px"
      >
        <template slot-scope="{row}">
          <span>{{ row.address }}</span>
        </template>
      </el-table-column>

      <el-table-column
        label="业务员"
        width="110px"
        align="center"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.salesman }}</span>
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
import { customersApi } from '@/api/erp'
import { Customer } from '@/defines/models'
import DataList from '../mixins/DataList'
import Pagination from '../mixins/Pagination'
import DataFormDialog from './DataFormDialog'

export default {
  name: 'Customers',
  components: { DataFormDialog },
  mixins: [DataList, Pagination],
  data() {
    return {
      api: customersApi,
      queryParams: {
        salesman: undefined
      }
    }
  },
  computed: {
    ...mapState('erp/customers', {
      formDialog: state => state.formDialog
    })
  },
  methods: {
    ...mapActions('erp/customers', [
      'resetFormDialog',
      'setFormDialog'
    ]),
    handleCreate() {
      this.setFormDialog({
        action: 'create',
        formData: Customer(),
        index: -1,
        visible: true
      })
    },
    handleUpdate(scope) {
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
