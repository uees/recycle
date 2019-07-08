<template>
  <div class="data-form-dialog">
    <el-dialog
      :title="dialogTitleMap[action]"
      :visible.sync="dialogFormVisible"
      @close="close"
    >
      <el-form
        ref="obj_form"
        :model="obj"
        :rules="objRules"
        label-position="left"
        label-width="70px"
        style="width: 400px; margin-left:50px;"
      >

        <!--prop 属性设置为需校验的字段名-->
        <el-form-item
          label="产品"
          prop="product_name"
        >
          <el-input v-model="obj.product_name" />
        </el-form-item>

        <el-form-item
          label="批号"
          prop="product_batch"
        >
          <el-input v-model="obj.product_batch" />
        </el-form-item>

        <el-form-item
          label="规格"
          prop="spec"
        >
          <el-input v-model="obj.spec" />
        </el-form-item>

        <el-form-item
          label="重量"
          prop="weight"
        >
          <el-input v-model="obj.weight" />
        </el-form-item>

        <el-form-item label="生产日期">
          <el-date-picker
            v-model="obj.made_at"
            align="right"
            type="date"
            placeholder="选择日期"
          />
        </el-form-item>

        <el-form-item label="入库日期">
          <el-date-picker
            v-model="obj.entered_at"
            align="right"
            type="date"
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
import dialog from '../mixins/dialog'
import { enteringWarehousesApi } from '@/api/erp'

export function newObj() {
  return {
    id: 0,
    product_name: '',
    product_batch: '',
    spec: '',
    weight: null,
    amount: null,
    entered_at: null,
    made_at: null,
    created_at: null,
    updated_at: null
  }
}

export default {
  name: 'DataForm',
  mixins: [
    dialog
  ],
  data() {
    return {
      api: enteringWarehousesApi,
      objRules: {
        name: { required: true, message: '必填项', trigger: 'blur' },
        slug: { required: true, message: '必填项', trigger: 'blur' }
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
  methods: {
    newObj() {
      return newObj()
    },
    update() {
      this.$refs['obj_form'].validate(valid => {
        if (valid) {
          const postData = Object.assign({}, this.obj, { with: 'testWay' })
          this.api.update(this.obj.id, postData).then(response => {
            const { data } = response.data
            this.obj = data
            this.done()
          })
        } else {
          return false
        }
      })
    }
  }
}
</script>
