@extends('layouts.app')

{{-- Custom styles for a beautiful scrollbar in the modal --}}
@push('styles')
<style>
    /* For Webkit browsers (Chrome, Safari) */
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9; /* gray-100 */
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #94a3b8; /* gray-400 */
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #64748b; /* gray-500 */
    }
</style>
@endpush


@section('content')

{{-- Alpine.js component jo poore page ko manage karega --}}
<div x-data="blogPage()">

    {{-- 1. Blog Hero Section --}}
    <div class="bg-gray-800">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight">The Fitub Blog</h1>
            <p class="mt-4 text-lg text-gray-300 max-w-2xl mx-auto">
                Health tips, workout plans, nutrition guides, and fitness inspiration.
            </p>
        </div>
    </div>

    {{-- Main Content Area --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16">
        {{-- ... (Yeh section pichle code jaisa hi rahega, koi change nahi) ... --}}
        {{-- Featured Post --}}
        @if($featuredPost)
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Featured Article</h2>
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden grid grid-cols-1 lg:grid-cols-5 gap-0 group">
                <div class="lg:col-span-3">
                    <img class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" src="{{ $featuredPost['image'] }}" alt="{{ $featuredPost['title'] }}">
                </div>
                <div class="lg:col-span-2 p-8 md:p-12 flex flex-col justify-center">
                    <p class="font-semibold text-indigo-600 uppercase text-sm tracking-wider">{{ $featuredPost['category'] }}</p>
                    <h3 class="mt-2 text-2xl font-bold text-gray-900 leading-tight">{{ $featuredPost['title'] }}</h3>
                    <p class="mt-4 text-gray-600">{{ $featuredPost['excerpt'] }}</p>
                    <div class="mt-6">
                        <button @click.prevent="openModal({{ json_encode($featuredPost) }})" class="font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                            Read Full Story →
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
        {{-- Main Grid (Posts + Sidebar) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Latest Articles</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @forelse ($regularPosts as $post)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col transform hover:-translate-y-2 transition-transform duration-300 border border-gray-200/80">
                            <img class="h-56 w-full object-cover" src="{{ $post['image'] }}" alt="{{ $post['title'] }}">
                            <div class="p-6 flex-grow flex flex-col">
                                <p class="font-semibold text-indigo-600 uppercase text-xs tracking-wider">{{ $post['category'] }}</p>
                                <h3 class="mt-2 text-lg font-bold text-gray-900 flex-grow">{{ $post['title'] }}</h3>
                                <div class="mt-4">
                                    <button @click.prevent="openModal({{ json_encode($post) }})" class="font-semibold text-indigo-600 hover:text-indigo-800 text-sm transition-colors">
                                        Read More →
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-600 md:col-span-2">No more articles found.</p>
                    @endforelse
                </div>
            </div>
            <aside class="lg:col-span-1">
                <div class="sticky top-28 space-y-8">
                    {{-- Categories Widget --}}
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200/80">
                        <h4 class="font-bold text-lg text-gray-800 mb-4">Categories</h4>
                        <ul class="space-y-2">
                            <li><a href="#" class="flex justify-between items-center text-gray-600 hover:text-indigo-600"><span class="font-medium">Nutrition</span> <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-2 py-1 rounded-full">2</span></a></li>
                            <li><a href="#" class="flex justify-between items-center text-gray-600 hover:text-indigo-600"><span class="font-medium">Workout</span> <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-2 py-1 rounded-full">1</span></a></li>
                            <li><a href="#" class="flex justify-between items-center text-gray-600 hover:text-indigo-600"><span class="font-medium">Wellness</span> <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-2 py-1 rounded-full">1</span></a></li>
                        </ul>
                    </div>
                    {{-- Recent Posts Widget --}}
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200/80">
                        <h4 class="font-bold text-lg text-gray-800 mb-4">Recent Posts</h4>
                        <ul class="space-y-4">
                            @foreach(collect($allPosts)->take(3) as $recentPost)
                            <li>
                                <button @click.prevent="openModal({{ json_encode($recentPost) }})" class="group flex items-center space-x-4 text-left">
                                    <img src="{{ $recentPost['image'] }}" alt="" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800 group-hover:text-indigo-600 transition-colors">{{ $recentPost['title'] }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $recentPost['date'] }}</p>
                                    </div>
                                </button>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </aside>
        </div>
    </div>


    {{-- ========================================================== --}}
    {{-- ========= EYE-CATCHING MODAL (POPUP) HTML ================= --}}
    {{-- ========================================================== --}}
    <div x-show="isModalOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center p-4 z-[999]"
         style="display: none;">

        {{-- Main modal panel with split layout --}}
        <div @click.away="isModalOpen = false"
             x-show="isModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="bg-white rounded-2xl shadow-2xl max-w-6xl w-full h-[90vh] grid grid-cols-1 lg:grid-cols-2 overflow-hidden">

            {{-- Left Side: Image (Visible on Desktop only) --}}
            <div class="hidden lg:block relative">
                <img class="absolute h-full w-full object-cover" :src="modalPost.image" :alt="modalPost.title">
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
            </div>

            {{-- Right Side: Content (Scrollable) --}}
            <div class="relative flex flex-col h-full custom-scrollbar overflow-y-auto">
                {{-- Close Button --}}
                <button @click="isModalOpen = false" class="absolute top-4 right-4 text-gray-500 bg-white/50 backdrop-blur-sm rounded-full p-2 hover:bg-white hover:text-gray-800 transition z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                {{-- Mobile Image (Visible on Mobile only) --}}
                <div class="lg:hidden">
                    <img class="w-full h-64 object-cover" :src="modalPost.image" :alt="modalPost.title">
                </div>

                {{-- Content Body --}}
                <div class="p-8 sm:p-12 flex-grow">
                    <span class="inline-block bg-indigo-100 text-indigo-800 text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wider" x-text="modalPost.category"></span>

                    <h2 class="mt-4 text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight" x-text="modalPost.title"></h2>

                    <div class="mt-6 flex items-center space-x-4">
                        <img class="h-12 w-12 rounded-full object-cover" :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(modalPost.author) + '&background=random'" :alt="modalPost.author">
                        <div>
                            <p class="font-semibold text-gray-900" x-text="modalPost.author"></p>
                            <p class="text-sm text-gray-500" x-text="modalPost.date + ' · ' + modalPost.read_time"></p>
                        </div>
                    </div>

                    <hr class="my-8">

                    {{-- 'prose' class from Tailwind Typography plugin for beautiful article styling --}}
                    <div class="prose lg:prose-lg max-w-none" x-html="modalPost.content">
                        {{-- Yahan content ayega --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Alpine.js ka logic (Koi change nahi) --}}
@push('scripts')
<script>
    function blogPage() {
        return {
            isModalOpen: false,
            modalPost: {},

            openModal(post) {
                this.modalPost = post;
                this.isModalOpen = true;
                document.body.style.overflow = 'hidden';
            },

            init() {
                this.$watch('isModalOpen', (value) => {
                    if (!value) {
                        document.body.style.overflow = 'auto';
                    }
                })
            }
        }
    }
</script>
@endpush

@endsection
