import { Navigate, Outlet, useLocation } from 'react-router-dom'
import type { Role } from './authStore'
import { useAuth } from './authStore'
import { PageSkeleton } from '../components/ui/PageSkeleton'

export function ProtectedRoute({ roles }: { roles?: Role[] }) {
  const { user, loading } = useAuth()
  const location = useLocation()
  if (loading) return <PageSkeleton />
  if (!user) return <Navigate to="/sign-in" replace state={{ from: location.pathname }} />
  if (roles && !roles.includes(user.role)) return <Navigate to={user.role === 'resident' ? '/dashboard' : '/staff'} replace />
  return <Outlet />
}
