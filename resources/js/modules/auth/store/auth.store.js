import apiUrl from "@/core/plugins/constants/apiUrl";
import actions from "@/core/plugins/constants/actions";
import Api from "@/core/services/Api";
import LOADING_MESSAGE from "@/core/plugins/constants/loadingMessage";

const defaultState = () => ({
    user: null,
    token: localStorage.getItem("token") || null,
    appMenuList: [],
});

export const Store = {
    namespaced: true,
    state: defaultState(),
    mutations: {
        SET_TOKEN(state, token) {
            state.token = token;
            localStorage.setItem("token", token);
        },
        SET_USER(state, user) {
            state.user = user;
        },
        SET_APP_MENU(state, payload) {
            state.appMenuList = payload;
        },
        RESET_AUTH(state) {
            Object.assign(state, defaultState());
            localStorage.removeItem("token");
            localStorage.removeItem("user_roles");
            localStorage.removeItem("identity_number");
            delete Api.defaults.headers.common["Authorization"];
        },
    },
    actions: {
        async [actions.LOGIN]({ commit, dispatch }, payload) {
            commit("SET_LOADING_MESSAGE", LOADING_MESSAGE.LOADING, { root: true });
            commit("SET_LOADING", true, { root: true });
            try {
                const response = await Api.post(apiUrl.LOGIN, payload);

                const result = response.data.result;
                const token = result.token || result.access_token;
                const user = result.user;

                commit("SET_TOKEN", token);
                commit("SET_USER", user);
                localStorage.setItem("user_roles", JSON.stringify(user.roles));
                localStorage.setItem("identity_number", user.identity_number);

                Api.defaults.headers.common["Authorization"] =
                    `Bearer ${token}`;

                await dispatch(actions.GET_USER_PROFILE);

                return response.data;
            } catch (error) {
                console.error("Login Error:", error);
                throw error;
            } finally {
                commit('SET_LOADING', false, { root: true });
            }
        },

        async [actions.GET_USER_PROFILE]({ commit, dispatch }) {
            try {
                const response = await Api.get(apiUrl.USER_PROFILE);
                const userData =
                    response.data.result.user || response.data.result;

                commit("SET_USER", userData);
                localStorage.setItem(
                    "user_roles",
                    JSON.stringify(userData.roles),
                );

                // Fetch app menu after profile is loaded
                await dispatch(actions.GET_APP_MENU);

                return response.data;
            } catch (error) {
                if (error.response?.status === 401) {
                    commit("RESET_AUTH");
                }
                throw error;
            }
        },
        async [actions.GET_APP_MENU]({ commit }) {
            try {
                const response = await Api.get(apiUrl.APP_MENU);
                const data = response.data.result;
                commit("SET_APP_MENU", data);
                return data;
            } catch (error) {
                console.error("Error Fetching App Menu:", error);
                throw error;
            }
        },
    },
};
