# FaceCheck - Hệ thống Điểm danh Nhận diện Khuôn mặt

## Kiến trúc

```
├── backend/              # Python Flask API
│   ├── app.py           # Flask API server (JSON only)
│   ├── config.py        # Cấu hình
│   ├── models/          # Database models, face recognition, ERP
│   ├── services/        # Attendance service, tracking, import
│   └── requirements.txt
├── src/                  # React frontend
│   ├── components/      # Layout, shared components
│   ├── pages/           # Dashboard, Attendance, Register, ManageFaces, Report, Login
│   └── services/        # API client
├── electron/             # Electron main process
│   ├── main.js          # App lifecycle, Python backend management
│   └── preload.js       # Context bridge
├── package.json          # Node dependencies & build scripts
├── vite.config.js        # Vite configuration
├── backend.spec          # PyInstaller spec for Python backend
└── run_backend.py        # Python backend entry point
```

## Yêu cầu

- **Node.js** >= 18
- **Python** 3.10
- npm hoặc yarn

## Cài đặt

### 1. Install Node dependencies
```bash
npm install
```

### 2. Install Python dependencies
```bash
pip install -r backend/requirements.txt
```

### 3. Copy .env
```bash
cp .env.example .env
# Chỉnh sửa .env theo cấu hình của bạn
```

## Development

### Chạy riêng từng phần:
```bash
# Backend Python
python run_backend.py --port 5000

# Frontend React
npm run dev:react

# Electron (sau khi backend & react đã chạy)
npm run dev:electron
```

### Chạy tất cả:
```bash
dev.bat
```

## Production Build

```bash
build.prod.bat
```

Hoặc từng bước:
```bash
# 1. Build React
npm run build:react

# 2. Build Python backend
pyinstaller backend.spec --noconfirm --clean --distpath python-dist

# 3. Build Electron
npx electron-builder
```

Output: `release/`

## Tính năng

- **Điểm danh tự động**: Camera nhận diện khuôn mặt realtime
- **Chống giả mạo**: Anti-spoofing detection
- **Học thích ứng**: Tự động cập nhật ảnh khuôn mặt
- **Tích hợp ERP**: Đồng bộ chấm công với MySQL ERP
- **Quản lý nhân viên**: CRUD nhân viên, quản lý ảnh khuôn mặt
- **Báo cáo**: Thống kê, xuất CSV
- **Camera linh hoạt**: Hỗ trợ webcam và RTSP stream
