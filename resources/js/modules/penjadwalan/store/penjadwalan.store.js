import apiUrl from "@/core/plugins/constants/apiUrl";
import actions from "@/core/plugins/constants/actions";
import Api from "@/core/services/Api";

// ============================================================
// VUEX STORE MODULE — Penjadwalan Ujian
// ============================================================
const penjadwalanStore = {
    namespaced: true,
    state: {
        // Konteks filter (sebelum generate)
        context: {
            type:       'uas',
            start_date: '',
            periode_id: null,
            prodi_id:   '',
            kelas_id:   '',
        },

        // Tab aktif filter prodi di tabel hasil
        activeProdiTab: 'semua',

        // Hasil generate (draft dari backend / CSP)
        draftJadwal: [],

        // Statistik ringkas
        stats: { ok: 0, conflict: 0, edited: 0 },

        // Status proses
        isGenerating:  false,
        isSaving:      false,  // untuk simpan permanen
        isSavingDraft: false,  // untuk simpan draft

        // Metadata draft tersimpan
        lastDraftSavedAt: null,   // timestamp terakhir simpan draft
        isPermanen:       false,  // apakah jadwal sudah permanen (readonly)

        // Master data pendukung (dari API)
        periodeList:  [],
        ruanganList:  [],
        dosenList:    [],
        hariLiburList:[],
    },

    getters: {
        // Daftar prodi yang muncul di tabs (hanya yang ada jadwalnya)
        prodiTabs(state) {
            const prodiMap = {};
            state.draftJadwal.forEach(j => {
                if (!prodiMap[j.prodi_id]) {
                    prodiMap[j.prodi_id] = { id: j.prodi_id, kode: j.prodi_kode, nama: j.prodi_nama, count: 0 };
                }
                prodiMap[j.prodi_id].count++;
            });
            return Object.values(prodiMap);
        },

        // Jadwal tanpa filter store (filter dipindah ke UI component)
        filteredJadwal(state) {
            return state.draftJadwal;
        },

        // Tanggal-tanggal yang disabled di date picker
        disabledDates(state) {
            return state.hariLiburList.map(h => h.tanggal);
        },
    },

    mutations: {
        SET_CONTEXT(state, payload) {
            state.context = { ...state.context, ...payload };
        },
        SET_ACTIVE_PRODI_TAB(state, prodiId) {
            state.activeProdiTab = prodiId;
        },
        SET_DRAFT_JADWAL(state, jadwal) {
            state.draftJadwal = jadwal;
            state.stats = {
                ok:       jadwal.filter(j => j.status === 'ok').length,
                conflict: jadwal.filter(j => j.status === 'conflict').length,
                edited:   jadwal.filter(j => j.status === 'edited').length,
            };
        },
        UPDATE_JADWAL_ROW(state, updatedRow) {
            const idx = state.draftJadwal.findIndex(j => j.id === updatedRow.id);
            if (idx !== -1) {
                state.draftJadwal.splice(idx, 1, { ...state.draftJadwal[idx], ...updatedRow, status: 'edited' });
                state.stats = {
                    ok:       state.draftJadwal.filter(j => j.status === 'ok').length,
                    conflict: state.draftJadwal.filter(j => j.status === 'conflict').length,
                    edited:   state.draftJadwal.filter(j => j.status === 'edited').length,
                };
            }
        },
        SET_PERIODE_LIST(state, list)  { state.periodeList  = list; },
        SET_RUANGAN(state, list)       { state.ruanganList  = list; },
        SET_DOSEN(state, list)         { state.dosenList    = list; },
        SET_HARI_LIBUR(state, list)    { state.hariLiburList= list; },
        SET_GENERATING(state, val)     { state.isGenerating = val; },
        SET_SAVING(state, val)         { state.isSaving     = val; },
        SET_SAVING_DRAFT(state, val)   { state.isSavingDraft= val; },
        SET_DRAFT_META(state, { savedAt }) {
            state.lastDraftSavedAt = savedAt;
        },
        SET_PERMANEN(state, val) {
            state.isPermanen = val;
        },
    },

    actions: {
        // ── Ambil daftar periode dari API ──────────────────────────
        async [actions.GET_PERIODE]({ commit }) {
            try {
                const res = await Api.get(apiUrl.PERIODE, { params: { size: 100 } });
                const allPeriods = res.data.result.content || [];
                // Hanya ambil yang berstatus 'Aktif'
                const activePeriods = allPeriods.filter(p => p.status === 'Aktif');
                commit('SET_PERIODE_LIST', activePeriods);
            } catch (e) {
                console.error('Error fetching periode:', e);
            }
        },

        // ── Ambil ruangan (hanya yg can_ujian) ────────────────────
        async [actions.GET_ROOMS]({ commit }, params) {
            try {
                const res = await Api.get(apiUrl.GET_ROOMS, { params: { ...params, size: 100, can_ujian: 1 } });
                const rooms = res.data.result.content || res.data.result || [];
                const mappedRooms = rooms.map(r => ({
                    ...r,
                    nama: r.room_name || r.nama
                }));
                commit('SET_RUANGAN', mappedRooms);
            } catch (e) {
                console.error('Error fetching rooms:', e);
            }
        },
        // ── Ambil dosen (semua aktif) ────────────────────────────
        async [actions.GET_DOSEN]({ commit }, params) {
            try {
                const res = await Api.get(apiUrl.DOSEN, { 
                    params: { 
                        ...params, 
                        size: params?.search ? 20 : 1000, 
                        status: 'Aktif' 
                    } 
                });
                commit('SET_DOSEN', res.data.result.content || res.data.result || []);
            } catch (e) {
                console.error('Error fetching dosen:', e);
            }
        },

        // ── Ambil hari libur per periode ──────────────────────────
        async [actions.GET_HARI_LIBUR]({ commit }, periodeId) {
            try {
                const res = await Api.get(apiUrl.HARI_LIBUR, {
                    params: { periode_id: periodeId, size: 100 },
                });
                commit('SET_HARI_LIBUR', res.data.result.content || []);
            } catch (e) {
                console.error('Error fetching hari libur:', e);
            }
        },

        // ── Cek & load draft lama ─────────────────────────────────
        async [actions.GET_JADWAL_DRAFT]({ commit, state }) {
            if (!state.context.periode_id || !state.context.type) return null;
            try {
                const res = await Api.get(apiUrl.JADWAL_DRAFT, {
                    params: { periode_id: state.context.periode_id, tipe: state.context.type },
                });
                const data = res.data.result;
                if (data && data.exists) {
                    commit('SET_DRAFT_META', { savedAt: data.saved_at });
                    return data; // { exists, count, saved_at, items }
                }
                return null;
            } catch (e) {
                console.error('Error fetching draft:', e);
                return null;
            }
        },

        // ── Load items draft ke tabel ─────────────────────────────
        loadDraftItems({ commit }, items) {
            commit('SET_DRAFT_JADWAL', items);
        },

        // ── Generate jadwal via CSP backend ───────────────────────
        async [actions.GENERATE_JADWAL]({ commit, state }, payload = null) {
            commit('SET_GENERATING', true);
            try {
                const body = {
                    periode_id: state.context.periode_id,
                    tipe:       state.context.type,
                    start_date: state.context.start_date,
                };
                if (payload) body.jadwal = payload;

                const res = await Api.post(apiUrl.JADWAL_GENERATE, body);
                commit('SET_DRAFT_JADWAL', res.data.result || []);
            } catch (e) {
                console.error('Error generating/validating jadwal:', e);
                throw e;
            } finally {
                commit('SET_GENERATING', false);
            }
        },

        // ── Hapus draft (sebelum generate ulang) ──────────────────
        async [actions.DELETE_JADWAL_DRAFT]({ state }) {
            try {
                await Api.delete(apiUrl.JADWAL_DRAFT, {
                    params: { periode_id: state.context.periode_id, tipe: state.context.type },
                });
            } catch (e) {
                console.error('Error deleting draft:', e);
                throw e;
            }
        },

        // ── Update 1 baris dari edit modal ────────────────────────
        async [actions.UPDATE_JADWAL_ROW]({ commit, state }, row) {
            // Update lokal di store dulu (optimistic)
            commit('UPDATE_JADWAL_ROW', row);

            // Jika row punya id DB nyata (bukan TMP-), update ke backend
            if (row.id && !String(row.id).startsWith('TMP-')) {
                try {
                    await Api.patch(`${apiUrl.JADWAL_DRAFT_ROW}/${row.id}`, {
                        dosen_id:        row.dosen_id,
                        ruangan_id:      row.ruangan_id,
                        tanggal:         row.tanggal,
                        jam_mulai:       row.jam_mulai,
                        jam_selesai:     row.jam_selesai,
                        status_konflik:  'edited',
                        conflict_reason: null,
                    });
                } catch (e) {
                    console.error('Error updating jadwal row:', e);
                }
            }
        },

        // ── Simpan Draft ──────────────────────────────────────────
        async [actions.SAVE_JADWAL_DRAFT]({ commit, state }) {
            commit('SET_SAVING_DRAFT', true);
            try {
                const res = await Api.post(apiUrl.JADWAL_DRAFT, {
                    periode_id: state.context.periode_id,
                    tipe:       state.context.type,
                    jadwal:     state.draftJadwal,
                });
                commit('SET_DRAFT_META', { savedAt: res.data.result.saved_at });
                return res.data.result;
            } catch (e) {
                console.error('Error saving draft:', e);
                throw e;
            } finally {
                commit('SET_SAVING_DRAFT', false);
            }
        },

        // ── Simpan Permanen ───────────────────────────────────────
        async [actions.SAVE_JADWAL_PERMANEN]({ commit, state }) {
            commit('SET_SAVING', true);
            try {
                const res = await Api.patch(apiUrl.JADWAL_PERMANEN, {
                    periode_id: state.context.periode_id,
                    tipe:       state.context.type,
                    jadwal:     state.draftJadwal,
                });
                commit('SET_PERMANEN', true);
                return res.data.result;
            } catch (e) {
                console.error('Error saving permanen:', e);
                throw e;
            } finally {
                commit('SET_SAVING', false);
            }
        },
        async [actions.EXPORT_JADWAL]({ commit, state }, params = {}) {
            try {
                const response = await Api.get(apiUrl.EXPORT_JADWAL, { 
                    params: {
                        periode_id: state.context.periode_id,
                        tipe:       state.context.type,
                        status:     params.status || undefined
                    },
                    responseType: 'blob' 
                });
                return response;
            } catch (error) {
                console.error("Error Exporting Jadwal:", error);
                throw error;
            }
        },
    },
};

export default penjadwalanStore;
