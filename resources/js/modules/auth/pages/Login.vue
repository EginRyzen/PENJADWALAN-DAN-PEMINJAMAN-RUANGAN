<template>
  <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
      <img
        src="@/core/assets/logo.png"
        alt="Your Company"
        class="mx-auto h-32 w-auto"
      />
      <h2 class="text-center text-2xl/9 font-bold tracking-tight text-gray-900">
        Sign in to your account
      </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form @submit.prevent="handleLogin" class="space-y-6">
        <div>
          <AppInput
            id="username"
            name="username"
            type="text"
            label="Username"
            placeholder="Masukkan username Anda"
            v-model="form.username"
            :required="true"
            :markRequiredRight="true"
            :error="!!errors.username"
          >
            <template #error-message>
              {{ errors.username }}
            </template>
          </AppInput>
        </div>

        <div>
          <AppInput
            id="password"
            name="password"
            type="password"
            label="Password"
            placeholder="Masukkan password"
            v-model="form.password"
            :required="true"
            :markRequiredRight="true"
            :error="!!errors.password"
          >
            <template #error-message>
              {{ errors.password }}
            </template>
          </AppInput>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:bg-indigo-300"
          >
            <span v-if="loading">Signing in...</span>
            <span v-else>Sign in</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import DISPATCH from "@/core/plugins/constants/dispatches";
import AppInput from "@/core/components/AppInput.vue";

export default {
  components: {
    AppInput,
  },
  data() {
    return {
      form: {
        username: "",
        password: "",
      },
      errors: {
        username: "",
        password: "",
      },
      loading: false,
    };
  },
  methods: {
    validateForm() {
      let isValid = true;
      this.errors = { username: "", password: "" };

      if (!this.form.username) {
        this.errors.username = "Username wajib diisi";
        isValid = false;
      }
      if (!this.form.password) {
        this.errors.password = "Password wajib diisi";
        isValid = false;
      }
      return isValid;
    },
    async handleLogin() {
      if (!this.validateForm()) return;

      this.loading = true;
      try {
        await this.$store.dispatch(DISPATCH.LOGIN, this.form);
        this.$router.push({ name: "dashboard" });
      } catch (error) {
        const message = error.response?.data?.message || "Username atau password salah";
        
        this.errors.username = message;
        this.errors.password = " ";
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>