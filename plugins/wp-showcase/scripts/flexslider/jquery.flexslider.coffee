###
jQuery FlexSlider v2.1
http://www.woothemes.com/flexslider/

Copyright 2012 WooThemes
Free to use under the GPLv2 license.
http://www.gnu.org/licenses/gpl-2.0.html

Contributing author: Tyler Smith (@mbmufffin)
###
(($) ->
	
	#FlexSlider: Object Instance
	$.flexslider = (el, options) ->
		slider = $(el)
		vars = $.extend({}, $.flexslider.defaults, options)
		namespace = vars.namespace
		touch = ("ontouchstart" of window) or window.DocumentTouch and document instanceof DocumentTouch
		eventType = (if (touch) then "touchend" else "click")
		vertical = vars.direction is "vertical"
		reverse = vars.reverse
		carousel = (vars.itemWidth > 0)
		fade = true #vars.animation is "fade"
		asNav = vars.asNavFor isnt ""
		methods = {}
		
		# Store a reference to the slider object
		$.data el, "flexslider", slider
		
		# Private slider methods
		methods =
			init: ->
				slider.animating = false
				slider.currentSlide = vars.startAt
				slider.animatingTo = slider.currentSlide
				slider.atEnd = (slider.currentSlide is 0 or slider.currentSlide is slider.last)
				slider.containerSelector = vars.selector.substr(0, vars.selector.search(" "))
				slider.slides = $(vars.selector, slider)
				slider.container = $(slider.containerSelector, slider)
				slider.count = slider.slides.length
				
				# SYNC:
				slider.syncExists = $(vars.sync).length > 0
				
				# SLIDE:
				vars.animation = "swing"  if vars.animation is "slide"
				slider.prop = (if (vertical) then "top" else "marginLeft")
				slider.args = {}
				
				# SLIDESHOW:
				slider.manualPause = false
				
				# TOUCH/USECSS:
				slider.transitions = not vars.video and not fade and vars.useCSS and (->
					obj = document.createElement("div")
					props = ["perspectiveProperty", "WebkitPerspective", "MozPerspective", "OPerspective", "msPerspective"]
					for i of props
						if obj.style[props[i]] isnt `undefined`
							slider.pfx = props[i].replace("Perspective", "").toLowerCase()
							slider.prop = "-" + slider.pfx + "-transform"
							return true
					false
				)
				
				# CONTROLSCONTAINER:
				slider.controlsContainer = $(vars.controlsContainer).length > 0 and $(vars.controlsContainer)  if vars.controlsContainer isnt ""
				
				# MANUAL:
				slider.manualControls = $(vars.manualControls).length > 0 and $(vars.manualControls)  if vars.manualControls isnt ""
				
				# RANDOMIZE:
				if vars.randomize
					slider.slides.sort ->
						Math.round(Math.random()) - 0.5

					slider.container.empty().append slider.slides
				slider.doMath()
				
				# ASNAV:
				methods.asNav.setup()  if asNav
				
				# INIT
				slider.setup "init"
				
				# CONTROLNAV:
				methods.controlNav.setup()  if vars.controlNav
				
				# DIRECTIONNAV:
				methods.directionNav.setup()  if vars.directionNav
				
				# KEYBOARD:
				if vars.keyboard and ($(slider.containerSelector).length is 1 or vars.multipleKeyboard)
					$(document).bind "keyup", (event) ->
						keycode = event.keyCode
						if not slider.animating and (keycode is 39 or keycode is 37)
							target = (if (keycode is 39) then slider.getTarget("next") else (if (keycode is 37) then slider.getTarget("prev") else false))
							slider.flexAnimate target, vars.pauseOnAction

				
				# MOUSEWHEEL:
				if vars.mousewheel
					slider.bind "mousewheel", (event, delta, deltaX, deltaY) ->
						event.preventDefault()
						target = (if (delta < 0) then slider.getTarget("next") else slider.getTarget("prev"))
						slider.flexAnimate target, vars.pauseOnAction

				
				# PAUSEPLAY
				methods.pausePlay.setup()  if vars.pausePlay
				
				# SLIDSESHOW
				if vars.slideshow
					if vars.pauseOnHover
						slider.hover (->
							slider.pause()  if not slider.manualPlay and not slider.manualPause
						), ->
							slider.play()  if not slider.manualPause and not slider.manualPlay
					
					# initialize animation
					(if (vars.initDelay > 0) then setTimeout(slider.play, vars.initDelay) else slider.play())
				
				# TOUCH
				methods.touch()  if touch and vars.touch
				
				# FADE&&SMOOTHHEIGHT || SLIDE:
				$(window).bind "resize focus", methods.resize  if not fade or (fade and vars.smoothHeight)
				
				# API: start() Callback
				setTimeout (->
					vars.start slider
				), 200

			asNav:
				setup: ->
					slider.asNav = true
					slider.animatingTo = Math.floor(slider.currentSlide / slider.move)
					slider.currentItem = slider.currentSlide
					slider.slides.removeClass(namespace + "active-slide").eq(slider.currentItem).addClass namespace + "active-slide"
					slider.slides.click (e) ->
						e.preventDefault()
						$slide = $(this)
						target = $slide.index()
						if not $(vars.asNavFor).data("flexslider").animating and not $slide.hasClass("active")
							slider.direction = (if (slider.currentItem < target) then "next" else "prev")
							slider.flexAnimate target, vars.pauseOnAction, false, true, true

			controlNav:
				setup: ->
					unless slider.manualControls
						methods.controlNav.setupPaging()
					else # MANUALCONTROLS:
						methods.controlNav.setupManual()

				setupPaging: ->
					type = (if (vars.controlNav is "thumbnails") then "control-thumbs" else "control-paging")
					j = 1
					item = undefined
					slider.controlNavScaffold = $("<ol class=\"" + namespace + "control-nav " + namespace + type + "\"></ol>")
					if slider.pagingCount > 1
						i = 0

						while i < slider.pagingCount
							item = (if (vars.controlNav is "thumbnails") then "<img src=\"" + slider.slides.eq(i).attr("data-thumb") + "\"/>" else "<a>" + j + "</a>")
							slider.controlNavScaffold.append "<li>" + item + "</li>"
							j++
							i++
					
					# CONTROLSCONTAINER:
					(if (slider.controlsContainer) then $(slider.controlsContainer).append(slider.controlNavScaffold) else slider.append(slider.controlNavScaffold))
					methods.controlNav.set()
					methods.controlNav.active()
					slider.controlNavScaffold.delegate "a, img", eventType, (event) ->
						event.preventDefault()
						$this = $(this)
						target = slider.controlNav.index($this)
						unless $this.hasClass(namespace + "active")
							slider.direction = (if (target > slider.currentSlide) then "next" else "prev")
							slider.flexAnimate target, vars.pauseOnAction

					
					# Prevent iOS click event bug
					if touch
						slider.controlNavScaffold.delegate "a", "click touchstart", (event) ->
							event.preventDefault()

				setupManual: ->
					slider.controlNav = slider.manualControls
					methods.controlNav.active()
					slider.controlNav.live eventType, (event) ->
						event.preventDefault()
						$this = $(this)
						target = slider.controlNav.index($this)
						unless $this.hasClass(namespace + "active")
							(if (target > slider.currentSlide) then slider.direction = "next" else slider.direction = "prev")
							slider.flexAnimate target, vars.pauseOnAction

					# Prevent iOS click event bug
					if touch
						slider.controlNav.live "click touchstart", (event) ->
							event.preventDefault()

				set: ->
					selector = (if (vars.controlNav is "thumbnails") then "img" else "a")
					slider.controlNav = $("." + namespace + "control-nav li " + selector, (if (slider.controlsContainer) then slider.controlsContainer else slider))

				active: ->
					slider.controlNav.removeClass(namespace + "active").eq(slider.animatingTo).addClass namespace + "active"

				update: (action, pos) ->
					if slider.pagingCount > 1 and action is "add"
						slider.controlNavScaffold.append $("<li><a>" + slider.count + "</a></li>")
					else if slider.pagingCount is 1
						slider.controlNavScaffold.find("li").remove()
					else
						slider.controlNav.eq(pos).closest("li").remove()
					methods.controlNav.set()
					(if (slider.pagingCount > 1 and slider.pagingCount isnt slider.controlNav.length) then slider.update(pos, action) else methods.controlNav.active())

			directionNav:
				setup: ->
					directionNavScaffold = $("<ul class=\"" + namespace + "direction-nav\"><li><a class=\"" + namespace + "prev\" href=\"#\">" + vars.prevText + "</a></li><li><a class=\"" + namespace + "next\" href=\"#\">" + vars.nextText + "</a></li></ul>")
					
					# CONTROLSCONTAINER:
					if slider.controlsContainer
						$(slider.controlsContainer).append directionNavScaffold
						slider.directionNav = $("." + namespace + "direction-nav li a", slider.controlsContainer)
					else
						slider.append directionNavScaffold
						slider.directionNav = $("." + namespace + "direction-nav li a", slider)
					methods.directionNav.update()
					slider.directionNav.bind eventType, (event) ->
						event.preventDefault()
						target = (if ($(this).hasClass(namespace + "next")) then slider.getTarget("next") else slider.getTarget("prev"))
						slider.flexAnimate target, vars.pauseOnAction

					# Prevent iOS click event bug
					if touch
						slider.directionNav.bind "click touchstart", (event) ->
							event.preventDefault()

				update: ->
					disabledClass = namespace + "disabled"
					if slider.pagingCount is 1
						slider.directionNav.addClass disabledClass
					else unless vars.animationLoop
						if slider.animatingTo is 0
							slider.directionNav.removeClass(disabledClass).filter("." + namespace + "prev").addClass disabledClass
						else if slider.animatingTo is slider.last
							slider.directionNav.removeClass(disabledClass).filter("." + namespace + "next").addClass disabledClass
						else
							slider.directionNav.removeClass disabledClass
					else
						slider.directionNav.removeClass disabledClass

			pausePlay:
				setup: ->
					pausePlayScaffold = $("<div class=\"" + namespace + "pauseplay\"><a></a></div>")
					
					# CONTROLSCONTAINER:
					if slider.controlsContainer
						slider.controlsContainer.append pausePlayScaffold
						slider.pausePlay = $("." + namespace + "pauseplay a", slider.controlsContainer)
					else
						slider.append pausePlayScaffold
						slider.pausePlay = $("." + namespace + "pauseplay a", slider)
					methods.pausePlay.update (if (vars.slideshow) then namespace + "pause" else namespace + "play")
					slider.pausePlay.bind eventType, (event) ->
						event.preventDefault()
						if $(this).hasClass(namespace + "pause")
							slider.manualPause = true
							slider.manualPlay = false
							slider.pause()
						else
							slider.manualPause = false
							slider.manualPlay = true
							slider.play()
					
					# Prevent iOS click event bug
					if touch
						slider.pausePlay.bind "click touchstart", (event) ->
							event.preventDefault()

				update: (state) ->
					(if (state is "play") then slider.pausePlay.removeClass(namespace + "pause").addClass(namespace + "play").text(vars.playText) else slider.pausePlay.removeClass(namespace + "play").addClass(namespace + "pause").text(vars.pauseText))

			touch: ->
				onTouchStart = (e) ->
					if slider.animating
						e.preventDefault()
					else if e.touches.length is 1
						slider.pause()
						
						# CAROUSEL:
						cwidth = (if (vertical) then slider.h else slider.w)
						startT = Number(new Date())
						
						# CAROUSEL:
						offset = (if (carousel and reverse and slider.animatingTo is slider.last) then 0 else (if (carousel and reverse) then slider.limit - (((slider.itemW + vars.itemMargin) * slider.move) * slider.animatingTo) else (if (carousel and slider.currentSlide is slider.last) then slider.limit else (if (carousel) then ((slider.itemW + vars.itemMargin) * slider.move) * slider.currentSlide else (if (reverse) then (slider.last - slider.currentSlide + slider.cloneOffset) * cwidth else (slider.currentSlide + slider.cloneOffset) * cwidth)))))
						startX = (if (vertical) then e.touches[0].pageY else e.touches[0].pageX)
						startY = (if (vertical) then e.touches[0].pageX else e.touches[0].pageY)
						el.addEventListener "touchmove", onTouchMove, false
						el.addEventListener "touchend", onTouchEnd, false
				onTouchMove = (e) ->
					dx = (if (vertical) then startX - e.touches[0].pageY else startX - e.touches[0].pageX)
					scrolling = (if (vertical) then (Math.abs(dx) < Math.abs(e.touches[0].pageX - startY)) else (Math.abs(dx) < Math.abs(e.touches[0].pageY - startY)))
					if not scrolling or Number(new Date()) - startT > 500
						e.preventDefault()
						if not fade and slider.transitions
							dx = dx / ((if (slider.currentSlide is 0 and dx < 0 or slider.currentSlide is slider.last and dx > 0) then (Math.abs(dx) / cwidth + 2) else 1))  unless vars.animationLoop
							slider.setProps offset + dx, "setTouch"
					
				onTouchEnd = (e) ->
					# finish the touch by undoing the touch session
					el.removeEventListener "touchmove", onTouchMove, false
					if slider.animatingTo is slider.currentSlide and not scrolling and (dx isnt null)
						updateDx = (if (reverse) then -dx else dx)
						target = (if (updateDx > 0) then slider.getTarget("next") else slider.getTarget("prev"))
						if slider.canAdvance(target) and (Number(new Date()) - startT < 550 and Math.abs(updateDx) > 50 or Math.abs(updateDx) > cwidth / 2)
							slider.flexAnimate target, vars.pauseOnAction
						else
							slider.flexAnimate slider.currentSlide, vars.pauseOnAction, true  unless fade
					el.removeEventListener "touchend", onTouchEnd, false
					startX = null
					startY = null
					dx = null
					offset = null
				startX = undefined
				startY = undefined
				offset = undefined
				cwidth = undefined
				dx = undefined
				startT = undefined
				scrolling = false
				el.addEventListener "touchstart", onTouchStart, false

			resize: ->
				if not slider.animating and slider.is(":visible")
					slider.doMath()  unless carousel
					if fade
						
						# SMOOTH HEIGHT:
						methods.smoothHeight()
					else if carousel #CAROUSEL:
						slider.slides.width slider.computedW
						slider.update slider.pagingCount
						slider.setProps()
					else if vertical #VERTICAL:
						slider.viewport.height slider.h
						slider.setProps slider.h, "setTotal"
					else
						
						# SMOOTH HEIGHT:
						methods.smoothHeight()  if vars.smoothHeight
						slider.newSlides.width slider.computedW
						slider.setProps slider.computedW, "setTotal"

			smoothHeight: (dur) ->
				if not vertical or fade
					$obj = (if (fade) then slider else slider.viewport)
					(if (dur) then $obj.animate(
						height: slider.slides.eq(slider.animatingTo).height()
					, dur) else $obj.height(slider.slides.eq(slider.animatingTo).height()))

			sync: (action) ->
				$obj = $(vars.sync).data("flexslider")
				target = slider.animatingTo
				switch action
					when "animate"
						$obj.flexAnimate target, vars.pauseOnAction, false, true
					when "play"
						$obj.play()  if not $obj.playing and not $obj.asNav
					when "pause"
						$obj.pause()

		
		# public methods
		slider.flexAnimate = (target, pause, override, withSync, fromNav) ->
			slider.direction = (if (slider.currentItem < target) then "next" else "prev")  if asNav and slider.pagingCount is 1
			if not slider.animating and (slider.canAdvance(target, fromNav) or override) and slider.is(":visible")
				if asNav and withSync
					master = $(vars.asNavFor).data("flexslider")
					slider.atEnd = target is 0 or target is slider.count - 1
					master.flexAnimate target, true, false, true, fromNav
					slider.direction = (if (slider.currentItem < target) then "next" else "prev")
					master.direction = slider.direction
					if Math.ceil((target + 1) / slider.visible) - 1 isnt slider.currentSlide and target isnt 0
						slider.currentItem = target
						slider.slides.removeClass(namespace + "active-slide").eq(target).addClass namespace + "active-slide"
						target = Math.floor(target / slider.visible)
					else
						slider.currentItem = target
						slider.slides.removeClass(namespace + "active-slide").eq(target).addClass namespace + "active-slide"
						return false
				slider.animating = true
				slider.animatingTo = target
				
				# API: before() animation Callback
				vars.before slider
				
				# SLIDESHOW:
				slider.pause()  if pause
				
				# SYNC:
				methods.sync "animate"  if slider.syncExists and not fromNav
				
				# CONTROLNAV
				methods.controlNav.active()  if vars.controlNav
				
				# !CAROUSEL:
				# CANDIDATE: slide active class (for add/remove slide)
				slider.slides.removeClass(namespace + "active-slide").eq(target).addClass namespace + "active-slide"  unless carousel
				
				# INFINITE LOOP:
				# CANDIDATE: atEnd
				slider.atEnd = target is 0 or target is slider.last
				
				# DIRECTIONNAV:
				methods.directionNav.update()  if vars.directionNav
				if target is slider.last
					
					# API: end() of cycle Callback
					vars.end slider
					
					# SLIDESHOW && !INFINITE LOOP:
					slider.pause()  unless vars.animationLoop
				
				# SLIDE:
				unless fade
					dimension = (if (vertical) then slider.slides.filter(":first").height() else slider.computedW)
					margin = undefined
					slideString = undefined
					calcNext = undefined
					
					# INFINITE LOOP / REVERSE:
					if carousel
						margin = (if (vars.itemWidth > slider.w) then vars.itemMargin * 2 else vars.itemMargin)
						calcNext = ((slider.itemW + margin) * slider.move) * slider.animatingTo
						slideString = (if (calcNext > slider.limit and slider.visible isnt 1) then slider.limit else calcNext)
					else if slider.currentSlide is 0 and target is slider.count - 1 and vars.animationLoop and slider.direction isnt "next"
						slideString = (if (reverse) then (slider.count + slider.cloneOffset) * dimension else 0)
					else if slider.currentSlide is slider.last and target is 0 and vars.animationLoop and slider.direction isnt "prev"
						slideString = (if (reverse) then 0 else (slider.count + 1) * dimension)
					else
						slideString = (if (reverse) then ((slider.count - 1) - target + slider.cloneOffset) * dimension else (target + slider.cloneOffset) * dimension)
					slider.setProps slideString, "", vars.animationSpeed
					if slider.transitions
						if not vars.animationLoop or not slider.atEnd
							slider.animating = false
							slider.currentSlide = slider.animatingTo
						slider.container.unbind "webkitTransitionEnd transitionend"
						slider.container.bind "webkitTransitionEnd transitionend", ->
							slider.wrapup dimension

					else
						slider.container.animate slider.args, vars.animationSpeed, vars.easing, ->
							slider.wrapup dimension

				else # FADE:
					unless touch
						slider.slides.eq(slider.currentSlide).fadeOut vars.animationSpeed, vars.easing
						slider.slides.eq(target).fadeIn vars.animationSpeed, vars.easing, slider.wrapup
					else
						slider.slides.eq(slider.currentSlide).css
							opacity: 0
							zIndex: 1

						slider.slides.eq(target).css
							opacity: 1
							zIndex: 2

						slider.slides.unbind "webkitTransitionEnd transitionend"
						slider.slides.eq(slider.currentSlide).bind "webkitTransitionEnd transitionend", ->
							
							# API: after() animation Callback
							vars.after slider

						slider.animating = false
						slider.currentSlide = slider.animatingTo
				
				# SMOOTH HEIGHT:
				methods.smoothHeight vars.animationSpeed  if vars.smoothHeight

		slider.wrapup = (dimension) ->
			
			# SLIDE:
			if not fade and not carousel
				if slider.currentSlide is 0 and slider.animatingTo is slider.last and vars.animationLoop
					slider.setProps dimension, "jumpEnd"
				else slider.setProps dimension, "jumpStart"  if slider.currentSlide is slider.last and slider.animatingTo is 0 and vars.animationLoop
			slider.animating = false
			slider.currentSlide = slider.animatingTo
			
			# API: after() animation Callback
			vars.after slider

		# SLIDESHOW:
		slider.animateSlides = ->
			slider.flexAnimate slider.getTarget("next")  unless slider.animating

		# SLIDESHOW:
		slider.pause = ->
			clearInterval slider.animatedSlides
			slider.playing = false
			
			# PAUSEPLAY:
			methods.pausePlay.update "play"  if vars.pausePlay
			
			# SYNC:
			methods.sync "pause"  if slider.syncExists

		# SLIDESHOW:
		slider.play = ->
			slider.animatedSlides = setInterval(slider.animateSlides, vars.slideshowSpeed)
			slider.playing = true
			
			# PAUSEPLAY:
			methods.pausePlay.update "pause"  if vars.pausePlay
			
			# SYNC:
			methods.sync "play"  if slider.syncExists

		slider.canAdvance = (target, fromNav) ->
			# ASNAV:
			last = (if (asNav) then slider.pagingCount - 1 else slider.last)
			(if (fromNav) then true else (if (asNav and slider.currentItem is slider.count - 1 and target is 0 and slider.direction is "prev") then true else (if (asNav and slider.currentItem is 0 and target is slider.pagingCount - 1 and slider.direction isnt "next") then false else (if (target is slider.currentSlide and not asNav) then false else (if (vars.animationLoop) then true else (if (slider.atEnd and slider.currentSlide is 0 and target is last and slider.direction isnt "next") then false else (if (slider.atEnd and slider.currentSlide is last and target is 0 and slider.direction is "next") then false else true)))))))

		slider.getTarget = (dir) ->
			slider.direction = dir
			if dir is "next"
				(if (slider.currentSlide is slider.last) then 0 else slider.currentSlide + 1)
			else
				(if (slider.currentSlide is 0) then slider.last else slider.currentSlide - 1)

		# SLIDE:
		slider.setProps = (pos, special, dur) ->
			target = (->
				posCheck = (if (pos) then pos else ((slider.itemW + vars.itemMargin) * slider.move) * slider.animatingTo)
				posCalc = (->
					if carousel
						(if (special is "setTouch") then pos else (if (reverse and slider.animatingTo is slider.last) then 0 else (if (reverse) then slider.limit - (((slider.itemW + vars.itemMargin) * slider.move) * slider.animatingTo) else (if (slider.animatingTo is slider.last) then slider.limit else posCheck))))
					else
						switch special
							when "setTotal"
								(if (reverse) then ((slider.count - 1) - slider.currentSlide + slider.cloneOffset) * pos else (slider.currentSlide + slider.cloneOffset) * pos)
							when "setTouch"
								(if (reverse) then pos else pos)
							when "jumpEnd"
								(if (reverse) then pos else slider.count * pos)
							when "jumpStart"
								(if (reverse) then slider.count * pos else pos)
							else
								pos
				)
				(posCalc * -1) + "px"
			)
			if slider.transitions
				target = (if (vertical) then "translate3d(0," + target + ",0)" else "translate3d(" + target + ",0,0)")
				dur = (if (dur isnt `undefined`) then (dur / 1000) + "s" else "0s")
				slider.container.css "-" + slider.pfx + "-transition-duration", dur
			slider.args[slider.prop] = target
			slider.container.css slider.args  if slider.transitions or dur is `undefined`

		slider.setup = (type) ->
			# SLIDE:
			unless fade
				sliderOffset = undefined
				arr = undefined
				if type is "init"
					slider.viewport = $("<div class=\"" + namespace + "viewport\"></div>").css(
						overflow: "hidden"
						position: "relative"
					).appendTo(slider).append(slider.container)
					
					# INFINITE LOOP:
					slider.cloneCount = 0
					slider.cloneOffset = 0
					
					# REVERSE:
					if reverse
						arr = $.makeArray(slider.slides).reverse()
						slider.slides = $(arr)
						slider.container.empty().append slider.slides
				
				# INFINITE LOOP && !CAROUSEL:
				if vars.animationLoop and not carousel
					slider.cloneCount = 2
					slider.cloneOffset = 1
					
					# clear out old clones
					slider.container.find(".clone").remove()  if type isnt "init"
					slider.container.append(slider.slides.first().clone().addClass("clone")).prepend slider.slides.last().clone().addClass("clone")
				slider.newSlides = $(vars.selector, slider)
				sliderOffset = (if (reverse) then slider.count - 1 - slider.currentSlide + slider.cloneOffset else slider.currentSlide + slider.cloneOffset)
				
				# VERTICAL:
				if vertical and not carousel
					slider.container.height((slider.count + slider.cloneCount) * 200 + "%").css("position", "absolute").width "100%"
					setTimeout (->
						slider.newSlides.css display: "block"
						slider.doMath()
						slider.viewport.height slider.h
						slider.setProps sliderOffset * slider.h, "init"
					), (if (type is "init") then 100 else 0)
				else
					slider.container.width (slider.count + slider.cloneCount) * 200 + "%"
					slider.setProps sliderOffset * slider.computedW, "init"
					setTimeout (->
						slider.doMath()
						slider.newSlides.css
							width: slider.computedW
							float: "left"
							display: "block"

						
						# SMOOTH HEIGHT:
						methods.smoothHeight()  if vars.smoothHeight
					), (if (type is "init") then 100 else 0)
			else # FADE:
				slider.slides.css
					width: "100%"
					marginRight: "-100%"
					position: "relative"
					float: "left"

				if type is "init"
					unless touch
						slider.slides.eq(slider.currentSlide).fadeIn vars.animationSpeed, vars.easing
					else
						slider.slides.css(
							opacity: 0
							display: "block"
							webkitTransition: "opacity " + vars.animationSpeed / 1000 + "s ease"
							zIndex: 1
						).eq(slider.currentSlide).css
							opacity: 1
							zIndex: 2

				
				# SMOOTH HEIGHT:
				methods.smoothHeight()  if vars.smoothHeight
			
			# !CAROUSEL:
			# CANDIDATE: active slide
			slider.slides.removeClass(namespace + "active-slide").eq(slider.currentSlide).addClass namespace + "active-slide"  unless carousel

		slider.doMath = ->
			slide = slider.slides.first()
			slideMargin = vars.itemMargin
			minItems = vars.minItems
			maxItems = vars.maxItems
			slider.w = slider.width()
			slider.h = slide.height()
			slider.boxPadding = slide.outerWidth() - slide.width()
			
			# CAROUSEL:
			if carousel
				slider.itemT = vars.itemWidth + slideMargin
				slider.minW = (if (minItems) then minItems * slider.itemT else slider.w)
				slider.maxW = (if (maxItems) then maxItems * slider.itemT else slider.w)
				slider.itemW = (if (slider.minW > slider.w) then (slider.w - (slideMargin * minItems)) / minItems else (if (slider.maxW < slider.w) then (slider.w - (slideMargin * maxItems)) / maxItems else (if (vars.itemWidth > slider.w) then slider.w else vars.itemWidth)))
				slider.visible = Math.floor(slider.w / (slider.itemW + slideMargin))
				slider.move = (if (vars.move > 0 and vars.move < slider.visible) then vars.move else slider.visible)
				slider.pagingCount = Math.ceil(((slider.count - slider.visible) / slider.move) + 1)
				slider.last = slider.pagingCount - 1
				slider.limit = (if (slider.pagingCount is 1) then 0 else (if (vars.itemWidth > slider.w) then ((slider.itemW + (slideMargin * 2)) * slider.count) - slider.w - slideMargin else ((slider.itemW + slideMargin) * slider.count) - slider.w - slideMargin))
			else
				slider.itemW = slider.w
				slider.pagingCount = slider.count
				slider.last = slider.count - 1
			slider.computedW = slider.itemW - slider.boxPadding

		slider.update = (pos, action) ->
			slider.doMath()
			
			# update currentSlide and slider.animatingTo if necessary
			unless carousel
				if pos < slider.currentSlide
					slider.currentSlide += 1
				else slider.currentSlide -= 1  if pos <= slider.currentSlide and pos isnt 0
				slider.animatingTo = slider.currentSlide
			
			# update controlNav
			if vars.controlNav and not slider.manualControls
				if (action is "add" and not carousel) or slider.pagingCount > slider.controlNav.length
					methods.controlNav.update "add"
				else if (action is "remove" and not carousel) or slider.pagingCount < slider.controlNav.length
					if carousel and slider.currentSlide > slider.last
						slider.currentSlide -= 1
						slider.animatingTo -= 1
					methods.controlNav.update "remove", slider.last
			
			# update directionNav
			methods.directionNav.update()  if vars.directionNav

		slider.addSlide = (obj, pos) ->
			$obj = $(obj)
			slider.count += 1
			slider.last = slider.count - 1
			
			# append new slide
			if vertical and reverse
				(if (pos isnt `undefined`) then slider.slides.eq(slider.count - pos).after($obj) else slider.container.prepend($obj))
			else
				(if (pos isnt `undefined`) then slider.slides.eq(pos).before($obj) else slider.container.append($obj))
			
			# update currentSlide, animatingTo, controlNav, and directionNav
			slider.update pos, "add"
			
			# update slider.slides
			slider.slides = $(vars.selector + ":not(.clone)", slider)
			
			# re-setup the slider to accomdate new slide
			slider.setup()
			
			#FlexSlider: added() Callback
			vars.added slider

		slider.removeSlide = (obj) ->
			pos = (if (isNaN(obj)) then slider.slides.index($(obj)) else obj)
			
			# update count
			slider.count -= 1
			slider.last = slider.count - 1
			
			# remove slide
			if isNaN(obj)
				$(obj, slider.slides).remove()
			else
				(if (vertical and reverse) then slider.slides.eq(slider.last).remove() else slider.slides.eq(obj).remove())
			
			# update currentSlide, animatingTo, controlNav, and directionNav
			slider.doMath()
			slider.update pos, "remove"
			
			# update slider.slides
			slider.slides = $(vars.selector + ":not(.clone)", slider)
			
			# re-setup the slider to accomdate new slide
			slider.setup()
			
			# FlexSlider: removed() Callback
			vars.removed slider

		#FlexSlider: Initialize
		methods.init()

	#FlexSlider: Default Settings
	$.flexslider.defaults =
		namespace: "flex-" # {NEW} String: Prefix string attached to the class of every element generated by the plugin
		selector: ".slides > li" # {NEW} Selector: Must match a simple pattern. '{container} > {slide}' -- Ignore pattern at your own peril
		animation: "slide" #String: Select your animation type, "fade" or "slide"
		easing: "swing" # {NEW} String: Determines the easing method used in jQuery transitions. jQuery easing plugin is supported!
		direction: "horizontal" #String: Select the sliding direction, "horizontal" or "vertical"
		reverse: false # {NEW} Boolean: Reverse the animation direction
		animationLoop: true #Boolean: Should the animation loop? If false, directionNav will received "disable" classes at either end
		smoothHeight: true # {NEW} Boolean: Allow height of the slider to animate smoothly in horizontal mode
		startAt: 0 #Integer: The slide that the slider should start on. Array notation (0 = first slide)
		slideshow: true #Boolean: Animate slider automatically
		slideshowSpeed: 7000 #Integer: Set the speed of the slideshow cycling, in milliseconds
		animationSpeed: 600 #Integer: Set the speed of animations, in milliseconds
		initDelay: 0 # {NEW} Integer: Set an initialization delay, in milliseconds
		randomize: false #Boolean: Randomize slide order
		
		# Usability features
		pauseOnAction: true #Boolean: Pause the slideshow when interacting with control elements, highly recommended.
		pauseOnHover: false #Boolean: Pause the slideshow when hovering over slider, then resume when no longer hovering
		useCSS: true # {NEW} Boolean: Slider will use CSS3 transitions if available
		touch: true # {NEW} Boolean: Allow touch swipe navigation of the slider on touch-enabled devices
		video: false # {NEW} Boolean: If using video in the slider, will prevent CSS3 3D Transforms to avoid graphical glitches
		
		# Primary Controls
		controlNav: true #Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
		directionNav: true #Boolean: Create navigation for previous/next navigation? (true/false)
		prevText: "Previous" #String: Set the text for the "previous" directionNav item
		nextText: "Next" #String: Set the text for the "next" directionNav item
		
		# Secondary Navigation
		keyboard: true #Boolean: Allow slider navigating via keyboard left/right keys
		multipleKeyboard: false # {NEW} Boolean: Allow keyboard navigation to affect multiple sliders. Default behavior cuts out keyboard navigation with more than one slider present.
		mousewheel: false # {UPDATED} Boolean: Requires jquery.mousewheel.js (https://github.com/brandonaaron/jquery-mousewheel) - Allows slider navigating via mousewheel
		pausePlay: false #Boolean: Create pause/play dynamic element
		pauseText: "Pause" #String: Set the text for the "pause" pausePlay item
		playText: "Play" #String: Set the text for the "play" pausePlay item
		
		# Special properties
		controlsContainer: "" # {UPDATED} jQuery Object/Selector: Declare which container the navigation elements should be appended too. Default container is the FlexSlider element. Example use would be $(".flexslider-container"). Property is ignored if given element is not found.
		manualControls: "" # {UPDATED} jQuery Object/Selector: Declare custom control navigation. Examples would be $(".flex-control-nav li") or "#tabs-nav li img", etc. The number of elements in your controlNav should match the number of slides/tabs.
		sync: "" # {NEW} Selector: Mirror the actions performed on this slider with another slider. Use with care.
		asNavFor: "" # {NEW} Selector: Internal property exposed for turning the slider into a thumbnail navigation for another slider
		
		# Carousel Options
		itemWidth: 0 # {NEW} Integer: Box-model width of individual carousel items, including horizontal borders and padding.
		itemMargin: 0 # {NEW} Integer: Margin between carousel items.
		minItems: 0 # {NEW} Integer: Minimum number of carousel items that should be visible. Items will resize fluidly when below this.
		maxItems: 0 # {NEW} Integer: Maxmimum number of carousel items that should be visible. Items will resize fluidly when above this limit.
		move: 0 # {NEW} Integer: Number of carousel items that should move on animation. If 0, slider will move all visible items.
		
		# Callback API
		start: (slider) ->
			custom_setup slider

		#Callback: function(slider) - Fires when the slider loads the first slide
		before: -> #Callback: function(slider) - Fires asynchronously with each slider animation

		after: (slider) ->
			custom_setup slider

		#Callback: function(slider) - Fires after each slider animation completes
		end: -> # Callback: function(slider) - Fires when the slider reaches the last slide (asynchronous)

		added: -> # {NEW} Callback: function(slider) - Fires after a slide is added

		removed: -> # {NEW} Callback: function(slider) - Fires after a slide is removed

	custom_setup = (slider) ->
		$("#image_title").html $(slider.slides[slider.currentSlide]).attr("data-title") or '&nbsp;'
		tags = JSON.parse($(slider.slides[slider.currentSlide]).attr("data-tags"))
		html = ""
		i = tags.length - 1

		while i >= 0
			html += "<span>" + tags[i] + "</span>"
			i--
		$("#image_tags").html html
	
	#FlexSlider: Plugin Function
	$.fn.flexslider = (options) ->
		options = {}  if options is `undefined`
		if typeof options is "object"
			@each ->
				$this = $(this)
				selector = (if (options.selector) then options.selector else ".slides > li")
				$slides = $this.find(selector)
				if $slides.length is 1
					$slides.fadeIn 400
					options.start $this  if options.start
				else new $.flexslider(this, options)  if $this.data("flexslider") is `undefined`
		else
			# Helper strings to quickly perform functions on the slider
			$slider = $(this).data("flexslider")
			switch options
				when "play"
					$slider.play()
				when "pause"
					$slider.pause()
				when "next"
					$slider.flexAnimate $slider.getTarget("next"), true
				when "prev", "previous"
					$slider.flexAnimate $slider.getTarget("prev"), true
				else
					$slider.flexAnimate options, true  if typeof options is "number"
) jQuery