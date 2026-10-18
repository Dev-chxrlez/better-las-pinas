import { ChevronRight, Eye, EyeOff } from 'lucide-react'
import { useState } from 'react'
import type { FormEvent } from 'react'
import { Link, Navigate, useLocation, useNavigate } from 'react-router-dom'
import { Brand } from '../components/ui/Brand'
import { Button } from '../components/ui/Button'
import { ApiError } from '../lib/api'
import { useAuth } from './authStore'

function destination(role: string) { return role === 'resident' ? '/dashboard' : '/staff' }

export function SignInPage() {
  const { user, login } = useAuth(); const navigate = useNavigate(); const location = useLocation()
  const [email, setEmail] = useState(''); const [password, setPassword] = useState(''); const [show, setShow] = useState(false); const [error, setError] = useState(''); const [busy, setBusy] = useState(false)
  if (user) return <Navigate to={destination(user.role)} replace />
  const submit = async (event: FormEvent) => { event.preventDefault(); setBusy(true); setError(''); try { const signedIn = await login(email, password); const wanted = (location.state as { from?: string } | null)?.from; navigate(wanted || destination(signedIn.role), { replace: true }) } catch (reason) { setError(reason instanceof ApiError ? reason.message : 'Unable to sign in.') } finally { setBusy(false) } }
  return <AuthFrame title="Welcome back" subtitle="Sign in to submit and follow your reports."><form className="auth-form" onSubmit={submit}>{error && <p className="form-alert" role="alert">{error}</p>}<label>Email<input required type="email" autoComplete="email" value={email} onChange={e => setEmail(e.target.value)} /></label><label>Password<span className="password-field"><input required type={show ? 'text' : 'password'} autoComplete="current-password" value={password} onChange={e => setPassword(e.target.value)} /><button type="button" onClick={() => setShow(value => !value)} aria-label={show ? 'Hide password' : 'Show password'}>{show ? <EyeOff /> : <Eye />}</button></span></label><Button disabled={busy}>{busy ? 'Signing in…' : 'Sign in'}</Button><SocialAuthButtons /><p>New to Better Las Piñas? <Link to="/sign-up">Create an account</Link></p></form></AuthFrame>
}

export function SignUpPage() {
  const { user, register } = useAuth(); const navigate = useNavigate(); const [form, setForm] = useState({ full_name: '', email: '', password: '', password_confirmation: '' }); const [show, setShow] = useState(false); const [error, setError] = useState(''); const [fieldErrors, setFieldErrors] = useState<Record<string,string[]>>({}); const [busy, setBusy] = useState(false)
  if (user) return <Navigate to={destination(user.role)} replace />
  const set = (key: keyof typeof form) => (event: React.ChangeEvent<HTMLInputElement>) => setForm(value => ({ ...value, [key]: event.target.value }))
  const submit = async (event: FormEvent) => { event.preventDefault(); setBusy(true); setError(''); setFieldErrors({}); try { const created = await register(form); navigate(destination(created.role), { replace: true }) } catch (reason) { if (reason instanceof ApiError) { setError(reason.message); setFieldErrors(reason.errors) } else setError('Unable to create your account.') } finally { setBusy(false) } }
  return <AuthFrame title="Create your account" subtitle="One secure account keeps all your reports in one place."><form className="auth-form" onSubmit={submit}>{error && <p className="form-alert" role="alert">{error}</p>}<AuthField label="Full name" error={fieldErrors.full_name?.[0]}><input required autoComplete="name" value={form.full_name} onChange={set('full_name')} /></AuthField><AuthField label="Email" error={fieldErrors.email?.[0]}><input required type="email" autoComplete="email" value={form.email} onChange={set('email')} /></AuthField><AuthField label="Password" error={fieldErrors.password?.[0]}><span className="password-field"><input required minLength={12} type={show ? 'text' : 'password'} autoComplete="new-password" value={form.password} onChange={set('password')} /><button type="button" onClick={() => setShow(value => !value)} aria-label={show ? 'Hide password' : 'Show password'}>{show ? <EyeOff /> : <Eye />}</button></span></AuthField><small>Use at least 12 characters with uppercase, lowercase, a number, and a symbol.</small><label>Confirm password<input required type={show ? 'text' : 'password'} autoComplete="new-password" value={form.password_confirmation} onChange={set('password_confirmation')} /></label><Button disabled={busy}>{busy ? 'Creating account…' : 'Create account'}</Button><SocialAuthButtons /><p>Already registered? <Link to="/sign-in">Sign in</Link></p></form></AuthFrame>
}

function SocialAuthButtons() {
  const [notice, setNotice] = useState('')
  const preview = (provider: string) => setNotice(`${provider} sign-in is a visual preview and is not connected yet.`)
  return <div className="social-auth">
    <div className="social-divider"><span>or</span></div>
    <button type="button" className="social-button" onClick={() => preview('Google')}><GoogleLogo />Continue with Google<ChevronRight aria-hidden="true" /></button>
    <button type="button" className="social-button" onClick={() => preview('Facebook')}><FacebookLogo />Continue with Facebook<ChevronRight aria-hidden="true" /></button>
    {notice && <p className="social-notice" role="status">{notice}</p>}
  </div>
}

function GoogleLogo() { return <svg className="social-mark" viewBox="0 0 24 24" role="img" aria-label="Google"><path fill="#4285F4" d="M21.6 12.23c0-.71-.06-1.4-.18-2.06H12v3.9h5.38a4.6 4.6 0 0 1-2 3.02v2.53h3.24c1.9-1.75 2.98-4.32 2.98-7.39Z"/><path fill="#34A853" d="M12 22c2.7 0 4.97-.9 6.62-2.38l-3.24-2.53c-.9.6-2.05.96-3.38.96-2.61 0-4.82-1.76-5.61-4.13H3.04v2.61A10 10 0 0 0 12 22Z"/><path fill="#FBBC05" d="M6.39 13.92A6.02 6.02 0 0 1 6.08 12c0-.67.11-1.32.31-1.92V7.47H3.04A10 10 0 0 0 2 12c0 1.61.38 3.14 1.04 4.53l3.35-2.61Z"/><path fill="#EA4335" d="M12 5.95c1.47 0 2.79.5 3.83 1.5l2.87-2.87A9.64 9.64 0 0 0 12 2a10 10 0 0 0-8.96 5.47l3.35 2.61C7.18 7.71 9.39 5.95 12 5.95Z"/></svg> }
function FacebookLogo() { return <svg className="social-mark" viewBox="0 0 24 24" role="img" aria-label="Facebook"><circle cx="12" cy="12" r="12" fill="#1877F2"/><path fill="#fff" d="M16.67 15.47 17.2 12h-3.33V9.75c0-.95.47-1.88 1.96-1.88h1.51V4.92s-1.37-.24-2.69-.24c-2.74 0-4.53 1.66-4.53 4.67V12H7.08v3.47h3.04v8.39a12.1 12.1 0 0 0 3.75 0v-8.39h2.8Z"/></svg> }

function AuthField({ label, error, children }: { label: string; error?: string; children: React.ReactNode }) { return <label>{label}{children}{error && <small className="field__error">{error}</small>}</label> }
function AuthFrame({ title, subtitle, children }: { title: string; subtitle: string; children: React.ReactNode }) { return <main className="auth-screen"><section className="auth-brand"><Brand /><div><h1>Together for a<br/>Better Las Piñas.</h1><p>Report concerns, follow progress, and help improve the community we call home.</p></div><small>Your voice. Our community. A better city.</small></section><section className="auth-panel"><div><h2>{title}</h2><p>{subtitle}</p>{children}</div></section></main> }
