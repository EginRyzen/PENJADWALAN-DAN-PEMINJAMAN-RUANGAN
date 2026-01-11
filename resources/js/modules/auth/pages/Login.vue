<template>
  <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
      <img
        src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=600"
        alt="Your Company"
        class="mx-auto h-10 w-auto"
      />
      <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">
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
import AppInput from '@/core/components/AppInput.vue';

const router = useRouter();

const form = ref({
  username: '',
  password: ''
});

const handleLogin = () => {
  console.log("Login clicked", form.value);
  console.log("Username:", form.value.username);
  console.log("Password:", form.value.password);
  
  if(form.value.username && form.value.password) {
      router.push("/app/dashboard");
  }
};
</script>