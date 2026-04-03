export type RequestFailureTone = 'error' | 'warning';

type RequestFailure = {
  message: string;
  tone: RequestFailureTone;
};

const withRequestReference = (message: string, requestId?: string | null): string => {
  if (!requestId) {
    return message;
  }

  return `${message} Ref: ${requestId}.`;
};

export const resolveNetworkFailureMessage = (): string => {
  return 'Unable to reach the server. Check the connection and try again.';
};

export const resolveRequestFailureMessage = (status: number, requestId?: string | null): RequestFailure => {
  if (status === 401) {
    return {
      tone: 'warning',
      message: withRequestReference('Your session is no longer valid. Sign in again to continue.', requestId),
    };
  }

  if (status === 403) {
    return {
      tone: 'warning',
      message: withRequestReference('You do not have permission to complete this request.', requestId),
    };
  }

  if (status === 419) {
    return {
      tone: 'warning',
      message: withRequestReference('Your session expired. Refresh the page and try again.', requestId),
    };
  }

  if (status >= 500) {
    return {
      tone: 'error',
      message: withRequestReference('The server could not finish this request. The page is still safe to use.', requestId),
    };
  }

  return {
    tone: 'error',
    message: withRequestReference('Unable to complete the request right now.', requestId),
  };
};

export const resolveUnknownFailureMessage = (error: unknown, fallback: string): string => {
  if (error instanceof Error && error.message.trim() !== '') {
    return error.message;
  }

  return fallback;
};
