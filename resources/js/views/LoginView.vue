<template>
  <div class="min-h-screen bg-[#F8FAFC] flex flex-col items-center justify-center p-3 sm:p-4 font-sans text-slate-800">
    <!-- Centered Clean Compact White Card Container -->
    <div
      :class="[
        'w-full bg-white rounded-xl shadow-lg border border-slate-200/90 p-5 sm:p-6 my-auto transition-all',
        isRegisterMode ? 'max-w-md' : 'max-w-sm'
      ]"
    >
      <!-- Brand Header -->
      <div class="text-center mb-4">
        <h1 class="text-xl font-bold tracking-tight text-slate-900 leading-none">
          TaxVault
        </h1>
        <p class="text-[11px] text-slate-400 font-medium mt-1">
          Arsip Dokumen & Rekap Pajak
        </p>
      </div>
        
        <!-- ============================================== -->
        <!-- VIEW 1: LOGIN FORM (Password or WhatsApp Tab)  -->
        <!-- ============================================== -->
        <div v-if="!isRegisterMode && !isOtpStep" class="space-y-3.5">
          <div>
            <h2 class="text-base font-bold tracking-tight text-slate-900">
              Masuk ke Akun
            </h2>
            <p class="text-[11px] text-slate-400 mt-0.5">
              Silakan masuk untuk melanjutkan ke sistem
            </p>
          </div>

          <!-- Tabs (Email & Kata Sandi vs WhatsApp) -->
          <div class="flex border-b border-slate-200 text-xs font-semibold">
            <button
              type="button"
              class="pb-2 px-2 border-b-2 transition-all cursor-pointer flex-1 text-center text-xs"
              :class="loginTab === 'password' ? 'border-[#135A46] text-[#135A46] font-bold' : 'border-transparent text-slate-400 hover:text-slate-700'"
              @click="loginTab = 'password'; errorMessage = ''"
            >
              Email & Kata Sandi
            </button>
            <button
              type="button"
              class="pb-2 px-2 border-b-2 transition-all cursor-pointer flex-1 text-center text-xs"
              :class="loginTab === 'whatsapp' ? 'border-[#135A46] text-[#135A46] font-bold' : 'border-transparent text-slate-400 hover:text-slate-700'"
              @click="loginTab = 'whatsapp'; errorMessage = ''"
            >
              WhatsApp
            </button>
          </div>

          <!-- Error / Pending Alert -->
          <div
            v-if="errorMessage"
            class="p-2.5 rounded-lg border text-xs flex items-start gap-2"
            :class="isPendingAlert ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-rose-50 border-rose-200 text-rose-700'"
          >
            <AlertCircle class="w-3.5 h-3.5 shrink-0 mt-0.5" />
            <div class="flex-1 text-[11px]">
              <p class="font-semibold" v-if="isPendingAlert">Akun Menunggu ACC</p>
              <p>{{ errorMessage }}</p>
            </div>
          </div>

          <!-- Tab 1: Email & Password Form -->
          <form v-if="loginTab === 'password'" @submit.prevent="handlePasswordSubmit" class="space-y-3">
            <div class="space-y-1">
              <label class="block text-[11px] font-semibold text-slate-700">
                Email
              </label>
              <input
                v-model="email"
                type="email"
                placeholder="reza25022003@gmail.com"
                required
                class="w-full h-8.5 px-3 text-xs rounded-lg border border-slate-300 focus:border-[#135A46] focus:ring-1 focus:ring-[#135A46] focus:outline-hidden transition-all placeholder:text-slate-400"
              />
            </div>

            <div class="space-y-1">
              <label class="block text-[11px] font-semibold text-slate-700">
                Kata Sandi
              </label>
              <div class="relative">
                <input
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="••••••••"
                  required
                  class="w-full h-8.5 px-3 pr-9 text-xs rounded-lg border border-slate-300 focus:border-[#135A46] focus:ring-1 focus:ring-[#135A46] focus:outline-hidden transition-all placeholder:text-slate-400"
                />
                <button
                  type="button"
                  class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer p-0.5"
                  @click="showPassword = !showPassword"
                >
                  <EyeOff v-if="showPassword" class="w-3.5 h-3.5" />
                  <Eye v-else class="w-3.5 h-3.5" />
                </button>
              </div>
            </div>

            <div class="flex items-center">
              <label class="flex items-center gap-1.5 cursor-pointer text-[11px] text-slate-600">
                <input
                  v-model="rememberMe"
                  type="checkbox"
                  class="w-3.5 h-3.5 rounded border-slate-300 text-[#135A46] focus:ring-[#135A46] cursor-pointer"
                />
                <span>Ingat saya</span>
              </label>
            </div>

            <button
              type="submit"
              class="w-full h-8.5 rounded-lg bg-[#135A46] hover:bg-[#0F4939] active:bg-[#0C3B2E] text-white font-semibold text-xs transition-all cursor-pointer flex items-center justify-center gap-2 shadow-2xs"
              :disabled="isLoading"
            >
              <span v-if="isLoading" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
              <span v-else>Masuk</span>
            </button>
          </form>

          <!-- Tab 2: WhatsApp / Email OTP Form -->
          <form v-else @submit.prevent="handleWhatsAppSubmit" class="space-y-3">
            <div class="space-y-1">
              <label class="block text-[11px] font-semibold text-slate-700">
                Nomor WhatsApp / Email Terdaftar
              </label>
              <input
                v-model="identifier"
                type="text"
                placeholder="Contoh: 081224290502"
                required
                class="w-full h-8.5 px-3 text-xs rounded-lg border border-slate-300 focus:border-[#135A46] focus:ring-1 focus:ring-[#135A46] focus:outline-hidden transition-all placeholder:text-slate-400 font-mono"
              />
              <p class="text-[10px] text-slate-400 mt-0.5">
                Kode OTP 6-digit akan dikirimkan ke WhatsApp yang terdaftar.
              </p>
            </div>

            <button
              type="submit"
              class="w-full h-8.5 rounded-lg bg-[#135A46] hover:bg-[#0F4939] active:bg-[#0C3B2E] text-white font-semibold text-xs transition-all cursor-pointer flex items-center justify-center gap-2 shadow-2xs"
              :disabled="isLoading"
            >
              <span v-if="isLoading" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
              <span v-else>Kirim Kode OTP</span>
            </button>
          </form>

          <!-- Register Link -->
          <div class="text-center text-[11px] text-slate-500 pt-0.5">
            Belum memiliki akun?
            <button
              type="button"
              class="text-[#135A46] font-semibold hover:underline cursor-pointer ml-1"
              @click="goToRegister"
            >
              Daftar Akun Baru
            </button>
          </div>

          <!-- Demo Accounts Box (Reference Style - controlled by Admin ON/OFF) -->
          <div v-if="showDemoAccounts" class="pt-3 border-t border-slate-100 animate-in fade-in duration-200">
            <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
              AKUN DEMO (PASSWORD: PASSWORD123)
            </div>

            <div class="grid grid-cols-3 gap-1.5">
              <button
                v-for="user in demoAccounts"
                :key="user.email"
                type="button"
                class="p-1.5 rounded-md border border-slate-200 bg-[#F9FBFB] hover:bg-emerald-50/60 hover:border-emerald-300 transition-all text-left cursor-pointer group"
                @click="fillDemoAccount(user)"
              >
                <div class="font-bold text-[10px] text-slate-800 group-hover:text-[#135A46] leading-tight truncate">
                  {{ user.label }}
                </div>
                <div class="text-[9px] text-slate-400 truncate mt-0.5 font-mono">
                  {{ user.email.split('@')[0] }}
                </div>
              </button>
            </div>
          </div>
        </div>

        <!-- ============================================== -->
        <!-- VIEW 2: OTP VERIFICATION SCREEN                 -->
        <!-- ============================================== -->
        <div v-else-if="!isRegisterMode && isOtpStep" class="space-y-5">
          <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 text-[11px] font-semibold mb-2 border border-emerald-200/80">
              <Smartphone class="w-3.5 h-3.5" />
              <span>Verifikasi WhatsApp OTP</span>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">
              Masukkan Kode OTP
            </h2>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
              Kode verifikasi 6-digit telah dikirimkan via WhatsApp ke nomor <strong class="text-slate-800 font-mono">{{ maskedPhone }}</strong>.
            </p>
          </div>

          <!-- Error Alert if any -->
          <div
            v-if="errorMessage"
            class="p-3 bg-rose-50 rounded-lg border border-rose-200 text-xs text-rose-700 flex items-center gap-2"
          >
            <AlertCircle class="w-4 h-4 shrink-0" />
            <span>{{ errorMessage }}</span>
          </div>

          <!-- OTP Form -->
          <form @submit.prevent="handleOtpVerify" class="space-y-4">
            <div class="space-y-1.5">
              <label class="block text-xs font-semibold text-slate-700">
                Kode Verifikasi (6 Angka)
              </label>
              <input
                v-model="otpInput"
                type="text"
                maxlength="6"
                placeholder="123456"
                autofocus
                required
                class="w-full h-12 px-4 text-center font-mono text-xl font-bold tracking-[0.4em] rounded-lg border border-slate-300 focus:border-[#135A46] focus:ring-2 focus:ring-[#135A46]/20 focus:outline-hidden transition-all placeholder:text-slate-300 placeholder:tracking-normal"
              />
            </div>

            <button
              type="submit"
              class="w-full h-10 rounded-lg bg-[#135A46] hover:bg-[#0F4939] active:bg-[#0C3B2E] text-white font-semibold text-xs transition-all cursor-pointer flex items-center justify-center gap-2 shadow-sm shadow-[#135A46]/25"
              :disabled="isLoading"
            >
              <span v-if="isLoading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
              <span v-else>Verifikasi & Masuk</span>
            </button>
          </form>

          <!-- Resend Timer / Back Action -->
          <div class="flex items-center justify-between text-xs text-slate-500 pt-2 border-t border-slate-100">
            <button
              type="button"
              class="hover:text-slate-800 transition-colors cursor-pointer flex items-center gap-1.5"
              @click="backToLoginForm"
            >
              <ArrowLeft class="w-3.5 h-3.5" />
              <span>Ganti Nomor / Akun</span>
            </button>

            <button
              type="button"
              class="font-semibold text-[#135A46] hover:underline cursor-pointer disabled:text-slate-400 disabled:no-underline"
              :disabled="resendCooldown > 0"
              @click="handleResendOtp"
            >
              <span v-if="resendCooldown > 0">Kirim Ulang ({{ resendCooldown }}s)</span>
              <span v-else>Kirim Ulang OTP</span>
            </button>
          </div>
        </div>

        <!-- ============================================== -->
        <!-- VIEW 3: REGISTRATION FORM                       -->
        <!-- ============================================== -->
        <div v-else-if="isRegisterMode" class="space-y-5">
          <!-- Header -->
          <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">
              Formulir Pendaftaran
            </h2>
            <p class="text-xs text-slate-500 mt-1">
              Lengkapi data Anda dengan benar
            </p>
          </div>

          <!-- Register Success State (Clean, Minimal, Non-Slop) -->
          <div
            v-if="registerSuccess"
            class="space-y-5 text-center py-2"
          >
            <!-- Clean Icon -->
            <div class="w-12 h-12 rounded-full bg-emerald-50 text-[#135A46] border border-emerald-200 flex items-center justify-center mx-auto">
              <Check class="w-6 h-6 stroke-[2.5]" />
            </div>

            <!-- Title & Brief Message -->
            <div class="space-y-1.5">
              <h3 class="text-xl font-bold tracking-tight text-slate-900">
                Pendaftaran Berhasil
              </h3>
              <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                Pengajuan akun Anda telah tersimpan dan sedang menunggu persetujuan Administrator SCM. Notifikasi akan dikirimkan ke WhatsApp saat akun disetujui.
              </p>
            </div>

            <!-- Compact Details Box -->
            <div class="bg-slate-50 rounded-lg p-3.5 border border-slate-200 text-xs space-y-2 text-left max-w-sm mx-auto">
              <div class="flex items-center justify-between">
                <span class="text-slate-400">Nama</span>
                <span class="font-semibold text-slate-900">{{ registeredData.name }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-slate-400">Email</span>
                <span class="font-mono text-slate-800">{{ registeredData.email }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-slate-400">Role Diajukan</span>
                <span class="font-medium text-slate-800">{{ registeredData.role }}</span>
              </div>
              <div class="flex items-center justify-between pt-1.5 border-t border-slate-200/80">
                <span class="text-slate-400">Status</span>
                <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-amber-700">
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                  <span>Menunggu ACC Admin</span>
                </span>
              </div>
            </div>

            <!-- Return Button -->
            <div class="pt-2 max-w-sm mx-auto">
              <button
                type="button"
                class="w-full h-10 rounded-lg bg-[#135A46] hover:bg-[#0F4939] active:bg-[#0C3B2E] text-white font-semibold text-xs transition-all cursor-pointer shadow-sm"
                @click="backToLoginForm"
              >
                Kembali ke Halaman Masuk
              </button>
            </div>
          </div>

          <!-- Registration Form Fields -->
          <form v-else @submit.prevent="handleRegisterSubmit" class="space-y-3.5">
            <!-- Error Alert -->
            <div
              v-if="errorMessage"
              class="p-3 bg-rose-50 rounded-lg border border-rose-200 text-xs text-rose-700 flex items-center gap-2"
            >
              <AlertCircle class="w-4 h-4 shrink-0" />
              <span>{{ errorMessage }}</span>
            </div>

            <!-- Nama Lengkap -->
            <div class="space-y-1">
              <label class="block text-xs font-semibold text-slate-700">
                Nama Lengkap <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="regForm.name"
                type="text"
                placeholder="Nama sesuai identitas kantor"
                required
                class="w-full h-9 px-3 text-xs rounded-lg border border-slate-300 focus:border-[#135A46] focus:ring-2 focus:ring-[#135A46]/20 focus:outline-hidden transition-all placeholder:text-slate-400"
              />
            </div>

            <!-- 2-col: WhatsApp & Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700">
                  Nomor WhatsApp <span class="text-rose-500">*</span>
                </label>
                <input
                  v-model="regForm.phone"
                  type="text"
                  placeholder="Contoh: 081234567890"
                  required
                  class="w-full h-9 px-3 text-xs rounded-lg border border-slate-300 focus:border-[#135A46] focus:ring-2 focus:ring-[#135A46]/20 focus:outline-hidden transition-all placeholder:text-slate-400 font-mono"
                />
              </div>

              <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700">
                  Alamat Email <span class="text-rose-500">*</span>
                </label>
                <input
                  v-model="regForm.email"
                  type="email"
                  placeholder="nama@perusahaan.com"
                  required
                  class="w-full h-9 px-3 text-xs rounded-lg border border-slate-300 focus:border-[#135A46] focus:ring-2 focus:ring-[#135A46]/20 focus:outline-hidden transition-all placeholder:text-slate-400"
                />
              </div>
            </div>

            <!-- Role Dropdown: Tim Pajak & Staf SCM -->
            <div class="space-y-1">
              <label class="block text-xs font-semibold text-slate-700">
                Pengajuan Jabatan / Role <span class="text-rose-500">*</span>
              </label>
              <select
                v-model="regForm.role"
                required
                class="w-full h-9 px-3 text-xs rounded-lg border border-slate-300 focus:border-[#135A46] focus:ring-2 focus:ring-[#135A46]/20 focus:outline-hidden transition-all bg-white text-slate-800 font-medium"
              >
                <option value="Tim Pajak">Tim Pajak (Verifikasi Faktur & Compliance)</option>
                <option value="Staf SCM">Staf SCM (Pengadaan & Logistik)</option>
              </select>
            </div>

            <!-- 2-col: Password & Confirm Password -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700">
                  Kata Sandi <span class="text-rose-500">*</span>
                </label>
                <input
                  v-model="regForm.password"
                  type="password"
                  placeholder="Minimal 6 karakter"
                  required
                  minlength="6"
                  class="w-full h-9 px-3 text-xs rounded-lg border border-slate-300 focus:border-[#135A46] focus:ring-2 focus:ring-[#135A46]/20 focus:outline-hidden transition-all placeholder:text-slate-400"
                />
              </div>

              <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700">
                  Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
                </label>
                <input
                  v-model="regForm.passwordConfirmation"
                  type="password"
                  placeholder="Ketik ulang kata sandi"
                  required
                  minlength="6"
                  class="w-full h-9 px-3 text-xs rounded-lg border border-slate-300 focus:border-[#135A46] focus:ring-2 focus:ring-[#135A46]/20 focus:outline-hidden transition-all placeholder:text-slate-400"
                />
              </div>
            </div>

            <!-- Notice Banner -->
            <div class="p-2.5 rounded-lg bg-emerald-50/80 border border-emerald-200/80 text-[11px] text-emerald-950 flex items-start gap-2">
              <AlertCircle class="w-4 h-4 text-[#135A46] shrink-0 mt-0.5" />
              <span>
                Akun baru akan diverifikasi oleh Administrator sebelum dapat login ke sistem.
              </span>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              class="w-full h-10 rounded-lg bg-[#135A46] hover:bg-[#0F4939] active:bg-[#0C3B2E] text-white font-semibold text-xs transition-all cursor-pointer flex items-center justify-center gap-2 shadow-sm shadow-[#135A46]/25"
              :disabled="isLoading"
            >
              <span v-if="isLoading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
              <span v-else>Daftar Sekarang</span>
            </button>

            <!-- Back to Login Link -->
            <div class="text-center text-xs text-slate-500 pt-1">
              Sudah memiliki akun?
              <button
                type="button"
                class="text-[#135A46] font-semibold hover:underline cursor-pointer ml-1"
                @click="backToLoginForm"
              >
                Masuk di sini
              </button>
            </div>
          </form>
        </div>

    </div>

    <!-- Simple Footer -->
    <p class="text-[11px] text-slate-400 text-center mt-5 select-none">
      &copy; 2026 TaxVault Enterprise System.
    </p>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import {
  Shield,
  Check,
  CheckCircle2,
  AlertCircle,
  Eye,
  EyeOff,
  ArrowLeft,
  Smartphone,
  Lock
} from 'lucide-vue-next';
import { useTaxStore } from '../store/taxStore';

const router = useRouter();
const route = useRoute();
const store = useTaxStore();

// Navigation states
const isRegisterMode = ref(route.name === 'register' || route.query.mode === 'register');
const loginTab = ref('password'); // 'password' | 'whatsapp'
const isOtpStep = ref(false);

// Form models
const email = ref('admin@scm.corp');
const password = ref('password123');
const showPassword = ref(false);
const rememberMe = ref(true);

const identifier = ref('081224290502');
const otpInput = ref('');
const activeOtpCode = ref('');
const targetPhone = ref('');
const resendCooldown = ref(0);
let cooldownTimer = null;

const isLoading = ref(false);
const errorMessage = ref('');
const isPendingAlert = ref(false);

// Registration form
const registerSuccess = ref(false);
const registeredData = ref({
  name: '',
  email: '',
  phone: '',
  role: ''
});

const regForm = reactive({
  name: '',
  phone: '',
  email: '',
  role: 'Tim Pajak',
  password: '',
  passwordConfirmation: ''
});

// Demo accounts list for quick testing
const showDemoAccounts = computed(() => store.showDemoAccounts.value);

onMounted(() => {
  store.fetchSettings();
});

const demoAccounts = [
  { label: 'Admin SCM', email: 'admin@scm.corp', role: 'Admin SCM', phone: '081234567890' },
  { label: 'Tim Pajak', email: 'auditor@pajak.corp', role: 'Tim Pajak', phone: '081224290502' },
  { label: 'Staf SCM', email: 'staff@scm.corp', role: 'Staf SCM', phone: '081298765432' },
];

const maskedPhone = computed(() => {
  if (!targetPhone.value) return 'WhatsApp Anda';
  const clean = String(targetPhone.value);
  if (clean.length < 8) return clean;
  return clean.slice(0, 4) + '****' + clean.slice(-4);
});

watch(() => route.path, () => {
  isRegisterMode.value = route.name === 'register' || route.query.mode === 'register';
  errorMessage.value = '';
});

function goToRegister() {
  isRegisterMode.value = true;
  errorMessage.value = '';
  registerSuccess.value = false;
  router.push('/register').catch(() => {});
}

function backToLoginForm() {
  isRegisterMode.value = false;
  isOtpStep.value = false;
  errorMessage.value = '';
  isPendingAlert.value = false;
  router.push('/login').catch(() => {});
}

function fillDemoAccount(acc) {
  email.value = acc.email;
  password.value = 'password123';
  identifier.value = acc.phone;
  errorMessage.value = '';
  isPendingAlert.value = false;
}

// Handler for Tab 1 (Email & Password)
async function handlePasswordSubmit() {
  errorMessage.value = '';
  isPendingAlert.value = false;
  isLoading.value = true;

  try {
    const credCheck = await store.validatePasswordCredentials(email.value, password.value);
    if (!credCheck.success) {
      errorMessage.value = credCheck.message || 'Email atau kata sandi tidak cocok.';
      isPendingAlert.value = !!credCheck.isPending;
      isLoading.value = false;
      return;
    }

    // Akun admin dan demo langsung login tanpa OTP
    const cleanEmail = (email.value || '').trim().toLowerCase();
    const isDemoOrAdmin = cleanEmail === 'admin@scm.corp' ||
                          cleanEmail === 'auditor@pajak.corp' ||
                          cleanEmail === 'staff@scm.corp' ||
                          credCheck.user?.role === 'Admin SCM';

    if (isDemoOrAdmin) {
      store.loginDirect(credCheck.user);
      isLoading.value = false;
      router.push('/dashboard');
      return;
    }

    // Akun reguler diarahkan ke verifikasi WhatsApp OTP
    const otpRes = await store.sendOtp(email.value);
    isLoading.value = false;

    if (otpRes.success) {
      activeOtpCode.value = otpRes.otp;
      targetPhone.value = otpRes.phone;
      isOtpStep.value = true;
      startCooldown();
    } else {
      errorMessage.value = otpRes.message;
      isPendingAlert.value = !!otpRes.isPending;
    }
  } catch (e) {
    isLoading.value = false;
    errorMessage.value = 'Terjadi kesalahan sistem saat memproses login.';
  }
}

// Handler for Tab 2 (WhatsApp/Email OTP)
async function handleWhatsAppSubmit() {
  errorMessage.value = '';
  isPendingAlert.value = false;
  isLoading.value = true;

  try {
    const otpRes = await store.sendOtp(identifier.value);
    isLoading.value = false;

    if (otpRes.success) {
      activeOtpCode.value = otpRes.otp;
      targetPhone.value = otpRes.phone;
      isOtpStep.value = true;
      startCooldown();
    } else {
      errorMessage.value = otpRes.message;
      isPendingAlert.value = !!otpRes.isPending;
    }
  } catch (e) {
    isLoading.value = false;
    errorMessage.value = 'Gagal menghubungi server WhatsApp Gateway.';
  }
}

// Handler for OTP Verification
async function handleOtpVerify() {
  if (!otpInput.value) {
    errorMessage.value = 'Silakan masukkan 6-digit kode OTP.';
    return;
  }

  errorMessage.value = '';
  isLoading.value = true;

  try {
    const result = await store.verifyOtp(otpInput.value);
    isLoading.value = false;

    if (result.success) {
      router.push('/dashboard');
    } else {
      errorMessage.value = result.message || 'Kode OTP salah atau kedaluwarsa.';
    }
  } catch (e) {
    isLoading.value = false;
    errorMessage.value = 'Terjadi kesalahan saat memverifikasi kode OTP.';
  }
}

// Resend OTP
async function handleResendOtp() {
  if (resendCooldown.value > 0) return;
  const currentTarget = targetPhone.value || email.value || identifier.value;
  isLoading.value = true;
  errorMessage.value = '';

  try {
    const res = await store.sendOtp(currentTarget);
    isLoading.value = false;

    if (res.success) {
      activeOtpCode.value = res.otp;
      startCooldown();
    } else {
      errorMessage.value = res.message;
    }
  } catch (e) {
    isLoading.value = false;
    errorMessage.value = 'Gagal mengirim ulang kode OTP.';
  }
}

function startCooldown() {
  resendCooldown.value = 60;
  clearInterval(cooldownTimer);
  cooldownTimer = setInterval(() => {
    if (resendCooldown.value > 0) {
      resendCooldown.value--;
    } else {
      clearInterval(cooldownTimer);
    }
  }, 1000);
}

// Registration Submit Handler
async function handleRegisterSubmit() {
  errorMessage.value = '';

  if (regForm.password !== regForm.passwordConfirmation) {
    errorMessage.value = 'Kata Sandi dan Konfirmasi Kata Sandi tidak cocok.';
    return;
  }

  if (regForm.password.length < 6) {
    errorMessage.value = 'Kata Sandi minimal 6 karakter.';
    return;
  }

  isLoading.value = true;

  try {
    const res = await store.registerUser({
      name: regForm.name,
      email: regForm.email,
      phone: regForm.phone,
      role: regForm.role,
      password: regForm.password
    });

    isLoading.value = false;

    if (res.success) {
      registeredData.value = {
        name: regForm.name,
        email: regForm.email,
        phone: regForm.phone,
        role: regForm.role
      };
      registerSuccess.value = true;
    } else {
      errorMessage.value = res.message || 'Gagal melakukan pendaftaran akun.';
    }
  } catch (e) {
    isLoading.value = false;
    errorMessage.value = 'Terjadi kesalahan sistem saat melakukan pendaftaran.';
  }
}
</script>
