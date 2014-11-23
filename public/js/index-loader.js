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
			this.prevGeoCategory= null;
			this.prevGeoBrands  = null;
			this.init();
		};

		indexLoader.prototype = {

			init : function () {
				this.registerDOMElements();
				this.bindEvents();
				this.applyUrlState();
				// this.isNewsPage() && this.clearCategoryBrandsCookies();
				this.isFavoritesPage() && this.extendSliderForFavorites();
				this.isBrowserCompatible() && this.isSliderPage() && this.initWawSlider();	
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
				// $(".credit").on("click", function(e){
				// 	that.slider = null;
				// 	that.slider = new that.wawSlyder(that.duplicateResponce(that.originalData, 83));	
				// });
			},

			applyUrlState : function () {
				var urlFilterKey = "filter",
					urlIndexPageKey = "/",
					urlPath = location.pathname,
					urlParts = urlPath.split("/");
				if (urlPath == urlIndexPageKey) {
					this.clearAllCookies();
				}
				else if(urlParts && (urlParts[1] == urlFilterKey)){
					urlParts[2] && $.cookie('geo', urlParts[2], this.cookieOptions);
					urlParts[3] && $.cookie('category', urlParts[3], this.cookieOptions);
					urlParts[4] && $.cookie('brand', urlParts[4], this.cookieOptions);
					urlParts[5] && $.cookie('product', urlParts[5], this.cookieOptions);

					(urlParts[6] && urlParts[6] == 'new') 		&& $.cookie('sort', urlParts[6], this.cookieOptions);
					(urlParts[6] && urlParts[6] == 'favorite')  && $.cookie('sort', urlParts[6], this.cookieOptions);
					if(!urlParts[6]) {this.clearSortingCookies();};
				}
				this.setFiltersButtonHilight();
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

			// TODO: May be remove this.pageName because this property has no sense
			isMainPage : function () {
				return (this.pageName == "mainPage");
			},
			isSliderPage :  function () {
				return ((this.pageName == "mainPage")||(this.pageName == "newsPage")||(this.pageName == "favoritesPage"));
			},
			isFavoritesPage : function () {
				return ($.cookie('sort') == "favorite");
			},
			isNewsPage : function () {
				return ($.cookie('sort') == "new");
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
				if (_.isEmpty(this.categories) || this.isRegionForCategoryChanged()){
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
				this.setPrevGeoForCategory();
				this.categories && this.bindCategorySelectedEvent();
			},
			fetchingDataError : function () {
				this.dom.lockLoading.hide();
			},

			bindCategorySelectedEvent : function () {
				this.categories.eventObject.on("categorySelected", _.bind(function(e, data){
					this.setCategoryCookie(data.categorySeoName);
					this.setFiltersButtonHilight();
					this.hideCategoryModal();
					var url = this.prepareURL();
					this.isMainPage() ? (this.updateURL(url) && this.initWawSlider()) : this.loadURL(url);
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
					this.setRegionCookie(data.regionSeoName);
					this.setFiltersButtonHilight();
					this.hideRegionsModal();
					var url = this.prepareURL();
					this.isMainPage() ? (this.updateURL(url) && this.initWawSlider()) : this.loadURL(url);
				}, this));
			},

			/* Brands popup */
			initBrands : function () {
				if (_.isEmpty(this.brands) || this.isRegionForBrandsChanged()){
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
				this.setPrevGeoForBrands();
				this.brands && this.bindBrandsSelectedEvent();
			},

			bindBrandsSelectedEvent : function () {
				this.brands.eventObject.on("brandsSelected", _.bind(function(e, data){
					this.setBrandsCookie(data);
					this.setFiltersButtonHilight();
					this.hideBrandsModal();
					var url = this.prepareURL();
					this.isMainPage() ? (this.updateURL(url) && this.initWawSlider()) : this.loadURL(url);
				}, this));
			},

			/* URL methods */
			prepareURL : function () {
				var defaultRegion = defaultCategory = defaultBrand = "any",
					urlTemplate 	= _.template("/filter/<%= region %>/<%= category %>/<%= brand %>/<%= product %>");
				return urlTemplate({
					region 		: $.cookie('geo') 		|| defaultRegion,
					category 	: $.cookie('category') 	|| defaultCategory,
					brand 		: $.cookie('brand') 	|| defaultBrand,
					product 	: $.cookie('product') 	|| defaultBrand,
				});
			},

			updateURL : function (url) {
				var title = "title";
				history.pushState(null, title, url);
				this.clearSortingCookies();
				return true;
			},

			loadURL : function (url) {
				location.href = url;
			},

			
			setRegionCookie : function (data) {
				this.clearAllCookies();
				$.cookie('geo', data, this.cookieOptions);
            },
			setCategoryCookie : function (data) {
				this.clearCategoryBrandsCookies();
				$.cookie('category', data, this.cookieOptions);
                
			},
			setBrandsCookie : function (data) {
				this.clearCategoryBrandsCookies();
				if (data.brandSeoName) {
					$.cookie('brand',   data.brandSeoName, this.cookieOptions);
                }
				if (data.productSeoName) {
					$.cookie('product', data.productSeoName, this.cookieOptions);
				}
            },
            clearAllCookies : function () {
            	$.removeCookie("geo", this.cookieOptions);
            	this.clearCategoryBrandsCookies();
            	this.clearSortingCookies();
            },

			clearCategoryBrandsCookies : function () {
				$.removeCookie("category", this.cookieOptions);
				$.removeCookie("brand", this.cookieOptions);
				$.removeCookie("product", this.cookieOptions);
			},

			clearSortingCookies : function () {
				$.removeCookie("sort", this.cookieOptions);
			},

			setFiltersButtonHilight : function () {
				var defaultRegion = defaultCategory = defaultBrand = "any";
				($.cookie('geo') && ($.cookie('geo') != defaultRegion)) 
					? this.dom.regionsBtn.addClass("regactive") : this.dom.regionsBtn.removeClass("regactive");
				
				($.cookie('category') && ($.cookie('category') != defaultCategory)) 
					? this.dom.categoryBtn.addClass("catactive") : this.dom.categoryBtn.removeClass("catactive");
				
				(($.cookie('brand') && ($.cookie('brand') != defaultBrand)) || ($.cookie('product') && ($.cookie('product') != defaultBrand)))
					? this.dom.brandsBtn.addClass("bractive") : this.dom.brandsBtn.removeClass("bractive");
				
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

			isRegionForCategoryChanged : function () {
				return ($.cookie('geo') !== this.prevGeoCategory);
			},
			isRegionForBrandsChanged : function () {
				return ($.cookie('geo') !== this.prevGeoBrands);
			},

			setPrevGeoForCategory : function() {
				this.prevGeoCategory  = $.cookie('geo');
			},
			setPrevGeoForBrands : function() {
				this.prevGeoBrands  = $.cookie('geo');
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
