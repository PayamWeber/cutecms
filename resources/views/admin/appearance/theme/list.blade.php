{!! widget_before( [
    'title' => lang( 'Themes' ),
    'color' => 'white',
] ) !!}
<div class="masonry-grid my-gallery mx-1" itemscope itemtype="http://schema.org/ImageGallery">
    <!-- width of .grid-sizer used for columnWidth -->
    <div class="grid-sizer"></div>
    @if ( $themes )
        @foreach( $themes as $theme )
			<?php
			$active_theme = $GLOBALS[ 'theme' ];
			?>
            <div class="grid-item {{ $active_theme['name'] == $theme['name'] ? 'active' : '' }}">
                <figure class="card border-grey border-lighten-2" itemprop="associatedMedia" itemscope
                        itemtype="http://schema.org/ImageObject">
                    <a href="" itemprop="contentUrl"
                       data-size="600x441">
                        <img class="gallery-thumbnail card-img-top" src="{{ route( 'admin.appearance.theme.show_screen_shot', ['name' => $theme['name'] ] ) }}"
                             itemprop="thumbnail" alt="Image description"/>
                    </a>
                    <div class="card-body px-0">
                        <h4 class="card-title">{{ $theme['info']['theme_name'] }}</h4>
                        <p class="card-text">{{ $theme['info']['description'] }}</p>
                        <div class="btn-group" role="group" aria-label="Basic example">
                            @if ( is_user_can( 'themes_activate' ) )
                                <form action="{{ route('admin.appearance.theme.set_active') }}" method="post">
                                    {!! csrf_field() !!}
                                    <input type="hidden" name="name" value="{{ $theme['name'] }}">
                                    <button type="submit"
                                            class="btn btn-primary"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            data-original-title="{{ lang( 'Activate Theme' ) }}">
                                        <i class="la la-check"></i> {{ lang('Activate') }}
                                    </button>
                                </form>
                            @endif
                            @if ( is_user_can( 'themes_publish' ) )
                                <form action="{{ route('admin.appearance.theme.publish') }}" method="post">
                                    {!! csrf_field() !!}
                                    {!! method_field( 'PATCH' ) !!}
                                    <input type="hidden" name="name" value="{{ $theme['name'] }}">
                                    <button type="submit"
                                            class="btn btn-success"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            data-original-title="{{ lang( 'Publish Theme Styles' ) }}">
                                        <i class="la la-rocket"></i> {{ lang('Publish') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </figure>
            </div>
        @endforeach
    @endif
</div>
{!! widget_after() !!}
