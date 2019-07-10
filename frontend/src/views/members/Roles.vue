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
        min-width="300px"
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
        min-width="300px"
        label="Display Name"
      >
        <template slot-scope="scope">
          <template v-if="scope.row._is_edit">
            <el-input
              v-model="scope.row.display_name"
              class="edit-input"
              size="small"
            />
          </template>
          <span v-else>{{ scope.row.display_name }}</span>
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
  </div>
</template>

<script>
import { rolesApi } from '@/api/user'
import { Role } from '@/defines/models'
import InlineCrud from '../mixins/InlineCrud'

export default {
  name: 'Roles',
  mixins: [InlineCrud],
  data() {
    return {
      api: rolesApi,
      queryParams: {
        all: true
      }
    }
  },

  methods: {
    newObj() {
      return Role()
    },
    validateForm(row) {
      if (!row.name || !row.display_name) {
        this.$message({
          message: 'Name or Display Name required',
          type: 'error'
        })
        return false
      }
      return true
    }
  }
}
</script>
