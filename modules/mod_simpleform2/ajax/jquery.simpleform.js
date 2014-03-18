
jQuery.fn.simpleform = function(options){
    
	var options = jQuery.extend({
        checkCallBack: function(id){return true;},
        resultCallBack: function(id,result,text){return true;},
		tmp: null,
		url: '',
		loaderImg: '/modules/mod_simpleform2/images/loading.gif',
	},options);
	
	return this.each(function(){
		jQuery(this).bind("submit",function(e){
			e.preventDefault();
			var form = this;
			if(jQuery(form).find('input[type=submit]').parent()[0].tagName=='FORM') jQuery(form).find('input[type=submit]').wrap('<span></span>');
			var btnWrap = jQuery(form).find('input[type=submit]').parent();
			var tmp = btnWrap.html();
			if(options.url!='') jQuery(form).attr("action",options.url);
			btnWrap.html('<img src="'+options.loaderImg+'" alt="Loading..." title="Loading..." />');
			var uResult = options.checkCallBack(jQuery(form).attr('id'));
			if(uResult!=true){
				btnWrap.html(tmp);
				return false;
			}
			jQuery(form).ajaxSubmit({
				form : form,
				btnWrap : btnWrap,
				tmp : tmp,
				success : function(data){
					var key = data.substring(0,1);
					var text = data.substring(1);
					var captcha = jQuery(this.form).find('img.sf2Captcha');
					var srvResult = false;
					if(key=="=") srvResult = true;
					captcha.click();
					var uResult = options.resultCallBack(jQuery(this.form).attr('id'),srvResult,text);
					if(uResult==true){
						if(srvResult) jQuery(this.form).html(text);
						else if(key=="!"){
							btnWrap.html(this.tmp);
							alert(text);
						}
						else{
							btnWrap.html(this.tmp);
							alert('Ajax error');
						}
					}
					else btnWrap.html(this.tmp);
				}
			});
			return false;
		});
	});

};