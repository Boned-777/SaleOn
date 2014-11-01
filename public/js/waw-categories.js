	$(function () {
		var EMPTY_STRING = "";
		
		var wawCategories = function (categoriesData) {
			this.mainTemplate = '<div class="row">\
        							<div data-id="1" class="span4 category-group alert alert-success">\
        								<i class="shopping-icon2"></i>\
        								<div class="category-group-text">$goods <span id="first-group-count" class="counter"></span></div>\
        							</div>\
        							<div class="span1">&nbsp;</div>\
						        	<div data-id="2" class="span4 category-group category-group-inactive">\
						        		<i class="services-icon3"></i>\
						        		<div class="category-group-text">$services <span id="second-group-count" class="counter"></span></div>\
						        	</div>\
						      	</div>\
						      	<div id="category-group-list" class="filter-list"></div>';
			
			this.rowTemplate = ['<div class="row">', '</div>'];
			this.itemTemplate = '<div data-id="$catId" data-cat-seo-name="$seoName" class="span3 category-wrapper">\
									<div class="flagi-right"><div title="$catName" class="ellipsis">$catName</div><span class="counter">$catCount</span></div>\
								</div>';	
			this.eventObject = $({});
			// this.filterName = "Категории"; 
			this.currentCategoryGroup = 1;
			this.init(categoriesData);	 
		};
		
		wawCategories.prototype = {
			
			init : function (categoriesData) {
				this.data = categoriesData;				
				this.registerDOMElements();
				this.renderMainTemplate();
				this.updateDOMElements();
				this.renderCategoryGroupItems(this.currentCategoryGroup);
				this.countGroups();
				this.bindEvents();
				this.showModal();
			},

			registerDOMElements : function () {
				this.dom = {
					filterModal	      : $("#filters-modal"),
					filterContent	  : $("#filters-content")
				}
			},

			renderMainTemplate : function () {
				this.dom.filterContent && this.dom.filterContent.empty();
				var header = this.mainTemplate.replace("$goods",    this.data[1].name)
											  .replace("$services", this.data[2].name);
				this.dom.filterContent.html(header);
			},

			updateDOMElements : function () {
				this.dom = _.extend(this.dom, {
					categoryGroup 		: $(".category-group"),
					categoryGroupList 	: $("#category-group-list"),
					firstGroupCount 	: $("#first-group-count"),
					secondGroupCount 	: $("#second-group-count")
				})
			},

			renderCategoryGroupItems : function (groupId) {
				this.setCurrentCategoryGroup(groupId);
				var itemList = this.renderItemList(groupId);
				this.dom.categoryGroupList.html(itemList);
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
								.replace("$seoName", 		dataList[i].seo_name)
								.replace("$catCount",	 	dataList[i].count)
								.replace(/\$catName/gi, 	dataList[i].name)
						} 
					if (j==2) {
						j = 0;
						result += this.rowTemplate[1]; 
					} else {j++;}
				}
				return result;
			},	

			countGroups : function () {
				var firstGroupCount = secondGroupCount = 0;
				_.each(this.data[1].sub, function(el) {
					firstGroupCount += el.count;
				});
				_.each(this.data[2].sub, function(el) {
					secondGroupCount += el.count;
				});
				this.dom.firstGroupCount.html(firstGroupCount);
				this.dom.secondGroupCount.html(secondGroupCount);
			},

			bindEvents : function () {
				var	that = this;
				this.dom.categoryGroup.on("click", _.bind(function(e){
					var groupId = $(e.currentTarget).data("id");
					if (this.currentCategoryGroup != groupId) {
						this.switchCategoryGroupHeader();
						this.renderCategoryGroupItems(groupId);
					}
				},this));
				this.dom.categoryGroupList.on("click", _.bind(function(e){
					var categorySeoName = $(e.target).closest(".category-wrapper").data("cat-seo-name");
					this.eventObject.trigger("categorySelected", {categorySeoName : categorySeoName})
				},this));
			},

			switchCategoryGroupHeader : function () {
				var currentActive = this.dom.filterContent.find(".category-group.alert-success");
					currentInactive = this.dom.filterContent.find(".category-group.category-group-inactive");
				currentActive.removeClass("alert alert-success").addClass("category-group-inactive");
				currentInactive.removeClass("category-group-inactive").addClass("alert alert-success");
			},

			setCurrentCategoryGroup : function (groupNumber) {
				this.currentCategoryGroup = groupNumber;
			},

			showModal : function () {
				this.dom.filterModal.modal({show: true});
			}
				
		};
		window.wawCategories = wawCategories;
	})
