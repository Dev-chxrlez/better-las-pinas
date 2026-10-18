type SkeletonVariant = 'page' | 'dashboard' | 'list' | 'detail' | 'admin' | 'staff' | 'table' | 'analytics' | 'profile'

const Rows = ({ count = 4 }: { count?: number }) => <div className="skeleton-list">{Array.from({length:count},(_,item)=><span key={item}><i/><b><em/><em/></b><i/></span>)}</div>
const Metrics = ({ count = 4 }: { count?: number }) => <div className="skeleton-grid">{Array.from({length:count},(_,item)=><i key={item}/>)}</div>
const Heading = () => <div className="skeleton-heading"><i/><i/></div>

export function PageSkeleton({ variant = 'page' }: { variant?: SkeletonVariant }) {
  return <div className={`page-skeleton page-skeleton--${variant}`} role="status" aria-label="Loading content" aria-live="polite">
    <span className="sr-only">Loading content…</span>
    {variant === 'page' && <><Heading/><div className="skeleton-feature"><i/><span><b/><b/><b/></span></div><Metrics/><Rows/></>}
    {variant === 'dashboard' && <><div className="skeleton-welcome"><i/><b/></div><div className="skeleton-progress"><i/><span><b/><b/><em/></span></div><Metrics/><div className="skeleton-section-title"><i/><i/></div><Rows count={3}/></>}
    {variant === 'list' && <><div className="skeleton-search"><i/><i/></div><Rows count={5}/><div className="skeleton-pagination"><i/><b/><i/></div></>}
    {variant === 'detail' && <div className="skeleton-detail"><section><i/><b/><em/><em/><em/><em/><b/></section><section><b/>{[0,1,2,3].map(item=><span key={item}><i/><em/><em/></span>)}</section></div>}
    {variant === 'admin' && <><Heading/><div className="skeleton-admin-hero"><span><i/><b/><em/><em/></span><i/></div><Metrics/><div className="skeleton-admin-grid"><section><b/><em/><em/><em/><em/></section><section><b/><div/></section></div></>}
    {variant === 'staff' && <><Heading/><div className="skeleton-admin-hero skeleton-admin-hero--staff"><span><i/><b/><em/></span><i/></div><Metrics/><div className="skeleton-workspace"><Rows count={4}/><section><b/><em/><em/><em/></section></div></>}
    {variant === 'table' && <><Heading/><div className="skeleton-form"><b/><i/><i/><i/></div><div className="skeleton-tabs"><i/><i/><i/><i/></div><Rows count={5}/></>}
    {variant === 'analytics' && <><Heading/><Metrics/><div className="skeleton-chart"><b/><span>{[0,1,2,3,4,5].map(item=><i key={item}/>)}</span></div></>}
    {variant === 'profile' && <><Heading/><div className="skeleton-profile"><i/><span><b/><em/><em/></span></div><Rows count={3}/></>}
  </div>
}
