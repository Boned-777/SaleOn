	$(function () {
		var EMPTY_STRING = "";
		
		var wawBrands = function (brandsData) {
			this.mainTemplate = '<div class="row">\
        							<div data-id="0" class="span4 category-group alert alert-success">\
        								<i class="shopping-icon"></i>\
        								<div class="category-group-text">$brands <span id="first-group-count" class="counter"></span></div>\
        							</div>\
        							<div class="span1">&nbsp;</div>\
						        	<div data-id="1" class="span4 category-group category-group-inactive">\
						        		<i class="services-icon"></i>\
						        		<div class="category-group-text">$products <span id="second-group-count" class="counter"></span></div>\
						        	</div>\
						      	</div>\
						      	<div id="brands-group-list" class="filter-list"></div>';
			
			this.rowTemplate = ['<div class="row">', '</div>'];
			this.itemTemplate = '<div data-id="$brandId" class="span3 category-wrapper">\
									<div class="flagi-right">$catName <br><span class="counter">$catCount</span></div>\
								</div>';	
			this.eventObject = $({});
			this.currentBrandsGroup = 0;
			this.init(brandsData);	 
		};
		
		wawBrands.prototype = {
			
			init : function (brandsData) {
				this.data = brandsData;				
				this.registerDOMElements();
				this.renderMainTemplate();
				this.updateDOMElements();
				this.renderBrandsGroupItems(this.currentBrandsGroup);
				this.countGroups();
				this.bindEvents();
				this.showModal();
			},

			registerDOMElements : function () {
				this.dom = {
					filterModal	      : $("#brands-modal"),
					filterContent	  : $("#brands-content")
				}
			},

			renderMainTemplate : function () {
				this.dom.filterContent && this.dom.filterContent.empty();
				var header = this.mainTemplate.replace("$brands",    this.data[0].name)
											  .replace("$products",  this.data[1].name);
				this.dom.filterContent.html(header);
			},

			updateDOMElements : function () {
				this.dom = _.extend(this.dom, {
					brandsGroup 		: $(".category-group"),
					brandsGroupList 	: $("#brands-group-list"),
					firstGroupCount 	: $("#first-group-count"),
					secondGroupCount 	: $("#second-group-count")
				})
			},

			renderBrandsGroupItems : function (groupId) {
				this.setCurrentBrandsGroup(groupId);
				var itemList = this.renderItemList(groupId);
				this.dom.brandsGroupList.html(itemList);
			},

			renderItemList : function (itemId) {
				var	j 	 	 = 0,
					result 	 = EMPTY_STRING,
					dataList = this.data[itemId].sub;

				for (var i = 0; i <= dataList.length; i++) {
					if (j==0) {result += this.rowTemplate[0]}
						if (dataList[i]) {
							result += this.itemTemplate
								.replace("$brandId", 		dataList[i].value)
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

			countGroups : function () {
				var firstGroupCount = secondGroupCount = 0;
				_.each(this.data[0].sub, function(el) {
					firstGroupCount += el.count;
				});
				_.each(this.data[1].sub, function(el) {
					secondGroupCount += el.count;
				});
				this.dom.firstGroupCount.html(firstGroupCount);
				this.dom.secondGroupCount.html(secondGroupCount);
			},

			bindEvents : function () {
				var	that = this;
				this.dom.brandsGroup.on("click", _.bind(function(e){
					var groupId = $(e.currentTarget).data("id");
					if (this.currentBrandsGroup != groupId) {
						this.switchBrandsGroupHeader();
						this.renderBrandsGroupItems(groupId);
					}
				},this));
				this.dom.brandsGroupList.on("click", _.bind(function(e){
					var id = $(e.target).closest(".category-wrapper").data("id");
					var data = (this.currentBrandsGroup == "0") ? {brandsId: id, productsId: null} : {brandsId: null, productsId: id};
					this.eventObject.trigger("brandsSelected", data);
				},this));
			},

			switchBrandsGroupHeader : function () {
				var currentActive = this.dom.filterContent.find(".category-group.alert-success");
					currentInactive = this.dom.filterContent.find(".category-group.category-group-inactive");
				currentActive.removeClass("alert alert-success").addClass("category-group-inactive");
				currentInactive.removeClass("category-group-inactive").addClass("alert alert-success");
			},

			setCurrentBrandsGroup : function (groupNumber) {
				this.currentBrandsGroup = groupNumber;
			},

			showModal : function () {
				this.dom.filterModal.modal({show: true});
			}
				
		};
		window.wawBrands = wawBrands;
	})
