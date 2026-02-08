export function normalizeErrorMessages(input: unknown): string[] {
  const messages = collectMessages(input)
    .map((message) => message.trim())
    .filter(Boolean);

  return Array.from(new Set(messages));
}

function collectMessages(input: unknown): string[] {
  if (typeof input === "string") {
    return [input];
  }

  if (Array.isArray(input)) {
    return input.flatMap((value) => collectMessages(value));
  }

  if (input && typeof input === "object") {
    return Object.values(input).flatMap((value) => collectMessages(value));
  }

  return [];
}
