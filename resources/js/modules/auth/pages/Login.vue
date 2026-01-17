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
          />
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
          />
        </div>

        <div>
          <button
            type="submit"
            class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            Sign in
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import AppInput from "@/core/components/AppInput.vue";

const router = useRouter();

const form = ref({
  username: "",
  password: "",
});

const handleLogin = async () => {
  try {
    const response = await window.axios.post("/api/login", form.value);

    localStorage.setItem("token", response.data.result.access_token);
    localStorage.setItem(
      "user_roles",
      JSON.stringify(response.data.result.user.roles)
    );
    localStorage.setItem(
      "identity_number",
      response.data.result.user.identity_number
    );

    window.axios.defaults.headers.common[
      "Authorization"
    ] = `Bearer ${response.data.result.access_token}`;

    const responseProfile = await window.axios.get("/api/user/profile");
    const userData = responseProfile.data.result.user;
    localStorage.setItem("user_roles", JSON.stringify(userData.roles));

    router.push("/app/dashboard");
  } catch (error) {
    const message =
      error.response?.data?.message || "Terjadi kesalahan saat login";
    console.error("Login failed:", message);
  }
};
</script>