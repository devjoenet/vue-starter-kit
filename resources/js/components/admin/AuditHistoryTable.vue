<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { computed } from 'vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Card from '@/components/ui/card/Card.vue';
import { getCardInsetPanelClassNames } from '@/components/ui/card/variants';
import Skeleton from '@/components/ui/skeleton/Skeleton.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import { cn, toTitleCase } from '@/lib/utils';
import type { AuditHistoryItem } from '@/types/admin/audit';

defineOptions({
  inheritAttrs: false,
});

const props = withDefaults(
  defineProps<{
    class?: HTMLAttributes['class'];
    description?: string;
    emptyDescription?: string;
    items: AuditHistoryItem[];
    loading?: boolean;
    title?: string;
  }>(),
  {
    description: 'Review the latest stored changes for this record without leaving the editor.',
    emptyDescription: 'This record does not have captured audit history yet.',
    title: 'Audit history',
  },
);

const rowCountLabel = computed(() => {
  if (props.loading) {
    return 'Loading recent events.';
  }

  if (props.items.length === 0) {
    return props.emptyDescription;
  }

  return `Showing ${props.items.length} recent event${props.items.length === 1 ? '' : 's'}.`;
});

const formatAuditDate = (value: string): string =>
  new Intl.DateTimeFormat('en-US', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(new Date(value));

const formatValue = (value?: string | null): string => {
  if (value === null || value === undefined || value === '') {
    return 'None';
  }

  return value;
};
</script>

<template>
  <Card id="admin-audit-history-table" appearance="filled" variant="neutral" :class="cn('overflow-hidden py-0', props.class)">
    <div class="border-b border-border/60 px-6 py-5">
      <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
        <div class="space-y-2">
          <p class="section-kicker">Record timeline</p>
          <h2 class="text-lg font-semibold tracking-tight">{{ title }}</h2>
          <p class="max-w-2xl text-sm leading-6 text-muted-foreground">
            {{ description }}
          </p>
        </div>

        <p class="text-xs font-semibold tracking-[0.16em] text-muted-foreground uppercase">
          {{ rowCountLabel }}
        </p>
      </div>
    </div>

    <div v-if="props.loading" class="grid gap-3 p-4 md:hidden">
      <article v-for="row in 2" :key="`audit-history-loading-mobile-${row}`" :class="cn(getCardInsetPanelClassNames({ appearance: 'tinted', variant: 'neutral' }), 'space-y-4 px-4 py-4')">
        <div class="flex flex-wrap items-start justify-between gap-3">
          <div class="min-w-0 flex-1 space-y-2">
            <Skeleton class="h-4 w-36" />
            <Skeleton class="h-3 w-28" />
          </div>

          <Skeleton class="h-6 w-20 rounded-full" />
        </div>

        <Skeleton class="h-4 w-40" />

        <div class="space-y-2 border-t border-border/60 pt-3">
          <Skeleton class="h-3 w-24" />
          <Skeleton class="h-4 w-full" />
        </div>
      </article>
    </div>

    <div v-else-if="props.items.length" class="grid gap-3 p-4 md:hidden">
      <article v-for="auditLog in props.items" :key="auditLog.id" :class="cn(getCardInsetPanelClassNames({ appearance: 'tinted', variant: 'neutral' }), 'space-y-4 px-4 py-4')">
        <div class="flex flex-wrap items-start justify-between gap-3">
          <div class="min-w-0 space-y-1">
            <p class="text-sm font-semibold tracking-tight">{{ auditLog.summary }}</p>
            <p class="text-xs text-muted-foreground">{{ formatAuditDate(auditLog.created_at) }}</p>
          </div>

          <Badge variant="outline">{{ toTitleCase(auditLog.event) }}</Badge>
        </div>

        <p class="text-sm text-muted-foreground">Changed by {{ auditLog.actor_label ?? 'System process' }}.</p>

        <div v-if="auditLog.changes.length" class="space-y-3 border-t border-border/60 pt-3">
          <div v-for="change in auditLog.changes" :key="`${auditLog.id}-${change.field}`" class="space-y-1">
            <p class="text-[11px] font-semibold tracking-[0.18em] text-muted-foreground uppercase">{{ toTitleCase(change.field) }}</p>
            <p class="text-sm leading-6">
              <span class="break-words text-muted-foreground line-through decoration-muted-foreground/50">{{ formatValue(change.before) }}</span>
              <span class="px-2 text-muted-foreground">→</span>
              <span class="font-medium break-words">{{ formatValue(change.after) }}</span>
            </p>
          </div>
        </div>

        <p v-else class="border-t border-border/60 pt-3 text-sm text-muted-foreground">No field-level diff was captured for this event.</p>
      </article>
    </div>

    <div v-else class="p-4 md:hidden">
      <div :class="cn(getCardInsetPanelClassNames({ appearance: 'tinted', variant: 'accent' }), 'space-y-2 px-4 py-4')">
        <p class="text-sm font-semibold">No audit history yet.</p>
        <p class="text-sm leading-6 text-muted-foreground">{{ emptyDescription }}</p>
      </div>
    </div>

    <Table id="admin-audit-history-table-desktop" wrapper-class="hidden rounded-none border-0 md:block">
      <TableHeader>
        <TableRow>
          <TableHead>Timestamp</TableHead>
          <TableHead>Event</TableHead>
          <TableHead>Actor</TableHead>
          <TableHead>Changes</TableHead>
        </TableRow>
      </TableHeader>

      <TableBody>
        <template v-if="props.loading">
          <TableRow v-for="row in 3" :key="`audit-history-loading-desktop-${row}`">
            <TableCell class="align-top"><Skeleton class="h-4 w-32" /></TableCell>
            <TableCell class="align-top">
              <div class="space-y-2">
                <Skeleton class="h-6 w-24 rounded-full" />
                <Skeleton class="h-4 w-56" />
              </div>
            </TableCell>
            <TableCell class="align-top"><Skeleton class="h-4 w-36" /></TableCell>
            <TableCell class="align-top">
              <div class="grid gap-2">
                <Skeleton class="h-10 w-full" />
                <Skeleton class="h-10 w-4/5" />
              </div>
            </TableCell>
          </TableRow>
        </template>

        <template v-else>
          <TableRow v-for="auditLog in props.items" :key="auditLog.id">
            <TableCell class="align-top text-sm text-muted-foreground">
              {{ formatAuditDate(auditLog.created_at) }}
            </TableCell>

            <TableCell class="align-top">
              <div class="space-y-2">
                <Badge variant="outline">{{ toTitleCase(auditLog.event) }}</Badge>
                <p class="max-w-sm text-sm leading-6 whitespace-normal">{{ auditLog.summary }}</p>
              </div>
            </TableCell>

            <TableCell class="align-top text-sm">
              {{ auditLog.actor_label ?? 'System process' }}
            </TableCell>

            <TableCell class="align-top whitespace-normal">
              <div v-if="auditLog.changes.length" class="grid gap-2">
                <div v-for="change in auditLog.changes" :key="`${auditLog.id}-${change.field}`" class="grid gap-2 rounded-[1rem] border border-border/55 bg-background/60 px-3 py-2 lg:grid-cols-[9rem_minmax(0,1fr)]">
                  <p class="text-xs font-semibold tracking-[0.16em] text-muted-foreground uppercase">{{ toTitleCase(change.field) }}</p>
                  <p class="min-w-0 text-sm leading-6">
                    <span class="break-words text-muted-foreground line-through decoration-muted-foreground/50">{{ formatValue(change.before) }}</span>
                    <span class="px-2 text-muted-foreground">→</span>
                    <span class="font-medium break-words">{{ formatValue(change.after) }}</span>
                  </p>
                </div>
              </div>

              <span v-else class="text-sm text-muted-foreground">No field-level diff captured.</span>
            </TableCell>
          </TableRow>

          <TableRow v-if="props.items.length === 0">
            <TableCell colspan="4" class="text-center text-muted-foreground">No audit history has been captured for this record.</TableCell>
          </TableRow>
        </template>
      </TableBody>
    </Table>
  </Card>
</template>
