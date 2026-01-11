<template>
    <div id="app-wrapper">
        <GlobalLoader />
        
        <router-view></router-view>
    </div>
</template>

<script>
// Kita tidak perlu import GlobalLoader di sini jika sudah diregister 
// secara global di app.js (Lihat langkah 2).
export default {
    name: 'App'
}
</script>