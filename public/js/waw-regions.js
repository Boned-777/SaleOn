	$(function () {
		var EMPTY_STRING = "";
		
		var wawRegions = function (regionsData) {
			this.mainTemplate = '<div class="row">\
        							<div data-id="1" class="span6 category-group">\
        								<i class="shopping-icon"></i>\
        								<div id="header-name" class="category-group-text">$header <span class="counter"></span></div>\
        							</div>\
        							<div class="span3">&nbsp;</div>\
						      	</div>\
						      	<div id="regions-list" class="filter-list top-offset"></div>\
						      	<div id="areas-list" class="filter-list top-offset"></div>';
			
			this.rowTemplate = ['<div class="row">', '</div>'];
			this.itemTemplate = '<div data-id="$regionId" data-path="$isPath" class="span3 category-wrapper">\
									<div class="flagi-right">$regionName <br><span class="counter">$regionCount</span></div>\
								</div>';	
			this.eventObject = $({});
			this.init(regionsData);	 
		};
		
		wawRegions.prototype = {
			
			init : function (regionsData) {
				this.data = regionsData;
				this.data.area = [];				 
				this.registerDOMElements();
				this.renderMainTemplate();
				this.updateDOMElements();
				this.countGroup(this.data.list);
				this.renderRegions();
				this.bindEvents();
				this.showModal();
			},

			registerDOMElements : function () {
				this.dom = {
					lockLayer		  : $(".lock-loading"),
					regionsModal      : $("#regions-modal"),
					regionsContent	  : $("#regions-content"),
					backBtn 		  : $("#region-back")
				}
			},

			renderMainTemplate : function () {
				this.dom.regionsContent && this.dom.regionsContent.empty();
				var header = this.mainTemplate.replace("$header", window.messages.ukraine);
				this.dom.regionsContent.html(header);
			},

			updateDOMElements : function () {
				this.dom = _.extend(this.dom, {
					headerGroup 		: $(".category-group"),
					regionsList 		: $("#regions-list"),
					areasList   		: $("#areas-list"),
					headerName 		    : $("#header-name")
				})
			},

			renderRegions : function () {
				this.dom.areasList.hide();
				var itemList = this.renderItemList(this.data.list);
				this.dom.regionsList.html(itemList);
			},

			renderItemList : function (dataList) {
				var	j 	 	 = 0,
					result 	 = EMPTY_STRING;

				for (var i = 0; i <= dataList.length; i++) {
					if (j==0) {result += this.rowTemplate[0]}
						if (dataList[i]) {
							result += this.itemTemplate
								.replace("$regionId", 		dataList[i].name)
								.replace("$isPath", 		dataList[i].is_path)
								.replace("$regionCount",	dataList[i].count)
								.replace("$regionName", 	dataList[i].value)
						} 
					if (j==2) {
						j = 0;
						result += this.rowTemplate[1]; 
					} else {j++;}
				}
				return result;
			},	

			countGroup : function (dataList) {
				var headerCount = 0;
				_.each(dataList, function(el) {
					el.count && (headerCount += el.count);
				});
				dataList[0].count = headerCount;
			},

			bindEvents : function () {
				var	that = this;
				this.dom.backBtn.on("click", _.bind(function(e){
					this.dom.headerName.text(window.messages.ukraine);
					this.dom.regionsList.show();
					this.dom.areasList.hide();
					this.dom.backBtn.addClass("hide");
				},this));
				this.dom.regionsList.on("click", _.bind(function(e){
					var row = $(e.target).closest(".category-wrapper"),
						regionId = row.data("id"),
						isPath = row.data("path");
					isPath == "1" ? this.loadAreas(regionId) : this.eventObject.trigger("regionSelected", {regionId : regionId});
				}, this));

				this.dom.areasList.on("click",_.bind(function(e){
					var regionId = $(e.target).closest(".category-wrapper").data("id");
					this.eventObject.trigger("regionSelected", {regionId : regionId});
				}, this));
			},

			loadAreas : function (regionId) {
				if (_.isEmpty(this.data.area[regionId])){
					this.dom.lockLayer.show();
					$.ajax({
						url 	: "/geo/list?term="+regionId,
				        dataType: "json",
						cache	: false
					}).done(_.bind(function(data){this.onAreasLoaded(data, regionId)}, this)).fail(_.bind(this.showError, this));
				} else {
					this.renderAreas(regionId);
				}
			},

			onAreasLoaded : function (data, regionId) {
				if (data) {
					var regionElement = _.find(this.data.list, function(el) {
						return (el.name == regionId);
					})
					this.dom.headerName.text(regionElement.value);
					this.data.area[regionId] = data;
					this.countGroup(this.data.area[regionId].list);
					this.renderAreas(regionId);
					this.dom.lockLayer.hide();
				} else {
					this.showError();
				}
			},

			renderAreas : function (regionId) {
				this.dom.regionsList.hide();
				var itemList = this.renderItemList(this.data.area[regionId].list);
				this.dom.areasList.html(itemList).show();
				this.dom.backBtn.removeClass("hide");
			},

			showError : function () {
				this.dom.lockLayer.hide();
				var errorModal = $("#error-modal-block");
					errorModal.find(".block-label").html(window.messages.serverError);
					errorModal.fadeIn( "slow" ).delay( 5000 ).fadeOut( "slow" ); 
			},

			showModal : function () {
				this.dom.regionsModal.modal({show: true});
			}
				
		};
		window.wawRegions = wawRegions;
	})
