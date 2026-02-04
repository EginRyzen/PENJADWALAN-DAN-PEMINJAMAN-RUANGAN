import ACTIONS from "@/core/plugins/constants/actions.js";
const namespaces = {
    AUTH: "auth/",
    GEDUNG: "gedung/",
};

export default {
    LOGIN: namespaces.AUTH + ACTIONS.LOGIN,
    LOGOUT: namespaces.AUTH + ACTIONS.LOGOUT,
    GET_USER_PROFILE: namespaces.AUTH + ACTIONS.GET_USER_PROFILE,
    GET_GEDUNG_FACILITIES: namespaces.GEDUNG + ACTIONS.GET_GEDUNG_FACILITIES,
    SAVE_GEDUNG_DATA: namespaces.GEDUNG + ACTIONS.SAVE_GEDUNG_DATA,
};
