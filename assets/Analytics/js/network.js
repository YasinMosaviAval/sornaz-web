(function () {
    'use strict';

    if (window.__sornazNetworkFetchInstalled || typeof window.fetch !== 'function') return;
    window.__sornazNetworkFetchInstalled = true;

    const nativeFetch = window.fetch.bind(window);
    const retryStatuses = new Set([502, 503, 504]);
    const wait = milliseconds => new Promise(resolve => setTimeout(resolve, milliseconds));
    const queue = [];
    const maxConcurrentRequests = 6;
    let activeRequests = 0;

    function runQueued(input, options) {
        return new Promise((resolve, reject) => {
            queue.push({ input, options, resolve, reject });
            drainQueue();
        });
    }

    function drainQueue() {
        while (activeRequests < maxConcurrentRequests && queue.length) {
            const task = queue.shift();
            activeRequests += 1;
            nativeFetch(task.input, task.options)
                .then(task.resolve, task.reject)
                .finally(() => {
                    activeRequests -= 1;
                    drainQueue();
                });
        }
    }

    function methodOf(input, options) {
        return String(options?.method || input?.method || 'GET').toUpperCase();
    }

    function isTransientNetworkError(error) {
        if (!error || error.name === 'AbortError') return false;
        return error instanceof TypeError || /networkerror|failed to fetch|load failed|network request failed/i.test(String(error.message || ''));
    }

    window.fetch = async function resilientFetch(input, options) {
        const method = methodOf(input, options);
        const retryable = method === 'GET' || method === 'HEAD';
        const delays = retryable ? [250, 750] : [];

        for (let attempt = 0; ; attempt += 1) {
            try {
                const response = await runQueued(input, options);
                if (retryStatuses.has(response.status) && attempt < delays.length) {
                    await wait(delays[attempt]);
                    continue;
                }
                return response;
            } catch (error) {
                if (!isTransientNetworkError(error)) throw error;
                if (attempt < delays.length && document.visibilityState !== 'hidden') {
                    await wait(delays[attempt]);
                    continue;
                }
                throw new Error('ارتباط با سرور موقتاً برقرار نشد. لطفاً چند لحظه دیگر دوباره تلاش کنید.');
            }
        }
    };
})();
