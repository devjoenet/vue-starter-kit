import { ref } from 'vue';

type SubmitVisitCallbacks = {
  onCancel: () => void;
  onError: () => void;
  onFinish: () => void;
  onSuccess: () => void;
};

type SubmitVisit = (callbacks: SubmitVisitCallbacks) => void;

type SubmitStep = (() => Promise<boolean>) | null | undefined;

const toPromise = (visit: SubmitVisit): Promise<boolean> => {
  return new Promise<boolean>((resolve) => {
    let succeeded = false;

    visit({
      onSuccess: () => {
        succeeded = true;
      },
      onError: () => {
        succeeded = false;
      },
      onCancel: () => {
        succeeded = false;
      },
      onFinish: () => {
        resolve(succeeded);
      },
    });
  });
};

export function useSequentialSave() {
  const processing = ref(false);

  const createStep = (visit: SubmitVisit): (() => Promise<boolean>) => {
    return () => toPromise(visit);
  };

  const run = async (steps: SubmitStep[]): Promise<boolean> => {
    const activeSteps = steps.filter(
      (step): step is () => Promise<boolean> => step !== null && step !== undefined,
    );

    if (processing.value || activeSteps.length === 0) {
      return false;
    }

    processing.value = true;

    try {
      for (const step of activeSteps) {
        const succeeded = await step();

        if (!succeeded) {
          return false;
        }
      }

      return true;
    } finally {
      processing.value = false;
    }
  };

  return {
    createStep,
    processing,
    run,
  };
}
