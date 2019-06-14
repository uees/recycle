<template>
  <div>
    <mu-appbar style="width: 100%;" title="登录" color="primary"></mu-appbar>
    <mu-container class="login-container">
      <mu-form ref="form" :model="validateForm" class="login-form">
        <mu-form-item label="邮箱" prop="email" :rules="emailRules">
          <mu-text-field v-model="validateForm.email" prop="email"></mu-text-field>
        </mu-form-item>
        <mu-form-item label="密码" prop="password" :rules="passwordRules">
          <mu-text-field :type="passwordType" v-model="validateForm.password" prop="password"></mu-text-field>
        </mu-form-item>
        <mu-form-item>
          <mu-button
            color="primary"
            @click="submit"
            v-loading="loading"
            data-mu-loading-size="24"
          >登录</mu-button>
        </mu-form-item>
      </mu-form>
    </mu-container>
  </div>
</template>

<script>
import { isEmail } from "@/utils/validate";

export default {
  name: "Login",
  data() {
    return {
      emailRules: [
        { validate: val => !!val, message: "邮箱必须" },
        { validate: val => isEmail(val), message: "邮箱格式错误" }
      ],
      passwordRules: [
        { validate: val => !!val, message: "密码必须" },
        {
          validate: val => val.length >= 4 && val.length <= 16,
          message: "密码长度大于4小于16"
        }
      ],
      validateForm: {
        email: "",
        password: ""
      },
      passwordType: "password",
      loading: false,
      redirect: undefined
    };
  },
  watch: {
    $route: {
      handler: function(route) {
        this.redirect = route.query && route.query.redirect;
      },
      immediate: true
    }
  },
  methods: {
    showPwd() {
      if (this.passwordType === "password") {
        this.passwordType = "text";
      } else {
        this.passwordType = "password";
      }
    },
    async submit() {
      const valid = await this.$refs.form.validate();
      if (valid) {
        this.loading = true;
        try {
          await this.$store.dispatch("user/login", this.validateForm);
          this.loading = false;
          this.$router.push({ path: this.redirect || "/" });
        } catch (err) {
          this.loading = false;
        }
      } else {
        console.log("error submit!!");
        return false;
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.login-form {
  width: 100%;
  max-width: 460px;
  margin-top: 100px;
}
</style>
