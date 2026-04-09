<script setup lang="ts">
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { computed } from 'vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import { getCardInsetPanelClassNames } from '@/components/ui/card/variants';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import { cn, toTitleCase } from '@/lib/utils';
import type { AdminAuditLogIndexColumn, AdminAuditLogIndexQuery, AdminAuditLogPaginatedCollection } from '@/types/admin/audit';
import type { BadgeVariants } from '@/components/ui/badge/variants';

const props = defineProps<{
  auditLogs: AdminAuditLogPaginatedCollection;
  query: AdminAuditLogIndexQuery;
}>();

const emit = defineEmits<{
  (event: 'toggle-sort', column: AdminAuditLogIndexColumn): void;
  (event: 'visit-url', url: string): void;
}>();

const rowCountLabel = computed(() => {
  if (props.auditLogs.total === 0) {
    return 'No audit entries match the current filters.';
  }

  const from = props.auditLogs.from ?? 0;
  const to = props.auditLogs.to ?? props.auditLogs.data.length;

  return `Showing ${from}-${to} of ${props.auditLogs.total} stored events.`;
});

const paginationSummary = computed(() => `Page ${props.auditLogs.current_page} of ${props.auditLogs.last_page}`);

const sortDirectionFor = (column: AdminAuditLogIndexColumn): 'asc' | 'desc' | 'none' => {
  if (props.query.sort !== column) {
    return 'none';
  }

  return props.query.direction;
};

const formatAuditDate = (value: string): string =>
  new Intl.DateTimeFormat('en-US', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(new Date(value));

const formatSubjectType = (subjectType?: string | null): string => {
  if (!subjectType) {
    return 'System';
  }

  return toTitleCase(subjectType.split('\\').pop() ?? subjectType);
};

const methodBadgeVariant = (method?: string | null): BadgeVariants['variant'] => {
  switch (method?.toUpperCase()) {
    case 'GET':
      return 'info';
    case 'POST':
      return 'secondary';
    case 'PUT':
    case 'PATCH':
      return 'default';
    case 'DELETE':
      return 'destructive';
    default:
      return 'outline';
  }
};

const visitUrl = (url?: string | null) => {
  if (!url) {
    return;
  }

  emit('visit-url', url);
};

type SortableColumn = {
  column: AdminAuditLogIndexColumn;
  label: string;
};

const sortableColumns: SortableColumn[] = [
  { column: 'created_at', label: 'Timestamp' },
  { column: 'event', label: 'Event' },
  { column: 'actor', label: 'Actor' },
  { column: 'subject', label: 'Subject' },
];
</script>

<template>
  <Card id="admin-audit-logs-results-card" appearance="filled" variant="neutral" class="motion-step overflow-hidden py-0">
    <div class="border-b border-border/60 px-6 py-5">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div class="space-y-1.5">
          <p class="section-kicker">Captured events</p>
          <h2 class="text-xl font-semibold tracking-tight">Stored timeline</h2>
          <p class="max-w-2xl text-sm leading-6 text-muted-foreground">
            {{ rowCountLabel }}
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <Badge variant="info">{{ paginationSummary }}</Badge>
          <Button appearance="outline" variant="muted" size="sm" rounded="full" :disabled="!props.auditLogs.prev_page_url" @click="visitUrl(props.auditLogs.prev_page_url)">
            <ChevronLeft class="size-4" />
            Newer
          </Button>
          <Button appearance="outline" variant="muted" size="sm" rounded="full" :disabled="!props.auditLogs.next_page_url" @click="visitUrl(props.auditLogs.next_page_url)">
            Older
            <ChevronRight class="size-4" />
          </Button>
        </div>
      </div>
    </div>

    <div v-if="props.auditLogs.data.length" class="grid gap-3 p-4 md:hidden">
      <article v-for="auditLog in props.auditLogs.data" :key="auditLog.id" :class="cn(getCardInsetPanelClassNames({ appearance: 'tinted', variant: 'neutral' }), 'space-y-4 px-4 py-4')">
        <div class="flex flex-wrap items-start justify-between gap-3">
          <div class="min-w-0 space-y-1">
            <p class="text-sm font-semibold tracking-tight">{{ auditLog.summary }}</p>
            <p class="text-xs text-muted-foreground">{{ formatAuditDate(auditLog.created_at) }}</p>
          </div>

          <div class="flex flex-wrap gap-2">
            <Badge variant="outline">{{ toTitleCase(auditLog.event) }}</Badge>
            <Badge :variant="methodBadgeVariant(auditLog.method)">
              {{ auditLog.method ?? 'N/A' }}
            </Badge>
          </div>
        </div>

        <dl class="grid gap-3 text-sm sm:grid-cols-2">
          <div>
            <dt class="text-[11px] font-semibold tracking-[0.18em] text-muted-foreground uppercase">Actor</dt>
            <dd class="mt-1 font-medium">{{ auditLog.actor_label ?? 'System process' }}</dd>
            <p v-if="auditLog.ip_address" class="mt-1 text-xs text-muted-foreground">{{ auditLog.ip_address }}</p>
          </div>

          <div>
            <dt class="text-[11px] font-semibold tracking-[0.18em] text-muted-foreground uppercase">Subject</dt>
            <dd class="mt-1 font-medium">{{ auditLog.subject_label ?? 'System change' }}</dd>
            <p class="mt-1 text-xs text-muted-foreground">
              {{ formatSubjectType(auditLog.subject_type) }}
              <span v-if="auditLog.subject_id !== null && auditLog.subject_id !== undefined">#{{ auditLog.subject_id }}</span>
            </p>
          </div>
        </dl>

        <div v-if="auditLog.url" class="border-t border-border/60 pt-3">
          <p class="text-[11px] font-semibold tracking-[0.18em] text-muted-foreground uppercase">Request</p>
          <p class="mt-1 text-sm break-all text-muted-foreground">{{ auditLog.url }}</p>
        </div>
      </article>
    </div>

    <div v-else class="p-4 md:hidden">
      <div :class="cn(getCardInsetPanelClassNames({ appearance: 'tinted', variant: 'accent' }), 'space-y-2 px-4 py-4')">
        <p class="text-sm font-semibold">No audit entries match the current filters.</p>
        <p class="text-sm leading-6 text-muted-foreground">Clear the current constraints or widen the date window to pull older history back into view.</p>
      </div>
    </div>

    <Table id="admin-audit-logs-table" wrapper-class="hidden rounded-none border-0 md:block">
      <TableHeader>
        <TableRow>
          <TableHead v-for="sortableColumn in sortableColumns" :key="sortableColumn.column" class="align-top">
            <Button appearance="text" variant="muted" size="sm" rounded="full" class="-ml-3 inline-flex px-3" @click="$emit('toggle-sort', sortableColumn.column)">
              {{ sortableColumn.label }}
              <span class="text-xs text-muted-foreground/80">
                {{ sortDirectionFor(sortableColumn.column) === 'none' ? '↕' : sortDirectionFor(sortableColumn.column) === 'asc' ? '↑' : '↓' }}
              </span>
            </Button>
          </TableHead>
          <TableHead class="align-top">Request</TableHead>
        </TableRow>
      </TableHeader>

      <TableBody>
        <TableRow v-for="auditLog in props.auditLogs.data" :key="auditLog.id">
          <TableCell class="align-top text-sm text-muted-foreground">
            {{ formatAuditDate(auditLog.created_at) }}
          </TableCell>

          <TableCell class="align-top">
            <div class="space-y-2">
              <div class="flex flex-wrap gap-2">
                <Badge variant="outline">{{ toTitleCase(auditLog.event) }}</Badge>
                <Badge :variant="methodBadgeVariant(auditLog.method)">
                  {{ auditLog.method ?? 'N/A' }}
                </Badge>
              </div>
              <p class="max-w-md text-sm leading-6">{{ auditLog.summary }}</p>
            </div>
          </TableCell>

          <TableCell class="align-top">
            <div class="space-y-1">
              <p class="text-sm font-medium">{{ auditLog.actor_label ?? 'System process' }}</p>
              <p v-if="auditLog.ip_address" class="text-xs text-muted-foreground">{{ auditLog.ip_address }}</p>
            </div>
          </TableCell>

          <TableCell class="align-top">
            <div class="space-y-1">
              <p class="text-sm font-medium">{{ auditLog.subject_label ?? 'System change' }}</p>
              <p class="text-xs text-muted-foreground">
                {{ formatSubjectType(auditLog.subject_type) }}
                <span v-if="auditLog.subject_id !== null && auditLog.subject_id !== undefined">#{{ auditLog.subject_id }}</span>
              </p>
            </div>
          </TableCell>

          <TableCell class="align-top">
            <p v-if="auditLog.url" class="max-w-xs text-sm break-all text-muted-foreground">{{ auditLog.url }}</p>
            <span v-else class="text-sm text-muted-foreground">No request URL captured.</span>
          </TableCell>
        </TableRow>

        <TableRow v-if="props.auditLogs.data.length === 0">
          <TableCell colspan="5" class="text-center text-muted-foreground">No audit entries match the current filters.</TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </Card>
</template>
