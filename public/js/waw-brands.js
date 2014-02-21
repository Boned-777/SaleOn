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
						      	<div id="brands-group-list" class="filter-list"></div>\
						      	<div id="brands-letters" class="row">\
						      	    <div class="span8 brands-letters-wrapper text-center">\
								        <a class="muted brands-letters">0-9</a>\
								        <a class="muted brands-letters">А</a>\
								        <a class="muted brands-letters">Б</a>\
								        <a class="muted brands-letters">В</a>\
								        <a class="muted brands-letters">Г</a>\
								        <a class="muted brands-letters">Д</a>\
								        <a class="muted brands-letters">Е</a>\
								        <a class="muted brands-letters">Є</a>\
								        <a class="muted brands-letters">Ж</a>\
								        <a class="muted brands-letters">З</a>\
								        <a class="muted brands-letters">И</a>\
								        <a class="muted brands-letters">І</a>\
								        <a class="muted brands-letters">Ї</a>\
								        <a class="muted brands-letters">Й</a>\
								        <a class="muted brands-letters">К</a>\
								        <a class="muted brands-letters">Л</a>\
								        <a class="muted brands-letters">М</a>\
								        <a class="muted brands-letters">Н</a>\
								        <a class="muted brands-letters">О</a>\
								        <a class="muted brands-letters">П</a>\
								        <a class="muted brands-letters">Р</a>\
								        <a class="muted brands-letters">С</a>\
								        <a class="muted brands-letters">Т</a>\
								        <a class="muted brands-letters">У</a>\
								        <a class="muted brands-letters">Ф</a>\
								        <a class="muted brands-letters">Х</a>\
								        <a class="muted brands-letters">Ц</a>\
								        <a class="muted brands-letters">Ч</a>\
								        <a class="muted brands-letters">Ш</a>\
								        <a class="muted brands-letters">Щ</a>\
								        <a class="muted brands-letters">Ю</a>\
								        <a class="muted brands-letters">Я</a>\
								    </div>\
							        <div class="span8 brands-letters-wrapper text-center">\
								        <a class="muted brands-letters">0-9</a>\
								        <a class="muted brands-letters">A</a>\
								        <a class="muted brands-letters">B</a>\
								        <a class="muted brands-letters">C</a>\
								        <a class="muted brands-letters">D</a>\
								        <a class="muted brands-letters">E</a>\
								        <a class="muted brands-letters">F</a>\
								        <a class="muted brands-letters">G</a>\
								        <a class="muted brands-letters">H</a>\
								        <a class="muted brands-letters">I</a>\
								        <a class="muted brands-letters">J</a>\
								        <a class="muted brands-letters">K</a>\
								        <a class="muted brands-letters">L</a>\
								        <a class="muted brands-letters">M</a>\
								        <a class="muted brands-letters">N</a>\
								        <a class="muted brands-letters">O</a>\
								        <a class="muted brands-letters">P</a>\
								        <a class="muted brands-letters">Q</a>\
								        <a class="muted brands-letters">R</a>\
								        <a class="muted brands-letters">S</a>\
								        <a class="muted brands-letters">T</a>\
								        <a class="muted brands-letters">U</a>\
								        <a class="muted brands-letters">V</a>\
								        <a class="muted brands-letters">W</a>\
								        <a class="muted brands-letters">X</a>\
								        <a class="muted brands-letters">Y</a>\
								        <a class="muted brands-letters">Z</a>\
							        </div>\
							    </div>';
			
			this.rowTemplate = ['<div class="row">', '</div>'];
			this.itemTemplate = '<div data-id="$brandId" class="span3 category-wrapper">\
									<div class="flagi-right">$brandName <br><span class="counter">$brandCount</span></div>\
								</div>';	
			this.noDataTemplate = '<div class="muted text-center no-brand-data">'+window.messages.noData+'</div>';

			this.eventObject = $({});
			this.currentBrandsGroup = 0;
			this.init(brandsData);	 
		};
		
		wawBrands.prototype = {
			
			init : function (brandsData) {
				this.data = brandsData;		
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
					firstGroupCount 	: $("#first-group-count"),
					secondGroupCount 	: $("#second-group-count"),
					brandsLetters 	    : $("#brands-letters")
				})
			},

			renderBrandsGroupItems : function (groupId) {
				this.setCurrentBrandsGroup(groupId);
				var itemList = this.renderItemList(this.data[groupId].sub);
				this.dom.brandsGroupList.html(itemList);
			},

			renderItemList : function (dataList) {
				var	j 	 	 = 0,
					result 	 = EMPTY_STRING;

				for (var i = 0; i <= dataList.length; i++) {
					if (j==0) {result += this.rowTemplate[0]}
						if (dataList[i]) {
							result += this.itemTemplate
								.replace("$brandId", 		dataList[i].value)
								.replace("$brandCount",	 	dataList[i].count)
								.replace("$brandName", 		dataList[i].name)
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
					this.renderLetter(regionId);
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
					var itemList = this.renderItemList(data);
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
