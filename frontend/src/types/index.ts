export type ReportDraft = { fullName: string; email: string; category: string; title: string; description: string; barangay: string; locationNote: string; photoName: string }
export type FieldErrors = Partial<Record<keyof ReportDraft, string>>
