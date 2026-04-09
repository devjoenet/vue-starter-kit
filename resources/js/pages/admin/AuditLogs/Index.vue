<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Search, Sparkles, Telescope } from 'lucide-vue-next';
import { computed, reactive, watch } from 'vue';
import AuditLogIndexTable from '@/components/admin/AuditLogIndexTable.vue';
import AdminPageIntro from '@/components/admin/AdminPageIntro.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import { getCardInsetPanelClassNames } from '@/components/ui/card/variants';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import DatePicker from '@/components/ui/date-picker/DatePicker.vue';
import Input from '@/components/ui/input/Input.vue';
import { cn, toTitleCase } from '@/lib/utils';
import { adminPageLayout, setAdminBreadcrumbs } from '@/lib/page-layouts';
import { index } from '@/routes/admin/audit-logs';
import type { AdminAuditLogIndexColumn, AdminAuditLogIndexPageProps, AdminAuditLogIndexQuery } from '@/types/admin/audit';

defineOptions({
  layout: adminPageLayout,
});

setAdminBreadcrumbs({ title: 'Audit Logs', href: index.url() });

const props = defineProps<AdminAuditLogIndexPageProps>();

type AuditLogFilterKey = 'actors' | 'events' | 'subject_types';

type FilterDraft = {
  search: string;
  from: string;
  until: string;
  actors: string[];
  events: string[];
  subject_types: string[];
};

const defaultQuery: AdminAuditLogIndexQuery = {
  sort: 'created_at',
  direction: 'desc',
  actors: [],
  events: [],
  subject_types: [],
  search: undefined,
  from: undefined,
  until: undefined,
};

const filterDraft = reactive<FilterDraft>({
  search: '',
  from: '',
  until: '',
  actors: [],
  events: [],
  subject_types: [],
});

const syncDraftFromQuery = (query: AdminAuditLogIndexQuery) => {
  filterDraft.search = query.search ?? '';
  filterDraft.from = query.from ?? '';
  filterDraft.until = query.until ?? '';
  filterDraft.actors = [...query.actors];
  filterDraft.events = [...query.events];
  filterDraft.subject_types = [...query.subject_types];
};

watch(
  () => props.query,
  (query) => {
    syncDraftFromQuery(query);
  },
  { deep: true, immediate: true },
);

const normalizeString = (value: string | null | undefined): string | undefined => {
  const normalizedValue = value?.trim() ?? '';

  return normalizedValue === '' ? undefined : normalizedValue;
};

const normalizeList = (values: string[]): string[] => {
  return Array.from(new Set(values.map((value) => value.trim()).filter((value) => value !== ''))).sort();
};

const currentQuery = computed<AdminAuditLogIndexQuery>(() => ({
  ...defaultQuery,
  ...props.query,
}));

const activeFilterCount = computed(() => {
  return [currentQuery.value.search, currentQuery.value.from, currentQuery.value.until].filter(Boolean).length + currentQuery.value.actors.length + currentQuery.value.events.length + currentQuery.value.subject_types.length;
});

const windowLabel = computed(() => {
  if (currentQuery.value.from && currentQuery.value.until) {
    return `${currentQuery.value.from} to ${currentQuery.value.until}`;
  }

  if (currentQuery.value.from) {
    return `Since ${currentQuery.value.from}`;
  }

  if (currentQuery.value.until) {
    return `Until ${currentQuery.value.until}`;
  }

  return 'Entire retained window';
});

const filterSummaryBadges = computed(() => {
  const badges: Array<{ tone: 'info' | 'secondary' | 'outline'; value: string }> = [];

  if (currentQuery.value.search) {
    badges.push({ tone: 'info', value: `Search: ${currentQuery.value.search}` });
  }

  currentQuery.value.actors.slice(0, 2).forEach((actor) => badges.push({ tone: 'secondary', value: actor }));
  currentQuery.value.events.slice(0, 2).forEach((event) => badges.push({ tone: 'outline', value: toTitleCase(event) }));

  return badges;
});

const buildVisitQuery = (query: Partial<AdminAuditLogIndexQuery> & { page?: number }) => {
  return {
    sort: query.sort ?? currentQuery.value.sort,
    direction: query.direction ?? currentQuery.value.direction,
    actors: normalizeList(query.actors ?? currentQuery.value.actors),
    events: normalizeList(query.events ?? currentQuery.value.events),
    subject_types: normalizeList(query.subject_types ?? currentQuery.value.subject_types),
    search: normalizeString(query.search ?? currentQuery.value.search ?? undefined),
    from: normalizeString(query.from ?? currentQuery.value.from ?? undefined),
    until: normalizeString(query.until ?? currentQuery.value.until ?? undefined),
    page: query.page,
  };
};

const visit = (query: Partial<AdminAuditLogIndexQuery> & { page?: number }) => {
  router.visit(
    index.url({
      query: buildVisitQuery(query),
    }),
    {
      preserveScroll: true,
      preserveState: true,
      replace: true,
      only: ['auditLogs', 'filterOptions', 'query'],
    },
  );
};

const applyFilters = () => {
  visit({
    page: undefined,
    actors: filterDraft.actors,
    events: filterDraft.events,
    subject_types: filterDraft.subject_types,
    search: filterDraft.search,
    from: filterDraft.from,
    until: filterDraft.until,
  });
};

const clearFilters = () => {
  syncDraftFromQuery(defaultQuery);

  visit({
    sort: 'created_at',
    direction: 'desc',
    actors: [],
    events: [],
    subject_types: [],
    search: undefined,
    from: undefined,
    until: undefined,
    page: undefined,
  });
};

const toggleSort = (column: AdminAuditLogIndexColumn) => {
  if (currentQuery.value.sort !== column) {
    visit({
      sort: column,
      direction: 'asc',
      page: undefined,
    });

    return;
  }

  visit({
    sort: column,
    direction: currentQuery.value.direction === 'asc' ? 'desc' : 'asc',
    page: undefined,
  });
};

const toggleFilterValue = (key: AuditLogFilterKey, value: string, nextValue: boolean | 'indeterminate') => {
  const currentValues = filterDraft[key];

  if (nextValue === true) {
    filterDraft[key] = normalizeList([...currentValues, value]);

    return;
  }

  filterDraft[key] = currentValues.filter((currentValue) => currentValue !== value);
};

const formatSubjectType = (subjectType: string): string => {
  return toTitleCase(subjectType.split('\\').pop() ?? subjectType);
};

const visitUrl = (url: string) => {
  router.visit(url, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
    only: ['auditLogs', 'filterOptions', 'query'],
  });
};
</script>

<template>
  <Head title="Audit Logs" />

  <div id="admin-audit-logs-page" class="motion-stage space-y-6 px-4">
    <Card id="admin-audit-logs-page-header" appearance="glow" variant="secondary" class="motion-step px-6 py-6 sm:px-8 sm:py-8">
      <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_18rem] xl:items-end">
        <AdminPageIntro
          kicker="Operational trace"
          title="Audit logs"
          description="Review the stored activity ledger without mutating the backend contract again. The server keeps the canonical snapshot data; this page just stops pretending raw JSON is a UX."
        >
          <template #aside>
            <Badge variant="info">{{ props.auditLogs.total }} recorded</Badge>
            <Badge variant="secondary">{{ activeFilterCount }} active filter{{ activeFilterCount === 1 ? '' : 's' }}</Badge>
          </template>
        </AdminPageIntro>

        <aside :class="cn(getCardInsetPanelClassNames({ appearance: 'outline', variant: 'secondary' }), 'relative space-y-3 px-4 py-4')">
          <p class="text-[11px] font-semibold tracking-[0.18em] text-muted-foreground uppercase">Current window</p>
          <p class="text-sm font-semibold">{{ windowLabel }}</p>
          <p class="text-sm leading-6 text-muted-foreground">Stored snapshots stay readable even when the live record changes names or disappears. A rare case of the logs being less dramatic than the humans.</p>
        </aside>
      </div>

      <div v-if="filterSummaryBadges.length" id="admin-audit-logs-summary-badges" class="relative mt-5 flex flex-wrap gap-2">
        <Badge v-for="badge in filterSummaryBadges" :key="badge.value" :variant="badge.tone">
          {{ badge.value }}
        </Badge>
      </div>
    </Card>

    <section id="admin-audit-logs-content" class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_18rem] xl:items-start">
      <AuditLogIndexTable class="motion-step" :audit-logs="props.auditLogs" :query="props.query" @toggle-sort="toggleSort" @visit-url="visitUrl" />

      <Card id="admin-audit-logs-filters-card" appearance="glow" variant="secondary" class="motion-step gap-5 px-5 py-5 xl:sticky xl:top-6 xl:self-start">
        <div class="space-y-2">
          <div class="flex items-center gap-2">
            <Telescope class="size-4 text-secondary" />
            <p class="section-kicker">Refine the ledger</p>
          </div>
          <p class="text-sm leading-6 text-muted-foreground">Filter by actor, event family, subject class, free-text summary search, or date window. Nothing clever. Just useful.</p>
        </div>

        <div class="space-y-4">
          <Input
            id="admin-audit-logs-search"
            v-model.trim="filterDraft.search"
            label="Search timeline"
            placeholder="Search summary, actor, event, or subject"
            variant="outlined"
            clearable
            :leading-icon="Search"
            @keydown.enter.prevent="applyFilters"
          />

          <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
            <DatePicker id="admin-audit-logs-from" v-model="filterDraft.from" label="From date" placeholder="Start date" variant="outlined" clearable />
            <DatePicker id="admin-audit-logs-until" v-model="filterDraft.until" label="Until date" placeholder="End date" variant="outlined" clearable />
          </div>
        </div>

        <div class="space-y-4">
          <section :class="cn(getCardInsetPanelClassNames({ appearance: 'outline', variant: 'neutral' }), 'space-y-3 px-4 py-4')">
            <div class="flex items-start gap-3">
              <Sparkles class="mt-0.5 size-4 text-primary" />
              <div>
                <p class="text-sm font-semibold">Actors</p>
                <p class="text-xs leading-5 text-muted-foreground">Who triggered the entry.</p>
              </div>
            </div>

            <div class="max-h-44 space-y-2 overflow-y-auto pr-1">
              <label v-for="actor in props.filterOptions.actors" :key="actor" class="flex cursor-pointer items-start gap-3 py-1.5">
                <Checkbox :model-value="filterDraft.actors.includes(actor)" @update:model-value="(value) => toggleFilterValue('actors', actor, value)" />
                <span class="min-w-0 text-sm leading-5">{{ actor }}</span>
              </label>
              <p v-if="props.filterOptions.actors.length === 0" class="text-sm leading-6 text-muted-foreground">No actor labels have been captured yet.</p>
            </div>
          </section>

          <section :class="cn(getCardInsetPanelClassNames({ appearance: 'outline', variant: 'neutral' }), 'space-y-3 px-4 py-4')">
            <div>
              <p class="text-sm font-semibold">Events</p>
              <p class="text-xs leading-5 text-muted-foreground">The recorded action family.</p>
            </div>

            <div class="max-h-44 space-y-2 overflow-y-auto pr-1">
              <label v-for="event in props.filterOptions.events" :key="event" class="flex cursor-pointer items-start gap-3 py-1.5">
                <Checkbox :model-value="filterDraft.events.includes(event)" @update:model-value="(value) => toggleFilterValue('events', event, value)" />
                <span class="min-w-0">
                  <span class="block text-sm leading-5">{{ toTitleCase(event) }}</span>
                  <span class="block text-xs leading-5 text-muted-foreground">{{ event }}</span>
                </span>
              </label>
              <p v-if="props.filterOptions.events.length === 0" class="text-sm leading-6 text-muted-foreground">No event families have been recorded yet.</p>
            </div>
          </section>

          <section :class="cn(getCardInsetPanelClassNames({ appearance: 'outline', variant: 'neutral' }), 'space-y-3 px-4 py-4')">
            <div>
              <p class="text-sm font-semibold">Subject types</p>
              <p class="text-xs leading-5 text-muted-foreground">The model or entity class touched by the entry.</p>
            </div>

            <div class="max-h-44 space-y-2 overflow-y-auto pr-1">
              <label v-for="subjectType in props.filterOptions.subject_types" :key="subjectType" class="flex cursor-pointer items-start gap-3 py-1.5">
                <Checkbox :model-value="filterDraft.subject_types.includes(subjectType)" @update:model-value="(value) => toggleFilterValue('subject_types', subjectType, value)" />
                <span class="min-w-0">
                  <span class="block text-sm leading-5">{{ formatSubjectType(subjectType) }}</span>
                  <span class="block text-xs leading-5 break-all text-muted-foreground">{{ subjectType }}</span>
                </span>
              </label>
              <p v-if="props.filterOptions.subject_types.length === 0" class="text-sm leading-6 text-muted-foreground">No subject classes have been captured yet.</p>
            </div>
          </section>
        </div>

        <div id="admin-audit-logs-actions" class="flex flex-wrap gap-3">
          <Button appearance="filled" variant="primary" rounded="full" @click="applyFilters">Apply filters</Button>
          <Button appearance="outline" variant="muted" rounded="full" :disabled="activeFilterCount === 0" @click="clearFilters">Reset</Button>
        </div>
      </Card>
    </section>
  </div>
</template>
