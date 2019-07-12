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

        <!--prop 属性设置为需校验的字段名-->
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
          label="生产日期"
          prop="made_at"
        >
          <el-date-picker
            v-model="formData.made_at"
            align="right"
            type="date"
            value-format="yyyy-MM-dd HH:mm:ss"
            placeholder="选择日期"
          />
        </el-form-item>

        <el-form-item label="入库日期">
          <el-date-picker
            v-model="formData.entered_at"
            align="right"
            type="date"
            value-format="yyyy-MM-dd HH:mm:ss"
            placeholder="选择日期"
            :picker-options="pickerOptions"
          />
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
import { enteringWarehousesApi } from '@/api/erp'
import { specs } from '@/defines/consts'
import DataFormDialog from '../mixins/DataFormDialog'

export default {
  name: 'DataForm',
  mixins: [
    DataFormDialog
  ],
  data() {
    return {
      api: enteringWarehousesApi,
      specs: specs,
      dataRules: {
        product_name: { required: true, message: '必填项', trigger: 'blur' },
        product_batch: { required: true, message: '必填项', trigger: 'blur' },
        weight: { required: true, message: '必填项', trigger: 'blur' },
        spec: { required: true, message: '必填项', trigger: 'blur' },
        made_at: { required: true, message: '必填项', trigger: 'blur' }
      },
      pickerOptions: {
        disabledDate(time) {
          return time.getTime() > Date.now()
        },
        shortcuts: [{
          text: '今天',
          onClick(picker) {
            picker.$emit('pick', new Date())
          }
        }, {
          text: '昨天',
          onClick(picker) {
            const date = new Date()
            date.setTime(date.getTime() - 3600 * 1000 * 24)
            picker.$emit('pick', date)
          }
        }, {
          text: '一周前',
          onClick(picker) {
            const date = new Date()
            date.setTime(date.getTime() - 3600 * 1000 * 24 * 7)
            picker.$emit('pick', date)
          }
        }]
      }
    }
  },
  computed: {
    ...mapState('erp/entering_warehouse', {
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
    ...mapActions('erp/entering_warehouse', [
      'updateFormDialog'
    ])
  }
}
</script>
