import type { Component } from 'vue';
import type { CardAppearance, CardVariantName } from '@/components/ui/card/variants';
import type { DashboardMetricSourceData, DashboardOverviewSourceData, DashboardSourcesData } from '@/types/wayfinder-generated';

export type AdminDashboardMetricSource = DashboardMetricSourceData;
export type AdminDashboardOverviewSource = DashboardOverviewSourceData;
export type AdminDashboardSources = DashboardSourcesData;

export type DashboardWidgetType = 'action' | 'anchor' | 'chart' | 'stat' | 'support';
export type DashboardWidgetAppearance = CardAppearance;
export type DashboardWidgetVariant = CardVariantName;
export type DashboardWidgetSize = 'full' | 'half' | 'hero' | 'third';
export type DashboardSourceKey = keyof AdminDashboardSources;
export type DashboardActionSourceKey = Exclude<DashboardSourceKey, 'overview'>;

type DashboardWidgetSchemaBase<TWidget extends DashboardWidgetType> = {
  appearance: DashboardWidgetAppearance;
  id: string;
  size: DashboardWidgetSize;
  variant: DashboardWidgetVariant;
  widget: TWidget;
};

export type DashboardAnchorWidgetSchema = DashboardWidgetSchemaBase<'anchor'> & {
  description: string;
  eyebrow: string;
  source: 'overview';
  title: string;
};

export type DashboardActionWidgetSchema = DashboardWidgetSchemaBase<'action'> & {
  ctaLabel?: string;
  description: string;
  emptyDescription: string;
  emptyTitle: string;
  eyebrow: string;
  href?: string;
  source: DashboardActionSourceKey;
  title?: string;
};

export type DashboardStatWidgetSchema = DashboardWidgetSchemaBase<'stat'> & {
  description: string;
  eyebrow: string;
  source: DashboardActionSourceKey;
  title?: string;
};

export type DashboardSupportWidgetSchema = DashboardWidgetSchemaBase<'support'> & {
  description: string;
  eyebrow: string;
  items: string[];
  title: string;
};

export type DashboardChartWidgetSchema = DashboardWidgetSchemaBase<'chart'> & {
  description: string;
  emptyDescription: string;
  emptyTitle: string;
  source: DashboardSourceKey;
  title: string;
};

export type DashboardWidgetSchema = DashboardActionWidgetSchema | DashboardAnchorWidgetSchema | DashboardChartWidgetSchema | DashboardStatWidgetSchema | DashboardSupportWidgetSchema;

export type DashboardResolvedWidget = {
  className: string;
  component: Component;
  id: string;
  props: Record<string, unknown>;
  widget: DashboardWidgetType;
};

export type AdminDashboardPageProps = {
  sources: AdminDashboardSources;
};
