import { BrowserRouter, Route, Routes } from 'react-router-dom'
import { SignInPage, SignUpPage } from '../auth/AuthPages'
import { ProtectedRoute } from '../auth/ProtectedRoute'
import { AppShell } from '../components/layout/PublicShell'
import { ResidentDashboardPage, ResidentNotificationsPage, MyReportsPage, ProfilePage, ReportDetailsPage } from '../features/portal/ResidentPages'
import { AdminAnalyticsPage, AdminAuditPage, AdminCategoriesPage, NotificationsPage, StaffProfilePage, StaffQueuePage, StaffReportDetailsPage, StaffShell, UserManagementPage } from '../features/portal/StaffPages'
import { ReportPage } from '../features/reports/ReportPage'
import { AboutPage, AnnouncementsPage, CommunityMapPage, HomePage, NotFoundPage, TrackPage } from '../pages/PublicPages'
import { SplashPage, WelcomePage } from '../pages/OnboardingPages'

const resident = ['resident'] as const
const staff = ['intake-officer','barangay-officer','city-department-officer','system-administrator'] as const

export function AppRouter() { return <BrowserRouter><Routes>
  <Route path="/" element={<SplashPage/>}/><Route path="/welcome" element={<WelcomePage/>}/>
  <Route element={<AppShell/>}>
    <Route path="/home" element={<HomePage/>}/><Route path="/track" element={<TrackPage/>}/><Route path="/map" element={<CommunityMapPage/>}/><Route path="/announcements" element={<AnnouncementsPage/>}/><Route path="/about" element={<AboutPage/>}/>
    <Route element={<ProtectedRoute roles={[...resident]}/>}><Route path="/dashboard" element={<ResidentDashboardPage/>}/><Route path="/report" element={<ReportPage/>}/><Route path="/my-reports" element={<MyReportsPage/>}/><Route path="/my-reports/:id" element={<ReportDetailsPage/>}/><Route path="/notifications" element={<ResidentNotificationsPage/>}/><Route path="/profile" element={<ProfilePage/>}/></Route>
  </Route>
  <Route path="/sign-in" element={<SignInPage/>}/><Route path="/sign-up" element={<SignUpPage/>}/><Route path="/staff/login" element={<SignInPage/>}/>
  <Route element={<ProtectedRoute roles={[...staff]}/>}><Route path="/staff" element={<StaffShell/>}><Route index element={<StaffQueuePage/>}/><Route path="reports/:id" element={<StaffReportDetailsPage/>}/><Route path="notifications" element={<NotificationsPage/>}/><Route path="profile" element={<StaffProfilePage/>}/><Route element={<ProtectedRoute roles={['system-administrator']}/>}><Route path="users" element={<UserManagementPage/>}/><Route path="categories" element={<AdminCategoriesPage/>}/><Route path="analytics" element={<AdminAnalyticsPage/>}/><Route path="audit-logs" element={<AdminAuditPage/>}/></Route></Route></Route>
  <Route path="*" element={<NotFoundPage/>}/>
</Routes></BrowserRouter> }
