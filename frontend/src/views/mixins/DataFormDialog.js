import { mapGetters } from 'vuex'

export default {
  data() {
    return {
      api: undefined,
      titleMap: {
        update: '编辑',
        create: '创建'
      }
    }
  },

  computed: {
    ...mapGetters([
      'user'
    ])
    // 实现者必须实现以下代码
    // ...mapState('some/nested/module', {
    //  action: state => state.action,
    //  obj: state => state.obj,
    //  dialogVisible: state.dialogVisible,
    // })
  },

  methods: {
    newObj() { // createObj 需要继承者实现
      return {}
    },
    create() {
      this.$refs['obj_form'].validate(async valid => {
        if (valid) {
          this.obj.user_id = this.user.id
          const response = await this.api.store(this.obj)
          const { data } = response
          this.obj = data // 灰常重要！！！
          this.done()
        } else {
          return false
        }
      })
    },
    update() {
      this.$refs['obj_form'].validate(async valid => {
        if (valid) {
          this.obj.modified_user_id = this.user.id
          const response = await this.api.update(this.obj.id, this.obj)
          const { data } = response.data
          this.obj = data // 灰常重要！！！
          this.done()
        } else {
          return false
        }
      })
    },
    done() {
      this.$emit('done')
      this.$notify({
        title: '成功',
        message: '操作成功',
        type: 'success',
        duration: 2000
      })
      this.close()
    },
    close() {
      // this.$store.dispatch('some/nested/module/colse')
    }
  }
}
