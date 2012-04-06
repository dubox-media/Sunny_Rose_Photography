/* Javascript document
 *
 * @Author:	Ken Stowell
 * @Date:		
 *
 * @Description: 
 */
 
/**********************************************************************************************************************************************************
 * GLOBAL VARS/FUNCTIONS																																																																	*
 *********************************************************************************************************************************************************/

/************************************************************* END GLOBAL VARS ***************************************************************************/ 


/**********************************************************************************************************************************************************
 * DOCUMENT READY																																																																		      *
 *********************************************************************************************************************************************************/
$(document).ready(function(){
 	var photoObj = new Photos();
	window.photoObj = photoObj;
});

/**********************************************************************************************************************************************************
 * WINDOW LOAD    																																																																	      *
 *********************************************************************************************************************************************************/
$(window).load(function(){

});
 

/**********************************************************************************************************************************************************
 * 	Photos Object																																																																		                  *
 **********************************************************************************************************************************************************
 *
 *
 *
 * @Methods: trace( this.constructor -> this.init -> this.addPhoto)
 *
 *
*/
var Photos = function(){
	var self = this;

	//member vars
	var content, clonedContent;

	//Init object methods
	this.init();
}

/**
 *  PHOTOS' PROTOTYPE
 *
 *  @desc: object methods for the Photos object
 */
Photos.prototype = {

	/**
	 * INIT
	 *
	 * @desc: Initialize elements and object methods in order of necessity
	 *
	 */
	init: function(){
		//resolve scope
		var self = this;

		//Set up event handlers used throughout the script
		this.bindEvents();

		//Build additional form elements on load as necessary
		this.buildAddPhotosForm();

		//Build additional elements on load or update
		this.buildEditPhotosTable();
	},

	/**
	 * BIND EVENTS
	 *
	 * @desc: Set up common event handlers
	 *
	 */
	bindEvents: function(){
		var self = this;

		//Add another form row when user clicks button
		$('#AddAnotherPhoto').live('click', function(){
			$(self.clonedContent).clone().appendTo('#fieldset-AddPhotos');
		});

		//Submit add photos button
		$('#submit-element #submit').live('click', function(){

			//get the total amount of child <dl>'s so that on the last iteration special attention can be given
			var total = $('#fieldset-AddPhotos dl').length;

			$('#fieldset-AddPhotos dl').each(function(idx, itm){

				//Use a timeout to prevent race conditions that facilitate db vals being stored out of order.
				setTimeout(function(){self.addPhotosAjax(itm, self.content, idx, total)	},800*idx);
			});
		});

		//Update line item photo
		$('a#update-this-photo').live('click', function(e){
			e.preventDefault();
			self.updatePhoto(this);
		});

		//Update All selected photos
		$('a#update-selected-photos').live('click', function(e){
			e.preventDefault();

			$('#list-photo-selected:checked').each(function(idx, itm){
					console.log(itm);
					self.updatePhoto(itm);
			});
		});

		//Delete line item photo
		$('a#delete-this-photo').live('click', function(e){
			e.preventDefault();
			self.deletePhoto(this);
		});

		//Delete selecte photos
		$('a#delete-selected-photos').live('click', function(e){
			e.preventDefault();
			$('#list-photo-selected:checked').each(function(idx, itm){
				console.log(itm);
				self.deletePhoto(itm);
			});
		});

		//Duplicate thumbnail handler
		$('input#is_thumbnail').live('change', function(){
			var elem = this;
			var i = 0;
			$('input#is_thumbnail:checked').each(function(idx, itm){
				//find parents
				if ($(this).parents('tr').children('input#album_name').val($(elem).parents('tr').children('input#album_name').val())){
					i++;
				}
			});

			//if the for each loop returned more than 2 elements
			if(i >= 2){

				//Set the duplicate image title
				$('#duplicate-image-title').text($(elem).parents('tr').children().find('#image_title').val());

				//open the dialog
				$('section#duplicate-thumbnail-dialog').dialog({
					title: "Duplicate thumbnail detected",
					height: 310,
					width: 320,
					modal: true,
					buttons:
						{"Yes, make this one the thumbnail" : function(){
									$('input#is_thumbnail:checked').not(elem).prop('checked', false);
									$(this).dialog("destroy");
							},
						 "No, dood - keep the other one" : function(){
							$(elem).prop('checked', false);
							 $(this).dialog("destroy")
						 }
						},
					closeOnEscape:true

				});
			}
		});
	},

	/**
	 * BUILD ADD PHOTOS FORM
	 *
	 * @desc: construct the add photos form of the /admin/photo page
	 */
	buildAddPhotosForm: function(){
		var self = this;

		//Bypass the Zend_Form image controls
		var data = '<dd id="image-upload">\n' +
										'<input type="file" name="image_upload" id="image_upload">\n' +
							 '</dd>';
		$('#thumbnail-label').before(data);

		/**
		 * get dl child object before form values are added by user
		 * to prevent entered values from being copied as well
		 */
		self.content = $('#fieldset-AddPhotos').children('dl')[0];
		self.clonedContent = $(this.content).clone();

	},

	/**
	 * BUILD EDIT PHOTOS TABLE
	 */
	buildEditPhotosTable: function(){
		var self = this;

		//if thumbnail checkboxes have a value - display as checked
		$('.load-wrapper #edit-photos-table input#is_thumbnail').each(function(idx, itm){
			if(itm.value == 1){
				itm.checked = true;
			}
		});
	},

	/**
	 * ADD PHOTOS AJAX:
	 *
	 * @desc: Ajax method to get Add Photos' form element values and send to server for db processing
	 *
	 *
	 * @param self = current element being processed within loop
	 * @param content = buildAddPhotosForm.content
	 * @param idx = current index of for each loop; used to isolate last iteration
	 * @param total = total number of parent elements that need iteration; used to determine last loop so that the form can be reset
	 *
	 */
	addPhotosAjax: function(self,content, idx, total){
		var base = this;
		$.ajax({
			url: '/admin/photo/add-photos/',
			data: {
				image_name: $(self).find('#image_title').val(),
				album_name: $(self).find('#album_name').val(),
				category_name: $(self).find('#category_name').val(),
				is_thumb:  $(self).find('input[name="thumbnail"]').is(':checked') ? 1 : 0
			},
			dataType: 'json',
			type: 'POST',
			beforeSend: function(){
				console.log(this.data);
			},
			success: function(data){

				//get the last ID and set to hidden input
				$(self).find('input[name=id]').val(data);

				//process attached image
				base.uploadPhoto(self);

				//Set this to occur only on the last iteration so the the db vals get stored correctly
				if(idx === total -1){
					$('#fieldset-AddPhotos input').each(function(){
						if($(this).is('input[type="checkbox"]')){
							$(this).prop('checked', false);
						} else {
							$(this).val('');
						}
						//Set form back to one row only
						$('#fieldset-AddPhotos').html(content);

						//Refresh edit photos section to include new data
						base.refreshList();
					});
				}
			},
			error: function(xhr){
				console.log('ERROR: ', xhr);
			},
			complete: function(xhr, textStatus, errorThrown, data){
				console.log('COMEPLTE: ', xhr, textStatus, errorThrown, data);
			}
		});
	},

	/**
	 * UPLOAD PHOTO
	 *
	 * @desc: use HTML5 FormData object to gather files in input elements to send to server script asynchronously for processing
	 * 				called only upon successful completion of non-file input elements being sent to server.
	 *
	 * @param self = submitPhoto()->each()->self, needed to align currently processed elements so one photo doesn't get put in the wrong directory, etc.
	 *
	 */
	uploadPhoto: function(self){
		var formdata = new FormData();

		//Even though each iteration will always return one, this is a more reliable way to append fle data to the Form object
		$.each($(self).find('input[name=image_upload]')[0].files, function(i, file){
			formdata.append('file_'+i, file);
		});

		//Append additional post vals for directory manipulation in the php script
		formdata.append('id',$(self).find('input[name=id]').val() )
		formdata.append('album-name',$(self).find('#album_name').val());
		formdata.append('category-name',$(self).find('#category_name').val());

		//Send to server
		$.ajax({
			url: '/admin/photo/upload',
			type: 'POST',
			data: formdata,
			beforeSend: function(){
				console.log('FORM DATA: ',formdata);
			},
			success: function(data){
				console.log('UPLOAD SUCCESS', data);
			},
			error: function(xhr){
				console.log('UPLOAD ERROR: ', xhr);
			},
			complete: function(xhr, textStatus){
				console.log('UPLOAD COMEPLTE: ', xhr, textStatus);
			},
			//Must set this for async file uploads so jQuery doesn't process the data with a specific protocol
			cache: false,
			contentType: false,
			processData: false
		});
	},

	/**
	 *  REFRESH LIST
	 *
	 *  @desc: refresh the photos listing after adding/updating/deleting a photo
	 */
	refreshList: function(){
		var self = this;
		$('section#edit-photos .load-wrapper').load('/admin/photo/edit-photos #edit-photos-table', function(){
			//if thumbnail checkboxes have a value - display as checked
			$('.load-wrapper #edit-photos-table input#is_thumbnail').each(function(idx, itm){
				if(itm.value == 1){
					itm.checked = true;
				} else {
					itm.checked = false;
				}
			});
		});
	},

	/**
	 *  UPDATE PHOTO
	 *  @param @param: elem - the current item selected for updating -comes from this.bindEvents click handler
	 */
	updatePhoto: function(elem){
		var self = this;

		console.log($(elem).parents('tr').children().find('#is_thumbnail'));
		//process code with arg passed to function
		if(elem){
			$.ajax({
				url: '/admin/photo/update-photos',
				data: {
					id: $(elem).parents('tr').children().find('#img_id').val(),
					image_title: $(elem).parents('tr').children().find('#image_title').val(),
					album_name:$(elem).parents('tr').children().find('#album_name').val(),
					album_name_existing: $(elem).parents('tr').children().find('#album_name_existing').val(),
					category_name: $(elem).parents('tr').children().find('#category_name').val(),
					category_name_existing: $(elem).parents('tr').children().find('#category_name_existing').val(),
					is_thumb:  ($(elem).parents('tr').children().find('#is_thumbnail').is(':checked')) ? 1 : 0,
					file_name: $(elem).parents('tr').children().find('#file_name').val(),
					file_name_existing: $(elem).parents('tr').children().find('#file_name_existing').val()
				},
				type: 'POST',
				beforeSend: function(){
					console.log(this.data);
				},
				success: function(data){
					console.log('UPDATE SUCCESS', data);
					self.refreshList();
				},
				error: function(xhr){
					console.log('UPDATE ERROR: ', xhr);
				},
				complete: function(xhr, textStatus){
					console.log('UPDATE COMEPLTE: ', xhr, textStatus);
				}
			});
		}
	},

	/**
	 * DELETE PHOTO
	 *
	 * @desc: deletes photo or photos based on link clicked.
	 * @param: elem - the current item selected for deletion -comes from this.bindEvents click handler
	 */
	deletePhoto: function(elem){
		var self = this;
		//process code with arg passed to function
		if(elem){
			$.ajax({
				url: '/admin/photo/delete-photos',
				data: {
					id: $(elem).parents('tr').children().find('#img_id').val(),
					album_name:$(elem).parents('tr').children().find('#album_name').val(),
					category_name: $(elem).parents('tr').children().find('#category_name').val()
				},
				type: 'POST',
				beforeSend: function(){
					console.log(this.data);
				},
				success: function(data){
					console.log('UPDATE SUCCESS', data);
					self.refreshList();
				},
				error: function(xhr){
					console.log('UPDATE ERROR: ', xhr);
				},
				complete: function(xhr, textStatus){
					console.log('UPDATE COMEPLTE: ', xhr, textStatus);
				}
			});
		}
	}
};

/************************************************************* END ***************************************************************************************/ 
