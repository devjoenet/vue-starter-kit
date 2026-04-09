import type { AuditHistoryChangeData, AuditHistoryItemData, AuditLogIndexFilterOptionsData, AuditLogIndexItemData, AuditLogIndexQueryData } from '@/types/wayfinder-generated';
import type { PaginatedCollection } from '@/types/admin/shared';

export type AdminAuditLogIndexColumn = 'created_at' | 'actor' | 'event' | 'subject';

export type AuditHistoryChange = Omit<AuditHistoryChangeData, 'before' | 'after'> & {
  before?: string | null;
  after?: string | null;
};

export type AuditHistoryItem = Omit<AuditHistoryItemData, 'actor_label' | 'changes'> & {
  actor_label?: string | null;
  changes: AuditHistoryChange[];
};

export type AuditLogIndexItem = Omit<AuditLogIndexItemData, 'actor_label' | 'subject_type' | 'subject_id' | 'subject_label' | 'method' | 'url' | 'ip_address'> & {
  actor_label?: string | null;
  subject_type?: string | null;
  subject_id?: number | null;
  subject_label?: string | null;
  method?: string | null;
  url?: string | null;
  ip_address?: string | null;
};

export type AdminAuditLogIndexFilterOptions = AuditLogIndexFilterOptionsData & {
  actors: string[];
  events: string[];
  subject_types: string[];
};

export type AdminAuditLogIndexQuery = Omit<AuditLogIndexQueryData, 'sort' | 'direction' | 'actors' | 'events' | 'subject_types'> & {
  sort: AdminAuditLogIndexColumn;
  direction: 'asc' | 'desc';
  actors: string[];
  events: string[];
  subject_types: string[];
};

export type AdminAuditLogPaginatedCollection = PaginatedCollection<AuditLogIndexItem> & {
  current_page: number;
  from?: number | null;
  last_page: number;
  next_page_url?: string | null;
  path: string;
  per_page: number;
  prev_page_url?: string | null;
  to?: number | null;
  total: number;
};

export type AdminAuditLogIndexPageProps = {
  auditLogs: AdminAuditLogPaginatedCollection;
  filterOptions: AdminAuditLogIndexFilterOptions;
  query: AdminAuditLogIndexQuery;
};
