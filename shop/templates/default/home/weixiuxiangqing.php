<?php defined('InShopNC') or exit('Access Invalid!'); ?>
<style type="text/css">
.centerauto{margin: 0 auto;width:1200px;}
.clear {	zoom: 1;}
.clear:after {	content: "";	clear: both;	display: block;}
.titlypp{width: 912px;height: 50px;line-height:50px;background-position:100px 0;background-size:233px 50px ;
background-image: url(http://www.nrwspt.com/data/upload/shop/common/wxxqbgf.png);padding-left: 176px;color: #21201b;font-size: 18px;
background-repeat: no-repeat;}
.titlexq{/*background-color: red;;*/width:812px;height: 290px;line-height: 290px;padding-left: 376px;}
.titlexq{/*background-color: green;opacity: 0.5;*/}
.titlexq dd{float: left;/*background-color: red;opacity: 0.5;*/}
.lx{background-image:url(http://www.nrwspt.com/data/upload/shop/common/pao.png);font-size: 28px;color: #202020;
background-repeat: no-repeat;background-size:442px 94px;width: 442px;height:104px;line-height: 104px;margin-top: 40px;}
.lx span{margin-left: 40px; display: inline-block;width: 150px;border-right: 1px solid#eeeeee;height: 50px;line-height: 50px; /*background-color:blue ;opacity: 0.5*/;}
.lx label{margin-left: 10px;}
.lx img{margin-left: 20px;}
.dingdan{width: 988px;font-size:18px;color: #212121;margin-left:146px;}
.ddgz p span{width: 50%;display:inline-block;margin: 0 auto;text-align: center;line-height: 48px;}
.ddgz p{background-color: #eeeeee;height: 48px;margin-top: 10px;}
.dingdan dl{width: 100%;}
.dingdan li{height: 80px;}
.ddgz dd{float: left;text-align: center;line-height:80px;}
.d2{width: 10%;}
.d3{width: 60%;}
.d1{width: 30%;}
.aniu{width: 988px;margin-bottom: 60px;}
.aniu input{color: #fff;font-size: 26px;height:48px;border: 0;/*background-color: #00bcd5;*/width: 344px;margin-left: 754px;}
#qxsubmit{margin-bottom: 20px;background-color: #00bcd5;/*display: none;*/}
.langan{width:600px;height: 200px;}
.box{position:absolute;width: 60%;margin-left: 300px;}
.ystep1{padding: 0;position:absolute;margin: 0 auto;width: 100%;overflow: hidden;zoom: 1;margin-top:40px;}



/*common css*/
.ystep-container {
  font-family: "Helvetica Neue",Helvetica,"Hiragino Sans GB","Wenquanyi Micro Hei","Microsoft Yahei",Arial,sans-serif;
  display: inline-block;  position: relative;  color: #000;}
.ystep-container ul {  list-style: none;}
.ystep-container ul,.ystep-container li,.ystep-container p {  margin: 0;  padding: 0;}

/*size css*/
.ystep-sm {  width: 360px;  height: 30px;  font-size: 12px;  line-height: 1;}
.ystep-lg {  width: 700px;  height: 60px;  font-size: 18px;  /*line-height: .3;*/}
/*small size css*/
.ystep-sm .ystep-container-steps {  position: absolute;  top: 2px;  cursor: pointer;  z-index: 10;}
.ystep-sm li {  float: left;  width: 65px;  height: 50px;}
.ystep-sm .ystep-step-done {  background-position: -119px -76px;}
.ystep-sm .ystep-step-undone {  background-position: -60px -76px;}
.ystep-sm .ystep-step-active {  background-position: -182px -76px;}
.ystep-sm .ystep-progress {  width: 260px;  height: 3px;  position: absolute;  top: 30px;  left: 8px;  float: left;  margin-right: 10px; overflow: hidden;}
.ystep-sm .ystep-progress-bar {  width: 260px;  height: 6px;  background: #e4e4e4;  display: inline-block;  float: left;}
.ystep-sm .ystep-progress-highlight {  height: 6px;	display: block;}
/*large size css*/
.ystep-lg .ystep-container-steps {  position: absolute;  top: 2px;  cursor: pointer;  z-index: 10;}
.ystep-lg li {  float: left;  width: 100px;  height: 85px;}
.ystep-lg .ystep-step-done {  background-position: -278px -132px;}
.ystep-lg .ystep-step-undone {  background-position: -137px -131px;}
.ystep-lg .ystep-step-active {  background-position: -414px -131px;}
.ystep-lg .ystep-progress {
	  width: 400px;  height: 10px; 
	   position: absolute;  top: 30px;  
	   left: 15px;  float: left;  margin-right: 10px;  overflow: hidden;}
.ystep-lg .ystep-progress-bar {  width: 400px;  height: 20px;  background: #e4e4e4;  display: inline-block;  float: left;
}.ystep-lg .ystep-progress-highlight {  height: 20px;	display: block;}
/*green css*/
.ystep-green .ystep-step-done {  
	background-image: url(http://www.nrwspt.com/data/upload/shop/common/pointes_green.png); 
	 background-repeat: no-repeat;
	 }
.ystep-green .ystep-step-undone {  
	background-image:url(http://www.nrwspt.com/data/upload/shop/common/pointes_green.png);  
	 background-repeat: no-repeat; 
	  color: #9c9a9b;}
.ystep-green .ystep-step-active {
	  background-image:url(http://www.nrwspt.com/data/upload/shop/common/pointes_green.png); 
	    background-repeat: no-repeat; 
	     color: #3d8e15;}
.ystep-green .ystep-progress-highlight { 
	 background: #89bc65;}
/*blue css*/
.ystep-blue .ystep-step-done {   
	background-image: url(http://www.nrwspt.com/data/upload/shop/common/pointes_blue.png);  
	 background-repeat: no-repeat;
	 }
.ystep-blue .ystep-step-undone {  
	background-image:url(http://www.nrwspt.com/data/upload/shop/common/pointes_blue.png); 
	 background-repeat: no-repeat;  
	 color: #9c9a9b;}
.ystep-blue .ystep-step-active { 
	 background-image: url(http://www.nrwspt.com/data/upload/shop/common/pointes_blue.png);  
	 background-repeat: no-repeat; 
	  color: #3276b1;}
.ystep-blue .ystep-progress-highlight { 
	 background: #60baff;
	 }
/*popover css*/*/
.popover {
  font-family: "Helvetica Neue",Helvetica,"Hiragino Sans GB","Wenquanyi Micro Hei","Microsoft Yahei",Arial,sans-serif;
  position: absolute;
  top: 0;
  left: 0;
  z-index: 1010;
  display: none;
  max-width: 276px;
  padding: 1px;
  text-align: left;
  white-space: normal;
  background-color: #ffffff;
  border: 1px solid #cccccc;
  border: 1px solid rgba(0, 0, 0, 0.2);
  border-radius: 6px;
  -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
          box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
  background-clip: padding-box;
}

.popover.top {
  margin-top: -10px;
}

.popover.right {
  margin-left: 10px;
}

.popover.bottom {
  margin-top: 10px;
}

.popover.left {
  



.popover-content {
  padding: 4px 10px;
  font-size: 12px;
}

.popover .arrow,
.popover .arrow:after {
  position: absolute;
  display: block;
  width: 0;
  height: 0;
  border-color: transparent;
  border-style: solid;
}

.popover .arrow {
  border-width: 11px;
}

.popover .arrow:after {
  border-width: 10px;
  content: "";
}

.popover.top .arrow {
  bottom: -11px;
  left: 50%;
  margin-left: -11px;
  border-top-color: #999999;
  border-top-color: rgba(0, 0, 0, 0.25);
  border-bottom-width: 0;
}

.popover.top .arrow:after {
  bottom: 1px;
  margin-left: -10px;
  border-top-color: #ffffff;
  border-bottom-width: 0;
  content: " ";
}

.popover.right .arrow {
  top: 50%;
  left: -11px;
  margin-top: -11px;
  border-right-color: #999999;
  border-right-color: rgba(0, 0, 0, 0.25);
  border-left-width: 0;
}

.popover.right .arrow:after {
  bottom: -10px;
  left: 1px;
  border-right-color: #ffffff;
  border-left-width: 0;
  content: " ";
}

.popover.bottom .arrow {
  top: -11px;
  left: 50%;
  margin-left: -11px;
  border-bottom-color: #999999;
  border-bottom-color: rgba(0, 0, 0, 0.25);
  border-top-width: 0;
}

.popover.bottom .arrow:after {
  top: 1px;
  margin-left: -10px;
  border-bottom-color: #ffffff;
  border-top-width: 0;
  content: " ";
}

.popover.left .arrow {
  top: 50%;
  right: -11px;
  margin-top: -11px;
  border-left-color: #999999;
  border-left-color: rgba(0, 0, 0, 0.25);
  border-right-width: 0;
}

.popover.left .arrow:after {
  right: 1px;
  bottom: -10px;
  border-left-color: #ffffff;
  border-right-width: 0;
  content: " ";
}


</style>
<script>
	/* ========================================================================
 * Bootstrap: tooltip.js v3.0.3
 * http://getbootstrap.com/javascript/#tooltip
 * Inspired by the original jQuery.tipsy by Jason Frame
 * ========================================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ======================================================================== */


+function ($) { "use strict";

  // TOOLTIP PUBLIC CLASS DEFINITION
  // ===============================

  var Tooltip = function (element, options) {
    this.type       =
    this.options    =
    this.enabled    =
    this.timeout    =
    this.hoverState =
    this.$element   = null

    this.init('tooltip', element, options)
  }

  Tooltip.DEFAULTS = {
    animation: true
  , placement: 'top'
  , selector: false
  , template: '<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
  , trigger: 'hover focus'
  , title: ''
  , delay: 0
  , html: false
  , container: false
  }

  Tooltip.prototype.init = function (type, element, options) {
    this.enabled  = true
    this.type     = type
    this.$element = $(element)
    this.options  = this.getOptions(options)

    var triggers = this.options.trigger.split(' ')

    for (var i = triggers.length; i--;) {
      var trigger = triggers[i]

      if (trigger == 'click') {
        this.$element.on('click.' + this.type, this.options.selector, $.proxy(this.toggle, this))
      } else if (trigger != 'manual') {
        var eventIn  = trigger == 'hover' ? 'mouseenter' : 'focus'
        var eventOut = trigger == 'hover' ? 'mouseleave' : 'blur'

        this.$element.on(eventIn  + '.' + this.type, this.options.selector, $.proxy(this.enter, this))
        this.$element.on(eventOut + '.' + this.type, this.options.selector, $.proxy(this.leave, this))
      }
    }

    this.options.selector ?
      (this._options = $.extend({}, this.options, { trigger: 'manual', selector: '' })) :
      this.fixTitle()
  }

  Tooltip.prototype.getDefaults = function () {
    return Tooltip.DEFAULTS
  }

  Tooltip.prototype.getOptions = function (options) {
    options = $.extend({}, this.getDefaults(), this.$element.data(), options)

    if (options.delay && typeof options.delay == 'number') {
      options.delay = {
        show: options.delay
      , hide: options.delay
      }
    }

    return options
  }

  Tooltip.prototype.getDelegateOptions = function () {
    var options  = {}
    var defaults = this.getDefaults()

    this._options && $.each(this._options, function (key, value) {
      if (defaults[key] != value) options[key] = value
    })

    return options
  }

  Tooltip.prototype.enter = function (obj) {
    var self = obj instanceof this.constructor ?
      obj : $(obj.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type)

    clearTimeout(self.timeout)

    self.hoverState = 'in'

    if (!self.options.delay || !self.options.delay.show) return self.show()

    self.timeout = setTimeout(function () {
      if (self.hoverState == 'in') self.show()
    }, self.options.delay.show)
  }

  Tooltip.prototype.leave = function (obj) {
    var self = obj instanceof this.constructor ?
      obj : $(obj.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type)

    clearTimeout(self.timeout)

    self.hoverState = 'out'

    if (!self.options.delay || !self.options.delay.hide) return self.hide()

    self.timeout = setTimeout(function () {
      if (self.hoverState == 'out') self.hide()
    }, self.options.delay.hide)
  }

  Tooltip.prototype.show = function () {
    var e = $.Event('show.bs.'+ this.type)

    if (this.hasContent() && this.enabled) {
      this.$element.trigger(e)

      if (e.isDefaultPrevented()) return

      var $tip = this.tip()

      this.setContent()

      if (this.options.animation) $tip.addClass('fade')

      var placement = typeof this.options.placement == 'function' ?
        this.options.placement.call(this, $tip[0], this.$element[0]) :
        this.options.placement

      var autoToken = /\s?auto?\s?/i
      var autoPlace = autoToken.test(placement)
      if (autoPlace) placement = placement.replace(autoToken, '') || 'top'

      $tip
        .detach()
        .css({ top: 0, left: 0, display: 'block' })
        .addClass(placement)

      this.options.container ? $tip.appendTo(this.options.container) : $tip.insertAfter(this.$element)

      var pos          = this.getPosition()
      var actualWidth  = $tip[0].offsetWidth
      var actualHeight = $tip[0].offsetHeight

      if (autoPlace) {
        var $parent = this.$element.parent()

        var orgPlacement = placement
        var docScroll    = document.documentElement.scrollTop || document.body.scrollTop
        var parentWidth  = this.options.container == 'body' ? window.innerWidth  : $parent.outerWidth()
        var parentHeight = this.options.container == 'body' ? window.innerHeight : $parent.outerHeight()
        var parentLeft   = this.options.container == 'body' ? 0 : $parent.offset().left

        placement = placement == 'bottom' && pos.top   + pos.height  + actualHeight - docScroll > parentHeight  ? 'top'    :
                    placement == 'top'    && pos.top   - docScroll   - actualHeight < 0                         ? 'bottom' :
                    placement == 'right'  && pos.right + actualWidth > parentWidth                              ? 'left'   :
                    placement == 'left'   && pos.left  - actualWidth < parentLeft                               ? 'right'  :
                    placement

        $tip
          .removeClass(orgPlacement)
          .addClass(placement)
      }

      var calculatedOffset = this.getCalculatedOffset(placement, pos, actualWidth, actualHeight)

      this.applyPlacement(calculatedOffset, placement)
      this.$element.trigger('shown.bs.' + this.type)
    }
  }

  Tooltip.prototype.applyPlacement = function(offset, placement) {
    var replace
    var $tip   = this.tip()
    var width  = $tip[0].offsetWidth
    var height = $tip[0].offsetHeight

    // manually read margins because getBoundingClientRect includes difference
    var marginTop = parseInt($tip.css('margin-top'), 10)
    var marginLeft = parseInt($tip.css('margin-left'), 10)

    // we must check for NaN for ie 8/9
    if (isNaN(marginTop))  marginTop  = 0
    if (isNaN(marginLeft)) marginLeft = 0

    offset.top  = offset.top  + marginTop
    offset.left = offset.left + marginLeft

    $tip
      .offset(offset)
      .addClass('in')

    // check to see if placing tip in new offset caused the tip to resize itself
    var actualWidth  = $tip[0].offsetWidth
    var actualHeight = $tip[0].offsetHeight

    if (placement == 'top' && actualHeight != height) {
      replace = true
      offset.top = offset.top + height - actualHeight
    }

    if (/bottom|top/.test(placement)) {
      var delta = 0

      if (offset.left < 0) {
        delta       = offset.left * -2
        offset.left = 0

        $tip.offset(offset)

        actualWidth  = $tip[0].offsetWidth
        actualHeight = $tip[0].offsetHeight
      }

      this.replaceArrow(delta - width + actualWidth, actualWidth, 'left')
    } else {
      this.replaceArrow(actualHeight - height, actualHeight, 'top')
    }

    if (replace) $tip.offset(offset)
  }

  Tooltip.prototype.replaceArrow = function(delta, dimension, position) {
    this.arrow().css(position, delta ? (50 * (1 - delta / dimension) + "%") : '')
  }

  Tooltip.prototype.setContent = function () {
    var $tip  = this.tip()
    var title = this.getTitle()

    $tip.find('.tooltip-inner')[this.options.html ? 'html' : 'text'](title)
    $tip.removeClass('fade in top bottom left right')
  }

  Tooltip.prototype.hide = function () {
    var that = this
    var $tip = this.tip()
    var e    = $.Event('hide.bs.' + this.type)

    function complete() {
      if (that.hoverState != 'in') $tip.detach()
    }

    this.$element.trigger(e)

    if (e.isDefaultPrevented()) return

    $tip.removeClass('in')

    $.support.transition && this.$tip.hasClass('fade') ?
      $tip
        .one($.support.transition.end, complete)
        .emulateTransitionEnd(150) :
      complete()

    this.$element.trigger('hidden.bs.' + this.type)

    return this
  }

  Tooltip.prototype.fixTitle = function () {
    var $e = this.$element
    if ($e.attr('title') || typeof($e.attr('data-original-title')) != 'string') {
      $e.attr('data-original-title', $e.attr('title') || '').attr('title', '')
    }
  }

  Tooltip.prototype.hasContent = function () {
    return this.getTitle()
  }

  Tooltip.prototype.getPosition = function () {
    var el = this.$element[0]
    return $.extend({}, (typeof el.getBoundingClientRect == 'function') ? el.getBoundingClientRect() : {
      width: el.offsetWidth
    , height: el.offsetHeight
    }, this.$element.offset())
  }

  Tooltip.prototype.getCalculatedOffset = function (placement, pos, actualWidth, actualHeight) {
    return placement == 'bottom' ? { top: pos.top + pos.height,   left: pos.left + pos.width / 2 - actualWidth / 2  } :
           placement == 'top'    ? { top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2 - 20  } :
           placement == 'left'   ? { top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth } :
        /* placement == 'right' */ { top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width   }
  }

  Tooltip.prototype.getTitle = function () {
    var title
    var $e = this.$element
    var o  = this.options

    title = $e.attr('data-original-title')
      || (typeof o.title == 'function' ? o.title.call($e[0]) :  o.title)

    return title
  }

  Tooltip.prototype.tip = function () {
    return this.$tip = this.$tip || $(this.options.template)
  }

  Tooltip.prototype.arrow = function () {
    return this.$arrow = this.$arrow || this.tip().find('.tooltip-arrow')
  }

  Tooltip.prototype.validate = function () {
    if (!this.$element[0].parentNode) {
      this.hide()
      this.$element = null
      this.options  = null
    }
  }

  Tooltip.prototype.enable = function () {
    this.enabled = true
  }

  Tooltip.prototype.disable = function () {
    this.enabled = false
  }

  Tooltip.prototype.toggleEnabled = function () {
    this.enabled = !this.enabled
  }

  Tooltip.prototype.toggle = function (e) {
    var self = e ? $(e.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type) : this
    self.tip().hasClass('in') ? self.leave(self) : self.enter(self)
  }

  Tooltip.prototype.destroy = function () {
    this.hide().$element.off('.' + this.type).removeData('bs.' + this.type)
  }


  // TOOLTIP PLUGIN DEFINITION
  // =========================

  var old = $.fn.tooltip

  $.fn.tooltip = function (option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.tooltip')
      var options = typeof option == 'object' && option

      if (!data) $this.data('bs.tooltip', (data = new Tooltip(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.tooltip.Constructor = Tooltip


  // TOOLTIP NO CONFLICT
  // ===================

  $.fn.tooltip.noConflict = function () {
    $.fn.tooltip = old
    return this
  }

}(jQuery);

/* ========================================================================
 * Bootstrap: popover.js v3.0.3
 * http://getbootstrap.com/javascript/#popovers
 * ========================================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ======================================================================== */


+function ($) { "use strict";

  // POPOVER PUBLIC CLASS DEFINITION
  // ===============================

  var Popover = function (element, options) {
    this.init('popover', element, options)
  }

  if (!$.fn.tooltip) throw new Error('Popover requires tooltip.js')

  Popover.DEFAULTS = $.extend({} , $.fn.tooltip.Constructor.DEFAULTS, {
    placement: 'right'
  , trigger: 'click'
  , content: ''
  , template: '<div class="popover"><div class="arrow"></div></h3><div class="popover-content"></div></div>'
  })


  // NOTE: POPOVER EXTENDS tooltip.js
  // ================================

  Popover.prototype = $.extend({}, $.fn.tooltip.Constructor.prototype)

  Popover.prototype.constructor = Popover

  Popover.prototype.getDefaults = function () {
    return Popover.DEFAULTS
  }

  Popover.prototype.setContent = function () {
    var $tip    = this.tip()
    var title   = this.getTitle()
    var content = this.getContent()

    $tip.find('.popover-title')[this.options.html ? 'html' : 'text'](title)
    $tip.find('.popover-content')[this.options.html ? 'html' : 'text'](content)

    $tip.removeClass('fade top bottom left right in')

    // IE8 doesn't accept hiding via the `:empty` pseudo selector, we have to do
    // this manually by checking the contents.
    if (!$tip.find('.popover-title').html()) $tip.find('.popover-title').hide()
  }

  Popover.prototype.hasContent = function () {
    return this.getTitle() || this.getContent()
  }

  Popover.prototype.getContent = function () {
    var $e = this.$element
    var o  = this.options

    return $e.attr('data-content')
      || (typeof o.content == 'function' ?
            o.content.call($e[0]) :
            o.content)
  }

  Popover.prototype.arrow = function () {
    return this.$arrow = this.$arrow || this.tip().find('.arrow')
  }

  Popover.prototype.tip = function () {
    if (!this.$tip) this.$tip = $(this.options.template)
    return this.$tip
  }


  // POPOVER PLUGIN DEFINITION
  // =========================

  var old = $.fn.popover

  $.fn.popover = function (option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.popover')
      var options = typeof option == 'object' && option

      if (!data) $this.data('bs.popover', (data = new Popover(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.popover.Constructor = Popover


  // POPOVER NO CONFLICT
  // ===================

  $.fn.popover.noConflict = function () {
    $.fn.popover = old
    return this
  }

}(jQuery);

(function($){
  $.fn.extend({
    //初始化
    loadStep: function(params){
      
      //基础框架
      var baseHtml =  "<div class='ystep-container'>"+
                        "<ul class='ystep-container-steps'>"+
                        "</ul>"+
                        "<div class='ystep-progress'>"+
                          "<p class='ystep-progress-bar'>"+
                            "<span class='ystep-progress-highlight' style='width:0%'>"+
                            "</span>"+
                          "</p>"+
                        "</div>"+
                      "</div>";
      //步骤框架
      var stepHtml = "<li class='ystep-step ystep-step-undone' data-container='body' data-toggle='popover' data-placement='top' data-title=''  >"+
                     "</li>";
      //决策器
      var logic = {
        size: {
          small: function($html){
            var stepCount = $html.find("li").length-1,
            containerWidth = (stepCount*65+100)+"px",
            progressWidth = (stepCount*65)+"px";
            $html.css({
              width: containerWidth
            });
            $html.find(".ystep-progress").css({
              width: progressWidth
            });
            $html.find(".ystep-progress-bar").css({
              width: progressWidth
            });
            $html.addClass("ystep-sm");
          },
          large: function($html){
            var stepCount = $html.find("li").length-1,
            containerWidth = (stepCount*100+120)+"px",
            progressWidth = (stepCount*100)+"px";
            $html.css({
              width: containerWidth
            });
            $html.find(".ystep-progress").css({
              width: progressWidth
            });
            $html.find(".ystep-progress-bar").css({
              width: progressWidth
            });
            $html.addClass("ystep-lg"); 
          }
        },
        color: {
          green: function($html){
            $html.addClass("ystep-green");
          },
          blue: function($html){
            $html.addClass("ystep-blue");
          }
        }
      };
      
      //支持填充多个步骤容器
      $(this).each(function(i,n){
        var $baseHtml = $(baseHtml),
        $stepHtml = $(stepHtml),
        $ystepContainerSteps = $baseHtml.find(".ystep-container-steps"),
        arrayLength = 0,
        $n = $(n),
        i=0;
        
        //步骤
        arrayLength = params.steps.length;
        for(i=0;i<arrayLength;i++){
          var _s = params.steps[i];
          //构造步骤html
          $stepHtml.attr("data-title",_s.title);
          $stepHtml.attr("data-content",_s.content);
          $stepHtml.text(_s.title);
          //将步骤插入到步骤列表中
          $ystepContainerSteps.append($stepHtml);
          //重置步骤
          $stepHtml = $(stepHtml);
        }        
        //尺寸
        logic.size[params.size||"large"]($baseHtml);
        //配色
        logic.color[params.color||"blue"]($baseHtml);
        
        //插入到容器中
        $n.append($baseHtml);
        //渲染提示气泡
        $n.find(".ystep-step").popover({});
        //默认执行第一个步骤
        $n.setStep(1);
      });
    },
    //跳转到指定步骤
    setStep: function(step) {
      $(this).each(function(i,n){
        //获取当前容器下所有的步骤
        var $steps = $(n).find(".ystep-container").find("li");
        var $progress =$(n).find(".ystep-container").find(".ystep-progress-highlight");
        //判断当前步骤是否在范围内
        if(1<=step && step<=$steps.length){
          //更新进度
          var scale = "%";
          scale = Math.round((step-1)*100/($steps.length-1))+scale;
          $progress.animate({
            width: scale
          },{
            speed: 1000,
            done: function() {
              //移动节点
              $steps.each(function(j,m){
                var _$m = $(m);
                var _j = j+1;
                if(_j < step){
                  _$m.attr("class","ystep-step-done");
                }else if(_j === step){
                  _$m.attr("class","ystep-step-active");
                }else if(_j > step){
                  _$m.attr("class","ystep-step-undone");
                }
              });
            }
          });
        }else{
          return false;
        }
      });
    },
    //获取当前步骤
    getStep: function() {
      var result = [];
      
      $(this)._searchStep(function(i,j,n,m){
        result.push(j+1);
      });
      
      if(result.length == 1) {
        return result[0];
      }else{
        return result;
      }
    },
    //下一个步骤
    nextStep: function() {
      $(this)._searchStep(function(i,j,n,m){
        $(n).setStep(j+2);
      });
    },
    //上一个步骤
    prevStep: function() {
      $(this)._searchStep(function(i,j,n,m){
        $(n).setStep(j);
      });
    },
    //通用节点查找
    _searchStep: function (callback) {
      $(this).each(function(i,n){
        var $steps = $(n).find(".ystep-container").find("li");
        $steps.each(function(j,m){
          //判断是否为活动步骤
          if($(m).attr("class") === "ystep-step-active"){
            if(callback){
              callback(i,j,n,m);
            }
            return false;
          }
        });
      });
    }
  });
})(jQuery);



$(document).ready(function(){
	//根据jQuery选择器找到需要加载ystep的容器
		//loadStep 方法可以初始化ystep
		$(".ystep1").loadStep({
			//ystep的外观大小
			//可选值：small,large
			size: "large",
			//ystep配色方案
			//可选值：green,blue
			color: "blue",
			//ystep中包含的步骤
			steps: [{
				//步骤名称
				title: "下单"
				//步骤内容(鼠标移动到本步骤节点时，会提示该内容)
		
			}, {
				title: "师傅接单"

			}, {
				title: "师傅出发"

			}, {
				title: "维修中"

			}, {
				title: "完成"

			}]
		});
		var i=$("#ddgz").find("li").length;
		$(".ystep1").setStep(i);
		if(i==5){
			$("#submit").removeAttr("disabled");
			$("#submit").css("background-color","#00bcd4")
		};
		/*遮幕*/
		$("#qxbtn").click(function(){
			$("#zhemu").show();
			$("#quxiaodiv").show();
		});
		$("#close").click(function(){
			$("#zhemu").hide();
			$("#quxiaodiv").hide();
		});
		$("#fbtn").click(function(){
			$("#zhemu").hide();
			$("#quxiaodiv").hide();
		});
		
})

</script>
<div class="warp-all">
	<div class="mainbox">
		<div class="nch-breadcrumb wrapper">
			<i class="icon-home">
			</i>
			<span>
				<a href="http://b2bc.zm-y.com/shop">
					首页
				</a></span>
			<span class="arrow">></span>
			<span>我的订单</span>
			<span class="arrow">></span>
			<span>维修详情</span>
		</div>
		
		<div class="centerauto">
			<div class="titlypp">
			<p>已为您匹配师傅</p>
		</div>
		<div class="titlexq">
			<dl>
				<dd><img src="http://www.nrwspt.com/data/upload/shop/common/photo.png"></dd>
				<dd class="lx">	<span>张师傅</span><img src="http://www.nrwspt.com/data/upload/shop/common/wxxqtel.png"><label>13988889984</label></dd>
			</dl>
		</div>
		<!--进度条-->
		<div class="langan">
			<div class="box">
				<div class="ystep1">
				</div>
			</div>
		</div>
		<!---->

<!--订单详情-->
<div class="titlypp">
			<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp订单跟踪</p>
		</div>
				<div class="dingdan">
					<!--<p class="dg"><span>订单跟踪</span></p>-->
					<!--详情列表-->
					<div class="ddgz">
						<p><span>处理时间</span><span>处理信息</span></p>
						<ul id="ddgz">
							<li>
								<dl>
									<dd class="d1">2016-06-01</dd>
									<dd class="d2">15:30</dd>
									<dd class="d3"> 下单</dd>
								</dl>
							</li>
							<!--2栏-->
							<li>
								<dl>
									<dd class="d1">2016-06-01</dd>
									<dd class="d2">15:30</dd>
									<dd class="d3">孙师傅已接单12345678901</dd>
								</dl>
							</li>
							<!--3-->
							<li>
								<dl>
									<dd class="d1">2016-06-01</dd>
									<dd class="d2">15:30</dd>
									<dd class="d3"> 维修师傅已出发</dd>
								</dl>
							</li>
							<!--4-->
							<li>
								<dl>
									<dd class="d1">2016-06-01</dd>
									<dd class="d2">15:30</dd>
									<dd class="d3">维修中</dd>
								</dl>
							</li>

							<!--5-->
							<li>
								<dl>
									<dd class="d1">2016-06-01</dd>
									<dd class="d2">15:30</dd>
									<dd class="d3">完成</dd>
								</dl>
							</li>

						</ul>
					</div>
				</div>
				<!--完成按钮-->
				<div class="aniu">
					<input id="qxsubmit" type="submit" value="取消订单"  />
					<input id="submit" type="submit" value="维修完成，进行评价" disabled="disabled" />
				</div>
			</div>		
		</div>
		
	</div>
</div>