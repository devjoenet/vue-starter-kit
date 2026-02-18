<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from "vue";
  import { useVModel } from "@vueuse/core";
  import { Check, Eraser } from "lucide-vue-next";
  import { Button } from "@/components/ui/button";
  import type { InputVariants } from "@/components/ui/input";
  import { inputAssistiveTextVariants } from "@/components/ui/input";
  import { cn } from "@/lib/utils";
  import type { SignatureDataUrlType } from ".";
  import { signatureSurfaceVariants } from ".";

  defineOptions({
    inheritAttrs: false,
  });

  const props = withDefaults(
    defineProps<{
      defaultValue?: string;
      modelValue?: string;
      class?: HTMLAttributes["class"];
      label?: string;
      supportingText?: string;
      errorText?: string;
      message?: string;
      variant?: InputVariants["variant"];
      state?: InputVariants["state"];
      error?: boolean | string;
      required?: boolean;
      noAsterisk?: boolean;
      disabled?: boolean;
      readonly?: boolean;
      id?: string;
      name?: string;
      height?: number;
      lineWidth?: number;
      strokeStyle?: string;
      backgroundColor?: string;
      dataUrlType?: SignatureDataUrlType;
      showActions?: boolean;
      clearText?: string;
      confirmText?: string;
      clearable?: boolean;
      immediate?: boolean;
    }>(),
    {
      variant: "filled",
      state: "default",
      height: 220,
      lineWidth: 2,
      strokeStyle: "currentColor",
      backgroundColor: "transparent",
      dataUrlType: "png",
      showActions: true,
      clearText: "Clear",
      confirmText: "Confirm",
      clearable: true,
      immediate: false,
    },
  );

  const emit = defineEmits<{
    (event: "update:modelValue", value: string): void;
    (event: "change", value: string): void;
    (event: "clear", value: string): void;
    (event: "confirm", value: string): void;
    (event: "start"): void;
    (event: "signing"): void;
    (event: "end"): void;
  }>();

  const modelValue = useVModel(props, "modelValue", emit, {
    passive: true,
    defaultValue: props.defaultValue,
  });

  const canvasRef = ref<HTMLCanvasElement | null>(null);
  const rootRef = ref<HTMLElement | null>(null);
  const isDrawing = ref(false);
  const hasStroke = ref(Boolean(props.defaultValue ?? props.modelValue));

  let context: CanvasRenderingContext2D | null = null;
  let activePointerId: number | null = null;
  let resizeObserver: ResizeObserver | null = null;

  const hasError = computed(() => {
    if (props.state === "error" || props.state === "destructive") {
      return true;
    }

    if (typeof props.error === "string") {
      return true;
    }

    return Boolean(props.error);
  });

  const fieldState = computed<InputVariants["state"]>(() => {
    if (hasError.value) {
      if (props.state === "destructive") {
        return "destructive";
      }

      return "error";
    }

    if (props.state === "info") {
      return "info";
    }

    if (props.state === "warning") {
      return "warning";
    }

    if (props.state === "success") {
      return "success";
    }

    return "default";
  });

  const showAsterisk = computed(() => props.required && !props.noAsterisk);
  const hasValue = computed(() => hasStroke.value && Boolean(modelValue.value));

  const supportingText = computed(() => {
    if (hasError.value) {
      if (typeof props.error === "string") {
        return props.error;
      }

      return props.errorText ?? props.message ?? props.supportingText;
    }

    return props.supportingText ?? props.message;
  });

  const assistiveTextClasses = computed(() =>
    cn(
      inputAssistiveTextVariants({
        state: fieldState.value,
      }),
    ),
  );

  const labelClasses = computed(() =>
    cn(
      "text-sm font-medium text-[var(--field-label)]",
      fieldState.value === "error" && "text-[var(--error)]",
      fieldState.value === "destructive" && "text-destructive",
      fieldState.value === "info" && "text-info",
      fieldState.value === "warning" && "text-warning",
      fieldState.value === "success" && "text-success",
    ),
  );

  const surfaceClasses = computed(() =>
    cn(
      signatureSurfaceVariants({
        state: fieldState.value,
      }),
      (props.disabled || props.readonly) && "cursor-not-allowed opacity-70",
      props.class,
    ),
  );

  const canDraw = computed(() => !props.disabled && !props.readonly);
  const canClear = computed(() => props.clearable && canDraw.value && hasValue.value);

  watch(
    () => [props.height, props.backgroundColor],
    async () => {
      await nextTick();
      resizeCanvas();
      redrawFromModel();
    },
  );

  watch(
    () => modelValue.value,
    (value) => {
      if (!value) {
        clearCanvas(false);
        return;
      }

      redrawFromModel();
    },
  );

  onMounted(async () => {
    await nextTick();
    resizeCanvas();

    if (modelValue.value) {
      redrawFromModel();
    }

    if (rootRef.value) {
      resizeObserver = new ResizeObserver(() => {
        resizeCanvas();
        redrawFromModel();
      });

      resizeObserver.observe(rootRef.value);
    }
  });

  onBeforeUnmount(() => {
    if (resizeObserver) {
      resizeObserver.disconnect();
      resizeObserver = null;
    }
  });

  function getContext(): CanvasRenderingContext2D | null {
    if (!canvasRef.value) {
      return null;
    }

    if (!context) {
      context = canvasRef.value.getContext("2d");
    }

    return context;
  }

  function resizeCanvas(): void {
    if (!canvasRef.value || !rootRef.value) {
      return;
    }

    const canvas = canvasRef.value;
    const pixelRatio = window.devicePixelRatio || 1;
    const width = Math.max(1, Math.floor(rootRef.value.clientWidth));
    const height = Math.max(1, Math.floor(props.height));

    canvas.width = Math.floor(width * pixelRatio);
    canvas.height = Math.floor(height * pixelRatio);
    canvas.style.width = `${width}px`;
    canvas.style.height = `${height}px`;

    const nextContext = canvas.getContext("2d");

    if (!nextContext) {
      return;
    }

    context = nextContext;
    context.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
    context.lineCap = "round";
    context.lineJoin = "round";
    context.lineWidth = props.lineWidth;
    context.strokeStyle = resolveStrokeColor();

    if (props.backgroundColor !== "transparent") {
      context.fillStyle = props.backgroundColor;
      context.fillRect(0, 0, width, height);
    }
  }

  function resolveStrokeColor(): string {
    if (props.strokeStyle !== "currentColor") {
      return props.strokeStyle;
    }

    if (!rootRef.value) {
      return "currentColor";
    }

    return getComputedStyle(rootRef.value).color;
  }

  function getRelativePoint(event: PointerEvent): { x: number; y: number } | null {
    if (!canvasRef.value) {
      return null;
    }

    const rect = canvasRef.value.getBoundingClientRect();

    return {
      x: event.clientX - rect.left,
      y: event.clientY - rect.top,
    };
  }

  function handlePointerDown(event: PointerEvent): void {
    if (!canDraw.value || !canvasRef.value) {
      return;
    }

    const nextContext = getContext();

    if (!nextContext) {
      return;
    }

    const point = getRelativePoint(event);

    if (!point) {
      return;
    }

    activePointerId = event.pointerId;
    isDrawing.value = true;
    hasStroke.value = true;

    canvasRef.value.setPointerCapture(event.pointerId);

    nextContext.beginPath();
    nextContext.lineWidth = props.lineWidth;
    nextContext.strokeStyle = resolveStrokeColor();
    nextContext.moveTo(point.x, point.y);

    emit("start");
  }

  function handlePointerMove(event: PointerEvent): void {
    if (!isDrawing.value || activePointerId !== event.pointerId) {
      return;
    }

    const nextContext = getContext();

    if (!nextContext) {
      return;
    }

    const point = getRelativePoint(event);

    if (!point) {
      return;
    }

    nextContext.lineTo(point.x, point.y);
    nextContext.stroke();

    emit("signing");

    if (props.immediate) {
      const dataUrl = confirmSignature();
      modelValue.value = dataUrl;
      emit("change", dataUrl);
    }
  }

  function finishDrawing(event: PointerEvent): void {
    if (!isDrawing.value || activePointerId !== event.pointerId) {
      return;
    }

    isDrawing.value = false;
    activePointerId = null;

    if (canvasRef.value?.hasPointerCapture(event.pointerId)) {
      canvasRef.value.releasePointerCapture(event.pointerId);
    }

    emit("end");

    const dataUrl = confirmSignature();
    modelValue.value = dataUrl;
    emit("change", dataUrl);
  }

  function clearCanvas(shouldEmit = true): void {
    const nextContext = getContext();

    if (!nextContext || !canvasRef.value) {
      return;
    }

    const width = canvasRef.value.width / (window.devicePixelRatio || 1);
    const height = canvasRef.value.height / (window.devicePixelRatio || 1);

    nextContext.clearRect(0, 0, width, height);

    if (props.backgroundColor !== "transparent") {
      nextContext.fillStyle = props.backgroundColor;
      nextContext.fillRect(0, 0, width, height);
    }

    hasStroke.value = false;

    if (!shouldEmit) {
      return;
    }

    modelValue.value = "";
    emit("clear", "");
    emit("change", "");
  }

  function confirmSignature(): string {
    if (!canvasRef.value || !hasStroke.value) {
      return "";
    }

    if (props.dataUrlType === "jpg") {
      return canvasRef.value.toDataURL("image/jpeg");
    }

    return canvasRef.value.toDataURL("image/png");
  }

  function redrawFromModel(): void {
    if (!canvasRef.value) {
      return;
    }

    clearCanvas(false);

    if (!modelValue.value) {
      return;
    }

    const image = new Image();

    image.onload = () => {
      const nextContext = getContext();

      if (!nextContext || !canvasRef.value) {
        return;
      }

      const width = canvasRef.value.width / (window.devicePixelRatio || 1);
      const height = canvasRef.value.height / (window.devicePixelRatio || 1);
      nextContext.drawImage(image, 0, 0, width, height);
      hasStroke.value = true;
    };

    image.src = modelValue.value;
  }

  function confirm(): string {
    const dataUrl = confirmSignature();

    modelValue.value = dataUrl;
    emit("confirm", dataUrl);
    emit("change", dataUrl);

    return dataUrl;
  }

  function reset(): void {
    clearCanvas(true);
  }

  function isEmpty(): boolean {
    return !hasStroke.value;
  }

  defineExpose({
    reset,
    confirm,
    isEmpty,
  });
</script>

<template>
  <div class="w-full space-y-2">
    <input v-if="name" type="hidden" :name="name" :value="modelValue ?? ''" />

    <label v-if="label" :for="id" :class="labelClasses">
      {{ label }}
      <span v-if="showAsterisk" class="text-[var(--error)]"> *</span>
    </label>

    <div ref="rootRef" :id="id" :class="surfaceClasses" :style="{ height: `${height}px` }" v-bind="$attrs">
      <canvas
        ref="canvasRef"
        class="h-full w-full touch-none"
        :aria-disabled="disabled ? 'true' : undefined"
        :aria-readonly="readonly ? 'true' : undefined"
        @pointerdown="handlePointerDown"
        @pointermove="handlePointerMove"
        @pointerup="finishDrawing"
        @pointercancel="finishDrawing"
        @pointerleave="finishDrawing"
      />

      <div v-if="showActions" class="absolute inset-x-0 bottom-0 flex items-center justify-between border-t border-black/10 bg-background/80 px-2 py-2 backdrop-blur-sm dark:border-white/10">
        <Button appearance="text" size="sm" :disabled="!canClear" @click="reset">
          <Eraser class="size-4" />
          {{ clearText }}
        </Button>

        <Button appearance="tonal" size="sm" :disabled="!hasValue" @click="confirm">
          <Check class="size-4" />
          {{ confirmText }}
        </Button>
      </div>
    </div>

    <div v-if="supportingText" class="mt-1">
      <p :class="assistiveTextClasses">
        {{ supportingText }}
      </p>
    </div>
  </div>
</template>
