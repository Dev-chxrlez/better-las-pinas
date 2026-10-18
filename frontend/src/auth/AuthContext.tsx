import { useEffect, useMemo, useState } from 'react'
import type { ReactNode } from 'react'
import { api, csrf, json } from '../lib/api'
import { AuthContext } from './authStore'
import type { AuthContextValue, AuthUser } from './authStore'

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<AuthUser | null>(null)
  const [loading, setLoading] = useState(true)
  useEffect(() => { api<{ data: { user: AuthUser } }>('/auth/me').then(result => setUser(result.data.user)).catch(() => setUser(null)).finally(() => setLoading(false)) }, [])
  const value = useMemo<AuthContextValue>(() => ({
    user, loading,
    login: async (email, password) => { await csrf(); const result = await api<{ data: { user: AuthUser } }>('/auth/login', json('POST', { email, password })); setUser(result.data.user); return result.data.user },
    register: async data => { await csrf(); const result = await api<{ data: { user: AuthUser } }>('/auth/register', json('POST', data)); setUser(result.data.user); return result.data.user },
    logout: async () => { await csrf(); await api('/auth/logout', json('POST')); setUser(null) },
  }), [user, loading])
  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}
