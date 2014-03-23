
function handleFileUploads(){
    var fileData = {
        'action': 'WriteFile'
    }; //should be the metadata of the files

    var request;
    request = $.ajax({
        url:"upload.php",
        type: "post",
        data: fileData,
        datatype: 'json'
    });

    request.done(function(response, textStatus, jqXHR){
        //prepare to allow user to download the file
        console.log('request successfully sent!');
    });

    request.fail(function (jqXHR, textStatus, errorThrown){
        console.log('request failed');
    });
}

$(function(){

    var ul = $('#upload ul');

    $('#drop a').click(function(){
        // Simulate a click on the file input button
        // to show the file browser dialog
        $(this).parent().find('input').click();
    });

    // Initialize the jQuery File Upload plugin
    $('#upload').fileupload({

        // This element will accept file drag/drop uploading
        dropZone: $('#drop'),

        // This function is called when a file is added to the queue;
        // either via the browse button, or via drag/drop:
        add: function (e, data) {

            var tpl = $('<li class="working"><input type="text" value="0" data-width="48" data-height="48"'+
                ' data-fgColor="#0788a5"    data-readOnly="1" data-bgColor="#3e4043" /><p></p><span></span></li>');

            // Append the file name and file size
            tpl.find('p').text(data.files[0].name)
                         .append('<i>' + formatFileSize(data.files[0].size) + '</i>');

            // Add the HTML to the UL element
            data.context = tpl.appendTo(ul);

            // Initialize the knob plugin
            tpl.find('input').knob();

            // Listen for clicks on the cancel icon
            tpl.find('span').click(function(){

                if(tpl.hasClass('working')){
                    jqXHR.abort();
                }

                tpl.fadeOut(function(){
                    tpl.remove();
                });

            });

            console.log(data);
            // data.email = "hello";
            // Automatically upload the file once it is added to the queue
            var jqXHR = data.submit().success(function(result, textStatus, jqXHR){
                console.log(result);
                var json = JSON.parse(result);
                var status = json['status'];

                if(status == 'error'){
                    data.context.addClass('error');
                }

                setTimeout(function(){
                    data.context.fadeOut('slow');
                },3000);
            });
        },

        progress: function(e, data){

            // Calculate the completion percentage of the upload
            var progress = parseInt(data.loaded / data.total * 100, 10);

            // Update the hidden input field and trigger a change
            // so that the jQuery knob plugin knows to update the dial
            data.context.find('input').val(progress).change();

            if(progress == 100){
                data.context.removeClass('working');
            }
        },

        fail:function(e, data){
            // Something has gone wrong!
            data.context.addClass('error');
        }

        // stop:function(e){
        //     // All the files have been uploaded.
        //     console.log('All files have been uploaded!');
        //     var fileData = {
        //         'action': 'WriteFile'
        //     }; //should be the metadata of the files

        //     var request;
        //     request = $.ajax({
        //         url:"upload.php",
        //         type: "post",
        //         data: fileData,
        //         datatype: 'json'
        //     });

        //     request.done(function(response, textStatus, jqXHR)){
        //         //prepare to allow user to download the file
        //     });

        //     request.fail(function (jqXHR, textStatus, errorThrown){
        
        //     });

        // }

    });
    
   

    // Prevent the default action when a file is dropped on the window
    $(document).on('drop dragover', function (e) {
        e.preventDefault();
    });

    // Helper function that formats the file sizes

    function formatFileSize(bytes) {
        if (typeof bytes !== 'number') {
            return '';
        }

        if (bytes >= 1000000000) {
            return (bytes / 1000000000).toFixed(2) + ' GB';
        }

        if (bytes >= 1000000) {
            return (bytes / 1000000).toFixed(2) + ' MB';
        }

        return (bytes / 1000).toFixed(2) + ' KB';
    }

});