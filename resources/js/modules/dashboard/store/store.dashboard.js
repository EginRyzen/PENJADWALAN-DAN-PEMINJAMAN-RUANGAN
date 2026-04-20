import apiUrl from "@/core/plugins/constants/apiUrl";
import actions from "@/core/plugins/constants/actions";
import Api from "@/core/services/Api";

const defaultState = () => ({
    dashboardData: null,
    notifications: [],
    unreadCount: 0,
    notificationPagination: {
        current_page: 1,
        last_page: 1,
        total: 0
    }
});

export const Store = {
    namespaced: true,
    state: defaultState(),
    mutations: {
        SET_DASHBOARD_DATA(state, payload) {
            state.dashboardData = payload;
        },
        SET_NOTIFICATIONS(state, payload) {
            if (payload.current_page === 1) {
                state.notifications = payload.data;
            } else {
                state.notifications = [...state.notifications, ...payload.data];
            }
            state.notificationPagination = {
                current_page: payload.current_page,
                last_page: payload.last_page,
                total: payload.total
            };
        },
        SET_UNREAD_COUNT(state, count) {
            state.unreadCount = count;
        },
        UPDATE_NOTIFICATION_READ_STATUS(state, id) {
            const notif = state.notifications.find(n => n.id === id);
            if (notif && !notif.read_at) {
                notif.read_at = new Date().toISOString();
                state.unreadCount = Math.max(0, state.unreadCount - 1);
            }
        },
        MARK_ALL_AS_READ(state) {
            state.notifications.forEach(n => {
                if (!n.read_at) n.read_at = new Date().toISOString();
            });
            state.unreadCount = 0;
        }
    },
    actions: {
        async [actions.GET_DASHBOARD_DATA]({ commit }) {
            try {
                // Implement dashboard data fetch if needed
                // const response = await Api.get(apiUrl.DASHBOARD_DATA);
                // commit('SET_DASHBOARD_DATA', response.data.result);
            } catch (error) {
                console.error("Error fetching dashboard data:", error);
            }
        },
        async [actions.GET_NOTIFICATIONS]({ commit }, params = {}) {
            try {
                const response = await Api.get(apiUrl.NOTIFICATIONS, { params });
                commit('SET_NOTIFICATIONS', response.data.result);
                return response.data;
            } catch (error) {
                console.error("Error fetching notifications:", error);
                throw error;
            }
        },
        async [actions.GET_UNREAD_NOTIFICATION_COUNT]({ commit }) {
            try {
                const response = await Api.get(apiUrl.NOTIFICATION_UNREAD_COUNT);
                commit('SET_UNREAD_COUNT', response.data.result.count);
                return response.data.result.count;
            } catch (error) {
                console.error("Error fetching unread count:", error);
            }
        },
        async [actions.MARK_NOTIFICATION_READ]({ commit }, id) {
            try {
                await Api.post(`${apiUrl.MARK_NOTIFICATION_READ}/${id}/mark-read`);
                commit('UPDATE_NOTIFICATION_READ_STATUS', id);
            } catch (error) {
                console.error("Error marking notification as read:", error);
            }
        },
        async [actions.MARK_ALL_NOTIFICATIONS_READ]({ commit }) {
            try {
                await Api.post(apiUrl.MARK_ALL_NOTIFICATIONS_READ);
                commit('MARK_ALL_AS_READ');
            } catch (error) {
                console.error("Error marking all as read:", error);
            }
        }
    }
};
