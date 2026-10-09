@section('title', 'Payment gateways')

<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Page header -->
        <div class="mb-8">

            <!-- Title -->
            <h1 class="font-display text-4xl font-bold uppercase leading-none tracking-tight text-ink-950 sm:text-5xl">Payment gateways</h1>
            <p class="mt-2 text-ink-500">Keys for online payment. Secret keys are never shown again after saving.</p>

        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl mb-8">
            <div class="flex flex-col md:flex-row md:-mr-px">
                
                <x-settings.payment-gateway-config-panel :gateways="$gateways" />
        
            </div>
        </div>
    </div>
</x-app-layout>