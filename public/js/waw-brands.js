	$(function () {
		var EMPTY_STRING = "",
			ITEMS_PER_PAGE = 20;
		
		var wawBrands = function (brandsData) {
			this.mainTemplate = '<div class="row">\
        							<div data-id="0" class="span4 category-group alert alert-success">\
        								<i class="shopping-icon"></i>\
        								<div class="category-group-text">$brands <span id="first-brand-group-count" class="counter"></span></div>\
        							</div>\
        							<div class="span1">&nbsp;</div>\
						        	<div data-id="1" class="span4 category-group category-group-inactive">\
						        		<i class="services-icon"></i>\
						        		<div class="category-group-text">$products <span id="second-brand-group-count" class="counter"></span></div>\
						        	</div>\
						      	</div>\
						      	<div id="brands-group-list" class="filter-list"></div>\
						      	<div class="left-arrow"></div><div class="right-arrow"></div>';
			
			this.rowTemplate = ['<div class="row">', '</div>'];
			this.itemTemplate = '<div data-id="$brandId" class="span3 category-wrapper">\
									<div class="flagi-right"><div title="$brandName" class="ellipsis">$brandName</div><span class="counter">$brandCount</span></div>\
								</div>';	
			this.noDataTemplate = '<div class="muted text-center no-brand-data">'+window.messages.noData+'</div>';

			this.eventObject = $({});
			this.currentBrandsGroup = 0;
			this.currentPage = 1;
			this.init(brandsData);	 
		};
		
		wawBrands.prototype = {
			
			duplicateResponce : function (source, count) {
				var result = [],
					temp = [];
				for (var i = 0; i<= count; i++) {
					temp = temp.concat(_.clone(source[0].sub));
				}
				for (var j = 0; j< temp.length; j++) {
					temp[j] = {
						name  : temp[j].name  + " название " + (j+1),
						count : temp[j].count,
						value : temp[j].value
					};
				}
				result[0] = [];
				result[0].sub = temp;
				result[0].name = source[0].name;
				result[1] = [];
				result[1].name = source[1].name;
				result[1].sub = source[1].sub;
				return result;
			},

			init : function (brandsData) {
				this.data = this.duplicateResponce(brandsData, 17);
				this.data.letters = [];
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
					filterContent	  : $("#brands-content"),
					lockLayer		  : $(".lock-loading")
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
					firstGroupCount 	: $("#first-brand-group-count"),
					secondGroupCount 	: $("#second-brand-group-count"),
					brandsLetters 	    : $("#brands-letters"),
					leftArrow 			: $(".left-arrow"),
					rightArrow  		: $(".right-arrow")
				})
			},

			renderBrandsGroupItems : function (groupId) {
				this.currentPage = 1;
				this.setCurrentBrandsGroup(groupId);
				var itemList = this.renderItemList(this.data[groupId].sub, this.getIndexes(1));
				this.dom.brandsGroupList.html(itemList);
			},

			renderItemList : function (dataList, indexes) {
				var	j 	 	 = 0,
					result 	 = EMPTY_STRING,
					count 	 = dataList.length;
				for (var i = indexes.startIndex; i <= indexes.endIndex; i++) {
					if (j==0) {result += this.rowTemplate[0]}
						if (dataList[i]) {
							result += this.itemTemplate
								.replace("$brandId", 		dataList[i].value)
								.replace("$brandCount",	 	dataList[i].count)
								.replace(/\$brandName/gi, 		dataList[i].name)
						} 
					if (j==2) {
						j = 0;
						result += this.rowTemplate[1]; 
					} else {j++;}
				}
				this.initPaging(count);
				return result;
			},	

			initPaging : function (totalCount) {
				if (totalCount > ITEMS_PER_PAGE) {
					if (this.currentPage == 1)								{ this.dom.leftArrow.hide();this.dom.rightArrow.show(); }
					if (this.currentPage == this.getPageCount(totalCount))	{ this.dom.leftArrow.show();this.dom.rightArrow.hide(); }
					if (this.currentPage > 1 && this.currentPage < this.getPageCount(totalCount)) {this.dom.leftArrow.add(this.dom.rightArrow).show();}	
					
				} else {
					this.dom.leftArrow.add(this.dom.rightArrow).hide();
				}
			},
			getIndexes : function (page) {
				return {
					startIndex 	: (page - 1) * ITEMS_PER_PAGE,
					endIndex 	: (page * ITEMS_PER_PAGE) - 1
				}
			},
			
			getPageCount : function (totalCount) {
				return Math.ceil(totalCount / ITEMS_PER_PAGE);
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
					if (!id) {return;}
					var data = (this.currentBrandsGroup == "0") ? {brandsId: id, productsId: null} : {brandsId: null, productsId: id};
					this.eventObject.trigger("brandsSelected", data);
				},this));

				this.dom.leftArrow.on("click", _.bind(function(e){
					this.currentPage--;
					var itemList = this.renderItemList(this.data[this.currentBrandsGroup].sub, this.getIndexes(this.currentPage));
					this.dom.brandsGroupList.html(itemList);

				},this));
				this.dom.rightArrow.on("click", _.bind(function(e){
					this.currentPage++;
					var itemList = this.renderItemList(this.data[this.currentBrandsGroup].sub, this.getIndexes(this.currentPage));
					this.dom.brandsGroupList.html(itemList);
				},this));

				this.dom.brandsLetters.on("click", _.bind(function(e){
					var letter = $(e.target).closest(".brands-letters").text();
					letter && this.loadLetter(letter);
				},this));
			},
			/* Letter filtering */
			loadLetter : function (letter) {
				if (_.isEmpty(this.data.letters[letter])){
					this.dom.lockLayer.show();
					var link = (this.currentBrandsGroup == "0") ? "brands/list?term="+letter : "products/list?term="+letter;
					$.ajax({
						url 	: link,
				        dataType: "json",
						cache	: false
					}).done(_.bind(function(data){this.onLetterLoaded(data, letter)}, this)).fail(_.bind(this.showError, this));
				} else {
					this.renderLetter(letter);
				}
			},

			onLetterLoaded : function (data, letter) {
				if (data) {
					this.data.letters[letter] = data;
					this.countGroups(this.data.letters[letter].list);
					this.renderLetter(letter);
					this.dom.lockLayer.hide();
				} else {
					this.showError();
				}
			},

			renderLetter : function (letter) {
				var data = this.data.letters[letter].list;
				if (!_.isEmpty(data)) {
					var itemList = this.renderItemList(data, this.getIndexes(1));
					this.dom.brandsGroupList.html(itemList);
				} else {
					this.dom.brandsGroupList.html(this.noDataTemplate);
				}
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

			showError : function () {
				this.dom.lockLayer.hide();
				var errorModal = $("#error-modal-block");
					errorModal.find(".block-label").html(window.messages.serverError);
					errorModal.fadeIn( "slow" ).delay( 5000 ).fadeOut( "slow" ); 
			},


			showModal : function () {
				this.dom.filterModal.modal({show: true});
			}
				
		};
		window.wawBrands = wawBrands;
	})
