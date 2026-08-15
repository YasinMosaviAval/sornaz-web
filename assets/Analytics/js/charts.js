(function () {
    'use strict';

    const instances = new Map();
    const observers = new Map();

    function resolveElement(target) {
        return typeof target === 'string' ? document.querySelector(target) : target;
    }

    function defaults() {
        const dark = document.documentElement.dataset.mode === 'dark';
        const english = document.documentElement.lang === 'en';
        return {
            backgroundColor: 'transparent',
            textStyle: {
                fontFamily: getComputedStyle(document.body).fontFamily,
                color: dark ? '#e5e7eb' : '#374151'
            },
            aria: { enabled: true },
            animationDuration: 500,
            animationEasing: 'cubicOut',
            tooltip: { textStyle: { align: english ? 'left' : 'right' } }
        };
    }

    function merge(base, extra) {
        const result = { ...base, ...extra };
        ['textStyle', 'aria', 'tooltip'].forEach(key => {
            if (base[key] || extra[key]) result[key] = { ...(base[key] || {}), ...(extra[key] || {}) };
        });
        return result;
    }

    function dispose(target) {
        const element = resolveElement(target);
        if (!element) return;
        observers.get(element)?.disconnect();
        observers.delete(element);
        const chart = instances.get(element) || window.echarts?.getInstanceByDom(element);
        if (chart && !chart.isDisposed()) chart.dispose();
        instances.delete(element);
    }

    window.SornazCharts = {
        version: () => window.echarts?.version || null,
        create(target, option, settings = {}) {
            const element = resolveElement(target);
            if (!element) throw new Error('محل نمایش نمودار پیدا نشد.');
            if (!window.echarts) throw new Error('کتابخانه نمودار بارگذاری نشده است.');
            dispose(element);
            const chart = window.echarts.init(element, settings.theme || null, {
                renderer: settings.renderer || 'canvas',
                useDirtyRect: true
            });
            chart.setOption(merge(defaults(), option || {}), { notMerge: true });
            const observer = new ResizeObserver(() => chart.isDisposed() || chart.resize());
            observer.observe(element);
            instances.set(element, chart);
            observers.set(element, observer);
            return chart;
        },
        update(target, option, replace = false) {
            const element = resolveElement(target);
            const chart = element && (instances.get(element) || window.echarts?.getInstanceByDom(element));
            if (!chart) throw new Error('نمودار موردنظر ایجاد نشده است.');
            chart.setOption(option || {}, { notMerge: Boolean(replace) });
            return chart;
        },
        get(target) {
            const element = resolveElement(target);
            return element ? instances.get(element) || window.echarts?.getInstanceByDom(element) || null : null;
        },
        dispose,
        disposeAll() {
            [...instances.keys()].forEach(dispose);
        },
        exportPng(target, pixelRatio = 2) {
            const chart = this.get(target);
            if (!chart) throw new Error('نمودار موردنظر ایجاد نشده است.');
            return chart.getDataURL({ type: 'png', pixelRatio, backgroundColor: '#ffffff' });
        }
    };

    window.addEventListener('beforeunload', () => window.SornazCharts.disposeAll());
})();
