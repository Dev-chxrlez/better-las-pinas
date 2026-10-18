import { Link } from 'react-router-dom'

export function Brand({ compact = false }: { compact?: boolean }) {
  return <Link to="/home" className="brand" aria-label="Better Las Piñas home">
    <img className="brand__mark" src="/branding/las-pinas-seal.png" alt="" aria-hidden="true" />
    {!compact && <span><strong>Better Las Piñas</strong><small>Your Voice. Our Community.</small></span>}
  </Link>
}
