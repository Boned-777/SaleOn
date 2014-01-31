	$(function () {
		var EMPTY_STRING = "";
		
		var wawCategories = function(categoriesData) {
			this.mainTemplate = '<div class="row">\
        							<div class="span3 category-group alert alert-success">\
        								<i class="shopping-icon"></i>\
        								<div class="category-group-text">Товары <span class="counter">7 258</span></div>\
        							</div>\
        							<div class="span3">&nbsp;</div>\
						        	<div class="span3 category-group category-group-inactive">\
						        		<i class="services-icon"></i>\
						        		<div class="category-group-text">Услуги <span class="counter">12 754</span></div>\
						        	</div>\
						      	</div>';
			
			this.rowTemplate = ['<div class="row">', '</div>'];
			this.itemTemplate = '<div class="span2 category-wrapper">\
									<div class="flagi-right">Авто, транспорт <br><span class="counter">748</span></div>\
								</div>';	
											
			this.init(categoriesData);	 
		};
		
		wawCategories.prototype = {
			
			init : function (categoriesData) {
				this.data = categoriesData;
				if (_.isEmpty(this.data)) {
					this.showNoData();
					return;
				}
				$("#filters-modal").modal({show: true});

				this.renderMainTemplate();
				// this.registerDOMElements();
				// this.bindEvents();
				// this.initStartPages();
			},

			showNoData : function () {
				// $(".no-data").html(window.messages.noData).show();	
				$("#myCarousel").show();
				// $(".lock-loading").hide();
			},

			renderMainTemplate : function () {
				var filterContent = $("#filter-content");
				filterContent && filterContent.empty();
				filterContent.html(this.mainTemplate);
			},

			registerDOMElements : function () {
				this.dom = {
					lockLayer		: $(".lock-gray"),
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

			/* Bind handlers */
					
			initStartPages : function () {
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
			

			showError : function () {
				var errorModal = $("#error-modal-block");
					errorModal.find(".block-label").html(window.messages.serverError);
					errorModal.fadeIn( "slow" ).delay( 5000 ).fadeOut( "slow" ); 
			}
				
		};
		window.wawCategories = wawCategories;
	})
