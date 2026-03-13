<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardFooter from '@/components/ui/card/CardFooter.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import { cn } from '@/lib/utils';

withDefaults(
  defineProps<{
    contentClass?: HTMLAttributes['class'];
    description?: string;
    footerClass?: HTMLAttributes['class'];
    headerClass?: HTMLAttributes['class'];
    title: string;
    variant?:
      | 'default'
      | 'destructive'
      | 'info'
      | 'warning'
      | 'success'
      | 'glass';
  }>(),
  {
    variant: 'default',
  },
);
</script>

<template>
  <Card :variant="variant" class="overflow-hidden">
    <CardHeader :class="cn('gap-1.5 px-6 pb-0', headerClass)">
      <h2 class="text-lg font-semibold tracking-tight">
        {{ title }}
      </h2>
      <p v-if="description" class="text-sm text-muted-foreground">
        {{ description }}
      </p>
    </CardHeader>

    <CardContent :class="cn('space-y-4 px-6', contentClass)">
      <slot />
    </CardContent>

    <CardFooter
      v-if="$slots.footer"
      :class="cn('border-t border-border/70 px-6 pt-5', footerClass)"
    >
      <slot name="footer" />
    </CardFooter>
  </Card>
</template>
