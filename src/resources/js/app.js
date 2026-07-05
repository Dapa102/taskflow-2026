import './bootstrap';

import { Chart, DoughnutController, BarController, ArcElement, BarElement, CategoryScale, LinearScale, Tooltip, Legend, Filler } from 'chart.js';

Chart.register(DoughnutController, BarController, ArcElement, BarElement, CategoryScale, LinearScale, Tooltip, Legend, Filler);

const COLORS = {
    blue: ['#3b82f6', '#60a5fa', '#93c5fd'],
    indigo: ['#6366f1', '#818cf8', '#a5b4fc'],
    yellow: ['#eab308', '#facc15', '#fde047'],
    orange: ['#f97316', '#fb923c', '#fdba74'],
    purple: ['#a855f7', '#c084fc', '#d8b4fe'],
    green: ['#22c55e', '#4ade80', '#86efac'],
    red: ['#ef4444', '#f87171', '#fca5a5'],
};

function getGradient(ctx, chartArea, colorTop, colorBottom) {
    if (!chartArea) return colorTop;
    const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
    gradient.addColorStop(0, colorTop);
    gradient.addColorStop(1, colorBottom);
    return gradient;
}

function hexToRgba(hex, alpha) {
    const c = hex.replace('#', '');
    const r = parseInt(c.slice(0,2), 16);
    const g = parseInt(c.slice(2,4), 16);
    const b = parseInt(c.slice(4,6), 16);
    return `rgba(${r},${g},${b},${alpha})`;
}

function initCharts() {
    initDonutCharts();
    initBarCharts();
}

function initDonutCharts() {
    document.querySelectorAll('canvas[data-donut]').forEach(el => {
        if (el.__chartInstance) return;
        const raw = JSON.parse(el.dataset.donut);
        const hasData = raw.some(d => d.count > 0);
        const total = raw.reduce((s, d) => s + d.count, 0);

        const data = raw.map(item => ({
            label: item.label,
            count: item.count,
            bg: item.bg,
        }));

        el.__chartLabels = data.map(d => d.label);
        el.__chartInstance = new Chart(el, {
            type: 'doughnut',
            data: {
                labels: data.map(d => d.label),
                datasets: [{
                    data: data.map(d => d.count),
                    backgroundColor: data.map(d => hexToRgba(d.bg, 0.85)),
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverBorderColor: (ctx) => hexToRgba(data[ctx.dataIndex].bg, 1),
                    hoverBorderWidth: 4,
                    hoverOffset: 12,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                animation: {
                    animateRotate: true,
                    duration: 1200,
                    easing: 'easeOutQuart',
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        boxPadding: 4,
                        callbacks: {
                            label: (ctx) => {
                                const val = ctx.parsed;
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return ` ${ctx.label}: ${val} tugas (${pct}%)`;
                            },
                        },
                    },
                },
                onClick: (e, elements) => {
                    if (elements.length > 0) {
                        const idx = elements[0].index;
                        const labels = el.__chartLabels;
                        if (labels && labels[idx] && typeof Livewire !== 'undefined') {
                            Livewire.dispatch('showDetail', { label: labels[idx] });
                        }
                    }
                },
            },
            plugins: [{
                id: 'centerText',
                beforeDraw: function(chart) {
                    const { width, height, ctx } = chart;
                    ctx.save();
                    const centerX = width / 2;
                    const centerY = height / 2 - 8;

                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';

                    ctx.font = '700 24px Inter, sans-serif';
                    ctx.fillStyle = '#1e293b';
                    ctx.fillText(total, centerX, centerY - 10);

                    ctx.font = '400 11px Inter, sans-serif';
                    ctx.fillStyle = '#94a3b8';
                    ctx.fillText('Total Tugas', centerX, centerY + 16);

                    ctx.restore();
                }
            }],
        });

        el.style.cursor = 'pointer';
    });
}

function initBarCharts() {
    document.querySelectorAll('canvas[data-bar]').forEach(el => {
        if (el.__chartInstance) return;
        const raw = JSON.parse(el.dataset.bar);
        const data = raw.map(item => ({
            label: item.label,
            count: item.count,
            bg: item.bg,
        }));

        const maxVal = Math.max(...data.map(d => d.count), 1);

        el.__chartInstance = new Chart(el, {
            type: 'bar',
            data: {
                labels: data.map(d => d.label),
                datasets: [{
                    label: 'Selesai',
                    data: data.map(d => d.count),
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const { ctx, chartArea } = chart;
                        if (!chartArea) return data[context.dataIndex]?.bg || '#6366f1';
                        return getGradient(
                            ctx, chartArea,
                            hexToRgba(data[context.dataIndex]?.bg || '#6366f1', 0.9),
                            hexToRgba(data[context.dataIndex]?.bg || '#6366f1', 0.3)
                        );
                    },
                    borderRadius: 8,
                    borderSkipped: false,
                    hoverBackgroundColor: data.map(d => hexToRgba(d.bg, 0.85)),
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart',
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            title: (items) => `Hari ${items[0].label}`,
                            label: (ctx) => ` ${ctx.parsed.y} tugas selesai`,
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: Math.max(maxVal + 1, 3),
                        ticks: {
                            stepSize: 1,
                            font: { size: 11, family: 'Inter, sans-serif' },
                            color: '#94a3b8',
                        },
                        grid: {
                            display: true,
                            color: '#f1f5f9',
                            drawBorder: false,
                        },
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11, family: 'Inter, sans-serif', weight: '500' },
                            color: '#64748b',
                        },
                    },
                },
                onClick: (e, elements) => {
                    if (elements.length > 0) {
                        const idx = elements[0].index;
                        const label = data[idx]?.label || '';
                        const body = document.querySelector('[data-bar-detail]');
                        if (body) {
                            body.scrollIntoView({ behavior: 'smooth' });
                        }
                    }
                },
            },
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(initCharts, 100);
    (function waitLivewire() {
        if (typeof Livewire === 'undefined') return setTimeout(waitLivewire, 50);
        Livewire.hook('message.processed', () => setTimeout(initCharts, 50));
    })();
});

