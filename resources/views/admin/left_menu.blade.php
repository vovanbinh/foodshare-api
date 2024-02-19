<?php
$page = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'],"/")+1);
?>
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('show_dashboard')}}" class="app-brand-link">
      <img alt="logo" style="max-width:50px;" src="/assets/img/icons/brands/logo.png">
      <span class="app-brand-text demo menu-text fw-bolder ms-2">FoodShare</span>
    </a>
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
  </div>
  <div class="menu-inner-shadow"></div>
  <ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item {{ request()->is('dashboard*') ? 'active' : '' }}">
      <a href="{{ route('show_dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div data-i18n="Analytics">Dashboard</div>
      </a>
    </li>
    <li class="menu-item {{ request()->is('manage/donated*') ? 'active' : '' }}">
      <a href="{{ route('show_manage_donated') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-gift"></i>
        <div data-i18n="Basic">Quản Lí Thực Phẩm</div>
      </a>
    </li>
    <li class="menu-item {{ request()->is('manage/transactions*') ? 'active' : '' }}">
      <a href="{{route('show_manage_transactions')}}" class="menu-link ">
        <i class="menu-icon tf-icons bx bx-history"></i>
        <div data-i18n="Misc">Danh Sách Giao Dịch</div>
      </a>
    </li>
    <li class="menu-item {{ request()->is('manage/users*') || request()->is('manage/user-role/*') ? 'active' : '' }}">
      <a href="{{route('show_manage_users')}}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-user"></i>
        <div data-i18n="Basic">Quản Lí Người Dùng</div>
      </a>
    </li>
    <!-- positions -->
    <li class="menu-item {{ request()->is('location*') ? 'active' : '' }}">
      <a href="" class="menu-link menu-toggle">
        <i class='menu-icon bx bx-location-plus'></i>
        <div data-i18n="Locations">Quản lí Điểm Phát Thực Phẩm</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->is('location/add-new-location') ? 'active' : '' }}">
          <a href="{{ route('show_add_new_location') }}" class="menu-link">
            <div data-i18n="Without menu">Thêm Mới Địa Điểm</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('location/list-locations') ? 'active' : '' }}">
          <a href="{{ route('show_list_locations') }}" class="menu-link">
            <div data-i18n="Without navbar">Danh Sách Địa Điểm</div>
          </a>
        </li>
      </ul>
    </li>
    <li class="menu-item {{ request()->is('error-notification*') || request()->is('error-notification/*') ? 'active' : '' }}">
      <a href="{{route('show_error_notification')}}" class="menu-link">
        <i class='menu-icon tf-icons bx bxs-bell-ring'></i>
        <div data-i18n="Basic">Quản lí báo cáo</div>
      </a>
    </li>
    <li class="menu-item {{ request()->is('charts*') || request()->is('charts/*') ? 'active' : '' }}">
      <a href="{{route('view_Charts')}}" class="menu-link">
        <i class='menu-icon tf-icons bx bx-line-chart'></i>
        <div data-i18n="Basic">Thống kê</div>
      </a>
    </li>
    <li class="menu-item {{ request()->is('message*') ? 'active' : '' }}">
      <a href="{{route('admin_logout')}}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-power-off"></i>
        <div data-i18n="Basic">Log out</div>
      </a>
    </li>
  </ul>
</aside>