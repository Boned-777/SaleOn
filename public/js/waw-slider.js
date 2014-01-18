	$(function () {
		
		var ITEMS_PER_PAGE = 12,
			HIDDEN_PAGE_SHIFT = 2,
			NEXT_PREVIOUS_PAGE_SHIFT = 1,
			EMPTY_STRING = "";
		
		var wawSlyder = function(sliderData) {
			this.mainTemplate = '<div id="myCarousel" class="carousel slide">\
									<div class="carousel-inner">\
										<div class="hover-left hide-left item">\
											<div class="items-wrapper"></div>\
										</div>\
										<div class="hover-left visible item">\
											<div class="items-wrapper"></div>\
										</div>\
										<div class="item active">\
											<div class="items-wrapper"></div>\
										</div>\
										<div class="hover-right visible item">\
											<div class="items-wrapper"></div>\
										</div>\
										<div class="hover-right hide-right item">\
											<div class="items-wrapper"></div>\
										</div>\
									</div>\
									<div id="left-paging" class="mycarousel-control left" href="#myCarousel" data-slide="prev"></div>\
									<div id="right-paging" class="mycarousel-control right" href="#myCarousel" data-slide="next"></div>\
									<div class="text-center lead page-number"><span id="page-number"></span></div>\
									<div class="lock-gray"></div>\
								</div>';
			
			this.rowTemplate = ['<div class="row-fluid">', '</div>'];
			this.itemTemplate = '<div class="span3 bottom-offset">\
									<div class="img-wrapper">\
										<img  src="/ads/$imageLink" class="img-polaroid">\
										<div class="img-info">\
											<a href="$favoriteLink" title="$favoritesTooltip" data-id=$link class="favorites-icon $isFavorite"></a>\
											<a href="/ad/index/id/$link" class="post-link"><p class="ellipsis">$name</p></a>\
											<p class="ellipsis">$brand</p>\
											<p class="ellipsis">$daysMsgText: $daysLeft</p>\
										</div>\
									</div>\
								</div>';	

			this.noImgTemplate = '<div class="span3 bottom-offset">\
									<div class="img-wrapper">\
										<img src="/img/no-image.jpg" class="img-polaroid">\
										<div class="no-image">\
										</div>\
									</div>\
								</div>';									
			this.init(sliderData);	 
		};
		
		wawSlyder.prototype = {
			
			init : function (sliderData) {
				this.data = sliderData;

				this.currentPage = 1;
				this.renderMainTemplate();
				this.registerDOMElements();
				this.bindEvents();
				this.initStartPages();
				this.initCarousel();
				this.displayCurrentPage();
			},

			renderMainTemplate : function () {
				var mainPageSlider = $("#main-page-slider");
				mainPageSlider && mainPageSlider.empty();
				var template = this.mainTemplate.replace("$noData", window.messages.noData);
				mainPageSlider.html(template);
			},

			registerDOMElements : function () {
				this.dom = {
					mainPageSlider	: $("#main-page-slider"),
					carouselContainer : $('#myCarousel'),
					itemWrapper 	: $(".items-wrapper"),
					rightPagingBtn 	: $("#right-paging"),
					leftPagingBtn 	: $("#left-paging"),
					activeWrapper 		: $(".active").find(".items-wrapper"),
					leftVisibleWrapper 	: $(".hover-left.visible").find(".items-wrapper"),
					rightVisibleWrapper : $(".hover-right.visible").find(".items-wrapper"),
					leftHiddenWrapper  	: $(".hover-left.hide-left").find(".items-wrapper"),
					rightHiddenWrapper 	: $(".hover-right.hide-right").find(".items-wrapper")
				}
			},

			bindEvents : function () {
				var	that = this;
				this.dom.itemWrapper.on("click", function(e){
					var wrapperContainer = $(e.currentTarget).parent(),
						target = $(e.target);

					if (that.isRightClick(wrapperContainer)){
						that.showNextPage();	
					}
					if (that.isLeftClick(wrapperContainer)){
						that.showPreviousPage();	
					}
					
					if (that.isFavoritesClick(target)) {
						e.preventDefault();
						that.favoritesClickHandler(target);
					
					} else if (that.isPostLinkClick(target)) {
						if (!target.parent().hasClass("post-link")){
							e.preventDefault();
							window.location.href = target.parent().find(".post-link").attr("href");
						}
					}
				})
				$(document).on("keyup", function (e) {
					var keyCode = e.which;
					if(keyCode == 39) { //right key
						(that.isCycleAvailable() || (that.currentPage == 1 && that.isTwoPages())) && that.showNextPage();
					}
					if(keyCode == 37) { //left key
						(that.isCycleAvailable() || (that.currentPage == 2 && that.isTwoPages())) && that.showPreviousPage();
					}
				    e.preventDefault(); 
				})
			},

			/* Bind handlers */
			favoritesClickHandler : function (target) {
				var link = target.attr("href");
				(link == "/auth") ? (window.location.href = link) : (this.updateFavorites(target, link));
			},

			updateFavorites : function (target, link) {
				$(".lock-gray").show();
				$.ajax({
					dataType: "json",
					url		: link,
					cache	: false
				}).done(_.bind(function(data) {
					data.success ? this.onFavoritesUpdated(target) : this.showError();
					$(".lock-gray").hide();
				}, this)).fail(_.bind(function(data) {
					this.showError();
					$(".lock-gray").hide();
				}, this));		
			},

			onFavoritesUpdated : function (target) {
				_.each(this.data.list, function(item) {
					if (item.post_id == target.data("id")) {

						if (this.isFavoritesOff(target)) {
							item.is_favorite = 1;
							item.favorites_link = item.favorites_link.replace("add", "remove");
							target.attr("href", item.favorites_link);
							target.toggleClass("favorites-icon-off favorites-icon-on")
						} else {
							item.is_favorite = 0;
							item.favorites_link = item.favorites_link.replace("remove", "add");
							target.attr("href", item.favorites_link);
							target.toggleClass("favorites-icon-on favorites-icon-off")
						}  
					}
				}, this);
			},

			showNextPage : function () {
				var that = this;
				that.dom.rightPagingBtn.trigger("click", {
					postSlideCallback : function() {that.buildNextHiddenPage()}
				});
			},

			showPreviousPage : function () {
				var that = this;
				that.dom.leftPagingBtn.trigger("click", { 
					postSlideCallback : function() {that.buildPreviousHiddenPage()}
				});
			},
			/* Bind handlers */
					
			initStartPages : function () {
				this.isCycleAvailable() && this.buildPage(this.dom.leftHiddenWrapper, this.getIndexes(this.getPageCount()-1));			
				this.isCycleAvailable() && this.buildPage(this.dom.leftVisibleWrapper, this.getIndexes(this.getPageCount()));
											
				this.buildPage(this.dom.activeWrapper, this.getIndexes(1));
				
				this.isTwoPages() || this.isCycleAvailable() && this.buildPage(this.dom.rightVisibleWrapper, this.getIndexes(2));
				this.isCycleAvailable() && this.buildPage(this.dom.rightHiddenWrapper, this.getIndexes(3));
			},

			initCarousel : function () {
			    this.dom.carouselContainer.carousel({
					interval : false,
					pause	 : false
				});
			},
			
			buildPage : function (container, indexes){
				var	j		= 0,
					data 	= this.data,
					result 	= EMPTY_STRING;
					
				for (var i = indexes.startIndex; i <= indexes.endIndex; i++) {
					if (j==0) {result += this.rowTemplate[0]}
						if (data.list[i]) {
							result += this.itemTemplate
								.replace("$imageLink", 		data.list[i].photoimg)
								.replace(/\$link/gi, 		data.list[i].post_id)
								.replace("$favoriteLink", 	data.list[i].favorites_link)

								.replace("$isFavorite", 		(data.list[i].is_favorite) ? "favorites-icon-on" : "favorites-icon-off")
								.replace("$favoritesTooltip", 	(data.list[i].is_favorite) ? window.messages.removeFromFavorites : window.messages.addToFavorites)

								.replace("$name", 			data.list[i].name)
								.replace("$brand", 			data.list[i].brand_name)
								.replace("$daysLeft", 		data.list[i].days)
								.replace("$daysMsgText", 	data.options.days_left_text); 
						} else {
							result += this.noImgTemplate;
						}
					if (j==3) {
						j = 0;
						result += this.rowTemplate[1]; 
					} else {j++;}
				}
				container.html(result);
			},
			
			buildNextHiddenPage : function (){
				this.currentPage = this.getNextPage(this.currentPage + NEXT_PREVIOUS_PAGE_SHIFT);
				this.displayCurrentPage();
				if (this.isCycleAvailable()) {
					var pageToBuild  = this.getNextPage(this.currentPage + HIDDEN_PAGE_SHIFT);
					this.refreshHiddenElements();
					this.buildPage(this.dom.rightHiddenWrapper, this.getIndexes(pageToBuild));
				}
			},
			
			buildPreviousHiddenPage : function (){
				this.currentPage = this.getNextPage(this.currentPage - NEXT_PREVIOUS_PAGE_SHIFT);
				this.displayCurrentPage();
				if (this.isCycleAvailable()) {
					var pageToBuild  = this.getNextPage(this.currentPage - HIDDEN_PAGE_SHIFT);
					this.refreshHiddenElements();
					this.buildPage(this.dom.leftHiddenWrapper, this.getIndexes(pageToBuild));
				}
			},
			
			getIndexes : function (page) {
				return {
					startIndex 	: (page - 1) * ITEMS_PER_PAGE,
					endIndex 	: (page * ITEMS_PER_PAGE) - 1
				}
			},
			
			getPageCount : function () {
				return Math.ceil(this.data.list.length / ITEMS_PER_PAGE);
			},
			
			getNextPage : function (page) {
				var pageCount = this.getPageCount();
				if (page == (pageCount + 1)) 	{return 1}
				if (page == (pageCount + 2)) 	{return 2}
				
				if (page == 0) 			{return pageCount}
				if (page == -1)			{return pageCount - 1}
				return page;
			},

			isRightClick : function (el) {
				return el.hasClass("hover-right");
			},
			isLeftClick : function (el) {
				return el.hasClass("hover-left");
			},
			isFavoritesClick : function (el) {
				return el.hasClass("favorites-icon");
			},
			isPostLinkClick : function (el) {
				return el.closest(".img-info").get(0);
			},

			isFavoritesOff : function (target) {
				var itemId = target.data("id"),
					itemData = _.findWhere(this.data.list, {post_id: itemId}); 
				return target.hasClass("favorites-icon-off") && (itemData.is_favorite == 0);
			},

			isCycleAvailable : function (page) {
				var pageCount = this.getPageCount();
				return (pageCount >= 3);
			},

			isTwoPages : function () {
				var pageCount = this.getPageCount();
				return (pageCount == 2);	
			},

			refreshHiddenElements : function () {
				this.dom.leftHiddenWrapper = $(".hover-left.hide-left").find(".items-wrapper");
				this.dom.rightHiddenWrapper = $(".hover-right.hide-right").find(".items-wrapper");
			},

			displayCurrentPage : function () {
				var pageNumber = $("#page-number");	
				pageNumber.html(this.currentPage);
				pageNumber.parent().show();
			},

			showError : function () {
				var errorModal = $("#error-modal-block");
					errorModal.find(".block-label").html(window.messages.serverError);
					errorModal.fadeIn( "slow" ).delay( 5000 ).fadeOut( "slow" ); 
			}
				
		};
		
		/**
		** helpers
		**/
		var originalData, slider;
		var duplicateResponce = function (source, count) {
			var result = {};
			result.list = source.list;
			for (var i = 0; i<= count; i++) {
				result.list = result.list.concat(source.list);
			}
			result.options = source.options;
			return result;
		};

		var setCookies = function () {
			var cookieOptions = { expires: 7, path: '/' };
			$.cookie('geo', '1.2', 		cookieOptions);
			$.cookie('category', '3', 	cookieOptions);
			$.cookie('brands', '111', 	cookieOptions);
			$.cookie('products', '3', 	cookieOptions);	
			//$.removeCookie("geo");
		}

		$("#btn1").on("click", function(e){
			slider = null;
			slider = new wawSlyder(duplicateResponce(originalData, 1));	
		});
		$("#btn2").on("click", function(e){
			slider = null;
			slider = new wawSlyder(duplicateResponce(originalData, 3));	
		});
		$("#btn3").on("click", function(e){
			slider = null;
			slider = new wawSlyder(duplicateResponce(originalData, 9));	
		});

		var isBrowserCompatible = function () {
			if (navigator.sayswho.search("Firefox") != -1) {
		    	$("body").addClass("firefox-slider");
		    }
		    if (navigator.sayswho == "MSIE 8.0") {
		    	$("body").addClass("ie-slider");
		    }	
		    if (navigator.sayswho == "MSIE 7.0") {
		    	$(".no-data").html(window.messages.notSupported).show();
				$("#myCarousel").hide();
				return false;
		    }	
		    return true;
		}


		/**
		** run waw slider
		*/
		if (isBrowserCompatible()){
			setCookies();
			$(".lock-loading").show();
			$.ajax({
				url: window.favoritesPage ? "/ad/favorites" : "/ad/list",
		        dataType: "json",
				cache: false
			}).done(function( data ) {
				originalData = data;
				slider = new wawSlyder(data);
				$(".lock-loading").hide();
			}).fail(function( data ) {
				$(".no-data").show();
				$("#myCarousel").hide();
				$(".lock-loading").hide();
			});			
		}

	})
