import type { LucideIcon } from 'lucide-react'
export function EmptyState({ icon: Icon, title, message }: { icon: LucideIcon; title: string; message: string }) { return <div className="empty-state"><span className="empty-state__icon"><Icon aria-hidden="true" /></span><h2>{title}</h2><p>{message}</p></div> }
