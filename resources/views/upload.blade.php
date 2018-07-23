@extends('template')
@section('title', 'HotsApi')

@section('head')
    <link rel="stylesheet" href="{{ asset('/css/zoom.css') }}">
    <style>
        .fileinput-button {
            position: relative;
            overflow: hidden;
            display: inline-block;
            margin-bottom: 12px;
        }
        .fileinput-button input {
            position: absolute;
            top: 0;
            right: 0;
            margin: 0;
            opacity: 0;
            -ms-filter: 'alpha(opacity=0)';
            font-size: 200px !important;
            direction: ltr;
            cursor: pointer;
        }
        #location_container {
            margin-bottom: 1em;
        }
        #location_container ul {
            display:none;
        }
        #location_container ul li {
            padding-bottom:0.5em;
        }
        #location_container ul li .blanks {
            border:1px solid #CCC;
            padding:2px;
            color:#888;
        }
        #location_toggle:hover{
            cursor:pointer;
            color:#AAA;
        }
        .location_platform {
            font-weight: bold;
        }
        .location_path {
            margin-left:1em;
        }
        .upload_filecount {
            text-align: center;
        }
        .upload_status {
            width:100px;
        }
        .upload_status-Success {
            color: #2b542c;
        }
        .upload_status-UploadError {
            color: #411c0e;
        }
        #files > tbody > tr > td.upload_table_progress_container {
            line-height: 0;
            border: none;
            padding-top: 0px;
            padding-bottom: 0px;
        }
        .upload_table_progress_row {
            height: 4px;
        }
        .upload_table_progress {
            height: 2px;
            line-height:2px;
            font-size:2px;
        }
        .screenshot {
            margin-top: 22px;
        }
        
        #button_container {
            float:left;
        }
        
        #stats_container {
            float:right;
        }
        
        #upload_stat_skipped_toggle {
            cursor: help;
        }
        
        #upload_stat_skipped_hover {
            display:none;
            position:fixed;
            border: 1px solid #222;
            background-color: #F5F8FA;
            z-index: 100;
            padding: 10px 20px;
            border-radius: 4px;
        }

        #upload_stat_skipped_hover ul {
            list-style: none;
            padding:0px;
            margin:0px;
        }
        
        #progress {
            position: relative;
            clear: both;
        }
        #progress .progress-bar {
            position:absolute;
            top:0px;
            left:0px;
        }
        #progress .upload_filecount {
            position:absolute;
            top:0px;
            left:0px;
            width:100%;
            text-align:center;
            color:#222;
        }

        .download-wrapper {
            text-align: center;
            display: inline-block;
        }
    </style>
@endsection

@section('content')


    <img class="pull-right screenshot" src="{{asset('img/uploader.png')}}" width="261px" data-action="zoom">
    <h1>Replay uploader app</h1>
    <p>The easiest way to upload your replays is to get our uploader app</p>
    <div class="download-wrapper">
        <a class="btn btn-primary btn-lg" href="{{ $setupLink }}">
            <i class="glyphicon glyphicon-download-alt"></i>
            <span>Download</span>
        </a>
        <br>
        @if($setupVersion)
            <small>version: {{ $setupVersion }}</small>
        @endif
    </div>

    <div class="clearfix"></div>
    <hr>

    <h1>Or you can use the web uploader</h1>
    <p>Choose replay files, or drag them onto this page. <span id="container_fileupload_dir_text" class="hidden">If you are uploading &gt;700 replays, please select by directory</span></p>
    
    <div id='location_container'>
        <p><span id='location_toggle'>Help finding replays <span class="glyphicon glyphicon-question-sign"></span></span></p>
        <ul>
            <li>
                <span class='location_platform'>Windows:</span>
                <span class='location_path'>C:\Users\<span class='blanks'>username</span>\Documents\Heroes of the Storm\Accounts\<span class='blanks'>id</span>\<span class='blanks'>region</span>-Hero-1-<span class='blanks'>id</span>\Replays\Multiplayer</span>
            <li>
                <span class='location_platform'>Mac:</span>
                <span class='location_path'>~/Library/Application Support/Blizzard/Heroes of the Storm/Accounts/<span class='blanks'>id</span>/<span class='blanks'>region</span>-Hero-1-<span class='blanks'>id</span>/Replays/Multiplayer</span>
        </ul>
    </div>

    <p>
        <input type='checkbox' id='check_hotslogs' name='uploadToHotslogs'> 
        <label for='check_hotslogs'>Send a copy to hotslogs</label> 
        <span class="glyphicon glyphicon-question-sign" style='cursor:help' title="HotsApi can send a copy of your replays to hotslogs. You won't need to upload it twice!"></span>
    </p>
    
    <div id='button_container'>
        <span class="btn btn-success fileinput-button">
            <i class="glyphicon glyphicon-plus"></i>
            <span>Select replays...</span>
            <input id="fileupload" type="file" name="file" multiple>
        </span>

        <span class="btn btn-success fileinput-button hidden" id="container_fileupload_dir">
            <i class="glyphicon glyphicon-plus"></i>
            <span>Select directory...</span>
            <input id="fileupload_dir" type="file" name="file" multiple directory webkitdirectory>
        </span>
    </div>
    
    <div id='stats_container' class='hidden'>
        <div id='upload_stat_success'>
            <span class='upload_stat_num'>0</span>
            <span class='upload_stat_label'> Successful</span>
        </div>
        <div id='upload_stat_skipped'>
            <span class='upload_stat_num'>0</span>
            <span class='upload_stat_label'> Skipped</span>
            <span id='upload_stat_skipped_toggle' class="glyphicon glyphicon-info-sign"></span>
        </div>
        <div id='upload_stat_skipped_hover'>
            No skipped uploads
        </div>
    </div>

    <div id="progress" class="progress hidden">
        <div class="progress-bar progress-bar-success"></div>
        <div class='upload_filecount'><span id='count_complete'>0</span> out of <span id='count_total'>0</span> files</div>
    </div>

    <table id="files" class="table"><tbody></tbody></table>
@endsection

@section('body')
    <script src="{{ asset('/js/transitions.js') }}"></script>
    <script src="{{ asset('/js/zoom.min.js') }}"></script>
    <script>
        var filecount_total = 0;
        var filecount_status = [];
        var STATUS_SUCCESS = "Success"; //would love to replace this with a php variable, but that doesnt seem like a good enough reason to load ParserService into this template
        
        $(function () {
            $('#fileupload,#fileupload_dir').fileupload({
                url: '/api/v1/replays',
                limitConcurrentUploads: 3,
                dataType: 'json',

                add: function(e, data) {
                    
                    //default actions that take place when adding files -- do not modify
                    if (data.autoUpload || (data.autoUpload !== false &&
                            $(this).fileupload('option', 'autoUpload'))) {
                        data.process().done(function () {
                            data.submit();
                        });
                    }                  
                    
                    //additional actions go here:
                    filecount_total++;
                    $('#count_total').text(filecount_total);
                    
                },
                
                start: function(e) {
                    $('#progress,#stats_container').removeClass('hidden');
                    $('#check_hotslogs').attr('disabled','disabled');
                },
             
                drop: function (e, data) {
                    
                    //stop the drag and drop event for the second upload button to avoid triggering it twice
                    if ($(this).attr('id') == 'fileupload_dir') {
                        e.preventDefault();
                        return false;
                    }
                    
                },
                
                done: function (e, data) {
                    
                    let status = data.result.status ? data.result.status : 'UploadError';
                    statusUpdate(status,uniqueID(data));
                    
                },
                
                fail: function (e, data) {
                    
                    let statuscode = data.jqXHR.status;
                    if (statuscode >= 500) {
                        var status = 'ServerError';
                    } else {
                        var status = 'UploadError';
                    }
                    
                    statusUpdate(status,uniqueID(data));
                    
                },
                
                send: function(e, data) {
                    
                    //add the file to the table once the transfer begins and include a second row for its progress indicator
                    let uid = uniqueID(data);
                    $('#files > tbody').prepend('<tr id=info_'+uid+'><td class=upload_filename>'+data.files[0].name+'</td><td class=upload_status>&nbsp;</td></tr><tr class=upload_table_progress_row id=progress_'+uid+'><td colspan=2 class=upload_table_progress_container><div class="progress-bar upload_table_progress">&nbsp;</div></td></tr>');
               
               },
               
                progress: function(e, data) {
                    
                    //update the individual file progress indicator
                    let progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress_'+uniqueID(data)).find('.progress-bar').css('width', progress + '%');
                    
                },
                
                progressall: function (e, data) {
                    
                    //update the overall progress indicator
                    let progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress .progress-bar').css('width', progress + '%');
                    
                },
                
                submit: function(e, data) {
                    
                    if (document.getElementById('check_hotslogs').checked) {
                        //include the uploadToHotslogs parameter if the checkbox is checked
                        data.formData = { uploadToHotslogs : 'true' };
                        //save a cookie so that the checkbox state is remembered for this user's subsequent visits
                        setCookie('checked_hotslogs','true',30);
                    } else {
                        //erase the cookie if the checkbox is unchecked.
                        if (getCookie('checked_hotslogs')) {
                            eraseCookie('checked_hotslogs')
                        }
                    }
                }
            });
            
            $('#upload_stat_skipped_toggle').hover(
                function(e) { hoverdiv(e,'upload_stat_skipped_hover'); },
                function(e) { hoverdiv(e,'upload_stat_skipped_hover'); }
            );
            
            $('#location_toggle').click(function(e) {
                $('#location_container > ul').toggle('fast');
            });
            
            //if there's a cookie set to check the hotslogs box, act accordingly.
            if (getCookie('checked_hotslogs')) {
                document.getElementById('check_hotslogs').checked = true;
            } else {
                document.getElementById('check_hotslogs').checked = false;
            }
                
        });
        
        //check for directory-selection compatibility. same test used in Modernizr.
        //repurposed from https://stackoverflow.com/questions/12169585/how-to-detect-directory-select-capability-in-browsers
        function isInputDirSupported() {
            var tmpInput = document.createElement('input');
            if ('webkitdirectory' in tmpInput 
                || 'directory' in tmpInput) return true;
            return false;   
        }
        
        if (isInputDirSupported()) {
            $('#container_fileupload_dir_text,#container_fileupload_dir').removeClass('hidden');
        } 
        
        function statusUpdate(status,uid) {
                                
            //update status text and add an appropriate class.
            $('#info_'+uid).find('.upload_status').addClass('upload_status-'+status).text(status);
            
            //remove individual file progressbar (keep the empty row to avoid having the table contents jump around)
            $('#progress_'+uid).find('.upload_table_progress').remove();
            
            //keep track of upload results
            (status in filecount_status) ? filecount_status[status]++ : filecount_status[status] = 1;

            //update stats
            
            //number of files processed from queue
            $('#count_complete').text(statusSum(filecount_status));
            
            //number of successful uploads
            $('#upload_stat_success > .upload_stat_num').text(filecount_status[STATUS_SUCCESS]);
            
            //number of skipped uploads
            let skipcount = statusSum(filecount_status,[STATUS_SUCCESS]);
            $('#upload_stat_skipped > .upload_stat_num').text(skipcount);
            if (skipcount > 0) {
                $('#upload_stat_skipped_hover').html(statusSummary(filecount_status,[STATUS_SUCCESS]));
            }
        }
        
        //add up all elements in the status array. optionally pass exclude to skip specified elements
        function statusSum(array,exclude) {
            if (exclude === undefined) { exclude = []; }
            return Object.keys(array).reduce(function (a,b) {
                return (exclude.indexOf(b) >= 0) ? a : a+array[b];
            },0);
        }
        
        //format the status summary. optionally pass exclude to skip specified elements
        function statusSummary(array,exclude) {
            if (exclude === undefined) exclude = [];
            if (Object.keys(array).length == 0) return false;
            output = '<ul>';
            Object.keys(array).forEach(function(k) {
                 if (exclude.indexOf(k) == -1) {
                    output += '<li><span class=status_summary_count>'+array[k]+'</span> <span class=status_summary_key>'+k+'</span>';
                 }
            });
            output += '</ul>';
            return output;
        }
        
        //repurposed from https://stackoverflow.com/questions/15158180/show-div-at-mouse-cursor-on-hover-of-span
        function hoverdiv(e,divid){
            var div = document.getElementById(divid);

            div.style.right = ($(window).width() - e.clientX)  + "px";    
            div.style.top = e.clientY  + "px";
            
            $('#'+divid).toggle();
            return false;
        }
        
        //generate a unique ID using a fileupload data object.
        //originally wanted to generate a fingerprint using lastmodified miliseconds + filesize but it turns out some browsers dont support lastmodified.
        //using stripped down filename + filesize instead, with a prepended 'f_' to comply with html5 id specs in case people rename their replays to be just numbers or symbols for whatever reason.
        function uniqueID(data) {
            return 'f_'+data.files[0].name.split('.')[0].replace(/\W/g, '') + String(data.files[0].size);
        }
        
        //next 3 functions shamelessly copied from https://stackoverflow.com/questions/14573223/set-cookie-and-get-cookie-with-javascript
        function setCookie(name,value,days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days*24*60*60*1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "")  + expires + "; path=/";
        }
        function getCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
            }
            return null;
        }
        function eraseCookie(name) {   
            document.cookie = name+"=; Max-Age=-99999999;";  
        }

    </script>
@endsection
