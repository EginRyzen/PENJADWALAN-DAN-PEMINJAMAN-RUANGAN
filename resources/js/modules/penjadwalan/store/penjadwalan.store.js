import apiUrl from "@/core/plugins/constants/apiUrl";
import actions from "@/core/plugins/constants/actions";
import Api from "@/core/services/Api";

// ============================================================
// MOCK DATA — akan diganti dengan API calls nanti
// ============================================================
const MOCK_PRODI = [
    { id: 1, kode: 'TIF', nama: 'Teknik Informatika' },
    { id: 2, kode: 'SI',  nama: 'Sistem Informasi' },
    { id: 3, kode: 'TK',  nama: 'Teknik Komputer' },
    { id: 4, kode: 'AKT', nama: 'Akuntansi' },
    { id: 5, kode: 'MGT', nama: 'Manajemen' },
    { id: 6, kode: 'HKM', nama: 'Hukum' },
    { id: 7, kode: 'PSI', nama: 'Psikologi' },
    { id: 8, kode: 'PBI', nama: 'Pendidikan Bahasa Inggris' },
];

const MOCK_KELAS = {
    1: ['A', 'B', 'C'],
    2: ['A', 'B'],
    3: ['A', 'B', 'C', 'D'],
    4: ['A', 'B'],
    5: ['A', 'B', 'C'],
    6: ['A', 'B'],
    7: ['A'],
    8: ['A', 'B'],
};

const MOCK_RUANGAN = [
    { id: 1, nama: 'R.101', kapasitas: 40 },
    { id: 2, nama: 'R.102', kapasitas: 40 },
    { id: 3, nama: 'R.201', kapasitas: 50 },
    { id: 4, nama: 'R.202', kapasitas: 50 },
    { id: 5, nama: 'Aula A', kapasitas: 120 },
    { id: 6, nama: 'Lab. Komputer 1', kapasitas: 35 },
];

const MOCK_DOSEN = [
    { id: 1, nama: 'Dr. Budi Santoso, M.Kom' },
    { id: 2, nama: 'Dr. Sari Dewi, M.T' },
    { id: 3, nama: 'Prof. Ahmad Fauzi, Ph.D' },
    { id: 4, nama: 'Dr. Lisa Permata, M.Si' },
    { id: 5, nama: 'Ir. Hendra Wijaya, M.T' },
    { id: 6, nama: 'Dr. Ratna Sari, M.Kom' },
];

const MOCK_JADWAL = [
    { id: 'TMP-001', mk_kode: 'IF001', mk_nama: 'Basis Data', sks: 3, prodi_id: 1, prodi_kode: 'TIF', prodi_nama: 'Teknik Informatika', kelas: 'A', tanggal: '2025-05-12', hari: 'Senin', jam_mulai: '08:00', jam_selesai: '10:30', durasi: 150, ruangan_id: 1, ruangan_nama: 'R.101', kapasitas: 40, jumlah_peserta: 35, dosen_id: 1, dosen_nama: 'Dr. Budi Santoso, M.Kom', status: 'ok', conflict_reason: null },
    { id: 'TMP-002', mk_kode: 'IF002', mk_nama: 'Pemrograman Web', sks: 3, prodi_id: 1, prodi_kode: 'TIF', prodi_nama: 'Teknik Informatika', kelas: 'A', tanggal: '2025-05-12', hari: 'Senin', jam_mulai: '08:00', jam_selesai: '10:30', durasi: 150, ruangan_id: 1, ruangan_nama: 'R.101', kapasitas: 40, jumlah_peserta: 38, dosen_id: 2, dosen_nama: 'Dr. Sari Dewi, M.T', status: 'conflict', conflict_reason: 'Ruangan R.101 sudah digunakan oleh IF001 pada waktu yang sama' },
    { id: 'TMP-003', mk_kode: 'IF003', mk_nama: 'Algoritma & Struktur Data', sks: 3, prodi_id: 1, prodi_kode: 'TIF', prodi_nama: 'Teknik Informatika', kelas: 'B', tanggal: '2025-05-13', hari: 'Selasa', jam_mulai: '09:00', jam_selesai: '11:30', durasi: 150, ruangan_id: 3, ruangan_nama: 'R.201', kapasitas: 50, jumlah_peserta: 40, dosen_id: 3, dosen_nama: 'Prof. Ahmad Fauzi, Ph.D', status: 'edited', conflict_reason: null },
    { id: 'TMP-004', mk_kode: 'IF004', mk_nama: 'Jaringan Komputer', sks: 2, prodi_id: 1, prodi_kode: 'TIF', prodi_nama: 'Teknik Informatika', kelas: 'C', tanggal: '2025-05-14', hari: 'Rabu', jam_mulai: '08:00', jam_selesai: '09:40', durasi: 100, ruangan_id: 2, ruangan_nama: 'R.102', kapasitas: 40, jumlah_peserta: 32, dosen_id: 5, dosen_nama: 'Ir. Hendra Wijaya, M.T', status: 'ok', conflict_reason: null },
    { id: 'TMP-005', mk_kode: 'SI001', mk_nama: 'Sistem Basis Data', sks: 3, prodi_id: 2, prodi_kode: 'SI', prodi_nama: 'Sistem Informasi', kelas: 'A', tanggal: '2025-05-12', hari: 'Senin', jam_mulai: '11:00', jam_selesai: '13:30', durasi: 150, ruangan_id: 4, ruangan_nama: 'R.202', kapasitas: 50, jumlah_peserta: 45, dosen_id: 4, dosen_nama: 'Dr. Lisa Permata, M.Si', status: 'ok', conflict_reason: null },
    { id: 'TMP-006', mk_kode: 'SI002', mk_nama: 'Analisis Sistem', sks: 2, prodi_id: 2, prodi_kode: 'SI', prodi_nama: 'Sistem Informasi', kelas: 'B', tanggal: '2025-05-13', hari: 'Selasa', jam_mulai: '08:00', jam_selesai: '09:40', durasi: 100, ruangan_id: 3, ruangan_nama: 'R.201', kapasitas: 50, jumlah_peserta: 28, dosen_id: 6, dosen_nama: 'Dr. Ratna Sari, M.Kom', status: 'ok', conflict_reason: null },
    { id: 'TMP-007', mk_kode: 'TK001', mk_nama: 'Elektronika Dasar', sks: 4, prodi_id: 3, prodi_kode: 'TK', prodi_nama: 'Teknik Komputer', kelas: 'A', tanggal: '2025-05-14', hari: 'Rabu', jam_mulai: '08:00', jam_selesai: '11:20', durasi: 200, ruangan_id: 5, ruangan_nama: 'Aula A', kapasitas: 120, jumlah_peserta: 85, dosen_id: 1, dosen_nama: 'Dr. Budi Santoso, M.Kom', status: 'ok', conflict_reason: null },
    { id: 'TMP-008', mk_kode: 'AK001', mk_nama: 'Akuntansi Keuangan', sks: 3, prodi_id: 4, prodi_kode: 'AKT', prodi_nama: 'Akuntansi', kelas: 'A', tanggal: '2025-05-15', hari: 'Kamis', jam_mulai: '08:00', jam_selesai: '10:30', durasi: 150, ruangan_id: 2, ruangan_nama: 'R.102', kapasitas: 40, jumlah_peserta: 36, dosen_id: 4, dosen_nama: 'Dr. Lisa Permata, M.Si', status: 'ok', conflict_reason: null },
];

const MOCK_HARI_LIBUR = [
    { id: 1, tanggal: '2025-04-18', keterangan: 'Wafat Isa Almasih', tipe: 'nasional' },
    { id: 2, tanggal: '2025-05-01', keterangan: 'Hari Buruh Internasional', tipe: 'nasional' },
    { id: 3, tanggal: '2025-05-12', keterangan: 'Hari Raya Waisak', tipe: 'nasional' },
    { id: 4, tanggal: '2025-05-29', keterangan: 'Kenaikan Isa Almasih', tipe: 'nasional' },
    { id: 5, tanggal: '2025-06-01', keterangan: 'Hari Lahir Pancasila', tipe: 'nasional' },
    { id: 6, tanggal: '2025-04-21', keterangan: 'Dies Natalis Kampus', tipe: 'kampus' },
];

// ============================================================
// VUEX STORE MODULE
// ============================================================
const penjadwalanStore = {
    namespaced: true,
    state: {
        // Konteks filter (sebelum generate)
        context: {
            type: 'uas',
            start_date: '',
            prodi_id: null,
            kelas: null,
        },

        // Tab aktif filter prodi di tabel hasil
        activeProdiTab: 'semua',

        // Hasil generate (draft dari backend)
        draftJadwal: [],

        // Statistik ringkas
        stats: { ok: 0, conflict: 0, edited: 0 },

        // Status proses
        isGenerating: false,
        isSaving: false,

        // Master data pendukung
        prodiList: MOCK_PRODI,
        kelasList: [],
        ruanganList: MOCK_RUANGAN,
        dosenList: MOCK_DOSEN,
        hariLiburList: MOCK_HARI_LIBUR,
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

        // Jadwal yang difilter berdasarkan tab aktif
        filteredJadwal(state) {
            if (state.activeProdiTab === 'semua') return state.draftJadwal;
            return state.draftJadwal.filter(j => j.prodi_id === state.activeProdiTab);
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
            // Hitung statistik
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
                // Recalculate stats
                state.stats = {
                    ok:       state.draftJadwal.filter(j => j.status === 'ok').length,
                    conflict: state.draftJadwal.filter(j => j.status === 'conflict').length,
                    edited:   state.draftJadwal.filter(j => j.status === 'edited').length,
                };
            }
        },
        SET_KELAS_LIST(state, list) {
            state.kelasList = list;
        },
        SET_GENERATING(state, val) { state.isGenerating = val; },
        SET_SAVING(state, val) { state.isSaving = val; },
        ADD_HARI_LIBUR(state, item) { state.hariLiburList.push(item); },
        REMOVE_HARI_LIBUR(state, id) {
            state.hariLiburList = state.hariLiburList.filter(h => h.id !== id);
        },
        SET_RUANGAN(state, list) {
            state.ruanganList = list;
        },
    },

    actions: {
        // Simulate generate — nanti diganti API call
        async generateJadwal({ commit, state }) {
            commit('SET_GENERATING', true);
            await new Promise(r => setTimeout(r, 1800)); // simulasi delay API
            commit('SET_DRAFT_JADWAL', MOCK_JADWAL);
            commit('SET_GENERATING', false);
        },

        // Update satu baris jadwal (dari modal edit)
        updateJadwalRow({ commit }, row) {
            commit('UPDATE_JADWAL_ROW', row);
        },

        // Set kelas berdasarkan prodi yang dipilih
        setKelasByProdi({ commit }, prodiId) {
            const kelas = (MOCK_KELAS[prodiId] || []).map(k => ({ value: k, label: `Kelas ${k}` }));
            commit('SET_KELAS_LIST', kelas);
        },

        // Get Rooms from API
        async [actions.GET_ROOMS]({ commit }, params) {
            try {
                const response = await Api.get(apiUrl.GET_ROOMS, { params });
                const data = response.data.result;
                commit('SET_RUANGAN', data);
                return data;
            } catch (error) {
                console.error("Error Fetching Rooms:", error);
                throw error;
            }
        },

        // Simpan jadwal permanen (nanti diganti API)
        async saveJadwal({ commit, state }) {
            commit('SET_SAVING', true);
            await new Promise(r => setTimeout(r, 1500));
            commit('SET_SAVING', false);
            return true;
        },

        addHariLibur({ commit }, item) {
            commit('ADD_HARI_LIBUR', { ...item, id: Date.now() });
        },
        removeHariLibur({ commit }, id) {
            commit('REMOVE_HARI_LIBUR', id);
        },
    },
};

export default penjadwalanStore;
