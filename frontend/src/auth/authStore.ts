import { createContext, useContext } from 'react'

export type Role = 'resident' | 'intake-officer' | 'barangay-officer' | 'city-department-officer' | 'system-administrator'
export type AuthUser = { id: number; name: string; email: string; role: Role }
export type RegisterData = { full_name: string; email: string; password: string; password_confirmation: string }
export type AuthContextValue = { user: AuthUser | null; loading: boolean; login: (email: string, password: string) => Promise<AuthUser>; register: (data: RegisterData) => Promise<AuthUser>; logout: () => Promise<void> }

export const AuthContext = createContext<AuthContextValue | null>(null)
export function useAuth() { const value = useContext(AuthContext); if (!value) throw new Error('useAuth must be inside AuthProvider'); return value }
