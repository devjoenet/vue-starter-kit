# Laravel IDE Helper usage (project standard)

## Goal

Keep IDE autocompletion accurate for facades, models, and macros.

## Rules

- Never run in production
- Prefer a composer script:

  - ide-helper:generate
  - ide-helper:models --nowrite

- Commit strategy:

  - ide-helper.php can be committed or ignored (team choice)
  - model helpers typically NOT committed in modern workflows unless required
