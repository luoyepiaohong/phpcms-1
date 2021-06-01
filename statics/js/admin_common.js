$(function(){
	if ($('.table-checkable')) {
		var table = $('.table-checkable');
		table.find('.group-checkable').change(function () {
			var set = jQuery(this).attr("data-set");
			var checked = jQuery(this).is(":checked");
			jQuery(set).each(function () {
				if (checked) {
					$(this).prop("checked", true);
					$(this).parents('tr').addClass("active");
				} else {
					$(this).prop("checked", false);
					$(this).parents('tr').removeClass("active");
				}
			});
		});
	}
});
function geturlpathname() {
	var url = document.location.toString();
	var arrUrl = url.split("//");
	var start = arrUrl[1].indexOf("/");
	var relUrl = arrUrl[1].substring(start);
	if(relUrl.indexOf("?") != -1){
		relUrl = relUrl.split("?")[0];
	}
	return relUrl;
}
// 判断当前终端是否是移动设备
function is_mobile() {
	var ua = navigator.userAgent,
	 isWindowsPhone = /(?:Windows Phone)/.test(ua),
	 isSymbian = /(?:SymbianOS)/.test(ua) || isWindowsPhone, 
	 isAndroid = /(?:Android)/.test(ua), 
	 isFireFox = /(?:Firefox)/.test(ua), 
	 isChrome = /(?:Chrome|CriOS)/.test(ua),
	 isTablet = /(?:iPad|PlayBook)/.test(ua) || (isAndroid && !/(?:Mobile)/.test(ua)) || (isFireFox && /(?:Tablet)/.test(ua)),
	 isPhone = /(?:iPhone)/.test(ua) && !isTablet,
	 isPc = !isPhone && !isAndroid && !isSymbian;
	 if (isPc) {
		// pc
		return false;
	 } else {
		return true;
	 }
}
function confirmurl(url,message) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	Dialog.confirm(message,function() {
		redirect(url);
	});
}
function redirect(url) {
	location.href = url;
}
function topinyin(name, from, url) {
	var val = $("#" + from).val();
	if ($("#" + name).val()) {
		return false
	}
	$.get(url+'&name='+val+'&rand='+Math.random(), function(data){
		$('#'+name).val(data);
	});
}
//滚动条
$(function(){
	$(":text").addClass('input-text');
})

/**
 * 全选checkbox,注意：标识checkbox id固定为为check_box
 * @param string name 列表check名称,如 uid[]
 */
function selectall(name) {
	if ($("#check_box").attr("checked")=='checked') {
		$("input[name='"+name+"']").each(function() {
  			$(this).attr("checked","checked");
			$(this).parents('tr').addClass("active");
		});
	} else {
		$("input[name='"+name+"']").each(function() {
  			$(this).removeAttr("checked");
			$(this).parents('tr').removeClass("active");
		});
	}
}
function openwinx(url,name,w,h) {
	if(!w) w='100%';
	if(!h) h='100%';
	if (is_mobile()) {
		w = h = '100%';
	}
	if (w=='100%' && h=='100%') {
		var drag = false;
	} else {
		var drag = true;
	}
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	var diag = new Dialog({
		id:'content_id',
		title:name,
		url:url,
		width:w,
		height:h,
		modal:true,
		draggable:drag
	});
	diag.cancelText = '关闭(X)';
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}
function contentopen(url,name,w,h) {
	if(!w) w='100%';
	if(!h) h='100%';
	if (is_mobile()) {
		w = h = '100%';
	}
	if (w=='100%' && h=='100%') {
		var drag = false;
	} else {
		var drag = true;
	}
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	var diag = new Dialog({
		id:'content_id',
		title:name,
		url:url,
		width:w,
		height:h,
		modal:true,
		draggable:drag
	});
	diag.addButton('dosubmit','保存后自动关闭',function(){
		//var body = diag.innerFrame.contentWindow.document;
		//$(body).find('#myform').serialize()
		var form = $DW.$('#dosubmit');
		if(form.length > 0) {
			form.click();
		} else {
			if (parent.right) {
				parent.right.location.reload(true);
			} else {
				window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload(true);
			}
			diag.close();
		}
		return false;
	},0,1);
	diag.okText = '保存并继续发表';
	diag.onOk = function(){
		var form = $DW.$('#dosubmit_continue');
		if(form.length > 0) {
			form.click();
		} else {
			if (parent.right) {
				parent.right.location.reload(true);
			} else {
				window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload(true);
			}
			diag.close();
		}
		return false;
	};
	diag.cancelText = '关闭(X)';
	diag.onCancel=function(){
		if($DW.$V('#title') !='') {
			Dialog.confirm('内容已经录入，确定离开将不保存数据？', function(){
				if (parent.right) {
					parent.right.location.reload(true);
				} else {
					window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload(true);
				}
				diag.close();
			}, function(){});
		} else {
			if (parent.right) {
				parent.right.location.reload(true);
			} else {
				window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload(true);
			}
			diag.close();
		}
		return false;
	};
	diag.onClose=function(){
		if (parent.right) {
			parent.right.location.reload(true);
		} else {
			window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload(true);
		}
		$DW.close();
	};
	diag.show();
}
//弹出对话框
function artdialog(id,url,title,w,h) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (url.toLowerCase().indexOf("http://") != -1 || url.toLowerCase().indexOf("https://") != -1) {
	} else {
		url = geturlpathname()+url;
	}
	if(!w) w=700;
	if(!h) h=500;
	if (is_mobile()) {
		w = h = '100%';
	}
	if (w=='100%' && h=='100%') {
		var drag = false;
	} else {
		var drag = true;
	}
	var diag = new Dialog({
		id:id,
		title:title,
		url:url,
		width:w,
		height:h,
		modal:true,
		draggable:drag
	});
	diag.onOk = function(){
		var form = $DW.$('#dosubmit');
		form.click();
		return false;
	};
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}
//选择图标
function menuicon(id,linkurl,title,w,h) {
	if (typeof pc_hash == 'string') linkurl += (linkurl.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (linkurl.toLowerCase().indexOf("http://") != -1 || linkurl.toLowerCase().indexOf("https://") != -1) {
	} else {
		linkurl = geturlpathname()+linkurl;
	}
	if(!w) w=700;
	if(!h) h=500;
	if (is_mobile()) {
		w = h = '100%';
	}
	if (w=='100%' && h=='100%') {
		var drag = false;
	} else {
		var drag = true;
	}
	var diag = new Dialog({
		id:id,
		title:title,
		url:linkurl,
		width:w,
		height:h,
		modal:true,
		draggable:drag
	});
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}
//弹出对话框
function omnipotent(id,linkurl,title,close_type,w,h) {
	if (typeof pc_hash == 'string') linkurl += (linkurl.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (linkurl.toLowerCase().indexOf("http://") != -1 || linkurl.toLowerCase().indexOf("https://") != -1) {
	} else {
		linkurl = geturlpathname()+linkurl;
	}
	if(!w) w=700;
	if(!h) h=500;
	if (is_mobile()) {
		w = h = '100%';
	}
	if (w=='100%' && h=='100%') {
		var drag = false;
	} else {
		var drag = true;
	}
	var diag = new Dialog({
		id:id,
		title:title,
		url:linkurl,
		width:w,
		height:h,
		modal:true,
		draggable:drag
	});
	diag.onOk = function(){
		if(close_type==1) {
			diag.close();
		} else {
			var form = $DW.$('#dosubmit');
			form.click();
		}
		return false;
	};
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}
function map(id,linkurl,title,tcstr,w,h) {
	if (typeof pc_hash == 'string') linkurl += (linkurl.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (linkurl.toLowerCase().indexOf("http://") != -1 || linkurl.toLowerCase().indexOf("https://") != -1) {
	} else {
		linkurl = geturlpathname()+linkurl;
	}
	if(!w) w=700;
	if(!h) h=500;
	if (is_mobile()) {
		w = h = '100%';
	}
	if (w=='100%' && h=='100%') {
		var drag = false;
	} else {
		var drag = true;
	}
	var diag = new Dialog({
		id:id,
		title:title,
		url:linkurl,
		width:w,
		height:h,
		modal:true,
		draggable:drag
	});
	diag.onOk = function(){
		$S(tcstr).value = $DW.$V('#'+tcstr);
		diag.close();
	};
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}
function dr_admin_menu_ajax(e, t) {
	var a = layer.load(2, {
		shade: [.3, "#fff"],
		time: 1e4
	});
	$.ajax({
		type: "GET",
		dataType: "json",
		url: e,
		success: function(e) {
			if (layer.close(a), dr_tips(e.code, e.msg), 1 == e.code) {
				if (t) return;
				setTimeout("location.reload(true)", 2e3)
			}
		},
		error: function(e, t, a) {
			dr_ajax_admin_alert_error(e, t, a)
		}
	})
}
function dr_bfb(title, myform, url) {
	layer.load(2, {
		shade: [.3, "#fff"],
		time: 1e3
	}),
	layer.open({
		type: 2,
		title: title,
		scrollbar: !1,
		resize: !0,
		maxmin: !0,
		shade: 0,
		area: ["80%", "80%"],
		success: function(layero, index) {
			var body = layer.getChildFrame("body", index),
				r = $(body).html();
			if (r.indexOf('"code":0') > 0 && r.length < 150) {
				var i = JSON.parse(r);
				layer.closeAll(), dr_tips(0, i.msg)
			}
		},
		content: url + "&" + $("#" + myform).serialize()
	})
}
function dr_bfb_submit(title, myform, url) {
	layer.load(2, {shade:[ .3, "#fff" ],time:1000});
	$.ajax({
		type:"POST",
		dataType:"json",
		url:url,
		data:$("#"+myform).serialize(),
		success:function(json) {
			layer.closeAll("loading");
			if (json.code == 1) {
				layer.open({
					type:2,
					title:title,
					scrollbar:false,
					resize:true,
					maxmin:true,
					shade:0,
					area:[ "80%", "80%" ],
					success:function(layero, index) {
						var body = layer.getChildFrame("body", index);
						var json = $(body).html();
						if (json.indexOf('"code":0') > 0 && json.length < 150) {
							var obj = JSON.parse(json);
							layer.closeAll("loading");
							dr_tips(0, obj.msg);
						}
					},
					content:json.data.url
				});
			} else {
				dr_tips(0, json.msg, 90000);
			}
			return false;
		},
		error:function(HttpRequest, ajaxOptions, thrownError) {
			dr_ajax_admin_alert_error(HttpRequest, ajaxOptions, thrownError);
		}
	});
}
function dr_tips(code, msg, time) {
	if (!time || time == "undefined") {
		time = 3000;
	} else {
		time = time * 1000;
	}
	var is_tip = 0;
	if (time < 0) {
		is_tip = 1;
	} else if (code == 0 && msg.length > 15) {
		is_tip = 1;
	}

	if (is_tip) {
		if (code == 0) {
			layer.alert(msg, {
				shade: 0,
				title: "",
				icon: 2
			})
		} else {
			layer.alert(msg, {
				shade: 0,
				title: "",
				icon: 1
			})
		}
	} else {
		var tip = '<i class="fa fa-info-circle"></i>';
		//var theme = 'teal';
		if (code >= 1) {
			tip = '<i class="fa fa-check-circle"></i>';
			//theme = 'lime';
		} else if (code == 0) {
			tip = '<i class="fa fa-times-circle"></i>';
			//theme = 'ruby';
		}
		layer.msg(tip+'&nbsp;&nbsp;'+msg, {time: time});
	}
}
function dr_ajax_admin_alert_error(HttpRequest, ajaxOptions, thrownError) {
	layer.closeAll("loading");
	var msg = HttpRequest.responseText;
	if (!msg) {
		dr_tips(0, "系统错误");
	} else {
		layer.open({
			type:1,
			title:"系统错误",
			fix:true,
			shadeClose:true,
			shade:0,
			area:[ "50%", "50%" ],
			content:'<div style="padding:10px;">' + msg + '</div>'
		});
	}
}
function check_title(linkurl,title) {
	if (typeof pc_hash == 'string') linkurl += (linkurl.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if (linkurl.toLowerCase().indexOf("http://") != -1 || linkurl.toLowerCase().indexOf("https://") != -1) {
	} else {
		linkurl = geturlpathname()+linkurl;
	}
	var val = $('#'+title).val();
	$.get(linkurl+"&data=" + val + "&is_ajax=1",
	function(data) {
		if (data) {
			dr_tips(0, data);
		}
	});
}
function get_wxurl(field,linkurl,titlename,keywordname,contentname) {
	var index = layer.load(2, {
		shade: [0.3,'#fff'], //0.1透明度的白色背景
		time: 5000
	});
	$.ajax({type: "GET",dataType:"json", url: linkurl+'&url='+encodeURIComponent($('#'+field).val()),
		success: function(json) {
			layer.close(index);
			dr_tips(json.code, json.msg);
			if (json.code > 0) {
				var arr = json.data;
				$('#'+titlename).val(arr.title);
				if ($('#'+keywordname).length > 0) {
					$('#'+keywordname).val(arr.keyword);
					$('#'+keywordname).tagsinput('add', arr.keyword);
				}
				UE.getEditor(contentname).setContent(arr.content);
			}
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {
			dr_ajax_admin_alert_error(HttpRequest, ajaxOptions, thrownError);
		}
	});
}
function get_wxurlckeditor(field,linkurl,titlename,keywordname,contentname) {
	var index = layer.load(2, {
		shade: [0.3,'#fff'], //0.1透明度的白色背景
		time: 5000
	});
	$.ajax({type: "GET",dataType:"json", url: linkurl+'&url='+encodeURIComponent($('#'+field).val()),
		success: function(json) {
			layer.close(index);
			dr_tips(json.code, json.msg);
			if (json.code > 0) {
				var arr = json.data;
				$('#'+titlename).val(arr.title);
				if ($('#'+keywordname).length > 0) {
					$('#'+keywordname).val(arr.keyword);
					$('#'+keywordname).tagsinput('add', arr.keyword);
				}
				CKEDITOR.instances[contentname].setData(arr.content);
			}
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {
			dr_ajax_admin_alert_error(HttpRequest, ajaxOptions, thrownError);
		}
	});
}