import type { InertiaLinkProps } from "@inertiajs/vue3";
import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export function toUrl(href: NonNullable<InertiaLinkProps["href"]>) {
  return typeof href === "string" ? href : href?.url;
}

/**
 * Normalizes an arbitrary string into lowercase word tokens.
 *
 * Splits on whitespace, separators, and case boundaries while stripping apostrophes.
 */
function toWords(value: string): string[] {
  const normalizedValue = value
    .trim()
    .replace(/['’]/g, "")
    .replace(/([a-z0-9])([A-Z])/g, "$1 $2")
    .replace(/([A-Z]+)([A-Z][a-z])/g, "$1 $2")
    .replace(/[_\-.]+/g, " ")
    .replace(/\s+/g, " ");

  if (!normalizedValue) {
    return [];
  }

  return (normalizedValue.match(/[A-Za-z0-9]+/g) ?? []).map((word) => word.toLowerCase());
}

/**
 * Capitalizes the first character of a normalized word.
 */
function capitalizeWord(word: string): string {
  return word.charAt(0).toUpperCase() + word.slice(1);
}

/**
 * Converts any supported string format into snake_case.
 *
 * Handles plain text, spaced words, kebab-case, dotted.values, camelCase, and StudlyCase.
 */
export function toSnakeCase(value: string): string {
  return toWords(value).join("_");
}

/**
 * Converts any supported string format into kebab-case.
 *
 * Handles plain text, spaced words, snake_case, dotted.values, camelCase, and StudlyCase.
 */
export function toKebabCase(value: string): string {
  return toWords(value).join("-");
}

/**
 * Converts any supported string format into Title Case.
 *
 * Example: "user_profile" -> "User Profile".
 */
export function toTitleCase(value: string): string {
  return toWords(value).map(capitalizeWord).join(" ");
}

/**
 * Converts any supported string format into StudlyCase (PascalCase).
 *
 * Example: "user_profile" -> "UserProfile".
 */
export function toStudlyCase(value: string): string {
  return toWords(value).map(capitalizeWord).join("");
}

/**
 * Converts any supported string format into camelCase.
 *
 * Example: "user_profile" -> "userProfile".
 */
export function toCamelCase(value: string): string {
  const [firstWord, ...remainingWords] = toWords(value);

  if (!firstWord) {
    return "";
  }

  return firstWord + remainingWords.map(capitalizeWord).join("");
}
