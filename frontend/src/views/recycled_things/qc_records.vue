<template>
  <div class="qcrecords-dialog">
    <el-dialog
      title="检测"
      :visible="visible"
      @close="close"
    >
      <div class="filter-container">
        <el-button
          class="filter-item"
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
        style="width: 100%"
      >
        <el-table-column
          min-width="100px"
          label="不合格数量"
        >
          <template slot-scope="scope">
            <el-input
              v-if="scope.row._is_edit"
              v-model="scope.row.bad_amount"
              class="edit-input"
              size="small"
            />
            <span v-else>{{ scope.row.bad_amount }}</span>
          </template>
        </el-table-column>

        <el-table-column
          min-width="100px"
          label="检查人"
        >
          <template slot-scope="scope">
            <el-select
              v-if="scope.row._is_edit"
              v-model="scope.row.type"
              class="edit-input"
              size="small"
            >
              <el-option v-for="item in qc_types"
                         :key="item.value"
                         :label="item.label"
                         :value="item.value"
              />
            </el-select>
            <span v-else>{{ scope.row.type | qc_type }}</span>
          </template>
        </el-table-column>

        <el-table-column
          align="center"
          label="操作"
          width="220"
        >
          <template slot-scope="scope">
            <template v-if="scope.row._is_edit">
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
                type="primary"
                size="small"
                icon="el-icon-edit"
                @click="handleUpdate(scope)"
              >Edit</el-button>
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
        slot="footer"
        class="dialog-footer"
      >
        <el-button @click="close">关闭</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import { qcRecordsApi } from '@/api/erp'
import { QcRecord } from '@/defines/models'
import { QC_TYPES } from '@/defines/consts'
import InlineCrud from '../mixins/InlineCrud'

export default {
  name: 'QcRecords',
  filters: {
    qc_type(val) {
      const el = QC_TYPES.find(el => {
        return el.value === val
      })

      return el.label
    }
  },
  mixins: [
    InlineCrud
  ],
  data() {
    return {
      api: qcRecordsApi,
      qc_types: QC_TYPES,
      queryParams: {
        all: true,
        recycled_thing_id: undefined
      }
    }
  },
  computed: {
    ...mapState('erp/qc_records', {
      visible: state => state.visible,
      recycled_thing: state => state.recycled_thing,
      index: state => state.index
    })
  },
  watch: {
    visible(val) {
      if (val) {
        this.queryParams.recycled_thing_id = this.recycled_thing.id
        this.fetchData()
      }
    },
  },
  methods: {
    async fetchData() {
      if (this.visible) {  // 可见时才 fetchData
        this.listLoading = true
        const response = await this.api.list({ params: this.queryParams })
        this.tableData = response.data.map(value => {
          this.$set(value, '_is_edit', false)
          this.setOriginal(value)
          return value
        })
        this.paginate(response)
        this.listLoading = false
      }
    },
    newObj() {
      const obj = QcRecord()
      obj.recycled_thing_id = this.recycled_thing.id
      return obj
    },
    validateForm(row) {
      if (!row.bad_amount) {
        this.$message({
          message: '数量必填',
          type: 'error'
        })
        return false
      }
      return true
    },
    close() {
      this.$emit('qc-done', this.tableData, this.index)
      this.$store.commit('erp/qc_records/SET_VISIBLE', false)
    }
  }
}
</script>
