/* ==========================================================
 * bootstrap-carousel.js v2.3.1
 * http://twitter.github.com/bootstrap/javascript.html#carousel
 * ==========================================================
 * Copyright 2012 Twitter, Inc.
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
 * ========================================================== */


!function ($) {

  "use strict"; // jshint ;_;


 /* CAROUSEL CLASS DEFINITION
  * ========================= */

  var Carousel = function (element, options) {
    this.$element = $(element)
    this.$indicators = this.$element.find('.carousel-indicators')
    this.options = options
    this.options.pause == 'hover' && this.$element
      .on('mouseenter', $.proxy(this.pause, this))
      .on('mouseleave', $.proxy(this.cycle, this))
  }

  Carousel.prototype = {

    cycle: function (e) {
      if (!e) this.paused = false
      if (this.interval) clearInterval(this.interval);
      this.options.interval
        && !this.paused
        && (this.interval = setInterval($.proxy(this.next, this), this.options.interval))
      return this
    }

  , getActiveIndex: function () {
      this.$active = this.$element.find('.item.active')
      this.$items = this.$active.parent().children()
      return this.$items.index(this.$active)
    }

  , to: function (pos) {
      var activeIndex = this.getActiveIndex()
        , that = this

      if (pos > (this.$items.length - 1) || pos < 0) return

      if (this.sliding) {
        return this.$element.one('slid', function () {
          that.to(pos)
        })
      }

      if (activeIndex == pos) {
        return this.pause().cycle()
      }

      return this.slide(pos > activeIndex ? 'next' : 'prev', $(this.$items[pos]))
    }

  , pause: function (e) {
      if (!e) this.paused = true
      if (this.$element.find('.next, .prev').length && $.support.transition.end) {
        this.$element.trigger($.support.transition.end)
        this.cycle(true)
      }
      clearInterval(this.interval)
      this.interval = null
      return this
    }
	/* modified for waw slider */
  , next: function (postSlideCallback) {
	  this.callback = postSlideCallback;
      if (this.sliding) return
      return this.slide('next')
    }
	/* modified for waw slider */
  , prev: function (postSlideCallback) {
	  this.callback = postSlideCallback;
      if (this.sliding) return
      return this.slide('prev')
    }
	/* modified for waw slider */
  , slide: function (type, next) {
      var $active = this.$element.find('.item.active')
        , $next = next || $active[type]()
        , isCycling = this.interval
        , direction = type == 'next' ? 'left' : 'right'
        , fallback  = type == 'next' ? 'first' : 'last'
        
        , directionFrom = type == 'next' ? 'right' : 'left'
        , directionToClass   = 'hover-' + direction
        , directionFromClass = 'hover-' + directionFrom 
        , directionToHideClass   = 'hide-' + direction
        , directionFromHideClass = 'hide-' + directionFrom 
        , $toRemove          = this.$element.find("." + directionToClass)
        , $toShow            = this.$element.find("." + directionFromHideClass)
        , $toPutFirst        = this.$element.find("." + directionToHideClass)
		    , mathOperand = type == 'next' ? '-' : ''
        
        , that = this
        , e
        

      this.sliding = true

      isCycling && this.pause()

      $next = $next.length ? $next : this.$element.find('.item')[fallback]()

      e = $.Event('slide', {
        relatedTarget: $next[0]
      , direction: direction
      })

      if ($next.hasClass('active')) return

      if (this.$indicators.length) {
        this.$indicators.find('.active').removeClass('active')
        this.$element.one('slid', function () {
          var $nextIndicator = $(that.$indicators.children()[that.getActiveIndex()])
          $nextIndicator && $nextIndicator.addClass('active')
        })
      }

      if ($.support.transition && this.$element.hasClass('slide')) {
        this.$element.trigger(e)
        if (e.isDefaultPrevented()) return
        $next.addClass(type)
        $next[0].offsetWidth // force reflow

        $toRemove.addClass(directionToHideClass);
    		$active.addClass(direction).addClass(directionToClass);
    		$next.addClass(direction).removeClass(directionFromClass);

        $toShow.removeClass(directionFromHideClass);
        $toPutFirst.hide();
        $toPutFirst.removeClass(directionToClass + " " + directionToHideClass);
        $toPutFirst.addClass(directionFromClass + " " + directionFromHideClass);
               
        this.$element.one($.support.transition.end, function () {
		
		      $next.removeClass([type, direction].join(' ')).addClass('active');
          $active.removeClass(['active', direction].join(' '));
          $toPutFirst.show();
    		  that.callback();
    		  //console.log("post-slide");
          that.sliding = false
          setTimeout(function () { that.$element.trigger('slid') }, 1000);
        })
      } else {
        this.$element.trigger(e)
        if (e.isDefaultPrevented()) return
        $active.removeClass('active')
		
    		$toRemove.addClass(directionToHideClass);
        $active.addClass(directionToClass);
    		$next.removeClass(directionFromClass);
    		$toShow.removeClass(directionFromHideClass);

        $toPutFirst.hide();
        $toPutFirst.removeClass(directionToClass + " " + directionToHideClass);
        $toPutFirst.addClass(directionFromClass + " " + directionFromHideClass);
        $toPutFirst.show();
		
        $next.addClass('active');
    		this.callback();
    		//console.log("post-slide");
    		this.sliding = false
        this.$element.trigger('slid');
      }

      isCycling && this.cycle()
      return this
    }

  }


 /* CAROUSEL PLUGIN DEFINITION
  * ========================== */

  var old = $.fn.carousel

  $.fn.carousel = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('carousel')
        , options = $.extend({}, $.fn.carousel.defaults, typeof option == 'object' && option)
        , action = typeof option == 'string' ? option : options.slide
      if (!data) $this.data('carousel', (data = new Carousel(this, options)))
      if (typeof option == 'number') data.to(option)
      else if (action) data[action](options.postSlideCallback)
      else if (options.interval) data.pause().cycle()
    })
  }

  $.fn.carousel.defaults = {
    interval: 5000
  , pause: 'hover'
  }

  $.fn.carousel.Constructor = Carousel


 /* CAROUSEL NO CONFLICT
  * ==================== */

  $.fn.carousel.noConflict = function () {
    $.fn.carousel = old
    return this
  }

 /* CAROUSEL DATA-API
  * ================= */

  $(document).on('click.carousel.data-api', '[data-slide], [data-slide-to]', function (e, callback) {
    var $this = $(this), href
      , $target = $($this.attr('data-target') || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '')) //strip for ie7
      , options = $.extend({}, $target.data(), $this.data(), callback)
      , slideIndex

    $target.carousel(options)

    if (slideIndex = $this.attr('data-slide-to')) {
      $target.data('carousel').pause().to(slideIndex).cycle()
    }

    e.preventDefault()
  })

}(window.jQuery);