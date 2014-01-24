	$(function () {
			
		/**
		** helpers
		**/
		
		var duplicateResponce = function (source, count) {
			var result = {};
			result.list = source.list;
			for (var i = 0; i<= count; i++) {
				result.list = result.list.concat(source.list);
			}
			result.options = source.options;
			return result;
		};

		var setCookies = function () {
			var cookieOptions = { expires: 7, path: '/' };
			$.cookie('geo', '1.2', 		cookieOptions);
			$.cookie('category', '3', 	cookieOptions);
			$.cookie('brands', '111', 	cookieOptions);
			$.cookie('products', '3', 	cookieOptions);	
			//$.removeCookie("geo");
		}

		var isBrowserCompatible = function () {
		    if (navigator.sayswho == "MSIE 7.0") {
		    	$(".no-data").html(window.messages.notSupported).show();
				$("#myCarousel").hide();
				return false;
		    }	
		    return true;
		}

		var wawSlyder = window.wawSlyder,
			originalData, slider;

		$("#btn1").on("click", function(e){
			slider = null;
			slider = new wawSlyder(duplicateResponce(originalData, 1));	
		});
		$("#btn2").on("click", function(e){
			slider = null;
			slider = new wawSlyder(duplicateResponce(originalData, 3));	
		});
		$("#btn3").on("click", function(e){
			slider = null;
			slider = new wawSlyder(duplicateResponce(originalData, 9));	
		});


		/**
		** run waw slider
		*/
		if (isBrowserCompatible()){
			setCookies();
			$(".lock-loading").show();
			$.ajax({
				url: "/ad/list",
		        dataType: "json",
				cache: false
			}).done(function( data ) {
				originalData = data;
				slider = new wawSlyder(data);
				$(".lock-loading").hide();
			}).fail(function( data ) {
				$(".no-data").html(window.messages.noData).show();
				$("#myCarousel").hide();
				$(".lock-loading").hide();
			});			
		}

	})
