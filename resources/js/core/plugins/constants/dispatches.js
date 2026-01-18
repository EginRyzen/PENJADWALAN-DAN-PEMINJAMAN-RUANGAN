import ACTIONS from "@/core/plugins/constants/actions.js";
const namespaces = {
    AUTH: "auth/",
};

export default {
    LOGIN: namespaces.AUTH + ACTIONS.LOGIN,
    LOGOUT: namespaces.AUTH + ACTIONS.LOGOUT,
    GET_USER_PROFILE: namespaces.AUTH + ACTIONS.GET_USER_PROFILE,
};
