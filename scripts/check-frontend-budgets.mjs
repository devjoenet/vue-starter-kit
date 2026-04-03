import fs from 'node:fs';
import path from 'node:path';

const buildRoot = path.resolve(process.cwd(), 'public/build');
const manifestPath = path.join(buildRoot, 'manifest.json');

if (!fs.existsSync(manifestPath)) {
  throw new Error('Missing build manifest. Run `vite build` before checking frontend budgets.');
}

const manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf8'));

const budgets = {
  'resources/js/app.ts': {
    css: 225 * 1024,
    js: 340 * 1024,
  },
  'resources/js/pages/admin/Dashboard.vue': {
    js: 520 * 1024,
  },
  'resources/js/pages/admin/Users/Index.vue': {
    js: 520 * 1024,
  },
  'resources/js/pages/admin/Roles/Index.vue': {
    js: 520 * 1024,
  },
  'resources/js/pages/admin/Permissions/Index.vue': {
    js: 520 * 1024,
  },
};

const formatKiB = (bytes) => `${(bytes / 1024).toFixed(1)} KiB`;

const getAssetSize = (file) => fs.statSync(path.join(buildRoot, file)).size;

const collectEntrySizes = (entryKey, seen = new Set()) => {
  const entry = manifest[entryKey];

  if (!entry) {
    throw new Error(`Missing manifest entry: ${entryKey}`);
  }

  if (seen.has(entryKey)) {
    return { css: 0, js: 0 };
  }

  seen.add(entryKey);

  let css = (entry.css ?? []).reduce((total, file) => total + getAssetSize(file), 0);
  let js = entry.file ? getAssetSize(entry.file) : 0;

  for (const importedEntry of entry.imports ?? []) {
    const importedSizes = collectEntrySizes(importedEntry, seen);
    css += importedSizes.css;
    js += importedSizes.js;
  }

  return { css, js };
};

const failures = [];

for (const [entryKey, budget] of Object.entries(budgets)) {
  const sizes = collectEntrySizes(entryKey);

  if (budget.js !== undefined && sizes.js > budget.js) {
    failures.push(`${entryKey} initial JS ${formatKiB(sizes.js)} exceeds ${formatKiB(budget.js)}`);
  }

  if (budget.css !== undefined && sizes.css > budget.css) {
    failures.push(`${entryKey} initial CSS ${formatKiB(sizes.css)} exceeds ${formatKiB(budget.css)}`);
  }
}

if (failures.length > 0) {
  throw new Error(`Frontend asset budgets failed:\n- ${failures.join('\n- ')}`);
}

console.log('Frontend asset budgets passed.');
