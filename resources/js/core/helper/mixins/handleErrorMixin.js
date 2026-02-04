export default {
  methods: {
    handleError(
      error,
      errorMessages = [],
      hideTitle = false,
      modalBtnText = "Kembali"
    ) {
      this.$store.commit("closeDialog");
      this.$store.commit("setErrorDialog", {
        message: error.response?.data?.message ?? error.message,
        errorMessages: errorMessages || [],
        traceId: error.response ? error.response.headers["x-b3-traceid"] : "",
        hideTitle: hideTitle,
        modalBtnText: modalBtnText,
      });
    },
    handleErrorFieldDialog() {
      this.$store.commit("setErrorDialog", {
        message: "Mohon Lengkapi Data Yang Wajib Diisi",
        hideTitle: true,
      });
    },
  },
};
