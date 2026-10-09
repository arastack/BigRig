@section('title', 'Email server')

<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Page header -->
        <div class="mb-8">

            <!-- Title -->
            <h1 class="font-display text-4xl font-bold uppercase leading-none tracking-tight text-ink-950 sm:text-5xl">Email server</h1>
            <p class="mt-2 text-ink-500">The mail server used for application, payment and password emails.</p>

        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl mb-8">
            <div class="flex flex-col md:flex-row md:-mr-px">
                
                <x-settings.email-configuration-panel :emailConfig="$emailConfig" />
        
            </div>
        </div>
    </div>
</x-app-layout>