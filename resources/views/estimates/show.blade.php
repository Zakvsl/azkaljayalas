@extends('layouts.app')

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-gray-900">Price Estimate Details</h2>
                <a href="{{ route('estimates.index') }}" class="text-blue-600 hover:text-blue-900">
                    &larr; Back to Estimates
                </a>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Project Details -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Project Details</h3>
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Project Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $estimate->project_type)) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Material Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $estimate->material_type)) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dimensions</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    Length: {{ $estimate->dimensions['length'] }}m<br>
                                    Width: {{ $estimate->dimensions['width'] }}m<br>
                                    Thickness: {{ $estimate->dimensions['thickness'] }}mm
                                </dd>
                            </div>
                            @if(!empty($estimate->additional_features))
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Additional Features</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <ul class="list-disc list-inside">
                                        @foreach($estimate->additional_features as $feature)
                                            <li>{{ ucfirst(str_replace('_', ' ', $feature)) }}</li>
                                        @endforeach
                                    </ul>
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Estimate Details -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Estimate Details</h3>
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estimated Price</dt>
                                <dd class="mt-1 text-2xl font-bold text-gray-900">{{ $estimate->formatted_estimated_price }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($estimate->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($estimate->status === 'confirmed') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($estimate->status) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created On</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $estimate->created_at->format('F j, Y g:i A') }}</dd>
                            </div>
                            @if($estimate->notes)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $estimate->notes }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex justify-end space-x-4">
                    @if($estimate->status === 'pending')
                        <form action="{{ route('estimates.update', $estimate) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" name="status" value="cancelled" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancel Estimate
                            </button>
                            <button type="submit" name="status" value="confirmed" 
                                class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Confirm Estimate
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection