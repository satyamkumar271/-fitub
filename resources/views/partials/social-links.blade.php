<div>
    <h4 class="font-semibold text-gray-800 mb-2">Social Media Profiles</h4>
    @if(!empty(auth()->user()->social_links) && is_array(auth()->user()->social_links))
        <div class="flex flex-wrap gap-4 bg-white p-4 rounded-md border">
            @foreach(auth()->user()->social_links as $link)
                <a href="{{ $link['url'] ?? '#' }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 rounded-full hover:bg-gray-200 transition">
                    {{-- Aap yahan social media ke icons bhi laga sakte hain --}}
                    <span class="font-medium capitalize text-gray-700">{{ $link['platform'] ?? 'Link' }}</span>
                </a>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 bg-white p-4 rounded-md border">No social media links provided.</p>
    @endif
</div>
