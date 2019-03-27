<!-- 全局js -->
<script src="/js/jquery.min.js?v=2.1.4"></script>
<script src="/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="/js/jquery.validate.min.js"></script>
<script src="/js/plugins/layer/layer.min.js"></script>

<!-- 自定义js -->
<script src="/js/hplus.js?v=4.1.0"></script>
<script type="text/javascript" src="/js/contabs.js"></script>

<script type="text/javascript">
  function ajax_delete(url, params, msg){
   var m = msg||"确认删除该信息？";
    if(confirm(m)){
      $.post( url+"?"+params, function(e) {
        if(e.errno > 0){
          alert(e.errmsg);
        }
        window.location.reload();
      })
    }
  }
  function winpop(url, title, w, h){
    if(typeof(w) == "undefined" || w.length <= 0){
        w = '60%';
    }
    if(typeof(h) == "undefined" || h.length <= 0){
        h = '90%';
    }
    parent.layer.open({
        type: 2,
        title: title,
        shadeClose: true,
        shade: 0.6,
        area: [w, h],
        content: url,
        end: function(){
            window.location = window.location.href;
        },
    });
  }
  // select选择
  function optionSelect(a, b){
      var right = new Array();
      $(b+" option").each(function(){
        var text = $(this).text();
        if(text.length > 0){
            right.push(text)
        }
      });
      $(a+" option:selected").each(function(){
          var val = $(this).val();
          var tag = $(this).text();
          if(val.length <= 0){
              return
          }
          if(right.length > 0 && right.indexOf(tag) > -1){
              return
          }
          var html = '<option value="'+val+'">'+tag+'</option>';
          $(b).append(html);
      });
  }
  // select反向选择
  function optionUnselect(a){
      $(a+" option:selected").each(function(){
          $(this).remove();
      });
  }
  function ProChanged(){
    var pro_id = $('#input_pro_option').val();
    var def_city = $('#input_city_option').data('def');

    var html = '<option value="">请选择</option>';
    $('#input_city_option').html(html);
    if(Number(pro_id) <= 0) return false;

    $.post('/channel/city_option', {pro_id:pro_id}, function(e){
      if(e.errno == 0){
        $.each(e.data, function(k,v){
          if(v.Fid == def_city){
            html += '<option selected="selected" value="'+v.Fid+'">'+v.Fname+'</option>';
          }else{
            html += '<option value="'+v.Fid+'">'+v.Fname+'</option>';
          }
        })
        $('#input_city_option').html(html);
      }
    }, 'json');
  }
  function CityChanged(){
    var city_id = $('#input_city_option').val();
    if(city_id == ''){
      city_id = $('#input_city_option').data('def');
    }
    var def_channel = $('#input_channel_option').data('def');

    var html = '<option value="">请选择</option>';
    $('#input_channel_option').html(html);
    if(Number(city_id) <= 0) return false;

    $.post('/channel/channel_option', {city_id:city_id}, function(e){
      if(e.errno == 0){
        $.each(e.data, function(k,v){
          if(v.Fchannel_id == def_channel){
            html += '<option selected="selected" value="'+v.Fchannel_id+'">'+v.Fname+'</option>';
          }else{
            html += '<option value="'+v.Fchannel_id+'">'+v.Fname+'</option>';
          }
        })
        $('#input_channel_option').html(html);
      }
    }, 'json');
  }
</script>
