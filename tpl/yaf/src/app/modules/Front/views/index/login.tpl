<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录-轻小说管理系统</title>
    <meta name="keywords" content="">
    <meta name="description" content="">

    <link rel="shortcut icon" href="favicon.ico">
    <link href="/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/css/font-awesome.css?v=4.4.0" rel="stylesheet">

    <link href="/css/animate.css" rel="stylesheet">
    <link href="/css/style.css?v=4.1.0" rel="stylesheet">
    <!--[if lt IE 9]>
    <meta http-equiv="refresh" content="0;/ie.html" />
    <![endif]-->
    <script>if(window.top !== window.self){ window.top.location = window.location;}</script>
  </head>

  <body class="gray-bg">

    <div class="middle-box text-center loginscreen  animated fadeInDown">
      <div>
        <div>

          <h1 class="logo-name"></h1>

        </div>
        <h3>欢迎使用轻小说管理系统</h3>

        <form class="m-t" role="form" action="/index/login" method="POST" id="myform">
          <div class="form-group">
            <input type="text" name="username" class="form-control required" placeholder="用户名" data-msg="请填写用户名">
          </div>
          <div class="form-group">
            <input type="password" name="password" class="form-control required" placeholder="密码" data-msg="请填写密码">
          </div>
          <div class="form-group">
            <div class="col-sm-5" style="padding-left:0px; padding-right:0px;">
              <input type="text" name="capcha" id="input-code" class="form-control" placeholder="校验码" required="">&nbsp;
            </div>
            <a href="javascript:;" onclick="refresh()"><img id="img-verify" src="/index/captcha" border="0"></a>&nbsp;<a href="javascript:;" onclick="refresh()">点击刷新</a>
          </div>
          <input type="hidden" name="t" value="{%$token%}">
          <button type="submitBtn" class="btn btn-primary block full-width m-b">登 录</button>


          <!-- <p class="text&#45;muted text&#45;center"> <a target="_blank" href="/auth/login"><small>忘记密码了？</small></a> | <a target="_blank" href="/auth/login">注册一个新账号</a> -->
          </p>

        </form>
      </div>
    </div>

    <!-- 全局js -->
    <script src="/js/jquery.min.js?v=2.1.4"></script>
    <script src="/js/bootstrap.min.js?v=3.3.6"></script>
    <script>
      $(function(){
        $('#myform').validate();
      });
    </script>
    <script>
      function refresh(){
        var url = "/index/captcha?&r="+Math.random();
        $('#img-verify').attr("src", url);
        $('#input-code').val('');
      }
    </script>
  </body>
</html>
