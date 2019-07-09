<template>
  <div class="data-form-dialog">
    <el-dialog
      :title="titleMap[action]"
      :visible="visible"
      @close="close"
    >
      <el-form
        ref="data_form"
        :model="formData"
        :rules="dataRules"
        label-position="right"
        label-width="100px"
        style="width: 400px; margin-left:50px;"
      >

        <el-form-item
          label="公司名称"
          prop="name"
        >
          <el-input v-model="formData.name" />
        </el-form-item>

        <el-form-item label="公司地址">
          <el-input v-model="formData.address" />
        </el-form-item>

        <el-form-item label="业务员">
          <el-input v-model="formData.salesman" />
        </el-form-item>
      </el-form>

      <div
        slot="footer"
        class="dialog-footer"
      >
        <el-button @click="close">取 消</el-button>
        <el-button
          type="primary"
          @click="action==='create'?create():update()"
        >确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import DataFormDialog from '../mixins/DataFormDialog'
import { customersApi } from '@/api/erp'

export default {
  name: 'DataForm',
  mixins: [
    DataFormDialog
  ],
  data() {
    return {
      api: customersApi,
      dataRules: {
        name: { required: true, message: '必填项', trigger: 'blur' }
      }
    }
  },
  computed: {
    ...mapState('erp/customers', {
      formDialog: state => state.formDialog
    }),
    action() {
      return this.formDialog.action
    },
    formData() {
      return this.formDialog.formData
    },
    visible() {
      return this.formDialog.visible
    }
  },
  methods: {
    ...mapActions('erp/customers', [
      'updateFormDialog'
    ])
  }
}
</script>
