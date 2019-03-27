<nav class="navbar-default navbar-static-side" role="navigation">
  <div class="nav-close"><i class="fa fa-times-circle"></i>
  </div>
  <div class="sidebar-collapse">
    <ul class="nav" id="side-menu">
      <li class="nav-header">
        <div class="dropdown profile-element">
          <span><img alt="image" class="/static/img-circle" src="/img/profile_small.jpg" /></span>
          <a data-toggle="dropdown" class="dropdown-toggle" href="#">
            <span class="clear">
              <span class="block m-t-xs"><strong class="font-bold">{%$userinfo.nickname%}</strong></span>
              <span class="text-muted text-xs block"><!--超级管理员-->{%$userinfo.role%}<b class="caret"></b></span>
            </span>
          </a>
          <ul class="dropdown-menu animated fadeInRight m-t-xs">
            <!--
            <li><a class="j_menuitem" href="#">修改头像</a></li>
            <li><a class="j_menuitem" href="#">个人资料</a></li>
            -->
            <!-- <li class="divider"></li> -->
            <li><a href="/index/logout">安全退出</a></li>
          </ul>
        </div>
        <div class="logo-element">轻小说
          <a style="display:none;" class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
        </div>
      </li>
      {%foreach $authMenus as $value%}
      <li>
        <a href="#">
          <i class="fa {%$value.icon|default:'fa-gear'%}"></i>
          <span class="nav-label">{%$value.title%}</span>
          <span class="fa arrow"></span>
        </a>
        <ul class="nav nav-second-level">
              {%foreach $value.children as $k=>$val%}
              <li>
                <a class="J_menuItem" href="{%$val.url%}">{%$val.title%}</a>
              </li>
              {%/foreach%}
        </ul>
      </li>
      {%/foreach%}

    </ul>
  </div>
</nav>
