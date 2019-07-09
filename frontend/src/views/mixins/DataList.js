export default {
  data() {
    return {
      api: undefined,
      tableData: [],
      listLoading: false,
      queryParams: {
        q: '',
        sort_by: 'id',
        order: 'desc'
      }
    }
  },

  computed: {
    // 实现者必须实现以下代码
    // ...mapState('some/nested/module', {
    //  formDialog: state => state.formDialog
    // })
  },

  created() {
    this.fetchData()
  },

  methods: {
    // ...mapActions('some/nested/module', [
    //  'resetFormDialog',
    // ]),
    async fetchData() {
      this.listLoading = true
      const response = await this.api.list({ params: this.queryParams })
      const { data } = response
      this.tableData = data
      this.paginate(response)
      this.listLoading = false
    },
    paginate(response) {
      // Pagination 中实现
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
        // const index = this.tableData.indexOf(row)
        this.tableData.splice(scope.$index, 1)
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
    handleUpdate(scope) {
      // 具体的实现
      this.unimplemented()
    },
    handleDownload() {
      this.unimplemented()
    },
    actionDone(obj, index) {
      if (this.formDialog.action === 'create') {
        this.tableData.unshift(obj)
      } else if (this.formDialog.action === 'update') {
        this.tableData.splice(index, 1, obj)
      }
      this.resetFormDialog()
    },
    unimplemented() {
      this.$message({
        showClose: true,
        message: '还未实现此功能'
      })
    }
  }
}
