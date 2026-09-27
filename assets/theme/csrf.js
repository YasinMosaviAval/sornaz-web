/* Attach the session CSRF token only to same-origin mutation requests. */
(() => {
    const originalFetch = window.fetch;
    if (!originalFetch) return;
    window.fetch = function (input, options = {}) {
        const request = typeof Request !== 'undefined' && input instanceof Request;
        const url = new URL(request ? input.url : input, window.location.href);
        const method = String(options.method || (request ? input.method : 'GET')).toUpperCase();
        if (url.origin === window.location.origin && ['POST', 'PUT', 'PATCH', 'DELETE'].includes(method)) {
            const token = window.adminCsrfToken || window.siteCsrfToken || document.querySelector('meta[name="csrf-token"]')?.content;
            if (token) {
                const headers = new Headers(options.headers || (request ? input.headers : undefined));
                if (!headers.has('X-CSRF-TOKEN')) headers.set('X-CSRF-TOKEN', token);
                options = { ...options, headers };
            }
        }
        return originalFetch.call(this, input, options);
    };
})();
