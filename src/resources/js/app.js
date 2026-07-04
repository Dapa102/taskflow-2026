import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import { Chart, DoughnutController, BarController, ArcElement, BarElement, CategoryScale, LinearScale, Tooltip, Legend } from 'chart.js';

Chart.register(DoughnutController, BarController, ArcElement, BarElement, CategoryScale, LinearScale, Tooltip, Legend);

Alpine.plugin(collapse);

function initCharts() {
    document.querySelectorAll('canvas[data-donut]').forEach(el => {
        if (el.__chartInstance) return;
        const raw = JSON.parse(el.dataset.donut);
        const data = raw.map(item => ({
            label: item.label,
            count: item.count,
            bg: item.bg,
        }));

        el.__chartInstance = new Chart(el, {
            type: 'doughnut',
            data: {
                labels: data.map(d => d.label),
                datasets: [{
                    data: data.map(d => d.count),
                    backgroundColor: data.map(d => d.bg),
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverBorderColor: '#1e1b4b',
                    hoverBorderWidth: 3,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',

                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 16,
                            usePointStyle: true,
                            font: { size: 12 },
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                                return ` ${ctx.label}: ${ctx.parsed} (${pct}%)`;
                            },
                        },
                    },
                },
            },
        });

        el.style.cursor = 'pointer';
    });

    document.querySelectorAll('canvas[data-bar]').forEach(el => {
        if (el.__chartInstance) return;
        const raw = JSON.parse(el.dataset.bar);
        const data = raw.map(item => ({
            label: item.label,
            count: item.count,
            bg: item.bg,
        }));

        el.__chartInstance = new Chart(el, {
            type: 'bar',
            data: {
                labels: data.map(d => d.label),
                datasets: [{
                    label: 'Selesai',
                    data: data.map(d => d.count),
                    backgroundColor: data.map(d => d.bg),
                    borderRadius: 6,
                    borderSkipped: false,
                    hoverBackgroundColor: data.map(d => {
                        const c = d.bg.replace('#', '');
                        const r = parseInt(c.slice(0,2), 16);
                        const g = parseInt(c.slice(2,4), 16);
                        const b = parseInt(c.slice(4,6), 16);
                        return `rgba(${r},${g},${b},0.8)`;
                    }),
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ` ${ctx.parsed.y} tugas`,
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { size: 11 },
                        },
                        grid: {
                            display: true,
                            color: '#f3f4f6',
                        },
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } },
                    },
                },
            },
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Tunggu Alpine + Livewire selesai init DOM
    setTimeout(initCharts, 100);
});

window.Alpine = Alpine;

Alpine.start();
