@if($page_build)
    @foreach($page_build as $row)
        @if($row['acf_fc_layout'] == 'homepage_text')
            @include('blocks.homepage-text', ['homepage_text' => $row['homepage_text']])
        @endif

        @if($row['acf_fc_layout'] == 'big_slider_section')
            @include('blocks.big-slider', [
                'big_slider' => $row['big_slider'],
                'boxed_slider' => $row['boxed_slider'] // Add this line
            ])
        @endif

        @if($row['acf_fc_layout'] == 'product_list_section')
            @include('blocks.product-list', ['block_data' => $row])
        @endif

        @if($row['acf_fc_layout'] == 'category_list_section')
            @include('blocks.category-list', ['category_list_builder' => $row['category_list_builder']])
        @endif

        @if($row['acf_fc_layout'] == 'half_section')
            @include('blocks.half-section', ['block_data' => $row])
        @endif

        @if($row['acf_fc_layout'] == 'seo_section')
            @include('blocks.seo-section', ['block_data' => $row])
        @endif

        @if($row['acf_fc_layout'] == 'landing_hero_section')
            @include('blocks.landing-hero', ['block_data' => $row])
        @endif

        @if($row['acf_fc_layout'] == 'landing_introduction_section')
            @include('blocks.landing-intro', ['block_data' => $row])
        @endif

        @if($row['acf_fc_layout'] == 'landing_half_section')
            @include('blocks.landing-half-section', ['block_data' => $row])
        @endif

        @if($row['acf_fc_layout'] == 'landing_spacer')
            @include('blocks.landing-spacer', ['block_data' => $row])
        @endif

        @if($row['acf_fc_layout'] == 'landing_gallery_section')
            @include('blocks.landing-gallery', ['block_data' => $row])
        @endif

        @if($row['acf_fc_layout'] == 'landing_how_to_section')
            @include('blocks.landing-how-to', ['block_data' => $row])
        @endif

        @if($row['acf_fc_layout'] == 'landing_product_size')
            @include('blocks.landing-product-size', ['block_data' => $row])
        @endif

        @if($row['acf_fc_layout'] == 'landing_in_the_box_section')
            @include('blocks.landing-in-the-box', ['block_data' => $row])
        @endif



    @endforeach
@endif
