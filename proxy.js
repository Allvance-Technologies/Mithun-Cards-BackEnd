import http from 'http';
import httpProxy from 'http-proxy';

// Create a proxy server with custom application logic
const proxy = httpProxy.createProxyServer({
    proxyTimeout: 10000,
    timeout: 10000,
});

// Create your server that makes an operation that waits a while
const server = http.createServer(function (req, res) {
    console.log(`[${new Date().toISOString()}] Incoming request: ${req.method} ${req.url}`);

    // CORS headers to allow mobile app access
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');
    res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type,Authorization,Accept');

    if (req.method === 'OPTIONS') {
        res.writeHead(200);
        res.end();
        return;
    }

    // Proxy to the Laravel backend running on localhost:8000
    proxy.web(req, res, { target: 'http://127.0.0.1:8000' }, function (e) {
        console.error(`[${new Date().toISOString()}] Proxy Error:`, e);
        if (!res.headersSent) {
            res.writeHead(502, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({ error: "Proxy Error: Backend not reachable", details: e.message }));
        }
    });
});

proxy.on('error', function (err, req, res) {
    console.error('Proxy connection error:', err);
    if (res && !res.headersSent) {
        res.writeHead(500, {
            'Content-Type': 'text/plain'
        });
        res.end('Something went wrong. And we are reporting a custom error message.');
    }
});

console.log("🚀 Proxy Server running on http://0.0.0.0:8085 -> Forwarding to Laravel (8000)");
server.listen(8085, '0.0.0.0');
