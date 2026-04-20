import { Link } from 'react-router-dom'
import { ROUTES } from '../config/routes'

export default function EntryPortal() {
  return (
    <div className="min-h-screen bg-[radial-gradient(circle_at_top,_#dbeafe,_#f8fafc_45%,_#ffffff)] px-4 py-10">
      <div className="mx-auto max-w-5xl">
        <div className="text-center mb-10">
          <div className="inline-flex w-24 h-24 rounded-2xl bg-white shadow-lg items-center justify-center mb-4">
            <img src="https://sof.com.vn/logo.png" alt="SOF Logo" className="w-[78%] h-[78%] object-contain" />
          </div>
          <h1 className="text-3xl sm:text-4xl font-black text-slate-800 tracking-tight">FaceCheck Portal</h1>
          <p className="text-slate-500 mt-3 text-sm sm:text-base">
            Chọn cổng phù hợp để bắt đầu: chấm công nhân viên hoặc khu vực quản trị hệ thống.
          </p>
        </div>

        <div className="grid md:grid-cols-2 gap-6">
          <Link
            to={ROUTES.employeeLogin}
            className="group rounded-3xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-emerald-100 p-7 shadow-sm hover:shadow-lg transition-all"
          >
            <div className="text-emerald-700 text-sm font-semibold uppercase tracking-wide">Cổng nhân viên</div>
            <h2 className="mt-3 text-3xl font-extrabold text-emerald-900">Chấm Công</h2>
            <p className="mt-3 text-emerald-800/90 text-sm">
              Dành cho nhân viên đăng nhập và chấm công khuôn mặt cho chính mình.
            </p>
            <div className="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-emerald-900">
              Vào cổng chấm công
              <span className="transition-transform group-hover:translate-x-1">→</span>
            </div>
          </Link>

          <Link
            to={ROUTES.login}
            className="group rounded-3xl border border-sky-200 bg-gradient-to-br from-sky-50 to-blue-100 p-7 shadow-sm hover:shadow-lg transition-all"
          >
            <div className="text-sky-700 text-sm font-semibold uppercase tracking-wide">Cổng quản trị</div>
            <h2 className="mt-3 text-3xl font-extrabold text-sky-900">Quản Trị</h2>
            <p className="mt-3 text-sky-900/90 text-sm">
              Dành cho quản trị viên quản lý camera, dữ liệu khuôn mặt, báo cáo và cài đặt hệ thống.
            </p>
            <div className="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-sky-900">
              Vào cổng quản trị
              <span className="transition-transform group-hover:translate-x-1">→</span>
            </div>
          </Link>
        </div>
      </div>
    </div>
  )
}
