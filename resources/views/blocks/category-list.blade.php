@php
    $no_thumbnail_url = get_template_directory_uri() . '/resources/images/no-image.svg'; // Path to your default no-thumbnail image
    $category_count = count($category_list_builder);
@endphp

@if($category_list_builder)
    @if($category_count <= 3)

        <div class="container">
          <section class="flex gap-6 flex-col lg:flex-row items-start justify-between">
            @foreach($category_list_builder as $category_id)
                @php
                    $category = get_term($category_id, 'product_cat');
                    $category_link = get_term_link($category);
                    $thumbnail_id = get_term_meta($category_id, 'thumbnail_id', true);
                    $image_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : $no_thumbnail_url;
                @endphp
                <div class="relative w-full lg:w-1/3">
                    <a class="group" href="{{ $category_link }}">
                        <h3 class="bg-white text-black group-hover:bg-main group-hover:text-white px-6 py-2  absolute left-0 bottom-6 text-20 font-semibold transition-all duration-200">{{ $category->name }}</h3>

                        @if($image_url)
                            <img class="w-full block max-w-full" src="{{ $image_url }}" alt="{{ $category->name }}">
                        @endif

                    </a>
                </div>
                @php wp_reset_postdata(); @endphp
            @endforeach
          </section>
        </div>

    @else
        <section class="category-list-builder swiper">
            <div class="swiper-wrapper" data-slider="true">
                @foreach($category_list_builder as $category_id)
                    @php
                        $category = get_term($category_id, 'product_cat');
                        $category_link = get_term_link($category);
                        $thumbnail_id = get_term_meta($category_id, 'thumbnail_id', true);
                        $image_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : $no_thumbnail_url;
                    @endphp
                    <div class="swiper-slide">
                        <a href="{{ $category_link }}">
                            @if($image_url)
                                <img src="{{ $image_url }}" alt="{{ $category->name }}">
                            @endif
                            <h3>{{ $category->name }}</h3>
                        </a>
                    </div>
                    @php wp_reset_postdata(); @endphp
                @endforeach
            </div>
            <div class="swiper-button-prev category-list-prev"></div>
            <div class="swiper-button-next category-list-next"></div>
        </section>
    @endif
@endif
