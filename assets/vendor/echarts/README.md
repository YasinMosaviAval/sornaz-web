# Apache ECharts (local vendor)

- Version: `6.1.0`
- Full browser build: `echarts.min.js`
- License: Apache License 2.0 (`LICENSE`)
- Official project: https://echarts.apache.org/
- Source package: https://www.npmjs.com/package/echarts/v/6.1.0
- Downloaded distribution: https://cdn.jsdelivr.net/npm/echarts@6.1.0/dist/echarts.min.js
- SHA-256: `B66B25AEB4DF84E33199DC21694014D336D222CBD9DEB0E5A7C14BD6AA0D0FD0`

The complete build is stored locally and does not require a CDN at runtime. It includes line, bar, pie, scatter, effect scatter, radar, tree, treemap, sunburst, graph, gauge, funnel, parallel, Sankey, boxplot, candlestick, heatmap, pictorial bar, theme river, custom series and combined charts.

## Admin usage

```html
<div id="exampleChart" style="height: 360px"></div>
<script>
const chart = SornazCharts.create('#exampleChart', {
    tooltip: {},
    xAxis: { type: 'category', data: ['شنبه', 'یکشنبه', 'دوشنبه'] },
    yAxis: { type: 'value' },
    series: [{ type: 'bar', data: [12, 20, 15] }]
});
</script>
```

`SornazCharts` automatically handles responsive resizing, the project font, RTL/LTR tooltip alignment, dark/light text colors, disposal and PNG export.
