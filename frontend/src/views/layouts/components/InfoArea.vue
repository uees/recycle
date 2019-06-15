<template>
  <mu-container class="info-area">
    <mu-row>
      <mu-col span="3">
        <div class="grid-cell">
          <div class="info-title">2019</div>
          <div class="info-number">6月</div>
        </div>
      </mu-col>
      <mu-col span="3">
        <div class="grid-cell">
          <div class="info-title">客户</div>
          <div class="info-number">ABC</div>
        </div>
      </mu-col>
      <mu-col span="3">
        <div class="grid-cell">
          <div class="info-title">发出</div>
          <div class="info-number">100</div>
        </div>
      </mu-col>
      <mu-col span="3">
        <div class="grid-cell">
          <div class="info-title">回收</div>
          <div class="info-number">30</div>
        </div>
      </mu-col>
    </mu-row>
  </mu-container>
</template>

<script>
export default {
  name: "InfoArea"
};
</script>

<style lang="scss" scoped>
.info-area {
  padding: 20px;
  background-color: #2196f3;
}
</style>
