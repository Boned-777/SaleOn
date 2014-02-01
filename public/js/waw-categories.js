	$(function () {
		var EMPTY_STRING = "";
		
		var wawCategories = function (categoriesData) {
			this.mainTemplate = '<div class="row">\
        							<div class="span3 category-group alert alert-success">\
        								<i class="shopping-icon"></i>\
        								<div class="category-group-text">$goods <span class="counter">4 589</span></div>\
        							</div>\
        							<div class="span3">&nbsp;</div>\
						        	<div class="span3 category-group category-group-inactive">\
						        		<i class="services-icon"></i>\
						        		<div class="category-group-text">$services <span class="counter"> 24 587</span></div>\
						        	</div>\
						      	</div>';
			
			this.rowTemplate = ['<div class="row">', '</div>'];
			this.itemTemplate = '<div class="span3 category-wrapper">\
									<div data-id="$catId" class="flagi-right">$catName <br><span class="counter">$catCount</span></div>\
								</div>';	
			this.eventObject = $({});
			this.init(categoriesData);	 
		};
		
		wawCategories.prototype = {
			
			init : function (categoriesData) {
				this.data = categoriesData;				
				this.registerDOMElements();
				this.renderMainTemplate();
				this.addDOMElements();
				this.bindEvents();
				this.showModal();

				$("#test").on("click", _.bind(function(e){
					this.eventObject.trigger("categorySelected", {categoryId : 15})
				}, this));
				
				
			},

			registerDOMElements : function () {
				this.dom = {
					lockLayer		  : $(".lock-gray"),
					filterModal	      : $("#filters-modal"),
					filterContent	  : $("#filters-content"),
					categoryGroup     : $(".category-group")
				}
			},

			renderMainTemplate : function () {
				this.dom.filterContent && this.dom.filterContent.empty();
				var header = this.mainTemplate.replace("$goods",    this.data[1].name)
											  .replace("$services", this.data[2].name)
				this.dom.filterContent.html(header + this.renderItemList(1));
			},	

			renderItemList : function (itemId) {
				var	j 	 	 = 0,
					result 	 = EMPTY_STRING,
					dataList = this.data[itemId].sub;

				for (var i = 0; i <= dataList.length; i++) {
					if (j==0) {result += this.rowTemplate[0]}
						if (dataList[i]) {
							result += this.itemTemplate
								.replace("$catId", 			dataList[i].id)
								.replace("$catCount",	 	dataList[i].count)
								.replace("$catName", 		dataList[i].name)
						} 
					if (j==2) {
						j = 0;
						result += this.rowTemplate[1]; 
					} else {j++;}
				}
				return result;
			},	

			showModal : function () {
				this.dom.filterModal.modal({show: true});
			},





			bindEvents : function () {
				var	that = this;
				this.dom.categoryGroup.on("click", function(e){
					var target = $(e.target);

					if (that.isRightClick(wrapperContainer)){
						that.showNextPage();	
					}
					if (that.isLeftClick(wrapperContainer)){
						that.showPreviousPage();	
					}
					
					if (that.isFavoritesClick(target)) {
						e.preventDefault();
						that.favoritesClickHandler(target);
					
					} 
				})
			}

			/* Bind handlers */

			/* Bind handlers */
			
				
		};
		window.wawCategories = wawCategories;
	})
