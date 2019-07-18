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
        v-model="queryParams.product_name"
        placeholder="品名"
        style="width: 200px;"
        class="filter-item"
        @keyup.enter.native="handleFilter"
      />
      <el-input
        v-model="queryParams.product_batch"
        placeholder="批次"
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

    <!-- <div class="filter-container">
      <el-upload
        ref="upload"
        class="filter-item"
        action="nothing"
        accept="application/msexcel"
        :before-upload="beforeUpload"
        :http-request="handleUpload"
        :on-success="onUploadSuccess"
        :file-list="fileList"
        :auto-upload="false"
      >
        <el-button
          slot="trigger"
          size="small"
          type="primary"
        >选取文件</el-button>
        <el-button
          style="margin-left: 10px;"
          size="small"
          type="success"
          @click="submitUpload"
        >上传到服务器</el-button>
        <div
          slot="tip"
          class="el-upload__tip"
        >只能上传xlsx文件</div>
      </el-upload>
    </div> -->

    <el-table
      v-loading="listLoading"
      :data="tableData"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      @sort-change="sortChange"
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
        label="入库日期"
        width="130px"
        align="center"
      >
        <template slot-scope="scope">
          <!-- scope.row.entered_at | parseTime('{y}-{m}-{d} {h}:{i}') -->
          <span>{{ scope.row.entered_at }}</span>
        </template>
      </el-table-column>

      <el-table-column
        label="产品"
        min-width="130px"
      >
        <template slot-scope="{row}">
          <span
            class="link-type"
            @click="handleUpdate(row)"
          >{{ row.product_name }}</span>
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
          <span
            v-if="row.amount"
            style="color:red;"
          >{{ Number(row.amount) }}</span>
        </template>
      </el-table-column>

      <el-table-column
        label="生产日期"
        width="130px"
        align="center"
      >
        <template slot-scope="{row}">
          <span>{{ row.made_at }}</span>
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
import { enteringWarehousesApi } from '@/api/erp'
import { EnteringWarehouse } from '@/defines/models'
import { recyclableType } from '@/filters'
import { RECYCLABLE_TYPES } from '@/defines/consts'
import DataList from '../mixins/DataList'
import Pagination from '../mixins/Pagination'
import DataFormDialog from './DataFormDialog'

export default {
  name: 'EnteringWarehouses',
  components: { DataFormDialog },
  filters: { recyclableType },
  mixins: [DataList, Pagination],
  data() {
    return {
      api: enteringWarehousesApi,
      recyclable_types: RECYCLABLE_TYPES,
      queryParams: {
        product_name: undefined,
        product_batch: undefined,
        recyclable_type: 'bucket'
      },
      fileList: []
    }
  },
  computed: {
    ...mapState('erp/entering_warehouse', {
      formDialog: state => state.formDialog
    })
  },
  methods: {
    ...mapActions('erp/entering_warehouse', [
      'resetFormDialog',
      'setFormDialog'
    ]),
    sortChange(data) {
      const { prop, order } = data
      if (prop === 'id') {
        this.sortByID(order)
      }
    },
    sortByID(order) {
      if (order === 'ascending') {
        this.queryParams.sort_by = 'id'
        this.queryParams.order = 'asc'
      } else {
        this.queryParams.sort_by = 'id'
        this.queryParams.order = 'desc'
      }
      this.fetchData()
    },
    handleCreate() {
      this.setFormDialog({
        action: 'create',
        formData: EnteringWarehouse(),
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
    },
    submitUpload() {
      this.$refs.upload.submit()
    },
    beforeUpload(file) {
      console.log(file)
    },
    async handleUpload() {
      // todo 自定义上传
      this.unimplemented()
      return true
    },
    onUploadSuccess(response, file, fileList) {
      this.$refs.upload.clearFiles() // 清空已上传的文件列表
    }
  }
}
</script>
