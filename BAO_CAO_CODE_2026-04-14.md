# BAO CAO CONG VIEC CODE - 2026-04-14

## 1) Nguon du lieu tong hop
- Lich su chat trong ngay 2026-04-14 (cac luong sua loi va nang cap da thao luan/lam viec).
- Git commit gan nhat:
  - Commit: 48006d8cebcbdb089b3ee3b8b566e72269c48c9e
  - Author: LuongNgocChung <chungluonng4423@gmail.com>
  - Date: 2026-04-14 11:48:17 +0700
  - Subject: feat: implement system login modal and offline data push functionality

## 2) Tom tat ket qua trong ngay
Trong ngay, he thong duoc nang cap lon o ca backend va frontend theo cac nhom viec chinh:
- Hoan thien luong truy cap public (ngrok va cloudflare), toi uu script chay nhanh.
- Cai thien camera mobile/browser (preview doc, on dinh luong video, realtime detect).
- Bo sung va sua realtime detect bbox, preview sau chup, UX thao tac diem danh.
- Dong bo luong Register/ERP: cap nhat anh, day token ERP, overwrite du lieu khi import trung ma.
- Sua cac loi auth/session (401 het phien, thong bao ro rang, dieu huong ve login tu dong).
- Mo rong backend ERP/CouchDB auth va tang do ben cho luong dang nhap he thong.

## 3) Chi tiet cong viec da lam (theo lich su chat)

### 3.1 Ha tang truy cap web va script chay
- Sua loi quote trong script khoi dong va logic mo tunnel.
- Xu ly loi ngrok auth/config, huong dan token dung dinh dang.
- Sua Vite host allowlist de khong bi chan host public domain.
- Chuyen huong sang Cloudflare quick tunnel:
  - Ho tro tham so --cloud / --cloudflare trong start_web.bat.
  - Chay cloudflared tu local file hoac PATH.
  - Toi uu cau hinh quick tunnel de giam timeout: edge-ip-version 4, protocol http2, ha-connections 1.
  - Bo sung thong diep huong dan retry khi mang bat on dinh.

### 3.2 Camera Attendance va UX mobile
- Sua hien tuong camera den tren mobile/mac (attach stream sau render, cho frame san sang).
- Chuyen khung preview sang ti le doc tren dien thoai, tang de su dung.
- Them overlay huong dan can mat trong khung.
- Bo sung detect realtime tren luong camera browser theo chu ky (~0.9s).
- Hien bounding boxes realtime tren preview.
- Hien preview lan chup gan nhat kem bounding box.
- Dieu chinh vi tri nut "Chup va diem danh" len gan khung render de thao tac nhanh hon tren mobile.

### 3.3 Sua loi detect realtime
- Khac phuc loi backend:
  - "The truth value of an array with more than one element is ambiguous. Use a.any() or a.all()"
- Nguyen nhan chinh: xu ly truthy/falsy tren numpy array trong luong detect realtime.
- Cach sua:
  - Tranh dung mau "array or []" voi numpy array.
  - Chuan hoa bien ket qua match ve bool an toan truoc khi so sanh.

### 3.4 Register va dong bo ERP
- Chinh sua flow cap nhat/ dang ky de tu dong push token anh len ERP sau khi xu ly local.
- Overwrite khi import ERP trung ma nhan vien:
  - Ghi de day du thong tin (ten, phong ban, chuc vu, du lieu mat, anh).
  - Dong bo token/anh ERP nhat quan hon.
- Loai bo fallback uu tien anh local tren Register theo yeu cau.
- Sua luong tao token anh ERP:
  - Uu tien header/auth theo phien dang nhap he thong.
  - Giam tinh trang loi sai huong (Missing Header/Unauthorized bi tra ve khong dung ngu canh).

### 3.5 Session/Auth va tinh on dinh he thong
- Cai thien thong bao 401 o backend thanh thong diep het phien ro rang.
- Frontend request helper:
  - Bat 401 tap trung.
  - Tu clear token phien.
  - Phat event het phien de UI xu ly nhat quan.
- Layout:
  - Lang nghe event het phien va quay ve login.
  - Kiem tra session dinh ky de phat hien backend restart / session expire som.

## 4) Phan tich diff cua commit gan nhat

### 4.1 So lieu tong
- Tong file thay doi: 39 files
- So dong thay doi:
  - Insertions: 5355
  - Deletions: 626

### 4.2 Cac nhom file thay doi lon
- Backend auth/ERP/CouchDB:
  - backend/services/couchdb_auth_service.py (them moi lon)
  - backend/services/couchdb_adapter.py (them moi)
  - backend/services/erp_http_client.py (mo rong lon)
  - backend/routes/auth.py
  - backend/routes/_helpers.py
  - backend/routes/employee.py
  - backend/routes/registration.py
- Backend attendance/face:
  - backend/routes/attendance.py
  - backend/models/face_recognition_module.py
  - backend/models/database.py
- Frontend pages/chuc nang:
  - src/pages/Attendance.jsx
  - src/pages/Register.jsx
  - src/pages/Report.jsx
  - src/pages/ManageFaces.jsx
  - src/pages/Login.jsx
  - src/components/Layout.jsx
  - src/services/request.js
  - src/services/modules/attendance.js
  - src/services/modules/employee.js
- Van hanh va cau hinh:
  - start_web.bat
  - vite.config.js
  - .env.example
  - package-lock.json
- Clone PHP backend:
  - clone-php-backend/services.sof.vn/index.php
  - clone-php-backend/services.sof.vn/lv_controler.php
  - clone-php-backend/services.sof.vn/couchdb_functions.php (them moi)

### 4.3 Binary bo sung
- cloudflared.exe
- ngrok.exe

## 5) Ket qua xac minh trong ngay
- Nhieu vong build frontend da pass (vite build).
- Kiem tra compile Python cho cac file backend da sua.
- Smoke test API mot so luong quan trong (token/attendance detect flow).
- Script start_web.bat duoc chay lai voi --cloud va --cloud --public de xac nhan luong khoi dong.

## 6) Van de da gap va cach xu ly
- Loi cloudflare quick tunnel timeout (context deadline exceeded):
  - Da giam xac suat loi bang cau hinh IPv4 + HTTP/2.
  - Bo sung huong dan retry khi mang toi api.trycloudflare.com khong on dinh.
- Loi 401 Unauthorized trong flow cap nhat mat:
  - Da xu ly theo huong het phien -> clear token -> dang nhap lai.
  - Them co che UI tu dong chuyen ve login khi phat hien 401.
- Loi realtime detect ambiguous numpy truth value:
  - Da fix logic bool cho ket qua mang numpy.

## 7) Ket luan
Ngay 2026-04-14 da hoan thanh mot dot nang cap lon, gom:
- Nang cao do on dinh van hanh public access (ngrok/cloudflare).
- Cai thien trai nghiem mobile cho diem danh realtime.
- Dong bo du lieu Register/ERP chat che hon.
- Tang do ben auth/session va thong diep loi ro rang.
- Mo rong tang backend ERP/CouchDB de phuc vu dang nhap va dong bo du lieu he thong.
