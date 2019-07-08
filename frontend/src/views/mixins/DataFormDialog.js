import { mapGetters } from 'vuex'
import { deepClone } from '@/utils'

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
    // ...mapState('some/nested/module', {
    //  action: state => state.action,
    //  obj: state => state.obj,
    //  dialogVisible: state => state.dialogVisible
    // })
  },

  methods: {
    // ...mapActions('some/nested/module', [
    //  'doAction',
    //  'resetObj',
    //  'setObj',
    //  'close'
    // ]),
    async create() {
      this.$refs['obj_form'].validate(async valid => {
        if (valid) {
          this.obj.user_id = this.user.id
          const response = await this.api.store(this.obj)
          const { data } = response
          await this.setObj(data)
          this.done()
        }
      })
    },
    async update() {
      this.$refs['obj_form'].validate(async valid => {
        if (valid) {
          this.obj.modified_user_id = this.user.id
          const response = await this.api.update(this.obj.id, this.obj)
          const { data } = response.data
          await this.setObj(data)
          this.done()
        }
      })
    },
    done() {
      this.$emit('action-done', deepClone(this.obj))
      this.$notify({
        title: '成功',
        message: '操作成功',
        type: 'success',
        duration: 2000
      })
      this.close()
    }
  }
}
