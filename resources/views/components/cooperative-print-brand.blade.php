@if($cooperativeSettings->logo_path)
    <img src="{{ route('cooperative.logo') }}?v={{ $cooperativeSettings->updated_at?->timestamp ?? time() }}" alt="Logo {{ $cooperativeSettings->name }}" class="mx-auto mb-2 h-14 max-w-28 object-contain">
@endif
