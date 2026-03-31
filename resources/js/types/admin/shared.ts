export type PaginatedCollection<T> = {
  data: T[];
} & Record<string, unknown>;

export type AdminIndexDirection = 'asc' | 'desc';

export type AdminIndexQuery<TColumn extends string = string> = {
  sort: TColumn;
  direction: AdminIndexDirection;
  filters: Partial<Record<TColumn, string[]>>;
};
