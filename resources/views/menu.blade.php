<!-- resources/views/menu.blade.php -->

<ul class="navbar-nav">
    @foreach($menus as $menu)
        @can($menu->permission)
            <li class="nav-item">
                <a class="nav-link {{ $menu->is_active ? 'active' : '' }}" href="{{ url($menu->url) }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons">{{ $menu->icon }}</i>
                    </div>
                    <span class="nav-link-text ms-1">{{ $menu->title }}</span>
                </a>
                @if($menu->children->count())
                    <ul>
                        @foreach($menu->children as $child)
                            @can($child->permission)
                                <li class="nav-item">
                                    <a class="nav-link {{ $child->is_active ? 'active' : '' }}" href="{{ url($child->url) }}">
                                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                            <i class="material-icons">{{ $child->icon }}</i>
                                        </div>
                                        <span class="nav-link-text ms-1">{{ $child->title }}</span>
                                    </a>
                                </li>
                            @endcan
                        @endforeach
                    </ul>
                @endif
            </li>
        @endcan
    @endforeach
</ul>
