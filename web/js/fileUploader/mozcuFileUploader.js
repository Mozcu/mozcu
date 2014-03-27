/**
 * mozcuFileUpload
 *
 * Ajax uploading service based on jQuery. 
 * Allows upload single or multiple files depends the html version of the browser.
 * The server response MUST BE json!! 
 * Author: Mauro Cristy
 * 
 * Requires jquery 1.7.2 or latest (http://jquery.com/) and ajaxFileUpload (http://www.phpletter.com/Our-Projects/AjaxFileUpload/)
 * 
 * inputId      -> File input DOM element Id
 * actionUrl    -> Server action url
 * success      -> Success callback function
 * error	-> Error callback function
 */

jQuery.extend({
    
    processMultipleUpload: function(input, url, callback) {
        var files = input.files;
        var formdata = new FormData();
        var name = (input.name != '') ? input.name : input.id;
        $.each(files, function(key, value){
            formdata.append(name + '[]', value);
        });

        var response = {};
        var callbackFunction = callback;
        $.ajax({
            url: url,
            type: "POST",
            data: formdata,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(data) {
                response.error = false;
                response.data = data
                callbackFunction(response);
            },
            error: function(jqXHR, exception) {
                response.error = true;
                response.data = exception
                callbackFunction(response);
            }
        });
    },
	
    processSingleUpload: function(input, url, callback) {
        var response = {};
        var callbackFunction = callback;
        $.ajaxFileUpload({
            url: url,
            secureuri: false,
            fileElementId: input.id,
            dataType: 'json',
            success: function (data, status) {
                response.error = false;
                response.data = data
                callbackFunction(response);
            },
            error: function(data, status, e) {
                response.error = true;	
                response.data = e
                callbackFunction(response);
            }
        });
    },

    neoUploadCallback : function(options, response) {
        if(!response.error) {
            if(typeof options.success == 'function' && options.success) {
                options.success(response.data);
            }
        } else {
            if(typeof options.error == 'function' && options.error) {
                options.error(response.data);
            }
        }
    },
	
    mozcuFileUpload: function(options) {
        var options = jQuery.extend({}, options);
        var inputId = options.inputId;
        var actionUrl = options.actionUrl;
		
        var me = document.getElementById(inputId);
		
        if(me.files) {
            jQuery.processMultipleUpload(me, actionUrl, function(response) { jQuery.neoUploadCallback(options, response); });
        } else {
            jQuery.processSingleUpload(me, actionUrl, function(response) { jQuery.neoUploadCallback(options, response); });
        }
    }
});

