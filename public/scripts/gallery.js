


/**********************************************************************************************************************************************************
 * GLOBAL VARS/FUNCTIONS																																																																	*
 *********************************************************************************************************************************************************/
var docW = $(window).width();
var docH = window.outerHeight;
/************************************************************* END GLOBAL VARS ***************************************************************************/

/**********************************************************************************************************************************************************
 * GALLERY Object	Constructor			   	 																																																										*
 **********************************************************************************************************************************************************
 *
 *
 *
 *
 *
 *
 */
var Gallery = function(){
	var self = this;

	//object properties
	this.page = new Page();
	this.thumbnailWrappers = $('.side-images'); //The side image wrappers
	this.currentImage = $('#display img');//The currently displayed image
	this.categories = {};
	this.albums = {}; //Albums

	//Initialize object methods
	self._init();

	//Initialize Gallery events
	self.bindEvents();
}

/**********************************************************************************************************************************************************
 * GALLERY Object	Methods	   	 																																																										        *
 **********************************************************************************************************************************************************
 *
 *
 *
 *
 *
 *
 */
Gallery.prototype = {

	_init: function(){
		var self = this;
		//Build Album List
		self.buildCategories();
	},

	//Iterate though each 'ul.category-listing' and build object list with album titles.
	buildCategories: function(){
		var self = this;

		$('ul.category-listing').each(function(idx, itm){
			self.categories['category'+(idx+1)] = $(itm).children('.category-title').text();
		});

		console.log(self.categories);
	},

	//Build Album lists after a category has been selected.
	buildAlbums: function(trgt){
		var self = this;
		var album;

		$.each(self.categories, function(key, value){
			if($(trgt).attr('id') === value){
					album = value;
			} else {
				//DO SOMETHING...or nothing...whatever, you're not the boss of me!
			}
		});

		//Set current title text
		$('.current-title').stop().fadeOut(1000,function(){
			$(this).text(album);
		}).fadeIn(1000);

		//Get images
		if(album){
			self.getImages(album);
		}

		console.log(trgt);
	}
}

/**********************************************************************************************************************************************************
 * GALLERY Bind Events	   	 																																																										          *
 **********************************************************************************************************************************************************
 *
 *
 *
 *
 *
 *
 */
Gallery.prototype.bindEvents = function(){
	var self = this;

	//album-category events
	$('.category-listing img').bind('click', function(e){
			$trgt = $(e.target);
			self.buildAlbums($trgt);
	});

	//album events

	//image events

	//navigation events
}

/**********************************************************************************************************************************************************
 * GALLERY Get Images       	 																																																										        *
 **********************************************************************************************************************************************************
 *
 *
 *
 *
 *
 *
 */
Gallery.prototype.getImages = function(opt){
	var self = this;

	$.ajax({
		url: "/controllers/ajax.php",
		type: "POST",
		data: {action: opt},
		dataType: 'json',
		success: function(data){
			self.setImages(data.images, data.type, data.category);
			//console.log(data);
//			$(data).each(function(idx, itm){
//				console.log(idx, itm);
//			});
		},
		error: function(){
			console.log('NOPE');
		}
	})
}

/**********************************************************************************************************************************************************
 * GALLERY Set Images  	 																																																										              *
 **********************************************************************************************************************************************************
 *
 *
 *
 *
 *
 *
 */
Gallery.prototype.setImages = function(images, type, category){
	/*
	 Pseudo block -
	 	create a left/right image pane flag
	 	fade out exisiting selections -
	 	remove applicable class names
	 	clear html -

	  for each item in data
	  	append to approriate side
	  	fade in, vertically center
	  	set image pane flag to opposite value

	  present navigation controls - not sure where yet

	 */
	var self = this;
	var displaySide = 'left';
	var html;

	console.log(images, type, category);

	var renderContent = {
		_init: function(){

			var obj = this;

			//call all the object methods here in order
			obj.fadeOut();
			obj.clearHTML();
			obj.processData();
			obj.fadeIn();

		},
		fadeOut: function(){
			$('.side-images').children().fadeOut(1000);
		},
		fadeIn: function(){
			console.log($('.side-images').height(),$('#display').height());
			$('#images-left').css({
				'margin-top': ($('#images-left').children('ul').length > 1) ? ($('#display').height() - ($('#images-left').children('ul').length * 146))/2 : ($('#display').height() - 146)/2});
			$('#images-right').css({
				'margin-top': ($('#images-right').children('ul').length > 1) ? ($('#display').height() - ($('#images-right').children('ul').length * 146))/2 : ($('#display').height() - 146)/2});
			$('.side-images').children('ul').fadeIn(1000);
		},
		clearHTML: function(){
			$('.side-images').children().remove();
		},
		buildHTML: function(albumName, side, imageSrc){
			html = "<ul id='"+type +"-"+albumName +"' class='"+type+"-"+"listing'>";
			html+= "<li class='"+type+"-title'>"+albumName+"</li>";
			html+= "<li><img src='"+imageSrc+"' class='album-"+side+"-img'/></li>";
			html+= "</ul>";

			return html;
		},
		processData: function(){
			var obj = this;
			$.each(images, function(key, value){

				if(displaySide === 'left'){
					obj.buildHTML(key, displaySide, value);
					$('#images-left').append(html).children().hide();
					console.log(key,value);
				}

				if(displaySide === 'right'){
					obj.buildHTML(key, displaySide, value);
					$('#images-right').append(html).children().hide();
					console.log(key,value);
				}

				if(displaySide === 'left'){
					displaySide = 'right';
				} else {
					displaySide = 'left';
				}
			});
		}
	};

	renderContent._init();
}
/************************************************************* END ***************************************************************************************/
