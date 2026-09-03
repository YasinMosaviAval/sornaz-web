import { mkdir, writeFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';

const origin = 'https://sornaz.com';
const sitemapIndex = `${origin}/sitemap_index.xml`;
const outputFile = resolve('docs/migration/legacy-sornaz-urls.csv');
const summaryFile = resolve('docs/migration/legacy-sornaz-summary.json');
const userAgent = 'SornazMigrationAudit/1.0 (+https://sornaz.com)';
const crawlLimit = 1000;

const pageExtensions = new Set(['', '.html', '.htm', '.php']);
const ignoredCrawlPrefixes = ['/wp-admin', '/wp-json', '/wp-content', '/wp-includes'];

function decodeEntities(value) {
  return value
    .replaceAll('&amp;', '&')
    .replaceAll('&quot;', '"')
    .replaceAll('&#039;', "'")
    .replaceAll('&lt;', '<')
    .replaceAll('&gt;', '>');
}

function extractLocs(xml) {
  return [...xml.matchAll(/<loc>([\s\S]*?)<\/loc>/gi)].map((match) => decodeEntities(match[1].trim()));
}

function extractAttribute(html, tag, attribute) {
  const tags = html.match(new RegExp(`<${tag}\\b[^>]*>`, 'gi')) ?? [];
  const values = [];
  for (const item of tags) {
    const match = item.match(new RegExp(`\\b${attribute}\\s*=\\s*(["'])(.*?)\\1`, 'i'));
    if (match) values.push(decodeEntities(match[2].trim()));
  }
  return values;
}

function extractTitle(html) {
  const match = html.match(/<title\b[^>]*>([\s\S]*?)<\/title>/i);
  return match ? decodeEntities(match[1].replace(/<[^>]+>/g, '').replace(/\s+/g, ' ').trim()) : '';
}

function normalize(raw, base = origin) {
  try {
    const url = new URL(raw, base);
    if (!['http:', 'https:'].includes(url.protocol)) return null;
    if (!['sornaz.com', 'www.sornaz.com'].includes(url.hostname.toLowerCase())) return null;
    url.protocol = 'https:';
    url.hostname = 'sornaz.com';
    url.hash = '';
    for (const key of [...url.searchParams.keys()]) {
      if (/^(utm_|fbclid|gclid)/i.test(key)) url.searchParams.delete(key);
    }
    if (url.pathname !== '/') url.pathname = url.pathname.replace(/\/{2,}/g, '/');
    return url.toString();
  } catch {
    return null;
  }
}

function canCrawl(urlString) {
  const url = new URL(urlString);
  if (ignoredCrawlPrefixes.some((prefix) => url.pathname.startsWith(prefix))) return false;
  if (url.search && !url.searchParams.has('paged') && !url.searchParams.has('page')) return false;
  const last = url.pathname.split('/').filter(Boolean).at(-1) ?? '';
  const dot = last.lastIndexOf('.');
  const extension = dot >= 0 ? last.slice(dot).toLowerCase() : '';
  return pageExtensions.has(extension);
}

async function get(url) {
  const response = await fetch(url, {
    headers: { 'user-agent': userAgent, accept: 'text/html,application/xml;q=0.9,*/*;q=0.1' },
    redirect: 'follow',
    signal: AbortSignal.timeout(30000),
  });
  return { response, body: await response.text() };
}

async function loadSitemaps() {
  const { body: index } = await get(sitemapIndex);
  const maps = extractLocs(index).filter((url) => url.endsWith('.xml'));
  const urls = new Map();
  for (const map of maps) {
    const { body } = await get(map);
    const type = new URL(map).pathname.replace(/^\//, '').replace(/-sitemap\.xml$/i, '');
    for (const location of extractLocs(body)) {
      const normalized = normalize(location);
      if (normalized) urls.set(normalized, type);
    }
  }
  return { maps, urls };
}

function csvCell(value) {
  return `"${String(value ?? '').replaceAll('"', '""')}"`;
}

async function main() {
  const { maps, urls: sitemapUrls } = await loadSitemaps();
  const queue = [...sitemapUrls.keys()];
  const queued = new Set(queue);
  const records = new Map();

  while (queue.length && records.size < crawlLimit) {
    const batch = queue.splice(0, 6);
    const results = await Promise.all(batch.map(async (requestedUrl) => {
      try {
        const { response, body } = await get(requestedUrl);
        const contentType = response.headers.get('content-type') ?? '';
        const finalUrl = normalize(response.url) ?? response.url;
        const isHtml = contentType.includes('text/html');
        const hrefs = isHtml ? extractAttribute(body, 'a', 'href') : [];
        const canonicals = isHtml
          ? [...body.matchAll(/<link\b[^>]*rel\s*=\s*(["'])canonical\1[^>]*>/gi)]
            .flatMap((match) => extractAttribute(match[0], 'link', 'href'))
          : [];
        return {
          requestedUrl, finalUrl, status: response.status, contentType,
          title: isHtml ? extractTitle(body) : '', canonical: normalize(canonicals[0] ?? '', finalUrl) ?? '', hrefs,
        };
      } catch (error) {
        return { requestedUrl, finalUrl: '', status: 0, contentType: '', title: '', canonical: '', hrefs: [], error: error.message };
      }
    }));

    for (const result of results) {
      records.set(result.requestedUrl, result);
      for (const href of result.hrefs) {
        const normalized = normalize(href, result.finalUrl || result.requestedUrl);
        if (!normalized || queued.has(normalized) || !canCrawl(normalized)) continue;
        queued.add(normalized);
        queue.push(normalized);
      }
    }
  }

  const rows = [...queued].map((url) => {
    const result = records.get(url) ?? {};
    const parsed = new URL(url);
    return {
      old_url: url,
      decoded_path: decodeURIComponent(parsed.pathname),
      source: sitemapUrls.get(url) ? `sitemap:${sitemapUrls.get(url)}` : 'discovered-link',
      http_status: result.status ?? '',
      final_url: result.finalUrl ?? '',
      canonical_url: result.canonical ?? '',
      title: result.title ?? '',
      content_type: result.contentType ?? '',
      error: result.error ?? '',
      redirect_target: '',
      redirect_status: 301,
      mapping_status: 'pending',
    };
  }).sort((a, b) => a.old_url.localeCompare(b.old_url));

  const headers = Object.keys(rows[0]);
  const csv = [headers.map(csvCell).join(','), ...rows.map((row) => headers.map((key) => csvCell(row[key])).join(','))].join('\n') + '\n';
  const statusCounts = rows.reduce((counts, row) => {
    const key = String(row.http_status || 'not-crawled');
    counts[key] = (counts[key] ?? 0) + 1;
    return counts;
  }, {});
  const summary = {
    generated_at: new Date().toISOString(), origin, sitemap_index: sitemapIndex, sitemaps: maps,
    sitemap_url_count: sitemapUrls.size,
    total_internal_url_count: rows.length,
    crawled_count: records.size,
    discovered_link_count: rows.filter((row) => row.source === 'discovered-link').length,
    status_counts: statusCounts,
    crawl_limit: crawlLimit,
  };
  await mkdir(dirname(outputFile), { recursive: true });
  await writeFile(outputFile, `\uFEFF${csv}`, 'utf8');
  await writeFile(summaryFile, `${JSON.stringify(summary, null, 2)}\n`, 'utf8');
  console.log(JSON.stringify(summary, null, 2));
}

await main();
