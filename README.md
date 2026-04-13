# FaceCheck - Hệ thống Điểm danh Nhận diện Khuôn mặt

Hệ thống điểm danh tự động sử dụng nhận diện khuôn mặt, tích hợp với hệ thống ERP.

## Cấu trúc dự án

```
├── backend/             # Python Flask API Backend
│   ├── app.py           # Flask app chính (routes & logic)
│   ├── config.py        # Cấu hình (DB, camera, ERP...)
│   ├── runtime.py       # Quản lý đường dẫn runtime
│   ├── face_encoding_utils.py
│   ├── models/          # Database models & AI modules
│   │   ├── database.py
│   │   ├── face_recognition_module.py
│   │   ├── insightface_module.py
│   │   └── erp_integration.py
│   ├── services/        # Business logic services
│   │   ├── attendance_service.py
│   │   ├── erp_http_client.py
│   │   ├── import_employees.py
│   │   └── tracking_service.py
│   └── requirements.txt # Thư viện Python
│
├── src/                 # React Frontend (Vite)
│   ├── App.jsx
│   ├── main.jsx
│   ├── index.css
│   ├── components/      # React components
│   ├── pages/           # Các trang
│   └── services/        # API client
│
├── electron/            # Electron Desktop App
│   ├── main.js
│   └── preload.js
│
├── static/              # Tài nguyên tĩnh (fonts, images)
├── instance/            # Dữ liệu runtime (DB, ảnh khuôn mặt)
│
├── install.bat          # Cài đặt thư viện
├── start_web.bat        # Chạy bản Website
├── start_app.bat        # Chạy bản Electron
│
├── run_backend.py       # Entry point cho backend
├── package.json         # Cấu hình Node.js
├── vite.config.js       # Cấu hình Vite
├── tailwind.config.js   # Cấu hình TailwindCSS
├── postcss.config.js    # Cấu hình PostCSS
├── index.html           # HTML entry point
├── .env                 # Biến môi trường
└── .env.example         # Mẫu biến môi trường
```

## Yêu cầu

- **Node.js** >= 18
- **Python** >= 3.10
- **npm** (đi kèm Node.js)

## Cài đặt

```bash
install.bat
```

Script sẽ tự động cài đặt:
- Thư viện Node.js (React, Vite, Electron...)
- Thư viện Python (Flask, OpenCV, InsightFace...)

## Chạy dự án

### Bản Website (Development)

```bash
start_web.bat
```

- Frontend: http://localhost:5173
- Backend API: http://localhost:5000

### Bản Electron App

```bash
start_app.bat
```

Sẽ mở ứng dụng desktop Electron với đầy đủ tính năng.

## Cấu hình

Chỉnh sửa file `.env` để cấu hình:

- **ERP Database**: Host, port, user, password
- **Camera**: RTSP URL, device index, flip mode
- **Performance**: Skip frames, resize factor, FPS limit
- **ERP HTTP Service**: API URL, timeout

## Tính năng

- ✅ Nhận diện khuôn mặt real-time qua camera
- ✅ Điểm danh tự động khi nhận diện được nhân viên
- ✅ Chống giả mạo (Anti-spoofing)
- ✅ Quản lý nhân viên (CRUD)
- ✅ Tích hợp ERP (đồng bộ nhân viên, ảnh, chấm công)
- ✅ Import hàng loạt từ ERP
- ✅ Báo cáo điểm danh theo ngày
- ✅ Hỗ trợ webcam và camera RTSP
- ✅ Adaptive learning (tự cập nhật khuôn mặt)
