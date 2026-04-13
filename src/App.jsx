import { HashRouter, Routes, Route, Navigate } from 'react-router-dom'
import { ToastProvider } from './components/Toast'
import Layout from './components/Layout'
import Dashboard from './pages/Dashboard'
import Attendance from './pages/Attendance'
import Cameras from './pages/Cameras'
import Register from './pages/Register'
import ManageFaces from './pages/ManageFaces'
import Report from './pages/Report'
import Login from './pages/Login'

export default function App() {
  return (
    <ToastProvider>
      <HashRouter>
        <Routes>
          <Route path="/login" element={<Login />} />
          <Route element={<Layout />}>
            <Route path="/" element={<Dashboard />} />
            <Route path="/attendance" element={<Attendance />} />
            <Route path="/cameras" element={<Cameras />} />
            <Route path="/register" element={<Register />} />
            <Route path="/manage" element={<ManageFaces />} />
            <Route path="/report" element={<Report />} />
          </Route>
          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </HashRouter>
    </ToastProvider>
  )
}
