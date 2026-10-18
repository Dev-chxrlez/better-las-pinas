import { Bell, CircleUserRound, FilePlus2, HelpCircle, Home, Info, LogIn, Map, Megaphone, Search, ShieldCheck } from 'lucide-react'
import { NavLink, Outlet, useLocation } from 'react-router-dom'
import { Brand } from '../ui/Brand'
import { DISCLAIMER } from '../../lib/constants'
import { useAuth } from '../../auth/authStore'

const publicPrimary = [
  { to: '/home', label: 'Home', icon: Home },
  { to: '/map', label: 'Map', icon: Map },
  { to: '/report', label: 'Report', icon: FilePlus2, prominent: true },
  { to: '/track', label: 'Track', icon: Search },
  { to: '/profile', label: 'Profile', icon: CircleUserRound },
]

const residentPrimary = [
  { to: '/dashboard', label: 'Home', icon: Home },
  { to: '/map', label: 'Map', icon: Map },
  { to: '/report', label: 'Report', icon: FilePlus2, prominent: true },
  { to: '/my-reports', label: 'Track', icon: Search },
  { to: '/profile', label: 'Profile', icon: CircleUserRound },
]

const publicSecondary = [
  { to: '/announcements', label: 'Announcements', icon: Bell },
  { to: '/about', label: 'About this app', icon: Info },
  { to: '/sign-in', label: 'Sign in', icon: LogIn },
]

const residentSecondary = [
  { to: '/notifications', label: 'Notifications', icon: Bell },
  { to: '/announcements', label: 'Announcements', icon: Megaphone },
  { to: '/about', label: 'About this app', icon: Info },
]

export function AppShell() {
  const location = useLocation()
  const { user } = useAuth()
  const isResident = user?.role === 'resident'
  const primary = isResident ? residentPrimary : publicPrimary
  const secondary = isResident ? residentSecondary : publicSecondary
  const bellTarget = isResident ? '/notifications' : '/announcements'

  return <div className="app-shell">
    <aside className="app-sidebar" aria-label="Application navigation">
      <div className="sidebar-brand"><Brand /></div>
      <nav className="sidebar-nav" aria-label="Primary navigation">
        {primary.map(({ to, label, icon: Icon, prominent }) => <NavLink key={to} to={to} end={to === '/home' || to === '/dashboard'} className={prominent ? 'nav-report' : ''}><Icon aria-hidden="true" /><span>{label}</span></NavLink>)}
      </nav>
      <div className="sidebar-section"><p>More</p><nav aria-label="Secondary navigation">{secondary.map(({ to, label, icon: Icon }) => <NavLink key={to} to={to}><Icon aria-hidden="true" /><span>{label}</span></NavLink>)}</nav></div>
      <div className="sidebar-notice"><ShieldCheck /><p><strong>Independent prototype</strong>Not an official city government service.</p></div>
    </aside>

    <div className="app-viewport">
      <header className="mobile-appbar">
        <Brand />
        <NavLink to={bellTarget} className="appbar-action" aria-label={isResident ? 'Notifications' : 'Announcements'}><Bell /></NavLink>
      </header>
      <main className="app-main" id="main-content">
        <div className="screen-transition" key={location.pathname}><Outlet /></div>
      </main>
      <nav className="bottom-nav" aria-label="Primary navigation">
        {primary.map(({ to, label, icon: Icon, prominent }) => <NavLink key={to} to={to} end={to === '/home' || to === '/dashboard'} className={prominent ? 'bottom-nav__report' : ''}><span><Icon aria-hidden="true" /></span><small>{label}</small></NavLink>)}
      </nav>
    </div>

    <span className="sr-only">{DISCLAIMER}</span>
    <NavLink to="/about" className="mobile-help" aria-label="Help and about"><HelpCircle /></NavLink>
  </div>
}

export const PublicShell = AppShell
