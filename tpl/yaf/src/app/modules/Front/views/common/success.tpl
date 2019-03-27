<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
 <head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="renderer" content="webkit">
  <title>页面提示</title>
  <script type="text/javascript">
   function refresh(){
     location.href = "{%if $url%} {%$url%} {%else%} index.php {%/if%}";
   }
   setTimeout("refresh()",3000);
  </script>
  <style type="text/css">
   *{margin:0px;padding:0px;font-size:12px;font-family:Arial,Verdana;}
   #wrapper{width:450px;height:200px;background:#F5F5F5;border:1px solid #D2D2D2;position:absolute;top:40%;left:50%;margin-top:-100px;margin-left:-225px;}
   p.msg-title{width:100%;height:30px;line-height:30px;text-align:center;color:#EE7A38;margin-top:40px;font:14px Arial,Verdana;font-weight:bold;}
   p.message{width:100%;height:40px;line-height:40px;text-align:center;color:blue;margin-top:5px;margin-bottom:5px;}
   p.error{font-size:20px;width:100%;height:40px;line-height:40px;text-align:center;color:red;margin-top:5px;margin-bottom:5px;}
   p.notice{width:100%;height:25px;line-height:25px;text-align:center;}
  </style>
 </head>

 <body>
  <div id="wrapper">
    <p class="msg-title">{%if $type == 1%}警告！{%else%}提示！{%/if%}</p>
        <p class="error" style="height:auto">{%$msg%}</p>
        <p class="notice">系统将在 <span style="color:blue;font-weight:bold">3</span> 秒后自动跳转，如果不想等待,直接点击
        <a href="{%if $url%} {%$url%} {%else%} index.php {%/if%}";>这里</a> 跳转</p>  </div>
 </body>
</html>
