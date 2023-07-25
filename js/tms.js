(function($,undefined){
	var _TMS=window._TMS=$.fn._TMS=function(_){
		_=_||{}
		_.me=_.holder=this
		;(_=$.extend(clone(_TMS),_TMS.presets[_.preset],_)).init()
		return _.me.data({opt:_})
	},timer
	
	$.extend(_TMS,{
		show:0,
		pagination:false,
		slideShow:false,
		pagNums:true,
		items:false,
		pauseOnHover:false,
		bl:false,
		preload:false,
		banners:false,
		numStatus:false,
		numStatusCl:'num-status',
		pagCl:'pagination',
		pagCurrCl:'current',
		pauseCl:'paused',
		bannerCl:'banner',
		progCl:'progressBar',
		pic:'pic',
		mask:'mask',
		pagEv:'click',
		prevNextEv:'click',
		duration:5000,
		interval:100,
		bannerMeth:'fade',
		bannerDurr:400,
		bannerEasing:'',
		overflow:'hidden',
		preset:'simpleFade',
		etal:'<div></div>',
		progCSS:{
			height:0
		},
		picCSS:{
			position:'relative',
			overflow:'hidden',
			zIndex:0							
		},
		maskCSS:{
			position:'absolute',
			zIndex:0,
			left:0,
			top:0,
			width:'100%',
			height:'100%'							
		},
		afterAnimation:function(){},
		beforeAnimation:function(){},
		parseImgFu:function(){
			var _=this,
				itms=_.itms=[]
			$(_.items+' img',_.holder)				
				.each(function(){
					itms.push($(this).attr('src'))
				})
		},
		bannersFu:function(){
			var _=this,
				banners=[]
			if(_.items)
				$(_.items,_.holder).each(function(){
					var bn=$('.'+_.bannerCl,this)
					bn.data({sLeft:bn.css('left'),sTop:bn.css('top')})
					banners.push(bn)
				}),
				_.banners=banners
		},
		bannerShowFu:function(n){
			var _=this
			if(_.bannerMeth=='custom')
				_.banner=_.banners[n]
					.appendTo(_.holder)
					.css({
						left:_.banners[n].data('sLeft'),
						top:_.banners[n].data('sTop'),
						opacity:1
					})
			if(_.bannerMeth=='fade'&&_.banners.length)
				_.banners[n]
					.appendTo(_.holder)
					.hide()
					.fadeIn()			
		},
		preloadFu:function(){
			var _=this,
				prldBfr=_.prldBfr=[]
			$(_.itms).each(function(i){
				prldBfr[i]=new Image()
				prldBfr[i].src=this
			})
		},
		pagFu:function(){
			var _=this
			_.pags=(_.pagination===true)
				?(function(){
					var ret=$('<ul></ul>').addClass(_.pagCl)
						$(_.itms).each(function(i){
							ret.append('<li><a href="#">'+(_.pagNums?i+1:'')+'</a></li>')
						})
						ret.appendTo(_.holder)
						return $('>li',ret)
					})()
					:$(_.pagination)
			if(_.pagination!==true&&_.items===false)
				_.itms=[],
				_.pags.each(function(){
					_.itms.push($('a',this).attr('href'))
				})
			_.current=_.show
			_.pags.eq(_.show).addClass(_.pagCurrCl)
			_.pags.parent().each(function(){
				$(this).children().each(function(i){
					$('a',this).bind(_.pagEv,function(){
						if(_.current==i||_.bl)
							return false
						else
							_.changeFu(_.current=i)
						return false
					})
				})
			})
				
		},
		preFu:function(){
			var _=this,
				img=$(new Image())
			_.pic=$(_.etal)
				.addClass(_.pic)
				.appendTo(_.me)
			_.mask=$(_.etal)
				.addClass(_.mask)
				.appendTo(_.pic)
			
			if(_.me.css('position')=='static')
				_.me.css({position:'relative'})
			if(_.me.css('z-index')=='auto')
				_.me.css({zIndex:1})
			_.me.css({overflow:_.overflow})
			if(_.progressBar===true)
				_.progressBar=$(_.etal).addClass(_.progCl).css(_.progCSS).appendTo(_.me)
			else
				_.progressBar=_.progressBar?$(_.progressBar):false
			if(_.progressBar&&!(_.progressBar.parent().length))
				_.progressBar.appendTo(_.me)
			if(_.progressBar)
				_.wi=_.progressBar.width()||_.pic.width()
			if(_.items)
				_.parseImgFu()
			if(_.pagination)
				_.pagFu()
			
			img
				.appendTo(_.me)
				.load(function(){
					_.mask.css(_.maskCSS)
					_.pic
						.css(_.picCSS)
						.css({
							width:img.width(),
							height:img.height(),
							background:'url('+_.itms[_.show]+') 0 0 no-repeat'
						})
					img.remove()
					_.current=_.buff=_.show					
				})
				.attr({src:_.itms[_.show]})
		},
		pagChangeFu:function(n){
			var _=this
			_.pags.removeClass(_.pagCurrCl)
			_.pags.eq(n).addClass(_.pagCurrCl)
		},
		progFu:function(fu){
			var _=this,
				w=fu?_.progressBar.width():'0px',
				wi=_.wi,
				time=fu?(wi-w)/wi*_.slideShow:_.slideShow

			_.progressBar
				.width(w)
				.stop()
				.animate({width:wi},time,'linear',function(){
					_.progressBar.width(0)
					if(fu)setTimeout(fu,1)
				})
		},
		sliceFu:function(w,h){
			var _=this,
				eW=parseInt(_.pic.width()/w),
				eH=parseInt(_.pic.height()/h),
				etal=$(_.etal),
				fW=_.pic.width()-eW*w,
				fH=_.pic.height()-eH*h,
				x,y,
				matrix=_.matrix=[]
			_.mask.empty()
			for(y=0;y<h;y++)
				for(x=0;x<w;x++)
					matrix[y]=matrix[y]?matrix[y]:[],
					matrix[y][x]=etal.clone()
						.appendTo(_.mask)
						.css({
							 left:x*eW,
							 top:y*eH,
							 position:'absolute',
							 width:x==w-1?eW+fW:eW,
							 height:y==h-1?eH+fH:eH,
							 backgroundPosition:'-'+x*eW+'px -'+y*eH+'px',
							 display:'none'
						 })
			_.maskC=_.mask.find('>div')
		},
		afterShow:function(){
			var _=this
			if(_.playBlock)
				_.bl=false
			_.pic.css({backgroundImage:'url('+_.next+')'})
			_.maskC.hide()
			if(_.banners)
				_.bannerShowFu(_.current)
			if(_.banners&&_.banners.length)
				_.banner=($(_.banners).detach(),_.banners[_.current])
			_.afterAnimation(_.banner?_.banner:_)
		},
		dirMirrorFu:function(way){
			var _=this
			if(_.direction<0)
				void(0)
			return way
		},
		showFu:function(){
			var _=this,
				way,
				tmp,
				fu=function(){			
					way=_.ways[_.way].call(_)
					if(_.reverseWay)
						way.reverse()
					if(_.dirMirror)
						way=_.dirMirrorFu(way)
					if(_.int)
						clearInterval(_.int)
					_.int=setInterval(function(){
						if(way.length)
							_.anims[_.anim].apply(_,[way.shift(),!way.length])
						else
							clearInterval(_.int)
					},_.interval)
				}
			if(_.banner&&_.banner.length)
				if(_.bannerMeth!='custom')
					$(_.banners).each(function(){$(this).detach()})
			_.beforeAnimation(_.banner?_.banner:false)
			if(_.banner&&_.banner.length)
				tmp=_.banner.css('opacity'),
				_.banner.animate({opacity:tmp},1,fu)
			else
				fu()
		},
		changeFu:function(n){
			var _=this
			if(_.preset_!=_.preset)
				$.extend(_,_TMS.presets[_.preset]),
				_.preset_=_.preset
			if(_.maskC)
				_.maskC.stop(),
				_.pic.css({backgroundImage:'url('+_.next+')'})
			_.next=_.itms[n]
			_.direction=n-_.buff
			_.current=_.buff=n
			if(_.pagination)
				_.pagChangeFu(n)
			_.sliceFu(_.blocksX,_.blocksY)
			_.maskC.css({backgroundImage:'url('+_.next+')'})
			if(_.playBlock)
				_.bl=true
			_.showFu()
			clearInterval(timer)
			if(_.slideShow&&!_.paused)
				(function(){
					if(_.progressBar)
						_.progFu()
				})(),
				timer=setInterval(function(){
					_.nextFu()
				},_.slideShow)
			if(_.numStatus)
				_.numStatusFu()
		},
		numStatusFu:function(){
			var _=this
			if(!_.numSt)
				if(_.numStatus===true)
					_.numSt=$(_.etal)
				else
					_.numSt=$(_.numStatus)
			if(!_.numSt.parent().length)
				_.numSt.appendTo(_.me)
			_.numSt
				.html('<span class="curr"></span>/<span class="total"></span>')
				.addClass(_.numStatusCl)
			$('.curr',_.numSt).text(_.current+1)
			$('.total',_.numSt).text(_.itms.length+1)
		},
		nextFu:function(){
			var _=this
			if(++_.current<_.itms.length)
				_.changeFu(_.current)
			else
				_.buff=-1,
				_.changeFu(0)
		},
		prevFu:function(){
			var _=this
			if(--_.current>=0)
				_.changeFu(_.current)
			else
				_.buff=_.itms.length,
				_.changeFu(_.itms.length-1)
		},
		playFu:function(){
			var _=this
			
			_.progFu(function(){_.nextFu()})
		},
		pauseFu:function(){
			var _=this
			clearInterval(timer)
			_.progressBar.stop()
		},
		init:function(){
			var _=this
			_.preFu()
			
			if(_.playBu)
				_.playBu=$(_.playBu)
				.bind('click',function(){
					_.paused=_.paused?false:true
					if(!_.paused)
						_.playBu.removeClass(_.pauseCl),
						_.playFu()
					else
						_.playBu.addClass(_.pauseCl),
						_.pauseFu()
					return false
				})
			
			if(_.playBlock)
				_.bl=false
			if(_.pauseOnHover)
				_.holder.hover(function(){_.pauseFu()},function(){if(!_.paused)_.playFu()});
			if(_.prevBu)
				$(_.prevBu).bind(_.prevNextEv,function(){
					if(_.bl)
						return false
					else
						_.prevFu()
					return false
				})
			if(_.nextBu)
				$(_.nextBu).bind(_.prevNextEv,function(){
					if(_.bl)
						return false
					else
						_.nextFu()
					return false
				})
			if(_.slideShow)
				_.changeFu(_.show)
			if(_.banners)
				_.bannersFu()
			if(_.numStatus)
				_.numStatusFu()
			if(_.preload)
				_.preloadFu()
			_.preset_=_.preset
		}
	})
})(jQuery)
$(function(){
	$('.prev').hover(function(){
			$(this).stop().animate({width:'30px'},400,"easeOutElastic");
		},
		function(){
			$(this).stop().animate({width:'22px'},400,"easeOutElastic");
		}
	);
	$('.next').hover(function(){
			$(this).stop().animate({width:'30px',right:'111px'},400,"easeOutElastic");
		},
		function(){
			$(this).stop().animate({width:'22px',right:'119px'},400,"easeOutElastic");
		}
	);
})

function clone(obj){
	if(!obj||typeof obj!=typeof {})
		return obj
	if(obj instanceof Array)
		return [].concat(obj)
	var tmp=new obj.constructor(),
		i
	for(i in obj)
		if(obj.hasOwnProperty(i))
			tmp[i]=clone(obj[i])
	return tmp
}