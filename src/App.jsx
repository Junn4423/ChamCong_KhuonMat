import { useEffect, useState } from 'react'
import { HashRouter, Routes, Route, Navigate } from 'react-router-dom'
import { ToastProvider } from './components/Toast'
import Layout from './components/Layout'
import Dashboard from './pages/Dashboard'
import Attendance from './pages/Attendance'
import Cameras from './pages/Cameras'
import Register from './pages/Register'
import ManageFaces from './pages/ManageFaces'
import Report from './pages/Report'
import OnlineAttendanceCheck from './pages/OnlineAttendanceCheck'
import SyncVerify from './pages/SyncVerify'
import Login from './pages/Login'
import SystemSettings from './pages/SystemSettings'
import { LEGACY_ROUTE_ALIASES, ROUTES } from './config/routes'
import {
  MODULE_SETTINGS_EVENT,
  MODULE_TOGGLE_KEYS,
  isModuleEnabled,
} from './services/moduleSettings'
import { syncSystemSettingsFromServer } from './services/systemSettingsStore'

function ModuleGate({ moduleKey, children }) {
  const [enabled, setEnabled] = useState(() => isModuleEnabled(moduleKey))

  useEffect(() => {
    function refreshModuleState() {
      setEnabled(isModuleEnabled(moduleKey))
    }

    syncSystemSettingsFromServer()
    refreshModuleState()
    window.addEventListener(MODULE_SETTINGS_EVENT, refreshModuleState)
    window.addEventListener('storage', refreshModuleState)
    return () => {
      window.removeEventListener(MODULE_SETTINGS_EVENT, refreshModuleState)
      window.removeEventListener('storage', refreshModuleState)
    }
  }, [moduleKey])

  if (!enabled) {
    return <Navigate to={ROUTES.systemSettings} replace />
  }
  return children
}

export default function App() {
  return (
    <ToastProvider>
      <HashRouter>
        <Routes>
          <Route path={ROUTES.login} element={<Login />} />
          <Route element={<Layout />}>
            <Route path={ROUTES.dashboard} element={<Dashboard />} />
            <Route
              path={ROUTES.attendance}
              element={(
                <ModuleGate moduleKey={MODULE_TOGGLE_KEYS.attendance}>
                  <Attendance />
                </ModuleGate>
              )}
            />
            <Route
              path={ROUTES.cameraManagement}
              element={(
                <ModuleGate moduleKey={MODULE_TOGGLE_KEYS.cameraManagement}>
                  <Cameras />
                </ModuleGate>
              )}
            />
            <Route
              path={ROUTES.onlineSync}
              element={(
                <ModuleGate moduleKey={MODULE_TOGGLE_KEYS.onlineSync}>
                  <Register />
                </ModuleGate>
              )}
            />
            <Route
              path={ROUTES.syncVerify}
              element={(
                <ModuleGate moduleKey={MODULE_TOGGLE_KEYS.syncVerify}>
                  <SyncVerify />
                </ModuleGate>
              )}
            />
            <Route
              path={ROUTES.offlineManage}
              element={(
                <ModuleGate moduleKey={MODULE_TOGGLE_KEYS.offlineManage}>
                  <ManageFaces />
                </ModuleGate>
              )}
            />
            <Route
              path={ROUTES.report}
              element={(
                <ModuleGate moduleKey={MODULE_TOGGLE_KEYS.report}>
                  <Report />
                </ModuleGate>
              )}
            />
            <Route
              path={ROUTES.onlineAttendanceCheck}
              element={(
                <ModuleGate moduleKey={MODULE_TOGGLE_KEYS.onlineAttendanceCheck}>
                  <OnlineAttendanceCheck />
                </ModuleGate>
              )}
            />
            <Route path={ROUTES.systemSettings} element={<SystemSettings />} />
            {LEGACY_ROUTE_ALIASES.map(alias => (
              <Route
                key={alias.from}
                path={alias.from}
                element={<Navigate to={alias.to} replace />}
              />
            ))}
          </Route>
          <Route path="*" element={<Navigate to={ROUTES.dashboard} replace />} />
        </Routes>
      </HashRouter>
    </ToastProvider>
  )
}
