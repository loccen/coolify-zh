<div>
    <h2>{{ __('Destination') }}</h2>
    <div class="">{{ __('The destination server / network where your application will be deployed to.') }}</div>
    <div class="py-4 ">
        <p>{{ __('Server: :name', ['name' => data_get($destination, 'server.name')]) }}</p>
        <p>{{ __('Destination Network: :name', ['name' => $destination->network]) }}</p>
    </div>
</div>
