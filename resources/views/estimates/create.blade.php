@extends('layouts.app')

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Price Estimation</h2>

                <form id="estimationForm" class="space-y-6">
                    @csrf

                    <!-- Project Type -->
                    <div>
                        <label for="project_type" class="block text-sm font-medium text-gray-700">Project Type</label>
                        <select id="project_type" name="project_type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="">Select a project type</option>
                            <option value="canopy">Canopy</option>
                            <option value="fence">Fence</option>
                            <option value="gate">Gate</option>
                            <option value="railing">Railing</option>
                            <option value="stairs">Stairs</option>
                            <option value="truss">Truss</option>
                        </select>
                    </div>

                    <!-- Material Type -->
                    <div>
                        <label for="material_type" class="block text-sm font-medium text-gray-700">Material Type</label>
                        <select id="material_type" name="material_type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="">Select a material type</option>
                            <option value="stainless_steel">Stainless Steel</option>
                            <option value="mild_steel">Mild Steel</option>
                            <option value="galvanized_steel">Galvanized Steel</option>
                            <option value="aluminum">Aluminum</option>
                        </select>
                    </div>

                    <!-- Dimensions -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="length" class="block text-sm font-medium text-gray-700">Length (meters)</label>
                            <input type="number" id="length" name="dimensions[length]" step="0.1" min="0.1" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>

                        <div>
                            <label for="width" class="block text-sm font-medium text-gray-700">Width (meters)</label>
                            <input type="number" id="width" name="dimensions[width]" step="0.1" min="0.1" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>

                        <div>
                            <label for="thickness" class="block text-sm font-medium text-gray-700">Thickness (mm)</label>
                            <input type="number" id="thickness" name="dimensions[thickness]" step="0.1" min="0.1" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>
                    </div>

                    <!-- Additional Features -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Additional Features</label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="checkbox" id="custom_design" name="additional_features[custom_design]"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="custom_design" class="ml-2 text-sm text-gray-700">Custom Design</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="installation" name="additional_features[installation]"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="installation" class="ml-2 text-sm text-gray-700">Installation Service</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="finishing" name="additional_features[finishing]"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="finishing" class="ml-2 text-sm text-gray-700">Finishing Treatment</label>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                        <textarea id="notes" name="notes" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                            placeholder="Any specific requirements or details..."></textarea>
                    </div>

                    <!-- Instant Estimate Result -->
                    <div id="estimateResult" class="hidden p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900">Estimated Price</h3>
                        <p id="estimatedPrice" class="text-3xl font-bold text-blue-600 mt-2"></p>
                        <p class="text-sm text-gray-500 mt-2">
                            This is an estimated price. The final price may vary based on specific requirements and market conditions.
                        </p>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="hidden p-4 bg-red-50 text-red-700 rounded-lg"></div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-between">
                        <button type="button" onclick="getInstantEstimate()"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Get Instant Estimate
                        </button>

                        @auth
                            <button type="button" onclick="saveEstimate()"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Save Estimate
                            </button>
                        @else
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Login to Save Estimate
                            </a>
                        @endauth
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function getInstantEstimate() {
    const form = document.getElementById('estimationForm');
    const formData = new FormData(form);
    const errorDiv = document.getElementById('errorMessage');
    const resultDiv = document.getElementById('estimateResult');
    const priceElement = document.getElementById('estimatedPrice');

    // Hide previous results/errors
    errorDiv.classList.add('hidden');
    resultDiv.classList.add('hidden');

    fetch('{{ route("estimates.estimate") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            priceElement.textContent = data.formatted_price;
            resultDiv.classList.remove('hidden');
        } else {
            errorDiv.textContent = data.message;
            errorDiv.classList.remove('hidden');
        }
    })
    .catch(error => {
        errorDiv.textContent = 'An error occurred. Please try again later.';
        errorDiv.classList.remove('hidden');
    });
}

function saveEstimate() {
    const form = document.getElementById('estimationForm');
    const formData = new FormData(form);
    const errorDiv = document.getElementById('errorMessage');

    errorDiv.classList.add('hidden');

    fetch('{{ route("estimates.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("estimates.index") }}';
        } else {
            errorDiv.textContent = data.message;
            errorDiv.classList.remove('hidden');
        }
    })
    .catch(error => {
        errorDiv.textContent = 'An error occurred while saving the estimate. Please try again later.';
        errorDiv.classList.remove('hidden');
    });
}
</script>
@endpush
@endsection