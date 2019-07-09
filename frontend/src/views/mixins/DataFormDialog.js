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
    //  formDialog: state => state.formDialog
    // })
  },

  methods: {
    // ...mapActions('some/nested/module', [
    //  'updateFormDialog'
    // ]),
    async create() {
      this.$refs['data_form'].validate(async valid => {
        if (valid) {
          const formData = this.formDialog.formData
          formData.user_id = this.user.id
          const response = await this.api.store(formData)
          await this.updateFormDialog({
            formData: response.data
          })
          this.done()
        }
      })
    },
    async update() {
      this.$refs['data_form'].validate(async valid => {
        if (valid) {
          const { formData } = this.formDialog
          formData.modified_user_id = this.user.id
          const response = await this.api.update(formData.id, formData)
          await this.updateFormDialog({
            formData: response.data
          })
          this.done()
        }
      })
    },
    done() {
      const { formData, index } = this.formDialog
      this.$emit('action-done', deepClone(formData), index)
      this.$notify({
        title: '成功',
        message: '操作成功',
        type: 'success',
        duration: 2000
      })
      this.close()
    },
    close() {
      this.updateFormDialog({
        visible: false
      })
    }
  }
}
