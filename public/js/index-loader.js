	$(function () {

		var indexLoader = function () {
			this.cookieOptions  = { expires: 7, path: '/' };
			this.sliderUrl 		= window.requestUrl;
			this.pageName 		= window.pageName;
			this.wawSlyder 		= window.wawSlyder;
			this.wawCategories 	= window.wawCategories;
			this.wawRegions 	= window.wawRegions;
			this.wawBrands	 	= window.wawBrands;
			this.originalData 	= null;
			this.slider 		= null;
			this.categories 	= null; 
			this.init();
		};

		indexLoader.prototype = {

			init : function () {
				this.registerDOMElements();
				this.bindEvents();
				this.isFavoritesPage() && this.extendSliderForFavorites();
				this.isBrowserCompatible() && this.isSliderPage() && this.initWawSlider();	
			},

			extendSliderForFavorites : function () {
				window.messages.noFavoritesData && (window.messages.noData = window.messages.noFavoritesData);
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

			isMainPage : function () {
				return (this.pageName == "mainPage");
			},
			isSliderPage :  function () {
				return ((this.pageName == "mainPage")||(this.pageName == "newsPage")||(this.pageName == "favoritesPage"));
			},
			isFavoritesPage : function () {
				return (this.pageName == "favoritesPage");
			},

			registerDOMElements : function () {
				this.dom = {
					regionsBtn 	 : $("#btn1"),
					categoryBtn  : $("#btn2"),
					brandsBtn    : $("#btn3"),
					filterModal  : $("#filters-modal"),
					regionsModal : $("#regions-modal"),
					brandsModal  : $("#brands-modal"),
					lockLoading	 : $(".lock-loading"),
					noData 		 : $(".no-data")
				}
			},
			bindEvents : function () {
				var	that = this;
				this.dom.regionsBtn.on("click", function(e){
					that.initRegions();
				});
				this.dom.categoryBtn.on("click", function(e){
					that.initCategories();
				});
				this.dom.brandsBtn.on("click", function(e){
					that.initBrands();
				});
				$(".credit").on("click", function(e){
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
					this.showCategoryModal();
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
					this.hideCategoryModal();
					this.isMainPage() ? this.initWawSlider() : location.href = "/";
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
					this.isMainPage() ? this.initWawSlider() : location.href = "/";
				}, this));
			},

			/* Brands popup */
			initBrands : function () {
				if (_.isEmpty(this.brands)){
					this.dom.lockLoading.show();
					$.ajax({
						url 	: "/brands/list-all",
				        dataType: "json",
						cache	: false
					}).done(_.bind(this.renderBrands, this)).fail(_.bind(this.fetchingDataError, this));
				} else {
					this.showBrandsModal();
				}
			},

			renderBrands : function (data) {
				if (_.isEmpty(data)) {
					this.fetchingDataError();
					return;
				}
				this.brands = new this.wawBrands(data);
				this.dom.lockLoading.hide();
				this.brands && this.bindBrandsSelectedEvent();
			},

			bindBrandsSelectedEvent : function () {
				this.brands.eventObject.on("brandsSelected", _.bind(function(e, data){
					this.setBrandsCookie(data);
					this.hideBrandsModal();
					this.isMainPage() ? this.initWawSlider() : location.href = "/";
				}, this));
			},
			
			setRegionCookie : function (data) {
				$.removeCookie("geo");
				this.clearCategoryBrandsCookies();
				$.cookie('geo', data.regionId, this.cookieOptions);
			},
			setCategoryCookie : function (data) {
				this.clearCategoryBrandsCookies();
				$.cookie('category', parseInt(data.categoryId), this.cookieOptions);
			},
			setBrandsCookie : function (data) {
				this.clearCategoryBrandsCookies();
				if (data.brandsId) {
					$.cookie('brands',   data.brandsId, this.cookieOptions);	
				}
				if (data.productsId) {
					$.cookie('products', data.productsId, this.cookieOptions);
				}
			},
			clearCategoryBrandsCookies : function () {
				$.removeCookie("category");
				$.removeCookie("brands");
				$.removeCookie("products");
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

			isBrowserCompatible : function () {
			    if (navigator.sayswho == "MSIE 7.0") {
			    	this.dom.noData.html(window.messages.notSupported).show();
					return false;
			    } else {
					return true;
			    }
			},
			hideCategoryModal : function () {
				this.dom.filterModal.modal("hide");
			},
			showCategoryModal : function () {
				this.dom.filterModal.modal({show: true});
			},
			hideRegionsModal : function () {
				this.dom.regionsModal.modal("hide");
			},
			showRegionsModal : function () {
				this.dom.regionsModal.modal({show: true});
			},
			hideBrandsModal : function () {
				this.dom.brandsModal.modal("hide");
			},
			showBrandsModal : function () {
				this.dom.brandsModal.modal({show: true});
			}

		}
		new indexLoader();
	})
