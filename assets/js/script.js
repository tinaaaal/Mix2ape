
function handleFileUploads(){
    var sortedOrder = $('#sortable').sortable('toArray');
    var songTitles = $('.songTitle').map(function() {
                        return $(this).text();
                        }).get();
    var songAuthors = $('.songAuthor').map(function() {
                        return $(this).text();
                        }).get();
    
    var player_name = $('#playlist_name').val();

    console.log(songTitles);

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = 'upload.php';

    var input_order = document.createElement('input');
    input_order.name = 'order';
    input_order.value = JSON.stringify(sortedOrder);
    form.appendChild(input_order);

    var input_action = document.createElement('input');
    input_action.name = 'action';
    input_action.value = 'WriteFileViaForm';
    form.appendChild(input_action);

    var input_songTitle = document.createElement('input');
    input_songTitle.name = 'songTitle';
    input_songTitle.value = JSON.stringify(songTitles);
    form.appendChild(input_songTitle);

    var input_songAuthor = document.createElement('input');
    input_songAuthor.name = 'songAuthor';
    input_songAuthor.value = JSON.stringify(songAuthors);
    form.appendChild(input_songAuthor);

    var input_playerName = document.createElement('input');
    input_playerName.name = 'player_name';
    input_playerName.value = player_name;
    form.appendChild(input_playerName);

    form.submit();
    // var fileData = {
    //     'action': 'WriteFile',
    //     'order': sortedOrder,
    //     'songTitle': songTitles,
    //     'songAuthor': songAuthors,
    //     'player_name': player_name
    // }; //should be the metadata of the files
    // console.log(sortedOrder);

    // var request;
    // request = $.ajax({
    //     url:"upload.php",
    //     type: "post",
    //     data: fileData,
    //     datatype: 'json'
    // });

    // request.done(function(response, textStatus, jqXHR){
    //     //prepare to allow user to download the file
    //     console.log('request successfully sent!');
    //     console.log(response);
    //     var json = JSON.parse(response);

    //     var a = $('#download_link');
    //     a.attr('href', json['download_url']);
    //     a.text('Download Here');
    //     a.fadeIn();
    //     a.click(function(){
    //         document.location = json['download_url'];
    //     });
        
    // });

    // request.fail(function (jqXHR, textStatus, errorThrown){
    //     console.log('request failed');
    // });
}



$(function(){
    var count = 0;
    var totalDuration = 0;
    $("#sortable").sortable({axis: "y"});

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
            

            var tpl = $('<li class="working" id = "'+ count +'"><input class = "progress" type="text" value="0" data-width="48" data-height="48"'+
                ' data-fgColor="#0788a5"    data-readOnly="1" data-bgColor="#3e4043" /><p><a href= "#" class = "songTitle" data-type="text" data-pk="1" data-title= "Enter song title" id = "title_'+ count +'"></a></p><span class = "check"></span></li>');

            // Append the file name and file size
            // tpl.find('p').text(data.files[0].name)
                         // .append('<i>' + formatFileSize(data.files[0].size) + '</i>');
            tpl.find('a').text(data.files[0].name);
            tpl.find('p').append('<a href= "#" class = "songAuthor" data-type="text" data-pk="1" data-title= "Enter author" id = "author_'+ count +'">Unknown Author</a>')
            tpl.find('p').append('<i>' + formatFileSize(data.files[0].size) +'</i>');
            
            var reader = new FileReader();
            reader.onload= function(e){
                var audioFile = jQuery('<audio controls></audio>');
                // var audioFile = document.createElement('audio controls');
                // audioFile.setAttribute('src', e.target.result);
                audioFile.attr('src', e.target.result);
                audioFile.attr('id', 'audio_'+count);
                // tpl.append(audioFile);

                // $('#audio_'+count).on("canplaythrough", function(e){
                audioFile.on("canplaythrough", function(e){
                
                    var duration = e.currentTarget.duration;
                    console.log("2duration= " + duration);
                    tpl.find('p').append('<d>'+formatDuration(duration)+'</d>');
                    audioFile.attr('src', '');
                    totalDuration = totalDuration + duration;
                    $('#current_total_duration').text(formatDuration(totalDuration)); 
                });
            } 
            reader.readAsDataURL(data.files[0]);

            
            
            
            // Add the HTML to the UL element
            data.context = tpl.appendTo(ul);

            // Initialize the knob plugin
            tpl.find('input').knob();

            // Allow editable
            $.fn.editable.defaults.mode = 'inline';
            tpl.find('a').editable();
            tpl.find('p').find('a').editable();

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

            data.formData= {'songID' : count};
            count = count +1;
            // Automatically upload the file once it is added to the queue
            var jqXHR = data.submit().success(function(result, textStatus, jqXHR){
                console.log(result);
                var json = JSON.parse(result);
                var status = json['status'];

                if(status == 'error'){
                    data.context.addClass('error');
                }

                // setTimeout(function(){
                //     data.context.fadeOut('slow');
                // },3000);
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
    };
    
    function formatDuration(duration){
        var minute = Math.floor(duration/60);
        var second = Math.round(duration- minute*60);
        if(second < 10)
            return (minute + ":0" + second);
        else
            return (minute + ":" + second);
    }
});