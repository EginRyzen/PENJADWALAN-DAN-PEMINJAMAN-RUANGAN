import { createStore } from 'vuex';
import { Store as auth } from '@/modules/auth/store/auth.store';
import LOADING_MESSAGE from '@/core/plugins/constants/loadingMessage';

export default createStore({
  state: {
    isLoading: false,
    loadingMessage: LOADING_MESSAGE.LOADING,
  },
  mutations: {
    SET_LOADING(state, status) {
      state.isLoading = status;
      if (!status) {
        state.loadingMessage = LOADING_MESSAGE.LOADING;
      }
    },
    SET_LOADING_MESSAGE(state, message) {
      state.loadingMessage = message;
    }
  },
  modules: {
    auth,
  }
});