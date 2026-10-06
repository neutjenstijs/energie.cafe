import http from 'node:http';
import fs from 'node:fs';
import path from 'node:path';
import { loadNodeRuntime, createNodeFsMountHandler } from '@php-wasm/node';
import { PHP } from '@php-wasm/universal';

const SITE = path.resolve(process.argv[2] || '..');
const PORT = 8088;
const php = new PHP(await loadNodeRuntime('8.3', { emscriptenOptions: { processId: 1 } }));
await php.mount('/site', createNodeFsMountHandler(SITE));
const WWW = path.join(SITE, 'www');
const mime = { '.css': 'text/css', '.js': 'text/javascript', '.svg': 'image/svg+xml', '.png': 'image/png', '.jpg': 'image/jpeg', '.woff2': 'font/woff2', '.txt': 'text/plain', '.ico': 'image/x-icon' };

let queue = Promise.resolve();
http.createServer((req, res) => {
  const chunks = [];
  req.on('data', c => chunks.push(c));
  req.on('end', () => { queue = queue.then(() => handle(req, res, Buffer.concat(chunks))).catch(e => { console.error(e); res.writeHead(500); res.end(String(e)); }); });
}).listen(PORT, () => console.log('dev server on http://localhost:' + PORT));

async function handle(req, res, body) {
  const u = new URL(req.url, 'http://localhost');
  let p = decodeURIComponent(u.pathname);
  let local = path.join(WWW, p);
  let script = null, query = u.search;
  if (fs.existsSync(local) && fs.statSync(local).isFile() && !p.endsWith('.php')) {
    res.writeHead(200, { 'Content-Type': mime[path.extname(local)] || 'application/octet-stream' });
    return res.end(fs.readFileSync(local));
  }
  if (p.endsWith('.php') && fs.existsSync(local)) script = p;
  else if (fs.existsSync(local) && fs.statSync(local).isDirectory() && fs.existsSync(path.join(local, 'index.php'))) {
    if (!p.endsWith('/')) { res.writeHead(301, { Location: p + '/' + u.search }); return res.end(); }
    script = p + 'index.php';
  } else {
    const m = p.match(/^\/([a-z0-9-]+)\/?$/);
    if (m) { script = '/installateur.php'; query = '?slug=' + m[1] + (u.search ? '&' + u.search.slice(1) : ''); }
    else script = '/404.php';
  }
  const headers = {};
  for (const [k, v] of Object.entries(req.headers)) headers[k] = Array.isArray(v) ? v.join(', ') : v;
  const r = await php.run({
    scriptPath: '/site/www' + script,
    relativeUri: script + query,
    method: req.method,
    headers,
    body: body.length ? new Uint8Array(body) : undefined,
    $_SERVER: { DOCUMENT_ROOT: '/site/www', REQUEST_URI: req.url, SCRIPT_NAME: script, PHP_SELF: script, HTTP_HOST: 'localhost:' + PORT, SERVER_NAME: 'localhost', SERVER_PORT: String(PORT), REMOTE_ADDR: '127.0.0.1' },
  });
  const out = {};
  for (const [k, v] of Object.entries(r.headers || {})) out[k] = v;
  res.writeHead(r.httpStatusCode || 200, out);
  res.end(Buffer.from(r.bytes));
  if (r.errors) console.error('PHP stderr:', r.errors);
  console.log(req.method, req.url, '->', script, r.httpStatusCode);
}
