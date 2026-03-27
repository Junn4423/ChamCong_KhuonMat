import { HashRouter, Routes, Route } from 'react-router-dom'
import Layout from './components/Layout'
import Dashboard from './pages/Dashboard'
import Attendance from './pages/Attendance'
import Register from './pages/Register'
import ManageFaces from './pages/ManageFaces'
import Report from './pages/Report'
import Login from './pages/Login'
import ApiTest from './pages/ApiTest'

export default function App() {
  return (
    <HashRouter>
      <Routes>
        <Route path="/login" element={<Login />} />
        <Route element={<Layout />}>
          <Route path="/" element={<Dashboard />} />
          <Route path="/attendance" element={<Attendance />} />
          <Route path="/register" element={<Register />} />
          <Route path="/manage" element={<ManageFaces />} />
          <Route path="/report" element={<Report />} />
          <Route path="/api-test" element={<ApiTest />} />
        </Route>
      </Routes>
    </HashRouter>
  )
}
