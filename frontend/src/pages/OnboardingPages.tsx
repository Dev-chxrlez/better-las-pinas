import { ArrowRight, ChevronRight } from 'lucide-react'
import { Link, useNavigate } from 'react-router-dom'

const seal = '/branding/las-pinas-seal.png'

export function SplashPage() {
  const navigate = useNavigate()
  const continueToWelcome = () => navigate('/welcome')

  return <main className="onboarding splash-screen" role="button" tabIndex={0} aria-label="Continue to Better Las Piñas" onClick={continueToWelcome} onKeyDown={event => { if (event.key === 'Enter' || event.key === ' ') continueToWelcome() }}>
    <div className="onboarding-orb onboarding-orb--top" aria-hidden="true" />
    <div className="splash-identity">
      <img src={seal} alt="City of Las Piñas seal" />
      <h1>Better Las Piñas</h1>
      <p>Your voice. A better city.</p>
    </div>
    <span className="splash-continue">Tap anywhere to continue <ArrowRight aria-hidden="true" /></span>
    <div className="onboarding-orb onboarding-orb--bottom" aria-hidden="true" />
  </main>
}

export function WelcomePage() {
  return <main className="onboarding welcome-screen">
    <section className="welcome-hero" aria-label="Better Las Piñas">
      <div className="onboarding-orb onboarding-orb--top" aria-hidden="true" />
      <img src={seal} alt="City of Las Piñas seal" />
      <strong>Better Las Piñas</strong>
      <span>Your voice. Our community.</span>
    </section>
    <section className="welcome-sheet">
      <span className="welcome-kicker">Together for Las Piñas</span>
      <h1>Welcome</h1>
      <p>Report community concerns, follow their progress, and help make Las Piñas safer and better connected.</p>
      <div className="welcome-actions">
        <Link className="welcome-button welcome-button--primary" to="/sign-in">Sign In <ChevronRight aria-hidden="true" /></Link>
        <Link className="welcome-button welcome-button--secondary" to="/sign-up">Sign Up</Link>
      </div>
      <Link className="guest-link" to="/home">Explore public information as guest</Link>
    </section>
  </main>
}
