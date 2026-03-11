import { ref } from 'vue';
import type { Ref } from 'vue';

type SelectableValue = number | string;
type SelectionState = boolean | 'indeterminate';

export function useSelectionList<T extends SelectableValue>(
  initialValues: T[] = [],
) {
  const selectedValues = ref([...initialValues]) as Ref<T[]>;

  const hasSelectedValue = (value: T): boolean =>
    selectedValues.value.includes(value);

  const replaceSelectedValues = (values: T[]) => {
    selectedValues.value = [...values];
  };

  const toggleSelectedValue = (value: T, nextState: SelectionState) => {
    const nextSelectedValues = new Set(selectedValues.value);

    if (nextState === true) {
      nextSelectedValues.add(value);
    } else {
      nextSelectedValues.delete(value);
    }

    selectedValues.value = [...nextSelectedValues];
  };

  return {
    hasSelectedValue,
    replaceSelectedValues,
    selectedValues,
    toggleSelectedValue,
  };
}
