<template>
  <div class="bottom-sheet">
    <div v-if="value" class="bottom-sheet-background"></div>
    <div v-if="value" class="bottom-sheet-content">
      <div
        :class="{
          hidden: hiddenHeader,
        }"
        class="bottom-sheet-header"
      >
        <button
          @click="close"
          class="icon-x w-4 h-4 rounded-full hover:bg-black-100"
        ></button>
        <h1 class="text-sm font-weight-600">{{ title }}</h1>
        <button
          class="icon-x w-4 h-4 rounded-full hover:bg-black-100 invisible"
        ></button>
      </div>
      <div class="bottom-sheet-slot">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "BottomSheet",
  props: {
    value: Boolean,
    title: String,
    hiddenHeader: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    close() {
      this.$emit("close");
    },
  },
};
</script>

<style scoped>
.bottom-sheet-background {
  position: fixed;
  left: 50%;
  top: 50%;
  width: 100vw;
  height: 100vh;
  z-index: 91;
  background-color: rgba(0, 0, 0, 0.6);
  transform: translate(-50%, -50%);
}
.bottom-sheet-content {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  border-top-left-radius: 12px;
  border-top-right-radius: 12px;
  z-index: 92;
  background: #ffffff;
  max-height: 75%;
  overflow-y: auto;
}
.bottom-sheet-header {
  position: sticky;
  top: 0;
  display: flex;
  flex-wrap: nowrap;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  border-bottom: 1px;
  border-color: rgb(var(--white-200));
  border-top-left-radius: 12px;
  border-top-right-radius: 12px;
  background: white;
  z-index: 94;
}
.bottom-sheet-slot {
  height: auto;
  overflow-y: scroll;
  position: relative;
  z-index: 93;
}
.slide-enter-to,
.slide-leave {
  bottom: 0;
}
.slide-enter,
.slide-leave-t0 {
  bottom: -50%;
}
.slide-leave-active,
.slide-enter-active {
  transition: bottom 1s;
}
</style>
