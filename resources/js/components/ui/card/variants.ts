import type { ButtonVariants } from '@/components/ui/button/variants';
import { tv } from 'tailwind-variants';

export type CardAppearance = 'filled' | 'glow' | 'outline' | 'tinted';
export type CardBorderEffect = 'none' | 'trace';
export type CardVariantName = 'neutral' | 'muted' | 'primary' | 'secondary' | 'accent' | 'info' | 'success' | 'warning' | 'error';

export type CardVariants = {
  appearance?: CardAppearance;
  variant?: CardVariantName;
};

type ButtonAppearance = NonNullable<ButtonVariants['appearance']>;
type ButtonVariant = NonNullable<ButtonVariants['variant']>;

const defaultCardAppearance: CardAppearance = 'filled';
const defaultCardVariant: CardVariantName = 'neutral';

const cardSurfaceBaseClassName = 'relative isolate overflow-hidden rounded-3xl border shadow-[var(--elevation-1)]';
const cardBaseClassName = 'flex flex-col gap-6 py-6 text-[var(--foreground)]';
const cardBadgeBaseClassName = 'inline-flex min-w-13 justify-center rounded-full border px-3 py-1.5 text-sm font-semibold tabular-nums shadow-[var(--elevation-1)]';
const cardInsetPanelBaseClassName = 'rounded-3xl border';

const buildGlowSurface = (token: string, borderClassName: string): string => {
  return [
    borderClassName,
    `bg-[radial-gradient(circle_at_50%_0%,color-mix(in_oklab,var(--${token}-200)_24%,transparent)_0%,transparent_60%),radial-gradient(circle_at_50%_0%,color-mix(in_oklab,var(--${token}-500)_16%,transparent)_0%,transparent_70%),radial-gradient(circle_at_50%_0%,color-mix(in_oklab,var(--${token}-700)_10%,transparent)_0%,transparent_80%)]`,
    `dark:bg-[radial-gradient(circle_at_50%_100%,color-mix(in_oklab,var(--${token}-700)_50%,transparent)_0%,transparent_60%),radial-gradient(circle_at_50%_100%,color-mix(in_oklab,var(--${token}-500)_40%,transparent)_0%,transparent_70%),radial-gradient(circle_at_50%_100%,color-mix(in_oklab,var(--${token}-200)_30%,transparent)_0%,transparent_80%)]`,
  ].join(' ');
};

const cardSurfaceClassNames: Record<CardAppearance, Record<CardVariantName, string>> = {
  filled: {
    neutral: 'border-border bg-card',
    muted: 'border-border/72 bg-muted/72',
    primary: 'border-primary/32 bg-primary/18',
    secondary: 'border-secondary/32 bg-secondary/18',
    accent: 'border-accent/32 bg-accent/18',
    info: 'border-info/32 bg-info/16',
    success: 'border-success/32 bg-success/16',
    warning: 'border-warning/34 bg-warning/18',
    error: 'border-destructive/34 bg-destructive/16',
  },
  glow: {
    neutral: buildGlowSurface('primary', 'border-border/68'),
    muted: buildGlowSurface('muted', 'border-border/64'),
    primary: buildGlowSurface('primary', 'border-primary/28'),
    secondary: buildGlowSurface('secondary', 'border-secondary/28'),
    accent: buildGlowSurface('accent', 'border-accent/28'),
    info: buildGlowSurface('info', 'border-info/28'),
    success: buildGlowSurface('success', 'border-success/28'),
    warning: buildGlowSurface('warning', 'border-warning/30'),
    error: buildGlowSurface('destructive', 'border-destructive/30'),
  },
  outline: {
    neutral: 'border-border/68 bg-transparent',
    muted: 'border-border/64 bg-transparent',
    primary: 'border-primary/28 bg-transparent',
    secondary: 'border-secondary/28 bg-transparent',
    accent: 'border-accent/28 bg-transparent',
    info: 'border-info/28 bg-transparent',
    success: 'border-success/28 bg-transparent',
    warning: 'border-warning/30 bg-transparent',
    error: 'border-destructive/30 bg-transparent',
  },
  tinted: {
    neutral: 'border-border/68 bg-background/42',
    muted: 'border-border/64 bg-muted/36',
    primary: 'border-primary/28 bg-primary/10',
    secondary: 'border-secondary/28 bg-secondary/12',
    accent: 'border-accent/28 bg-accent/10',
    info: 'border-info/28 bg-info/10',
    success: 'border-success/28 bg-success/10',
    warning: 'border-warning/30 bg-warning/12',
    error: 'border-destructive/30 bg-destructive/12',
  },
};

const cardBadgeClassNames: Record<CardAppearance, Record<CardVariantName, string>> = {
  filled: {
    neutral: 'border-border/72 bg-card text-foreground',
    muted: 'border-border/70 bg-muted text-foreground',
    primary: 'border-primary/35 bg-primary text-primary-foreground',
    secondary: 'border-secondary/35 bg-secondary text-secondary-foreground',
    accent: 'border-accent/35 bg-accent text-accent-foreground',
    info: 'border-info/35 bg-info text-info-foreground',
    success: 'border-success/35 bg-success text-success-foreground',
    warning: 'border-warning/35 bg-warning text-warning-foreground',
    error: 'border-destructive/35 bg-destructive text-destructive-foreground',
  },
  glow: {
    neutral: 'border-border/65 bg-background/42 text-foreground',
    muted: 'border-border/60 bg-muted/65 text-foreground',
    primary: 'border-primary/18 bg-primary/10 text-primary',
    secondary: 'border-secondary/18 bg-secondary/12 text-secondary',
    accent: 'border-accent/18 bg-accent/10 text-accent',
    info: 'border-info/18 bg-info/10 text-info',
    success: 'border-success/18 bg-success/10 text-success',
    warning: 'border-warning/24 bg-warning/12 text-warning',
    error: 'border-destructive/24 bg-destructive/12 text-destructive',
  },
  outline: {
    neutral: 'border-border/68 bg-background/38 text-foreground',
    muted: 'border-border/60 bg-muted/60 text-foreground',
    primary: 'border-primary/20 bg-primary/10 text-primary',
    secondary: 'border-secondary/20 bg-secondary/10 text-secondary',
    accent: 'border-accent/20 bg-accent/10 text-accent',
    info: 'border-info/20 bg-info/10 text-info',
    success: 'border-success/20 bg-success/10 text-success',
    warning: 'border-warning/24 bg-warning/10 text-warning',
    error: 'border-destructive/24 bg-destructive/10 text-destructive',
  },
  tinted: {
    neutral: 'border-border/66 bg-background/44 text-foreground',
    muted: 'border-border/62 bg-muted/62 text-foreground',
    primary: 'border-primary/22 bg-primary/12 text-primary',
    secondary: 'border-secondary/22 bg-secondary/12 text-secondary',
    accent: 'border-accent/22 bg-accent/12 text-accent',
    info: 'border-info/22 bg-info/12 text-info',
    success: 'border-success/22 bg-success/12 text-success',
    warning: 'border-warning/24 bg-warning/14 text-warning',
    error: 'border-destructive/24 bg-destructive/14 text-destructive',
  },
};

const sharedMetricVariantClassNames: Record<CardVariantName, string> = {
  neutral: 'text-foreground',
  muted: 'text-muted-foreground',
  primary: 'text-primary',
  secondary: 'text-secondary',
  accent: 'text-accent',
  info: 'text-info',
  success: 'text-success',
  warning: 'text-warning',
  error: 'text-destructive',
};

const filledMetricVariantClassNames: Record<CardVariantName, string> = {
  neutral: 'text-foreground',
  muted: 'text-foreground/88',
  primary: 'text-primary/95',
  secondary: 'text-secondary/95',
  accent: 'text-accent/95',
  info: 'text-info/95',
  success: 'text-success/95',
  warning: 'text-warning/95',
  error: 'text-destructive/95',
};

const cardMetricClassNames: Record<CardAppearance, Record<CardVariantName, string>> = {
  filled: filledMetricVariantClassNames,
  glow: sharedMetricVariantClassNames,
  outline: sharedMetricVariantClassNames,
  tinted: sharedMetricVariantClassNames,
};

const cardDividerClassNames: Record<CardAppearance, Record<CardVariantName, string>> = {
  filled: {
    neutral: 'border-border/60',
    muted: 'border-border/58',
    primary: 'border-primary/24',
    secondary: 'border-secondary/24',
    accent: 'border-accent/24',
    info: 'border-info/24',
    success: 'border-success/24',
    warning: 'border-warning/26',
    error: 'border-destructive/26',
  },
  glow: {
    neutral: 'border-border/55',
    muted: 'border-border/52',
    primary: 'border-primary/16',
    secondary: 'border-secondary/16',
    accent: 'border-accent/16',
    info: 'border-info/16',
    success: 'border-success/16',
    warning: 'border-warning/18',
    error: 'border-destructive/18',
  },
  outline: {
    neutral: 'border-border/52',
    muted: 'border-border/48',
    primary: 'border-primary/14',
    secondary: 'border-secondary/14',
    accent: 'border-accent/14',
    info: 'border-info/14',
    success: 'border-success/14',
    warning: 'border-warning/16',
    error: 'border-destructive/16',
  },
  tinted: {
    neutral: 'border-border/56',
    muted: 'border-border/52',
    primary: 'border-primary/18',
    secondary: 'border-secondary/18',
    accent: 'border-accent/18',
    info: 'border-info/18',
    success: 'border-success/18',
    warning: 'border-warning/20',
    error: 'border-destructive/20',
  },
};

const cardInsetPanelClassNames: Record<CardAppearance, Record<CardVariantName, string>> = {
  filled: {
    neutral: 'border-border/60 bg-card/88',
    muted: 'border-border/58 bg-muted/68',
    primary: 'border-primary/20 bg-primary/12',
    secondary: 'border-secondary/20 bg-secondary/12',
    accent: 'border-accent/20 bg-accent/12',
    info: 'border-info/20 bg-info/12',
    success: 'border-success/20 bg-success/12',
    warning: 'border-warning/22 bg-warning/14',
    error: 'border-destructive/22 bg-destructive/14',
  },
  glow: {
    neutral: 'border-border/55 bg-background/18',
    muted: 'border-border/52 bg-muted/18',
    primary: 'border-primary/16 bg-background/18',
    secondary: 'border-secondary/16 bg-background/18',
    accent: 'border-accent/16 bg-background/18',
    info: 'border-info/16 bg-background/18',
    success: 'border-success/16 bg-background/18',
    warning: 'border-warning/18 bg-background/18',
    error: 'border-destructive/18 bg-background/18',
  },
  outline: {
    neutral: 'border-border/55 bg-background/18',
    muted: 'border-border/52 bg-background/18',
    primary: 'border-primary/16 bg-background/18',
    secondary: 'border-secondary/16 bg-background/18',
    accent: 'border-accent/16 bg-background/18',
    info: 'border-info/16 bg-background/18',
    success: 'border-success/16 bg-background/18',
    warning: 'border-warning/18 bg-background/18',
    error: 'border-destructive/18 bg-background/18',
  },
  tinted: {
    neutral: 'border-border/55 bg-background/24',
    muted: 'border-border/52 bg-muted/22',
    primary: 'border-primary/18 bg-primary/8',
    secondary: 'border-secondary/18 bg-secondary/8',
    accent: 'border-accent/18 bg-accent/8',
    info: 'border-info/18 bg-info/8',
    success: 'border-success/18 bg-success/8',
    warning: 'border-warning/20 bg-warning/10',
    error: 'border-destructive/20 bg-destructive/10',
  },
};

const cardButtonAppearanceMap: Record<CardAppearance, ButtonAppearance> = {
  filled: 'filled',
  glow: 'tonal',
  outline: 'outline',
  tinted: 'tonal',
};

const cardButtonVariantMap: Record<CardVariantName, ButtonVariant> = {
  neutral: 'muted',
  muted: 'muted',
  primary: 'primary',
  secondary: 'secondary',
  accent: 'accent',
  info: 'info',
  success: 'success',
  warning: 'warning',
  error: 'destructive',
};

const cardBorderTraceClassNames: Record<CardVariantName, string> = {
  neutral: 'bg-foreground/40',
  muted: 'bg-muted-foreground/60',
  primary: 'bg-primary/92',
  secondary: 'bg-secondary/92',
  accent: 'bg-accent/92',
  info: 'bg-info/92',
  success: 'bg-success/92',
  warning: 'bg-warning/92',
  error: 'bg-destructive/92',
};

const cardBorderTraceOverlayClassNames: Record<CardVariantName, string> = {
  neutral: 'border-foreground/28',
  muted: 'border-muted-foreground/42',
  primary: 'border-primary/72',
  secondary: 'border-secondary/72',
  accent: 'border-accent/72',
  info: 'border-info/72',
  success: 'border-success/72',
  warning: 'border-warning/72',
  error: 'border-destructive/72',
};

const resolveCardOptions = (options: CardVariants = {}): Required<CardVariants> => {
  return {
    appearance: options.appearance ?? defaultCardAppearance,
    variant: options.variant ?? defaultCardVariant,
  };
};

export const getCardSurfaceClassNames = (options: CardVariants = {}): string => {
  const resolved = resolveCardOptions(options);

  return [cardSurfaceBaseClassName, cardSurfaceClassNames[resolved.appearance][resolved.variant]].join(' ');
};

export const getCardClassNames = (options: CardVariants = {}): string => {
  return [cardBaseClassName, getCardSurfaceClassNames(options)].join(' ');
};

export const getCardBadgeClassNames = (options: CardVariants = {}): string => {
  const resolved = resolveCardOptions(options);

  return [cardBadgeBaseClassName, cardBadgeClassNames[resolved.appearance][resolved.variant]].join(' ');
};

export const getCardDividerClassNames = (options: CardVariants = {}): string => {
  const resolved = resolveCardOptions(options);

  return ['border-t', cardDividerClassNames[resolved.appearance][resolved.variant]].join(' ');
};

export const getCardMetricClassNames = (options: CardVariants = {}): string => {
  const resolved = resolveCardOptions(options);

  return cardMetricClassNames[resolved.appearance][resolved.variant];
};

export const getCardInsetPanelClassNames = (options: CardVariants = {}): string => {
  const resolved = resolveCardOptions(options);

  return [cardInsetPanelBaseClassName, cardInsetPanelClassNames[resolved.appearance][resolved.variant]].join(' ');
};

export const getCardButtonAppearance = (appearance: CardAppearance = defaultCardAppearance): ButtonAppearance => {
  return cardButtonAppearanceMap[appearance];
};

export const getCardButtonVariant = (variant: CardVariantName = defaultCardVariant): ButtonVariant => {
  return cardButtonVariantMap[variant];
};

export const getCardBorderTraceClassNames = (variant: CardVariantName = defaultCardVariant): string => {
  return cardBorderTraceClassNames[variant];
};

export const getCardBorderTraceOverlayClassNames = (variant: CardVariantName = defaultCardVariant): string => {
  return cardBorderTraceOverlayClassNames[variant];
};

export const cardHeaderVariants = tv({ base: '@container/card-header grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 px-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6' });
export const cardTitleVariants = tv({ base: 'leading-none font-semibold' });
export const cardDescriptionVariants = tv({ base: 'text-sm text-muted-foreground' });
export const cardContentVariants = tv({ base: 'px-6' });
export const cardFooterVariants = tv({ base: 'flex items-center px-6 [.border-t]:pt-6' });
export const cardActionVariants = tv({ base: 'col-start-2 row-span-2 row-start-1 self-start justify-self-end' });
