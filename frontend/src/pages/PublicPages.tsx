import {
  AlertCircle, Bell, ChevronDown, ChevronRight, ClipboardList, Construction,
  FilePlus2, FileSearch, Info, Layers3, Lightbulb, LockKeyhole, Map,
  Megaphone, Navigation, Recycle, Search, ShieldCheck, Wrench, X,
} from 'lucide-react'
import type { LucideIcon } from 'lucide-react'
import { useEffect, useMemo, useState } from 'react'
import { Link } from 'react-router-dom'
import { MapContainer, Marker, Popup, TileLayer, useMap } from 'react-leaflet'
import { divIcon } from 'leaflet'
import { Button, ButtonLink } from '../components/ui/Button'
import { Brand } from '../components/ui/Brand'
import { EmptyState } from '../components/shared/EmptyState'
import { BARANGAYS, CATEGORIES, DISCLAIMER } from '../lib/constants'
import { api } from '../lib/api'

const quickActions = [
  { to: '/report', label: 'Submit Report', icon: FilePlus2, tone: 'green' },
  { to: '/track', label: 'Track Report', icon: Search, tone: 'gold' },
  { to: '/map', label: 'Community Map', icon: Map, tone: 'blue' },
  { to: '/announcements', label: 'Announcements', icon: Bell, tone: 'plum' },
]

const categoryTiles = [
  { label: 'Road Damage', icon: Construction },
  { label: 'Flooding', icon: Layers3 },
  { label: 'Garbage', icon: Recycle },
  { label: 'Streetlights', icon: Lightbulb },
  { label: 'Infrastructure', icon: Wrench },
  { label: 'Other Issues', icon: ClipboardList },
]

const resolvedLocationIcon=divIcon({className:'resolved-location-marker',html:'<span><i></i></span>',iconSize:[44,52],iconAnchor:[22,48],popupAnchor:[0,-45]})

function MapViewport({positions}:{positions:[number,number][]}) {
  const map=useMap()
  useEffect(()=>{if(positions.length===1)map.setView(positions[0],15,{animate:true});else if(positions.length>1)map.fitBounds(positions,{padding:[42,42],maxZoom:15,animate:true})},[map,positions])
  return null
}

export function HomePage() {
  return <div className="screen home-screen">
    <header className="dashboard-header">
      <div><span className="screen-kicker">Good day</span><h1>Magandang araw, Las Piñero!</h1><p>How can we help your community today?</p></div>
      <Link to="/announcements" className="desktop-header-action"><Bell /><span>Announcements</span></Link>
    </header>

    <section className="primary-action-card">
      <div className="action-card__content"><h2>May concern sa inyong lugar?</h2><p>Report community issues quickly and easily.</p><ButtonLink to="/report" className="action-card__button">Report an Issue <ChevronRight /></ButtonLink></div>
      <div className="action-card__art action-card__art--city-hall" aria-hidden="true"><i /><i /><img src="/branding/las-pinas-city-hall-hero.png" alt="" /></div>
    </section>

    <section className="dashboard-section"><div className="dashboard-section__title"><h2>Quick actions</h2></div><div className="quick-action-grid">{quickActions.map(({ to, label, icon: Icon, tone }) => <Link to={to} className={`quick-action quick-action--${tone}`} key={to}><span><Icon /></span><strong>{label}</strong><ChevronRight className="quick-action__arrow" /></Link>)}</div></section>

    <section className="dashboard-section"><div className="dashboard-section__title"><div><h2>Common concerns</h2><p>Choose a category to start</p></div><Link to="/report">View all</Link></div><div className="category-tile-grid">{categoryTiles.map(({ label, icon: Icon }) => <Link to="/report" className="category-tile" key={label}><span><Icon /></span><small>{label}</small></Link>)}</div></section>

    <section className="dashboard-section how-card"><div className="dashboard-section__title"><div><h2>How it works</h2><p>Three simple steps</p></div></div><ol>{[['1','Submit concern','Tell us what happened.'],['2','Select location','Pinpoint where it is.'],['3','Track progress','Use private access later.']].map(([number,title,text]) => <li key={number}><span>{number}</span><div><strong>{title}</strong><p>{text}</p></div></li>)}</ol></section>

  </div>
}

export function TrackPage() {
  const [attempted, setAttempted] = useState(false)
  return <AppPage icon={Search} title="Track a report" subtitle="Private, secure access to future report updates.">
    <div className="track-layout track-layout--single">
      <form className="surface-card track-card" onSubmit={(event) => { event.preventDefault(); setAttempted(true) }} noValidate>
        <div className="track-icon"><FileSearch /></div><h2>Enter tracking details</h2><p>Your reference number alone will never unlock private report information.</p>
        <label>Report reference<input placeholder="Enter your report reference" aria-describedby="reference-help" /></label><small id="reference-help">Provided after a future live submission.</small>
        <label>Private access key<input type="password" placeholder="Enter private access key" /></label>
        {attempted && <div className="inline-message inline-message--warning" role="status"><AlertCircle /><span>Live tracking is not available in this prototype. No lookup was performed.</span></div>}
        <Button type="submit"><Search /> Check status</Button>
      </form>
    </div>
  </AppPage>
}

export function CommunityMapPage() {
  const [sheetOpen, setSheetOpen] = useState(true)
  const [filtersOpen, setFiltersOpen] = useState(false)
  const [category,setCategory]=useState('')
  const [barangay,setBarangay]=useState('')
  const [reports,setReports]=useState<{id:string;latitude:number;longitude:number;status:string;category:string;barangay:string}[]>([])
  const [mapError,setMapError]=useState('')
  useEffect(()=>{void api<{data:{items:typeof reports}}>('/map/resolved-reports').then(result=>setReports(result.data.items)).catch(()=>setMapError('Resolved locations could not be loaded.'))},[])
  const visibleReports=useMemo(()=>reports.filter(report=>(!category||report.category===category)&&(!barangay||report.barangay===barangay)),[reports,category,barangay])
  const markerPositions=useMemo<[number,number][]>(()=>visibleReports.map(report=>[report.latitude,report.longitude]),[visibleReports])
  const categories=[...new Set(reports.map(report=>report.category))].sort()
  const barangays=[...new Set(reports.map(report=>report.barangay))].sort()
  return <div className="map-screen">
    <header className="map-screen__header"><div><span className="screen-kicker">Public information</span><h1>Community map</h1></div><button className="map-filter-button" onClick={() => setFiltersOpen(!filtersOpen)} aria-expanded={filtersOpen}><Layers3 /> Filters</button></header>
    <div className="map-canvas">
      <MapContainer center={[14.45, 120.98]} zoom={12} scrollWheelZoom aria-label="Map of resolved community reports in Las Piñas"><TileLayer attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors' url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" /><MapViewport positions={markerPositions}/>{visibleReports.map(report=><Marker key={report.id} position={[report.latitude,report.longitude]} icon={resolvedLocationIcon} zIndexOffset={500}><Popup><div className="resolved-map-popup"><span><Navigation/> Resolved location</span><strong>{report.category}</strong><small>{report.barangay} · {report.status==='closed'?'Closed':'Resolved'}</small></div></Popup></Marker>)}</MapContainer>
      <div className={`map-filters ${filtersOpen ? 'is-open' : ''}`}><div className="map-filters__heading"><strong>Filter resolved reports</strong><button onClick={() => setFiltersOpen(false)} aria-label="Close filters"><X /></button></div><label>Category<select value={category} onChange={event=>setCategory(event.target.value)}><option value="">All categories</option>{(categories.length?categories:CATEGORIES).map(item => <option key={item}>{item}</option>)}</select></label><label>Barangay<select value={barangay} onChange={event=>setBarangay(event.target.value)}><option value="">All barangays</option>{(barangays.length?barangays:BARANGAYS).map(item => <option key={item}>{item}</option>)}</select></label></div>
      <div className={`map-sheet ${sheetOpen ? 'is-open' : ''}`}><button className="map-sheet__handle" onClick={() => setSheetOpen(!sheetOpen)} aria-expanded={sheetOpen}><span /><span className="sr-only">Toggle map information</span></button><div className="map-sheet__summary"><span className="map-sheet__icon"><Navigation /></span><div><strong>{mapError||`${visibleReports.length} resolved ${visibleReports.length===1?'location':'locations'}`}</strong><p>{visibleReports.length?'Tap a green location marker for details.':'Resolved reports with shared GPS coordinates will appear here.'}</p></div><ChevronDown /></div>{sheetOpen && <div className="map-sheet__details"><div className="map-legend"><span><i className="dot dot--green" /> Resolved location</span></div><p>Only the category, barangay, status, and submitted map position are displayed.</p></div>}</div>
    </div>
  </div>
}

export function AnnouncementsPage() {
  return <AppPage icon={Megaphone} title="Announcements" subtitle="Verified platform and community updates will appear here."><div className="surface-card"><EmptyState icon={Bell} title="No announcements yet" message="We will not publish invented or unofficial government announcements. Check back as the prototype develops." /></div></AppPage>
}

export function AboutPage() {
  return <AppPage icon={Info} title="About the app" subtitle="A clearer, safer path for documenting community concerns."><div className="about-stack"><article className="surface-card content-card"><span className="status-pill">Phase 1 · Foundation</span><h2>Built for residents first</h2><p>Better Las Piñas explores how residents could document road, drainage, waste, lighting, and infrastructure concerns without creating an account.</p><div className="feature-list"><span><FilePlus2 /><strong>Simple reporting</strong><small>Guided details, location, and optional evidence.</small></span><span><Navigation /><strong>Clear routing</strong><small>A future workflow for verified staff teams.</small></span><span><ShieldCheck /><strong>Private tracking</strong><small>Secure access—not a reference number alone.</small></span></div></article><aside className="surface-card independent-card"><ShieldCheck /><h2>Independent by design</h2><p>{DISCLAIMER}</p><p>Submitting the prototype form does not result in government action.</p></aside></div></AppPage>
}

function AppPage({ icon: Icon, title, subtitle, children }: { icon: LucideIcon; title: string; subtitle: string; children: React.ReactNode }) {
  return <div className="screen secondary-screen"><header className="screen-header"><span><Icon /></span><div><h1>{title}</h1><p>{subtitle}</p></div></header><div className="screen-content">{children}</div></div>
}

export function StaffLoginPage() {
  return <main className="staff-auth"><section className="staff-brand"><Brand /><div><span className="screen-kicker">Staff workspace</span><h1>One shared view of community concerns.</h1><p>Future authorized teams will triage and monitor cases through a secure workspace.</p></div><small>{DISCLAIMER}</small></section><section className="staff-login-panel"><Link to="/" className="back-link">← Return to public app</Link><div className="staff-form"><span className="staff-icon"><LockKeyhole /></span><h2>Staff sign in</h2><p>Authentication is being prepared for a later phase.</p><label>Work email<input type="email" disabled placeholder="name@organization.gov.ph" /></label><label>Password<input type="password" disabled placeholder="••••••••••••" /></label><Button disabled>Sign in</Button><div className="inline-message inline-message--warning"><Construction /> Sign-in is intentionally disabled. No credentials are accepted or stored.</div></div></section></main>
}

export function StaffDashboardGatePage() {
  const modules = ['Overview','Incoming Reports','Assigned Cases','Case Management','Analytics','Staff Management','Audit Logs','Settings']
  return <main className="gate-page"><Brand /><div className="gate-card"><LockKeyhole /><span className="status-pill">Protected area</span><h1>Staff dashboard unavailable</h1><p>Authentication and authorization are not implemented. This workspace remains inaccessible by design.</p><div className="dashboard-preview">{modules.map(module => <span key={module}>{module}<small>Not operational</small></span>)}</div><ButtonLink to="/staff/login">Return to staff sign in</ButtonLink><ButtonLink to="/" variant="ghost">Go to public app</ButtonLink></div></main>
}

export function NotFoundPage() {
  return <main className="not-found"><Brand /><div><span>404</span><h1>This screen wandered off the map.</h1><p>The address may have changed, or the screen might not exist.</p><ButtonLink to="/">Return home</ButtonLink></div></main>
}
