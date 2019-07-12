<template>
  <div class="dashboard-container">
    <div class="dashboard-text">你好: {{ user.name }}。此页面查看回收统计表：</div>

    <el-table
            :data="tableData"
            border
            style="width: 100%">
      <el-table-column
              prop="date"
              label="年份"
              width="100">
      </el-table-column>
      <el-table-column
              prop="month"
              label="月份"
              width="100">
      </el-table-column>
      <el-table-column
              prop="recyclable_type"
              label="可回收类型"
              width="120">
      </el-table-column>
      <el-table-column
              prop="entering_warehouse_amount"
              label="生产用量">
      </el-table-column>
      <el-table-column
              prop="shipment_amount"
              label="发货量">
      </el-table-column>
      <el-table-column
              prop="recycled_amount"
              label="回收量">
      </el-table-column>
      <el-table-column
              prop="bad_amount"
              label="回收不良量">
      </el-table-column>
      <el-table-column
              prop="good_amount"
              label="有效回收量">
      </el-table-column>
    </el-table>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'Dashboard',
  computed: {
    ...mapGetters([
      'user'
    ])
  },
  data() {
    return {
      tableData: [],
      listLoading: false
    }
  },
  methods: {
    async fetchData() {
      this.listLoading = true
      const response = await this.api.list({ params: this.queryParams })
      const { data } = response
      this.tableData = data
      this.paginate(response)
      this.listLoading = false
    },
  }
}
</script>

<style lang="scss" scoped>
.dashboard {
  &-container {
    margin: 30px;
  }
  &-text {
    font-size: 30px;
    line-height: 46px;
    margin-bottom: 36px;
  }
}
</style>
