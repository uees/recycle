export default {
  data() {
    return {
      api: undefined,
      tableData: [],
      listLoading: false,
      updateIndex: -1,
      queryParams: {
        q: ''
      }
    }
  },

  computed: {
    // 实现者必须实现以下代码
    // ...mapState('some/nested/module', {
    //  action: state => state.action,
    //  obj: state => state.obj
    // })
  },

  created() {
    this.fetchData()
  },

  methods: {
    async fetchData() {
      this.listLoading = true
      const response = await this.api.list({ params: this.queryParams })
      const { data } = response
      this.tableData = data
      this.pagination(response)
      this.listLoading = false
    },
    pagination(response) {
      // pagination 中实现
    },
    handleSearch() {
      this.fetchData()
    },
    handleDelete(row) {
      this.$confirm('此操作将永久删除该条目, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(async () => {
        await this.api.delete(row.id)
        const index = this.tableData.indexOf(row)
        this.tableData.splice(index, 1)
        this.$message({
          type: 'success',
          message: '删除成功!'
        })
      })
    },
    handleCreate() {
      // 具体的实现
      this.unimplemented()
    },
    handleUpdate(row) {
      // 具体的实现
      this.unimplemented()
    },
    handleDownload() {
      this.unimplemented()
    },
    createDone() {
      this.tableData.unshift(this.obj)
    },
    updateDone() {
      this.tableData.splice(this.updateIndex, 1, this.obj)
    },
    unimplemented() {
      this.$message({
        showClose: true,
        message: '还未实现此功能'
      })
    }
  }
}
