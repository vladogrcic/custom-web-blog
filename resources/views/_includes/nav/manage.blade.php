
@section('menu-content')
<div class="side-menu" id="admin-side-menu" @mouseover="manageMenuHidden = false" @mouseout="manageMenuHidden = true" :style="{ marginLeft: menuPosition('pos')+'%' }">
  <aside class="menu" @mouseover="manageMenuHidden = false" @mouseout="manageMenuHidden = true" :style="{ left: menuPosition('pos')*2+'%', top: topMenuSize()+'px', height: '100%', bottom: 0 }">
{{-- <div class="side-menu" id="admin-side-menu" :style="{ marginLeft: menuPosition('pos')+'%' }">
  <aside class="menu" :style="{ left: menuPosition('pos')*2+'%' }"> --}}
    <p class="menu-label">
      General
    </p>
    <ul class="menu-list">
      <li>
        <a href="{{route('manage.dashboard')}}" class="{{Nav::isRoute('manage.dashboard')}}">
          <span class="icon"><i class="fa fa-dashboard"></i></span>
          Dashboard
        </a>
      </li>
    </ul>
    <p class="menu-label">
      Content
    </p>
    <ul class="menu-list">
      <li>
        <a href="{{route('posts.index')}}" class="{{Nav::isResource('posts', 2)}}">
          <span class="icon"><i class="fa fa-font"></i></span>
          Posts
        </a>
      </li>
      @permission('create-general|read-general|update-general|delete-general')
      <li>
        <a href="{{route('categories.index')}}" class="{{Nav::isResource('categories', 2)}}">
          <span class="icon"><i class="fa fa-list-alt"></i></span>
          Categories
        </a>
      </li>
      <li>
        <a href="{{route('languages.index')}}" class="{{Nav::isResource('languages', 2)}}">
          <span class="icon"><i class="fa fa-language"></i></span>
          Languages
        </a>
      </li>
      
      <li>
        <a href="{{route('tags.index')}}" class="{{Nav::isResource('tags', 2)}}">
          <span class="icon"><i class="fa fa-tags"></i></span>
          Tags
        </a>
      </li>
      @endpermission
      @permission('create-media|read-media|update-media|delete-media')
      <li>
        <a href="{{route('media.index')}}" class="{{Nav::isResource('media', 2)}}">
          <span class="icon"><i class="fa fa-folder"></i></span>
          Media
        </a>
      </li>
      @endpermission
    </ul>
    <p class="menu-label">
      Administration
    </p>
    <ul class="menu-list">
        @permission('create-users|read-users|update-users|delete-users')
        <li>
        <a href="{{route('users.index')}}" class="{{Nav::isResource('users')}}">
          <span class="icon"><i class="fa fa-user-circle"></i></span>
          Users
        </a>
      </li>
      @endpermission
      <li>
        <a href="{{route('profile.index')}}" class="{{Nav::isRoute('profile.index')}} is-info">
          <span class="icon"><i class="fa fa-address-card"></i></span>
          Profile
        </a>
      </li>
      @permission('read-settings|update-settings')
      <li>
        <a href="{{route('settings.index')}}" class="{{Nav::isRoute('settings.index')}} is-info">
          <span class="icon"><i class="fa fa-cogs"></i></span>
          Settings
        </a>
      </li>
      @endpermission
    </ul>
  </aside>
</div>
@endsection
@section('menu-scripts')
  <script>
    var mainMenu = new Vue({
        el: '#admin-side-menu',
        data: {
            manageMenuHidden: true,
        },
        methods: {
            menuPosition: function (param) {
              if(param == 'pos'){
                if(this.manageMenuHidden){
                  return -12.8;
                }
                else{
                  return 0;
                }
              }
              else{
                if(this.manageMenuHidden){
                  return 10;
                }
                else{
                  return 15;
                }
              }
            },
            topMenuSize: function(){
              return document.querySelector('.navbar').scrollHeight;
            },
            screenSize: function(){
              return window.scrollHeight;
            }
        },
    });
  </script>
@endsection
