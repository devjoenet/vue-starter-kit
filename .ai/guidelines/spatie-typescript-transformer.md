# Spatie TypeScript Transformer usage (project standard)

## Goal

Generate TS types from PHP Data objects and enums.

## Rules

- Annotate Data classes meant for the frontend
- Output goes to: resources/js/types/generated.d.ts (or similar)
- Add an npm script and a composer script to regenerate consistently

## Typical flow

1. Add/modify PHP Data class
2. Run generation command
3. Commit updated TS output

## Boost integration

When boost generates Data objects:

- Ensure they are annotated for TS generation
- Ensure the output file is updated and committed
