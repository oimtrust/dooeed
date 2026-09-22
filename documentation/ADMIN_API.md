# Admin panel: API, cURL, dan akses web

Dokumentasi ini mengikuti `routes/api.php`, `routes/web.php`, Form Requests, API Resources, dan `ManageUser`. Backend mengembalikan JSON; frontend admin di `resources/js/domains/admin/` memuat data melalui API. Blade di `resources/views/admin/` hanya menjadi shell tampilan.

## 1. Persiapan

- Jalankan migration dan build frontend sesuai proses deployment proyek.
- Gunakan akun yang sudah ada, ber-role `admin`, ber-status `active`, dan belum dihapus.
- Ganti `BASE_URL` dengan URL aplikasi dari konfigurasi web/Herd, tanpa `/` terakhir. Skema, host, dan port harus sesuai lingkungan Anda. URL lokal tidak diasumsikan dalam contoh ini.
- Contoh memakai cURL dan Python 3 untuk membaca JSON. UUID/token adalah data dari respons aplikasi, bukan ID integer.

```bash
BASE_URL='GANTI_DENGAN_URL_APLIKASI'
ADMIN_EMAIL='admin@app.test'
TARGET_USER_ID='GANTI_DENGAN_UUID_USER'
ADMIN_ID='GANTI_DENGAN_UUID_ADMIN'
```

### Admin pertama

Registrasi selalu membuat role `user`; mengirim `role=admin` saat registrasi tidak memberikan hak admin. Belum tersedia seeder atau perintah khusus untuk membuat admin pertama.

Jika belum ada admin, daftarkan akun melalui `/register`, lalu operator yang memiliki akses terminal server dapat mempromosikan **akun yang sudah ada** berikut. Ganti email bila berbeda. Ini merupakan langkah bootstrap manual, bukan endpoint publik atau tindakan panel; langkah ini tidak menghasilkan audit log panel.

```bash
php artisan tinker --execute '$user = \App\Models\User::where("email", "admin@app.test")->firstOrFail(); $user->forceFill(["role" => "admin"])->save();'
```

Setelah itu login ulang agar profil di browser diperbarui. Untuk perubahan role berikutnya, gunakan panel/API agar tercatat di audit.

## 2. Login API dan bearer token

Login API tidak membuat sesi browser. Simpan seluruh `data.token`, termasuk prefiks ID dan karakter `|`.

```bash
LOGIN_JSON=$(curl --silent --show-error --fail-with-body \
  --request POST "$BASE_URL/api/v1/auth/login" \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --data '{"email":"admin@app.test","password":"GANTI_DENGAN_PASSWORD_ADMIN"}')

TOKEN=$(printf '%s' "$LOGIN_JSON" | python3 -c 'import json,sys; print(json.load(sys.stdin)["data"]["token"])')
ADMIN_ID=$(printf '%s' "$LOGIN_JSON" | python3 -c 'import json,sys; print(json.load(sys.stdin)["data"]["user"]["id"])')
```

Ganti email di payload jika akun Anda berbeda. Respons login `200` memuat `data.user`, `data.token`, dan `data.token_type`. Password/token contoh bukan kredensial bawaan aplikasi; jangan simpan kredensial nyata dalam dokumentasi atau Git.

Periksa identitas dan role:

```bash
curl --silent --show-error --fail-with-body "$BASE_URL/api/v1/auth/me" \
  --header 'Accept: application/json' \
  --header "Authorization: Bearer $TOKEN"
```

Semua endpoint admin berikut membutuhkan bearer token milik admin aktif. API tidak membutuhkan cookie atau token CSRF untuk alur bearer ini.

## 3. Daftar endpoint admin

| Method | Path | Fungsi |
| --- | --- | --- |
| GET | `/api/v1/admin/users` | Daftar, search, filter, sort, pagination |
| GET | `/api/v1/admin/users/{user}` | Profil dan ringkasan keuangan |
| PATCH | `/api/v1/admin/users/{user}` | Suspend, activate, reset password, ubah role, soft delete |
| GET | `/api/v1/admin/audit-logs` | Daftar audit dan filter actor/target |

`{user}` adalah UUID user. Tidak ada endpoint `DELETE /api/v1/admin/users/{user}`; penghapusan menggunakan PATCH dengan `action: "delete"`.

## 4. Daftar user

```bash
curl --silent --show-error --fail-with-body --get "$BASE_URL/api/v1/admin/users" \
  --header 'Accept: application/json' \
  --header "Authorization: Bearer $TOKEN" \
  --data-urlencode 'search=budi@app.test' \
  --data-urlencode 'status=active' \
  --data-urlencode 'sort=created_at' \
  --data-urlencode 'direction=desc' \
  --data-urlencode 'page=1'
```

| Parameter | Aturan / default |
| --- | --- |
| `search` | Opsional, maksimal 255 karakter; cocok sebagian nama atau email |
| `status` | Opsional: `active`, `suspended`; kosong berarti semua |
| `sort` | `name`, `email`, `status`, `role`, `accounts_count`, `transactions_count`, `created_at`; default `created_at` |
| `direction` | `asc` atau `desc`; default `desc` |
| `page` | Integer minimal 1; default 1 |

Pagination tetap **20 user per halaman**; tidak ada parameter `per_page`. User yang sudah soft delete tidak disertakan. Search dan status digabungkan sebagai filter.

Respons `200` mengikuti format pagination Laravel: `data` berupa array user, `links` berisi URL navigasi, dan `meta` berisi `current_page`, `last_page`, `per_page`, `total`, serta informasi pagination lainnya. Filter dipertahankan pada link pagination.

Setiap user berisi:

```json
{
  "id": "00000000-0000-4000-8000-000000000002",
  "name": "Budi",
  "email": "budi@app.test",
  "role": "user",
  "status": "active",
  "created_at": "2026-09-22T10:00:00.000000Z",
  "accounts_count": 2,
  "transactions_count": 25,
  "can_manage": true
}
```

Contoh di atas adalah ilustrasi satu elemen `data`. `can_manage` bernilai false untuk akun admin yang sedang melakukan request. Password dan remember token tidak dikirim.

## 5. Detail user

```bash
curl --silent --show-error --fail-with-body "$BASE_URL/api/v1/admin/users/$TARGET_USER_ID" \
  --header 'Accept: application/json' \
  --header "Authorization: Bearer $TOKEN"
```

Respons `200` memiliki satu objek `data`:

| Field | Isi |
| --- | --- |
| `user` | `id`, `name`, `email`, `role`, `status`, `created_at`, `can_manage` |
| `accounts[]` | `id`, `name`, `currency`, `current_balance` |
| `balances[]` | `currency`, `total`; saldo dijumlah per mata uang |
| `outstanding_debts` | Jumlah `outstanding_amount` dari tipe `debt` berstatus `active`/`overdue` |
| `investments[]` | `id`, `name`, `platform`, `assets_count` |
| `transactions[]` | `id`, `transaction_date`, `account_name`, `currency`, `type`, `amount`, `description` |

Nilai uang berupa string JSON. Transaksi maksimal 20, diurutkan berdasarkan tanggal transaksi, waktu pembuatan, lalu ID secara menurun. Data dibatasi ke user target. Count list tidak disertakan pada objek `user` detail; jumlah akun bisa dibaca dari array `accounts`.

Ringkasan investasi belum berisi valuasi pasar. Skema utang belum menyimpan mata uang sehingga `outstanding_debts` tidak memiliki label mata uang. Detail user tidak ada/sudah soft delete menghasilkan `404`.

## 6. Tindakan admin

Jalankan contoh tindakan secara terpisah sesuai kebutuhan; contoh ini bukan satu skrip yang harus dijalankan berurutan. `confirmed: true` wajib untuk setiap tindakan. `reason` wajib untuk suspend, opsional untuk tindakan lainnya, maksimal 2.000 karakter. Semua tindakan terhadap akun sendiri ditolak.

### Suspend

```bash
curl --silent --show-error --fail-with-body --request PATCH \
  "$BASE_URL/api/v1/admin/users/$TARGET_USER_ID" \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header "Authorization: Bearer $TOKEN" \
  --data '{"action":"suspend","reason":"spam","confirmed":true}'
```

Status menjadi `suspended`. Login dengan password yang benar ditolak dengan `422`, `errors.email: ["Akun ditangguhkan"]`. Token lama dicabut.

### Activate

```bash
curl --silent --show-error --fail-with-body --request PATCH \
  "$BASE_URL/api/v1/admin/users/$TARGET_USER_ID" \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header "Authorization: Bearer $TOKEN" \
  --data '{"action":"activate","reason":"Peninjauan selesai","confirmed":true}'
```

Status menjadi `active`. Token/sesi yang telah dicabut tidak dipulihkan; user perlu login lagi.

### Reset password

```bash
curl --silent --show-error --fail-with-body --request PATCH \
  "$BASE_URL/api/v1/admin/users/$TARGET_USER_ID" \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header "Authorization: Bearer $TOKEN" \
  --data '{"action":"reset_password","reason":"Permintaan pemilik akun","confirmed":true}'
```

Password lama langsung diganti dengan nilai acak yang tidak ditampilkan; semua token dicabut dan link reset dikirim melalui notification Laravel. User harus memakai link tersebut untuk menentukan password baru. Reset tidak otomatis mengaktifkan akun suspended.

Respons sukses:

```json
{"message":"Link reset dikirim. Password dan token lama sudah dibatalkan."}
```

Pengiriman mengikuti konfigurasi mail. Jika memakai `MAIL_MAILER=log`, link masuk ke log aplikasi, bukan inbox. Jika broker menolak pengiriman, misalnya karena throttle reset, perubahan password/token dan audit pada transaksi database dibatalkan; request dapat mengembalikan `422`.

### Ubah role

```bash
curl --silent --show-error --fail-with-body --request PATCH \
  "$BASE_URL/api/v1/admin/users/$TARGET_USER_ID" \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header "Authorization: Bearer $TOKEN" \
  --data '{"action":"change_role","role":"admin","reason":"Penugasan operator","confirmed":true}'
```

`role` wajib untuk `change_role`, hanya `user` atau `admin`. Untuk menurunkan role, kirim `"role":"user"`. User target perlu login ulang setelah token/sesinya dicabut.

### Soft delete

Target harus berstatus `suspended` terlebih dahulu.

```bash
curl --silent --show-error --fail-with-body --request PATCH \
  "$BASE_URL/api/v1/admin/users/$TARGET_USER_ID" \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header "Authorization: Bearer $TOKEN" \
  --data '{"action":"delete","reason":"Spam berulang","confirmed":true}'
```

`deleted_at` diisi; user hilang dari list dan detail API. Data keuangan dan audit tetap tersimpan. Belum tersedia endpoint restore. Menghapus user aktif menghasilkan `422`.

### Efek bersama

Setiap tindakan yang berhasil menghasilkan `200` dengan `message`. Selain reset password, pesannya:

```json
{"message":"Tindakan berhasil disimpan."}
```

Setiap tindakan di atas mencabut seluruh personal access token target, merotasi remember token, dan mencabut sesi database bila driver sesi adalah `database`. Sesi browser admin juga diperiksa terhadap token sesi/hash password pada request berikutnya.

Perubahan target dan audit disimpan dalam satu transaksi database. Audit menyimpan `admin_id`, `target_user_id`, `action`, `reason`, `created_at`, dan `metadata` before/after role/status. Membuka daftar/detail bukan tindakan mutasi dan tidak membuat audit log.

## 7. Audit log

```bash
curl --silent --show-error --fail-with-body --get "$BASE_URL/api/v1/admin/audit-logs" \
  --header 'Accept: application/json' \
  --header "Authorization: Bearer $TOKEN" \
  --data-urlencode "admin_id=$ADMIN_ID" \
  --data-urlencode "target_user_id=$TARGET_USER_ID" \
  --data-urlencode 'page=1'
```

`admin_id` dan `target_user_id` opsional dan harus UUID. Kirim salah satu untuk satu filter, keduanya untuk irisan filter, atau hilangkan keduanya untuk semua audit. `page` minimal 1.

Respons `200` menggunakan `data`, `links`, `meta`, 20 log per halaman, terbaru dahulu. Setiap log memuat `id`, `admin_id`, `admin_email`, `target_user_id`, `target_user_email`, `action`, `reason`, `created_at`. Timestamp audit adalah `created_at`; `metadata` tersimpan di database tetapi belum diekspos API. Email actor/target tetap dapat dibaca dari user yang soft delete.

## 8. Respons error dan rate limit

| Status | Kondisi |
| --- | --- |
| `401` | Bearer token tidak ada/tidak valid/dicabut |
| `403` | User bukan admin atau akun suspended |
| `404` | Target API tidak ditemukan atau sudah soft delete |
| `422` | Payload/filter tidak valid, konfirmasi/alasan tidak ada, tindakan terhadap diri sendiri, penghapusan user aktif, atau penolakan broker reset |
| `429` | Rate limit request terlampaui |

Error API berformat JSON, termasuk tanpa header Accept. Disarankan tetap mengirim `Accept: application/json`. Contoh struktur validasi; teks pesan dapat mengikuti locale aplikasi:

```json
{
  "message": "The reason field is required when action is suspend.",
  "errors": {
    "reason": ["The reason field is required when action is suspend."]
  }
}
```

Login/register: 10 request/menit. PATCH tindakan admin: 30 request/menit, dibagi antartindakan pada limiter route. Pembuatan sesi admin: 10 request/menit. Broker password reset memiliki throttle tersendiri; kegagalannya di action admin menggunakan `422`, bukan otomatis `429`.

## 9. Akses melalui web

1. Buka `/login` pada host aplikasi, lalu login dengan admin aktif.
2. Login browser melalui `POST /session/login` menghasilkan sesi web dan bearer token. Frontend menyimpan bearer token untuk request API berikutnya.
3. Di `/dashboard`, klik **Admin panel**. Tombol muncul setelah `/api/v1/auth/me` mengonfirmasi role `admin`; tombol membuat/menyegarkan sesi melalui `POST /admin/session` sebelum navigasi.
4. `/admin/users` menampilkan daftar dan filter. Klik nama user untuk membuka `/admin/users/{UUID}`.
5. Gunakan modal konfirmasi untuk tindakan; alasan wajib saat suspend. Menu **Audit log** membuka `/admin/audit-logs`; tautan **Riwayat tindakan** pada detail memfilter user tersebut.
6. Keluar melalui tombol **Keluar** di dashboard untuk mencabut token saat ini dan mengakhiri sesi web.

| Method | Web route | Kegunaan |
| --- | --- | --- |
| GET | `/admin/users` | Shell daftar user |
| GET | `/admin/users/{UUID}` | Shell detail; data/404 target diperiksa API |
| GET | `/admin/audit-logs` | Shell audit |
| POST | `/admin/session` | Menghubungkan autentikasi admin dengan sesi browser |
| DELETE | `/admin/session` | Mengakhiri sesi web; tidak mencabut bearer token dengan sendirinya |
| POST | `/session/login` | Login browser dengan CSRF, membuat sesi dan bearer token |
| POST | `/session/register` | Registrasi browser dengan CSRF; role tetap `user` |

Shell memakai `auth:web`, `auth.session`, pemeriksaan status, dan pemeriksaan role. Guest diarahkan ke login. Non-admin yang memiliki sesi web menerima halaman `403` yang mengarahkan ke dashboard setelah tiga detik. API mengecek izin secara mandiri; menyembunyikan tombol bukan mekanisme authorization.

Mendapatkan bearer token lewat cURL saja tidak membuat browser login. Sebaliknya, cookie sesi saja tidak mengisi localStorage bearer token yang diperlukan frontend. Gunakan halaman login untuk alur browser normal. Tidak ada lagi PATCH mutasi di `/admin/users/{UUID}`; frontend mengirim PATCH ke `/api/v1/admin/users/{UUID}`.

### cURL untuk sesi web admin (opsional)

Contoh ini menguji cookie dan CSRF dari terminal dengan `$TOKEN` hasil login API. Cookie jar terminal tidak dibagikan otomatis ke browser. Request GET halaman di terminal hanya mengembalikan shell HTML, bukan menjalankan JavaScript frontend.

```bash
ADMIN_COOKIE_JAR=$(mktemp)
ADMIN_LOGIN_HTML=$(mktemp)

curl --silent --show-error --fail-with-body \
  --cookie-jar "$ADMIN_COOKIE_JAR" \
  --output "$ADMIN_LOGIN_HTML" "$BASE_URL/login"

CSRF_TOKEN=$(sed -n 's/.*name="csrf-token" content="\([^"]*\)".*/\1/p' "$ADMIN_LOGIN_HTML")

curl --silent --show-error --fail-with-body --request POST "$BASE_URL/admin/session" \
  --cookie "$ADMIN_COOKIE_JAR" --cookie-jar "$ADMIN_COOKIE_JAR" \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header "X-CSRF-TOKEN: $CSRF_TOKEN" \
  --header "Authorization: Bearer $TOKEN" \
  --data '{}'
```

Respons `200` berisi `{"redirect":"<URL absolut halaman admin/users>"}`. Regenerasi sesi juga memperbarui CSRF token; ambil token baru sebelum request web berikutnya:

```bash
curl --silent --show-error --fail-with-body \
  --cookie "$ADMIN_COOKIE_JAR" --cookie-jar "$ADMIN_COOKIE_JAR" \
  --output "$ADMIN_LOGIN_HTML" "$BASE_URL/admin/users"

CSRF_TOKEN=$(sed -n 's/.*name="csrf-token" content="\([^"]*\)".*/\1/p' "$ADMIN_LOGIN_HTML")

curl --silent --show-error --fail-with-body --request DELETE "$BASE_URL/admin/session" \
  --cookie "$ADMIN_COOKIE_JAR" --cookie-jar "$ADMIN_COOKIE_JAR" \
  --header 'Accept: application/json' \
  --header "X-CSRF-TOKEN: $CSRF_TOKEN"
```

CSRF/cookie tidak sesuai pada route web dapat menghasilkan `419`. File temporer berisi cookie/CSRF: hapus setelah pengujian selesai.

Untuk mencabut bearer token yang dipakai cURL, lakukan logout API secara terpisah:

```bash
curl --silent --show-error --fail-with-body --request POST "$BASE_URL/api/v1/auth/logout" \
  --header 'Accept: application/json' \
  --header "Authorization: Bearer $TOKEN"
```

## 10. Referensi implementasi

- `routes/api.php`: kontrak route data/tindakan.
- `routes/web.php`: shell dan sesi browser.
- `app/Http/Requests/Admin/`: validasi filter/payload.
- `app/Http/Resources/Admin/`: field respons yang diizinkan.
- `app/Domains/Admin/Actions/ManageUser.php`: mutasi, revokasi, dan audit.
- `app/Domains/Admin/Queries/GetUserSummary.php`: ringkasan keuangan.
- `resources/js/domains/admin/`: API client, komponen DOM, dan page controller.
- `tests/Feature/AdminPanelTest.php`: pengujian kontrak, akses, dan tindakan admin.
