<script setup lang="ts">
import { Sparkles, Telescope } from 'lucide-vue-next';
import { useAttrs } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import { getCardInsetPanelClassNames } from '@/components/ui/card/variants';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import DatePicker from '@/components/ui/date-picker/DatePicker.vue';
import Input from '@/components/ui/input/Input.vue';
import { cn, toTitleCase } from '@/lib/utils';
import type { AdminAuditLogIndexFilterOptions } from '@/types/admin/audit';

defineOptions({
  inheritAttrs: false,
});

type AuditLogFilterKey = 'actors' | 'events' | 'subject_types';

type AuditLogFilterDraft = {
  search: string;
  from: string;
  until: string;
  actors: string[];
  events: string[];
  subject_types: string[];
};

const props = defineProps<{
  activeFilterCount: number;
  filterDraft: AuditLogFilterDraft;
  filterOptions: AdminAuditLogIndexFilterOptions;
  formatSubjectType: (subjectType: string) => string;
}>();

const emit = defineEmits<{
  (event: 'apply-filters'): void;
  (event: 'clear-filters'): void;
  (event: 'toggle-filter-value', key: AuditLogFilterKey, value: string, nextValue: boolean | 'indeterminate'): void;
}>();

const attrs = useAttrs();
const filterDraft = props.filterDraft;
</script>

<template>
  <Card v-bind="attrs" appearance="glow" variant="secondary" data-slot="admin-audit-log-filters-card" class="gap-5 px-5 py-5">
    <div class="space-y-2">
      <div class="flex items-center gap-2">
        <Telescope class="size-4 text-secondary" />
        <p class="section-kicker">Refine the ledger</p>
      </div>
      <p class="text-sm leading-6 text-muted-foreground">Filter by actor, event family, subject class, free-text summary search, or date window. Nothing clever. Just useful.</p>
    </div>

    <div class="space-y-4">
      <Input id="admin-audit-logs-search" v-model.trim="filterDraft.search" label="Search timeline" variant="outlined" clearable @keydown.enter.prevent="emit('apply-filters')" />

      <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
        <DatePicker id="admin-audit-logs-from" v-model="filterDraft.from" label="From date" variant="outlined" clearable />
        <DatePicker id="admin-audit-logs-until" v-model="filterDraft.until" label="Until date" variant="outlined" clearable />
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
            <Checkbox :model-value="filterDraft.actors.includes(actor)" @update:model-value="(value) => emit('toggle-filter-value', 'actors', actor, value)" />
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
            <Checkbox :model-value="filterDraft.events.includes(event)" @update:model-value="(value) => emit('toggle-filter-value', 'events', event, value)" />
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
            <Checkbox :model-value="filterDraft.subject_types.includes(subjectType)" @update:model-value="(value) => emit('toggle-filter-value', 'subject_types', subjectType, value)" />
            <span class="min-w-0">
              <span class="block text-sm leading-5">{{ props.formatSubjectType(subjectType) }}</span>
              <span class="block text-xs leading-5 break-all text-muted-foreground">{{ subjectType }}</span>
            </span>
          </label>
          <p v-if="props.filterOptions.subject_types.length === 0" class="text-sm leading-6 text-muted-foreground">No subject classes have been captured yet.</p>
        </div>
      </section>
    </div>

    <div id="admin-audit-logs-actions" class="flex flex-wrap gap-3">
      <Button appearance="filled" variant="primary" rounded="full" @click="emit('apply-filters')">Apply filters</Button>
      <Button appearance="outline" variant="muted" rounded="full" :disabled="props.activeFilterCount === 0" @click="emit('clear-filters')">Reset</Button>
    </div>
  </Card>
</template>
