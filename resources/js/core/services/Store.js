import { createStore } from 'vuex';

export default createStore({
  state: {
    isLoading: false, 
  },
  mutations: {
    SET_LOADING(state, status) {
      state.isLoading = status;
    }
  },
});