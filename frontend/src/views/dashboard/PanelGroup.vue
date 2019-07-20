<template>
  <el-row
    :gutter="40"
    class="panel-group"
  >
    <el-col
      :xs="24"
      :sm="12"
      :lg="6"
      class="card-panel-col"
    >
      <div class="card-panel">
        <div class="card-panel-icon-wrapper icon-people">
          <svg-icon
            icon-class="list"
            class-name="card-panel-icon"
          />
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">生产用量</div>
          <div class="card-panel-num">{{ sum.entering_warehouse_amount }}</div>
        </div>
      </div>
    </el-col>

    <el-col
      :xs="24"
      :sm="12"
      :lg="6"
      class="card-panel-col"
    >
      <div class="card-panel">
        <div class="card-panel-icon-wrapper icon-message">
          <svg-icon
            icon-class="list"
            class-name="card-panel-icon"
          />
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">发货量</div>
          <div class="card-panel-num">{{ sum.shipment_amount }}</div>
        </div>
      </div>
    </el-col>

    <el-col
      :xs="24"
      :sm="12"
      :lg="6"
      class="card-panel-col"
    >
      <div class="card-panel">
        <div class="card-panel-icon-wrapper icon-money">
          <svg-icon
            icon-class="list"
            class-name="card-panel-icon"
          />
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">回收量</div>
          <div class="card-panel-num">{{ sum.recycled_amount }}</div>
          <span style="color: green">回收率: {{ recycledRate }}%</span>
        </div>
      </div>
    </el-col>

    <el-col
      :xs="24"
      :sm="12"
      :lg="6"
      class="card-panel-col"
    >
      <div class="card-panel">
        <div class="card-panel-icon-wrapper icon-shopping">
          <svg-icon
            icon-class="bug"
            class-name="card-panel-icon"
          />
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">回收不良量</div>
          <div class="card-panel-num">{{ sum.bad_amount }}</div>
          <span style="color: #c26a3e">不良率: {{ badRate }}%</span>
        </div>
      </div>
    </el-col>
  </el-row>
</template>

<script>
export default {
  props: {
    totalStatistics: {
      type: Array,
      required: true
    },
    recyclableType: {
      type: String,
      required: true,
      default: ''
    }
  },
  data() {
    return {
    }
  },
  computed: {
    sum() {
      const result = {
        entering_warehouse_amount: 0,
        shipment_amount: 0,
        recycled_amount: 0,
        bad_amount: 0
      }

      let recyclable_type

      if (!this.recyclableType) {
        recyclable_type = 'bucket'
      } else {
        recyclable_type = this.recyclableType
      }

      const has_customer = this.totalStatistics.some(statistics => Boolean(statistics.customer_id))
      const no_customer = this.totalStatistics.some(statistics => Boolean(!statistics.customer_id))

      for (const statistics of this.totalStatistics) {
        if (has_customer && no_customer) {
          if (statistics.recyclable_type === recyclable_type && !statistics.customer_id) {
            for (const key of Object.keys(result)) {
              result[key] += +statistics[key]
            }
          }
        } else {
          if (statistics.recyclable_type === recyclable_type) {
            for (const key of Object.keys(result)) {
              result[key] += +statistics[key]
            }
          }
        }
      }

      return result
    },
    badRate() {
      if (this.sum.recycled_amount === 0) return 0
      const rate = this.sum.bad_amount / this.sum.recycled_amount * 100
      return +(rate.toFixed(2))
    },
    recycledRate() {
      if (this.sum.entering_warehouse_amount === 0) return 100
      const rate = this.sum.recycled_amount / this.sum.entering_warehouse_amount * 100
      return +(rate.toFixed(2))
    }
  },
  methods: {
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.panel-group {
  margin-top: 18px;
  .card-panel-col {
    margin-bottom: 32px;
  }
  .card-panel {
    height: 108px;
    cursor: pointer;
    font-size: 12px;
    position: relative;
    overflow: hidden;
    color: #666;
    background: #fff;
    box-shadow: 4px 4px 40px rgba(0, 0, 0, 0.05);
    border-color: rgba(0, 0, 0, 0.05);
    &:hover {
      .card-panel-icon-wrapper {
        color: #fff;
      }
      .icon-people {
        background: #40c9c6;
      }
      .icon-message {
        background: #36a3f7;
      }
      .icon-money {
        background: #f4516c;
      }
      .icon-shopping {
        background: #34bfa3;
      }
    }
    .icon-people {
      color: #40c9c6;
    }
    .icon-message {
      color: #36a3f7;
    }
    .icon-money {
      color: #f4516c;
    }
    .icon-shopping {
      color: #34bfa3;
    }
    .card-panel-icon-wrapper {
      float: left;
      margin: 14px 0 0 14px;
      padding: 16px;
      transition: all 0.38s ease-out;
      border-radius: 6px;
    }
    .card-panel-icon {
      float: left;
      font-size: 48px;
    }
    .card-panel-description {
      float: right;
      font-weight: bold;
      margin: 26px;
      margin-left: 0;
      .card-panel-text {
        line-height: 18px;
        color: rgba(0, 0, 0, 0.45);
        font-size: 16px;
        margin-bottom: 12px;
      }
      .card-panel-num {
        font-size: 20px;
      }
    }
  }
}
</style>
