@php
    use Darryldecode\Cart\Facades\CartFacade as Cart;
@endphp
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            {{-- Logo --}}
            <a class="navbar-brand" href="{{ route('user.main') }}">
                <img src="{{ asset('images/logo.png') }}" height="50" alt="Logo">
            </a>

            {{-- Hamburger button --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- Navbar items --}}
            <div class="collapse navbar-collapse" id="navbarNav">
                {{-- Left menu items --}}
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('user.main') ? 'active' : '' }}" href="{{ route('user.main') }}">
                            <i class="fas fa-home"></i> หน้าแรก
                        </a>
                    </li>

                    {{-- Shop Menu --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ Request::routeIs('user.shop.*') ? 'active' : '' }}" 
                           href="#" id="shopDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-store"></i> ร้านค้า
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="shopDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('user.shop.index') }}">
                                    <i class="fas fa-coffee"></i> เมนูทั้งหมด
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('user.orders.index') }}">
                                    <i class="fas fa-shopping-bag fa-fw me-2"></i>ประวัติการสั่งซื้อ
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('user.shop.cart') }}">
                                    <i class="fas fa-shopping-cart"></i> ตะกร้าสินค้า
                                    @if(Cart::getTotalQuantity() > 0)
                                        <span class="badge bg-danger rounded-pill ms-2">
                                            {{ Cart::getTotalQuantity() }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Activities Menu --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ Request::routeIs('user.activities.*') ? 'active' : '' }}" 
                           href="#" id="activitiesDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-calendar-alt"></i> กิจกรรม
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="activitiesDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('user.promotions.index') }}">
                                    <i class="fas fa-list"></i> กิจกรรมทั้งหมด
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('user.promotion-bookings.index') }}">
                                    <i class="fas fa-history"></i> ประวัติการจอง
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Gallery Menu --}}
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('user.gallery.index') ? 'active' : '' }}" href="{{ route('user.gallery.index') }}">
                            <i class="fas fa-images"></i> แกลเลอรี
                        </a>
                    </li>
                </ul>

                {{-- Right menu items --}}
                <ul class="navbar-nav ms-auto align-items-center">
                    {{-- Cart Icon --}}
                    <li class="nav-item me-2">
                        <a class="nav-link position-relative" href="{{ route('user.shop.cart') }}">
                            <i class="fas fa-shopping-cart fa-lg"></i>
                            @if(Cart::getTotalQuantity() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ Cart::getTotalQuantity() }}
                                </span>
                            @endif
                        </a>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.login') }}">
                                <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.register') }}">
                                <i class="fas fa-user-plus"></i> สมัครสมาชิก
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" class="rounded-circle me-2" 
                                         width="32" height="32" alt="{{ Auth::user()->name }}">
                                @else
                                    <i class="fas fa-user-circle fa-lg me-2"></i>
                                @endif
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.dashboard') }}">
                                        <i class="fas fa-tachometer-alt fa-fw me-2"></i>แดชบอร์ด
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.profile.index') }}">
                                        <i class="fas fa-user fa-fw me-2"></i>โปรไฟล์
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.profile.social') }}">
                                        <i class="fas fa-link fa-fw me-2"></i>เชื่อมต่อบัญชี
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('user.logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt fa-fw me-2"></i>ออกจากระบบ
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
</header>