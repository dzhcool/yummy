{%include file="common/header.tpl"%}
<div id="wrapper">
  <!--左侧导航开始-->
  {%include file="common/left_nav.tpl"%}
  <!--左侧导航结束-->
  <!--右侧部分开始-->
  <div id="page-wrapper" class="gray-bg dashbard-1">
    <div class="row content-tabs">
      <button class="roll-nav roll-left J_tabLeft"><i class="fa fa-backward"></i>
      </button>
      <nav class="page-tabs J_menuTabs">
        <div class="page-tabs-content">
          <a href="javascript:;" class="active J_menuTab" data-id="/admin/main">首页</a>
        </div>
      </nav>
      <button class="roll-nav roll-right J_tabRight"><i class="fa fa-forward"></i>
      </button>
      <div class="btn-group roll-nav roll-right">
        <button class="dropdown J_tabClose" data-toggle="dropdown">关闭操作<span class="caret"></span>

        </button>
        <ul role="menu" class="dropdown-menu dropdown-menu-right">
          <li class="J_tabShowActive"><a>定位当前选项卡</a>
          </li>
          <li class="divider"></li>
          <li class="J_tabCloseAll"><a>关闭全部选项卡</a>
          </li>
          <li class="J_tabCloseOther"><a>关闭其他选项卡</a>
          </li>
        </ul>
      </div>
      <a href="/index/logout" class="roll-nav roll-right J_tabExit"><i class="fa fa fa-sign-out"></i> 退出</a>
    </div>
    <div class="row J_mainContent" id="content-main">
      <iframe class="J_iframe" name="iframe0" width="100%" height="100%" src="/index/index" frameborder="0" data-id="/index/index"></iframe>
    </div>
    <!--
    <div class="footer">
      <div class="pull-right">&copy; 2016-2017 <a href="http://www.unique-technology.com/" target="_blank">Unique-Technology</a>
      </div>
    </div>
    -->
  </div>
  <!--右侧部分结束-->
  <!--右侧边栏开始-->
  <!--右侧边栏结束-->
  <!--mini聊天窗口开始-->
  <!--mini聊天窗口结束-->
</div>
{%include file='common/footer.tpl'%}
<script>
var _layer_hander = null;
</script>
</body>
</html>
