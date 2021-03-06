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
          label="可回收类型"
          prop="recyclable_type"
        >
          <el-select v-model="formData.recyclable_type">
            <el-option
              v-for="item in recyclable_types"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            />
          </el-select>
        </el-form-item>

        <el-form-item
          label="可回收数量"
          prop="amount"
        >
          <el-input v-model="formData.amount" />
        </el-form-item>

        <el-form-item
          label="客户"
          prop="customer_id"
        >
          <el-select
            v-model="formData.customer_id"
            filterable
            clearable
            allow-create
            remote
            default-first-option
            placeholder="请输入关键词"
            :remote-method="loadCustomers"
            :loading="customers.loading"
          >
            <el-option
              v-for="customer in customerOptions"
              :key="customer.id"
              :label="customer.name"
              :value="customer.id"
            />
          </el-select>
        </el-form-item>

        <el-form-item
          label="产品"
          prop="product_name"
        >
          <el-input v-model="formData.product_name" />
        </el-form-item>

        <el-form-item
          label="批号"
          prop="product_batch"
        >
          <el-input v-model="formData.product_batch" />
        </el-form-item>

        <el-form-item
          label="规格"
          prop="spec"
        >
          <el-select
            v-model="formData.spec"
            placeholder="规格"
            prop="spec"
          >
            <el-option
              v-for="item in specs"
              :key="item.key"
              :label="item.display_name"
              :value="item.key"
            />
          </el-select>
        </el-form-item>

        <el-form-item
          label="重量"
          prop="weight"
        >
          <el-input v-model="formData.weight" />
        </el-form-item>

        <el-form-item
          label="发货日期"
          prop="made_at"
        >
          <el-tooltip
            class="item"
            effect="dark"
            content="留空表示当前日期"
            placement="top"
          >
            <el-date-picker
              v-model="formData.created_at"
              align="right"
              type="date"
              value-format="yyyy-MM-dd HH:mm:ss"
              placeholder="选择日期"
            />
          </el-tooltip>
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
import { shipmentsApi } from '@/api/erp'
import { specs, RECYCLABLE_TYPES } from '@/defines/consts'
import DataFormDialog from '../mixins/DataFormDialog'

export default {
  name: 'DataForm',
  mixins: [
    DataFormDialog
  ],
  data() {
    return {
      api: shipmentsApi,
      specs: specs,
      recyclable_types: RECYCLABLE_TYPES,
      dataRules: {
        customer_id: { required: true, message: '必填项', trigger: 'blur' },
        product_name: { required: true, message: '必填项', trigger: 'blur' },
        product_batch: { required: true, message: '必填项', trigger: 'blur' },
        weight: { required: true, message: '必填项', trigger: 'blur' },
        spec: { required: true, message: '必填项', trigger: 'blur' }
      }
    }
  },
  computed: {
    ...mapState('erp/shipment', {
      formDialog: state => state.formDialog
    }),
    ...mapState('erp/basedata', [
      'customers'
    ]),
    action() {
      return this.formDialog.action
    },
    formData() {
      return this.formDialog.formData
    },
    visible() {
      return this.formDialog.visible
    },
    customerOptions() {
      if (this.customers.data.length === 0 && this.formData.customer) {
        // 以免编辑时只显示数字
        const customer = this.formData.customer.data
        return this.customers.data.concat([customer])
      }

      return this.customers.data
    }
  },
  methods: {
    ...mapActions('erp/shipment', [
      'updateFormDialog'
    ]),
    ...mapActions('erp/basedata', [
      'loadCustomers'
    ])
  }
}
</script>
