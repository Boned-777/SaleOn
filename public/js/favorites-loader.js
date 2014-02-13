	$(function () {
			
		var favoritesLoader = function () {
			this.cookieOptions  = { expires: 7, path: '/' };
			this.sliderUrl 		= "/ad/favorites";
			this.wawSlyder 		= window.wawSlyder;
			this.wawCategories 	= window.wawCategories;
			this.wawRegions 	= window.wawRegions;
			this.originalData 	= null;
			this.slider 		= null;
			this.categories 	= null; 
			this.init();
		};

		favoritesLoader.prototype = {

			init : function () {
				this.extendSlider();
				this.registerDOMElements();
				this.bindEvents();
				this.isBrowserCompatible() && this.initWawSlider();	
			},

			extendSlider : function () {
				_.extend(wawSlyder.prototype, {
					onFavoritesUpdated : function (target) {
						if (!this.isFavoritesOff(target)) {
							var list = _.reject(this.data.list, function(item){ 
								return (item.post_id == target.data("id"));
							});
							this.data.list = list;
							this.init(this.data);
						}  
					}
				});
			},

			registerDOMElements : function () {
				this.dom = {
					categoryBtn  : $("#btn2"),
					filterModal  : $("#filters-modal"),
					regionsModal : $("#regions-modal"),
					lockLoading	 : $(".lock-loading"),
					carousel 	 : $("#myCarousel"),
					noData 		 : $(".no-data")
				}
			},
			bindEvents : function () {
				var	that = this;
				this.dom.categoryBtn.on("click", function(e){
					that.initCategories();
				});
				$("#btn1").on("click", function(e){
					that.initRegions();
				});
				$("#btn3").on("click", function(e){
					that.slider = null;
					that.slider = new that.wawSlyder(that.duplicateResponce(that.originalData, 83));	
				});
			},

			/* slider */
			initWawSlider : function () {
				this.dom.lockLoading.show();
				$.ajax({
					url: this.sliderUrl,
			        dataType: "json",
					cache: false
				}).done(_.bind(this.renderWawSlider, this)).fail(_.bind(this.initSliderError, this));			
			},

			renderWawSlider : function (data) {
				this.originalData = data;
				this.slider = new this.wawSlyder(data);
				this.dom.lockLoading.hide();
			},

			initSliderError : function () {
				this.dom.noData.html(window.messages.noData).show();
				this.dom.lockLoading.hide();
			},
			
			/* Categories popup */
			initCategories : function () {
				if (_.isEmpty(this.categories)){
					this.dom.lockLoading.show();
					$.ajax({
						url: "/categories/list",
				        dataType: "json",
						cache: false
					}).done(_.bind(this.renderCategories, this)).fail(_.bind(this.fetchingDataError, this));
				} else {
					this.showModal();
				}
			},

			renderCategories : function (data) {
				if (_.isEmpty(data)) {
					this.fetchingDataError();
					return;
				}
				this.categories = new this.wawCategories(data);
				this.dom.lockLoading.hide();
				this.categories && this.bindCategorySelectedEvent();
			},
			fetchingDataError : function () {
				this.dom.lockLoading.hide();
			},

			bindCategorySelectedEvent : function () {
				this.categories.eventObject.on("categorySelected", _.bind(function(e, data){
					this.setCategoryCookie(data);
					this.hideModal();
					location.href = "/";
					//this.initWawSlider();
				}, this));
			},

			/* Regions popup */
			initRegions : function () {
				if (_.isEmpty(this.regions)){
					this.dom.lockLoading.show();
					$.ajax({
						url 	: "/geo/list?term=1",
				        dataType: "json",
						cache	: false
					}).done(_.bind(this.renderRegions, this)).fail(_.bind(this.fetchingDataError, this));
				} else {
					this.showRegionsModal();
				}
			},

			renderRegions : function (data) {
				if (_.isEmpty(data)) {
					this.fetchingDataError();
					return;
				}
				this.regions = new this.wawRegions(data);
				this.dom.lockLoading.hide();
				this.regions && this.bindRegionSelectedEvent();
			},

			bindRegionSelectedEvent : function () {
				this.regions.eventObject.on("regionSelected", _.bind(function(e, data){
					this.setRegionCookie(data);
					this.hideRegionsModal();
					location.href = "/";
					//this.initWawSlider();
				}, this));
			},
			
			setCategoryCookie : function (data) {
				$.removeCookie("category");
				$.cookie('category', parseInt(data.categoryId), this.cookieOptions);
			},
			setRegionCookie : function (data) {
				$.removeCookie("geo");
				$.cookie('geo', data.regionId, this.cookieOptions);
			},



			duplicateResponce : function (source, count) {
				var result = {};
				result.list = source.list;
				for (var i = 0; i<= count; i++) {
					result.list = result.list.concat(source.list);
				}
				result.options = source.options;
				return result;
			},

			setCookies : function () {
				$.cookie('geo', '1.2', 		cookieOptions);
				$.cookie('category', '3', 	cookieOptions);
				$.cookie('brands', '111', 	cookieOptions);
				$.cookie('products', '3', 	cookieOptions);	
			},

			isBrowserCompatible : function () {
			    if (navigator.sayswho == "MSIE 7.0") {
			    	this.dom.noData.html(window.messages.notSupported).show();
					return false;
			    } else {
					return true;
			    }
			},
			hideModal : function () {
				this.dom.filterModal.modal("hide");
			},
			showModal : function () {
				this.dom.filterModal.modal({show: true});
			},
			hideRegionsModal : function () {
				this.dom.regionsModal.modal("hide");
			},
			showRegionsModal : function () {
				this.dom.regionsModal.modal({show: true});
			}

		}
		new favoritesLoader();

	})
