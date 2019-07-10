<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input
        v-model="queryParams.q"
        placeholder="关键字"
        style="width: 200px;"
        class="filter-item"
        @keyup.enter.native="fetchData"
      />
      <el-button
        class="filter-item"
        type="primary"
        icon="el-icon-search"
        @click="fetchData"
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
      style="width: 100%"
    >
      <el-table-column
        align="center"
        label="序号"
        width="80"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>

      <el-table-column
        min-width="150px"
        label="Name"
      >
        <template slot-scope="scope">
          <el-input
            v-if="scope.row._is_edit"
            v-model="scope.row.name"
            class="edit-input"
            size="small"
          />
          <span v-else>{{ scope.row.name }}</span>
        </template>
      </el-table-column>

      <el-table-column
        min-width="200px"
        label="Email"
      >
        <template slot-scope="scope">
          <el-input
            v-if="scope.row._is_edit"
            v-model="scope.row.email"
            class="edit-input"
            size="small"
          />
          <span v-else>{{ scope.row.email }}</span>
        </template>
      </el-table-column>

      <el-table-column
        min-width="200px"
        label="Phone"
      >
        <template slot-scope="scope">
          <el-input
            v-if="scope.row._is_edit"
            v-model="scope.row.phone"
            class="edit-input"
            size="small"
          />
          <span v-else>{{ scope.row.phone }}</span>
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
  </div>
</template>

<script>
import { usersApi } from '@/api/user'
import { User } from '@/defines/models'
import InlineCrud from '../mixins/InlineCrud'
import Pagination from '../mixins/Pagination'

export default {
  name: 'Roles',
  mixins: [InlineCrud, Pagination],
  data() {
    return {
      api: usersApi
    }
  },

  methods: {
    newObj() {
      return User()
    },
    validateForm(row) {
      if (!row.name || !row.email) {
        this.$message({
          message: 'Name or Email required',
          type: 'error'
        })
        return false
      }
      return true
    }
  }
}
</script>
