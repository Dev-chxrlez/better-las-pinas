const configuredBase = (import.meta.env.VITE_API_BASE_URL as string | undefined)?.replace(/\/$/, '') ?? (import.meta.env.DEV ? 'http://localhost:8000' : '')

export class ApiError extends Error {
  status: number
  errors: Record<string, string[]>
  constructor(message: string, status: number, errors: Record<string, string[]> = {}) {
    super(message)
    this.status = status
    this.errors = errors
  }
}

function cookie(name: string) {
  return document.cookie.split('; ').find(value => value.startsWith(`${name}=`))?.split('=').slice(1).join('=')
}

async function parse(response: Response) {
  const payload = await response.json().catch(() => ({}))
  if (!response.ok) throw new ApiError(payload.message || 'Something went wrong. Please try again.', response.status, payload.errors || {})
  return payload
}

async function request(url: string, options: RequestInit) {
  try {
    return await fetch(url, options)
  } catch {
    throw new ApiError('Cannot connect to the Better Las Piñas service. Please make sure the local API is running, then try again.', 0)
  }
}

export async function csrf() {
  await request(`${configuredBase}/sanctum/csrf-cookie`, { credentials: 'include', headers: { Accept: 'application/json' } }).then(parse)
}

export async function api<T>(path: string, options: RequestInit = {}): Promise<T> {
  const headers = new Headers(options.headers)
  headers.set('Accept', 'application/json')
  if (options.body && !(options.body instanceof FormData)) headers.set('Content-Type', 'application/json')
  const xsrf = cookie('XSRF-TOKEN')
  if (xsrf) headers.set('X-XSRF-TOKEN', decodeURIComponent(xsrf))
  return parse(await request(`${configuredBase}/api/v1${path}`, { ...options, headers, credentials: 'include' })) as Promise<T>
}

export function json(method: string, body?: unknown): RequestInit {
  return { method, body: body === undefined ? undefined : JSON.stringify(body) }
}
