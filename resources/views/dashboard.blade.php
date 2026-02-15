<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OmniLocate Intelligence Dashboard</title>
    <!-- Tailwind (CDN for zero-config) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-900 text-gray-100 font-sans" x-data="dashboard()">

    <div class="min-h-screen p-6">
        <!-- Header -->
        <header class="flex justify-between items-center mb-8 border-b border-gray-700 pb-4">
            <div>
                <h1 class="text-3xl font-bold text-blue-400 tracking-tight">OmniLocate <span class="text-white font-light">Intelligence</span></h1>
                <p class="text-gray-400 text-sm mt-1">Real-time Global Threat & Traffic Monitoring</p>
            </div>
            <div class="flex items-center gap-4">
                <button @click="fetchStats()" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh Data
                </button>
            </div>
        </header>

        <!-- KPI Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Card 1 -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Total Requests</p>
                        <h2 class="text-4xl font-bold text-white mt-2" x-text="stats.total_requests || 0">0</h2>
                    </div>
                    <div class="p-2 bg-blue-900/30 rounded-lg text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Blocked Threats</p>
                        <h2 class="text-4xl font-bold text-red-500 mt-2" x-text="stats.blocked_threats || 0">0</h2>
                    </div>
                    <div class="p-2 bg-red-900/30 rounded-lg text-red-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Top Source</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-2xl" x-text="getFlag(stats.top_countries?.[0]?.country_code)"></span>
                            <h2 class="text-3xl font-bold text-white" x-text="stats.top_countries?.[0]?.country_code || 'N/A'">N/A</h2>
                        </div>
                    </div>
                    <div class="p-2 bg-purple-900/30 rounded-lg text-purple-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Risk Distribution -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
                <h3 class="text-lg font-semibold text-white mb-4">Risk Level Distribution</h3>
                <div class="relative h-64 w-full">
                    <canvas id="riskChart"></canvas>
                </div>
            </div>

            <!-- Top Countries -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
                <h3 class="text-lg font-semibold text-white mb-4">Traffic by Country</h3>
                <div class="relative h-64 w-full">
                    <canvas id="countryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Live Logs Table -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white">Recent Live Traffic</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-400">
                    <thead class="bg-gray-900 text-gray-200 uppercase font-medium">
                        <tr>
                            <th class="px-6 py-3">Time</th>
                            <th class="px-6 py-3">IP Address</th>
                            <th class="px-6 py-3">Details</th>
                            <th class="px-6 py-3">Risk</th>
                            <th class="px-6 py-3">Method</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <template x-for="log in stats.logs" :key="log.id">
                            <tr class="hover:bg-gray-750 transition">
                                <td class="px-6 py-4" x-text="new Date(log.created_at).toLocaleTimeString()"></td>
                                <td class="px-6 py-4 font-mono text-blue-300" x-text="log.ip"></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span x-text="getFlag(log.country_code)"></span>
                                        <span x-text="log.city || 'Unknown'"></span>
                                        <span class="text-xs text-gray-500" x-text="log.isp ? '('+log.isp+')' : ''"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded text-xs font-bold"
                                        :class="{
                                            'bg-green-900 text-green-300': log.risk_score < 30,
                                            'bg-yellow-900 text-yellow-300': log.risk_score >= 30 && log.risk_score < 70,
                                            'bg-red-900 text-red-300': log.risk_score >= 70
                                        }"
                                        x-text="log.risk_score + '%'">
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-gray-700 rounded text-xs text-gray-300" x-text="log.method + ' ' + log.url"></span>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="!stats.logs || stats.logs.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No traffic data recorded yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <footer class="mt-8 text-center text-gray-500 text-sm">
            Powered by <strong>OmniLocate</strong> · <a href="https://github.com/ermradulsharma/omniLocate" class="hover:text-blue-400">Documentation</a>
        </footer>
    </div>

    <script>
        function dashboard() {
            return {
                stats: {
                    total_requests: 0,
                    blocked_threats: 0,
                    top_countries: [],
                    logs: [],
                    risk_distribution: [0, 0, 0] // Low, Med, High
                },
                charts: {}, // Store chart instances

                init() {
                    this.fetchStats();
                    // Auto-refresh every 10 seconds
                    setInterval(() => this.fetchStats(), 10000);
                },

                fetchStats() {
                    fetch('/omni-locate/dashboard/stats', {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            this.stats = data;
                            this.updateCharts();
                        })
                        .catch(err => console.error("Stats fetch error:", err));
                },

                getFlag(countryCode) {
                    if (!countryCode) return '🏳️';
                    return countryCode.toUpperCase().replace(/./g, char => String.fromCodePoint(char.charCodeAt(0) + 127397));
                },

                updateCharts() {
                    this.renderRiskChart();
                    this.renderCountryChart();
                },

                renderRiskChart() {
                    const ctx = document.getElementById('riskChart').getContext('2d');
                    if (this.charts.risk) this.charts.risk.destroy();

                    this.charts.risk = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Low Risk (<30)', 'Medium Risk (30-70)', 'High Risk (>70)'],
                            datasets: [{
                                data: this.stats.risk_distribution,
                                backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: '#9CA3AF'
                                    }
                                }
                            }
                        }
                    });
                },

                renderCountryChart() {
                    const ctx = document.getElementById('countryChart').getContext('2d');
                    if (this.charts.country) this.charts.country.destroy();

                    const labels = this.stats.top_countries.map(c => c.country_code || 'Unknown');
                    const data = this.stats.top_countries.map(c => c.count);

                    this.charts.country = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Requests',
                                data: data,
                                backgroundColor: '#3B82F6',
                                borderRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: '#374151'
                                    },
                                    ticks: {
                                        color: '#9CA3AF'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: '#9CA3AF'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }
            }
        }
    </script>
</body>

</html>


