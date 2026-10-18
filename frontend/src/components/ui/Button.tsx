import type { ButtonHTMLAttributes, ReactNode } from 'react'
import { Link } from 'react-router-dom'
type Props = ButtonHTMLAttributes<HTMLButtonElement> & { children: ReactNode; variant?: 'primary' | 'secondary' | 'ghost' }
export function Button({ children, variant = 'primary', className = '', ...props }: Props) { return <button className={`button button--${variant} ${className}`} {...props}>{children}</button> }
export function ButtonLink({ to, children, variant = 'primary', className = '' }: { to: string; children: ReactNode; variant?: Props['variant']; className?: string }) { return <Link to={to} className={`button button--${variant} ${className}`}>{children}</Link> }
