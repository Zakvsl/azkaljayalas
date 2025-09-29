@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-semibold text-gray-900">ML Model Performance</h1>
            <button 
                onclick="retrainModel()"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                id="retrainButton"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Retrain Model
            </button>
        </div>

        <!-- Model Status Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-xl font-medium text-gray-900 mb-4">Model Status</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-500">Last Training</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900" id="lastTraining">Loading...</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Model Version</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900" id="modelVersion">Loading...</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Training Data Size</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900" id="dataSize">Loading...</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Performance Metrics -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-medium text-gray-900 mb-4">Performance Metrics</h2>
                <div class="space-y-4" id="performanceMetrics">
                    <div class="animate-pulse">
                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                    </div>
                </div>
            </div>

            <!-- Feature Importance -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-medium text-gray-900 mb-4">Feature Importance</h2>
                <div id="featureImportance" class="min-h-[300px]"></div>
            </div>
        </div>

        <!-- Training History -->
        <div class="mt-6 bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-medium text-gray-900 mb-4">Training History</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Version</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Training Date</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">R² Score</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RMSE</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="trainingHistory">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Training Status -->
        <div id="trainingStatus" class="fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-4 hidden">
            <div class="flex items-center text-blue-600">
                <svg class="w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Training in progress...</span>
            </div>
        </div>

        <!-- Error Message -->
        <div id="errorMessage" class="fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-4 hidden">
            <div class="bg-red-50 text-red-700 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium">Error</h3>
                        <div class="mt-2 text-sm" id="errorText"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let featureImportanceChart = null;

function updateModelStatus() {
    fetch('/api/ml/status')
        .then(response => response.json())
        .then(data => {
            document.getElementById('lastTraining').textContent = data.last_training;
            document.getElementById('modelVersion').textContent = data.version;
            document.getElementById('dataSize').textContent = data.data_size;
        })
        .catch(error => console.error('Error fetching model status:', error));
}

function updatePerformanceMetrics(metrics) {
    const container = document.getElementById('performanceMetrics');
    container.innerHTML = '';

    const sections = {
        'Training Set': metrics.train,
        'Test Set': metrics.test,
        'Cross Validation': metrics.cross_validation
    };

    for (const [title, data] of Object.entries(sections)) {
        const section = document.createElement('div');
        section.innerHTML = `
            <h3 class="text-sm font-medium text-gray-700 mb-2">${title}</h3>
            <div class="grid grid-cols-2 gap-4">
                ${Object.entries(data).map(([metric, value]) => `
                    <div>
                        <p class="text-xs text-gray-500">${metric.toUpperCase()}</p>
                        <p class="text-lg font-semibold">${typeof value === 'number' ? value.toFixed(4) : value}</p>
                    </div>
                `).join('')}
            </div>
        `;
        container.appendChild(section);
    }
}

function updateFeatureImportance(importance) {
    const ctx = document.getElementById('featureImportance');
    const data = {
        labels: Object.keys(importance),
        datasets: [{
            label: 'Feature Importance',
            data: Object.values(importance),
            backgroundColor: 'rgba(59, 130, 246, 0.5)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 1
        }]
    };

    if (featureImportanceChart) {
        featureImportanceChart.destroy();
    }

    featureImportanceChart = new Chart(ctx, {
        type: 'bar',
        data: data,
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });
}

function retrainModel() {
    const button = document.getElementById('retrainButton');
    const statusDiv = document.getElementById('trainingStatus');
    const errorDiv = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');

    // Reset UI
    button.disabled = true;
    statusDiv.classList.remove('hidden');
    errorDiv.classList.add('hidden');

    // Make API request
    fetch('/api/ml/retrain', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        statusDiv.classList.add('hidden');
        
        if (data.status === 'success') {
            updateModelStatus();
            updatePerformanceMetrics(data.metrics);
            updateFeatureImportance(data.metrics.feature_importance);
            loadTrainingHistory();
        } else {
            throw new Error(data.message || 'Unknown error occurred');
        }
    })
    .catch(error => {
        statusDiv.classList.add('hidden');
        errorDiv.classList.remove('hidden');
        errorText.textContent = error.message;
    })
    .finally(() => {
        button.disabled = false;
    });
}

function loadTrainingHistory() {
    fetch('/api/ml/history')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('trainingHistory');
            tbody.innerHTML = data.history.map(entry => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${entry.version}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${entry.created_at}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${entry.metrics.test.r2.toFixed(4)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${entry.metrics.test.rmse.toFixed(4)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <button onclick="rollbackModel('${entry.version}')" class="text-blue-600 hover:text-blue-900">Rollback</button>
                    </td>
                </tr>
            `).join('');
        })
        .catch(error => console.error('Error loading training history:', error));
}

function rollbackModel(version) {
    if (!confirm(`Are you sure you want to rollback to version ${version}?`)) {
        return;
    }

    fetch(`/api/ml/rollback/${version}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            updateModelStatus();
            updatePerformanceMetrics(data.metrics);
            updateFeatureImportance(data.metrics.feature_importance);
        } else {
            throw new Error(data.message || 'Unknown error occurred');
        }
    })
    .catch(error => {
        const errorDiv = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');
        errorDiv.classList.remove('hidden');
        errorText.textContent = error.message;
        setTimeout(() => errorDiv.classList.add('hidden'), 5000);
    });
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    updateModelStatus();
    loadTrainingHistory();
});
</script>
@endpush