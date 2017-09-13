@extends('template')
@section('title', 'HotsApi')

@section('head')
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
    </style>
@endsection

@section('content')


    <img class="pull-right screenshot" src="{{asset('img/uploader.png')}}" width="261px">
    <h1>Replay uploader app</h1>
    <p>The easiest way to upload your replays it to get our uploader app</p>
    <a class="btn btn-primary btn-lg" href="{{ $setupLink }}">Download</a>

    <div class="clearfix"></div>
    <hr>

    <h1>Or you can use the web uploader</h1>
    <p>Choose replay files, or drag them onto this page. <span id="container_fileupload_dir_text" class="hidden">Chrome users: If you are uploading &gt;700 replays, please select by directory</span></p>

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
            <span id='upload_stat_skipped_toggle'> (?)</span>
        </div>
        <div id='upload_stat_skipped_hover'>
        </div>
    </div>

    <div id="progress" class="progress hidden">
        <div class="progress-bar progress-bar-success"></div>
        <div class='upload_filecount'><span id='count_complete'>0</span> out of <span id='count_total'>0</span> files</div>
    </div>

    <table id="files" class="table"><tbody></tbody></table>
@endsection

@section('body')
    <script>
        var filecount_total = 0;
        var filecount_status = [];
        var STATUS_SUCCESS = "Success"; //would love to replace this with a php variable, but that doesnt seem like a good enough reason to load ParserService into this template
        
        $(function () {
            $('#fileupload,#fileupload_dir').fileupload({
                url: '/api/v1/replays',
                sequentialUploads: true,
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
                },
             
                drop: function (e, data) {
                    
                    //stop the drag and drop event for the second upload button to avoid triggering it twice
                    if ($(this).attr('id') == 'fileupload_dir') {
                        e.preventDefault();
                        return false;
                    }
                    
                },
                
                done: function (e, data) {
                    
                    let status = data.result.status ? data.result.status : "UploadError";
                    
                    //update status text and add an appropriate class. querySelector stops after the first element is found and should therefore be a little faster than jquery as the table gets larger
                    $(document.querySelector('.upload_status')).addClass('upload_status-'+status).text(status);
                    
                    //remove individual file progressbar (keep the empty row to avoid having the table contents jump around)
                    $('.upload_table_progress').remove();
                    
                    //keep track of upload results
                    (status in filecount_status) ? filecount_status[status]++ : filecount_status[status] = 1;

                    //update stats
                    
                    //number of files processed from queue
                    $('#count_complete').text(statusSum(filecount_status));
                    
                    //number of successful uploads
                    $('#upload_stat_success > .upload_stat_num').text(filecount_status[STATUS_SUCCESS]);
                    
                    //number of skipped uploads
                    $('#upload_stat_skipped > .upload_stat_num').text(statusSum(filecount_status,[STATUS_SUCCESS]));
                    
                    $('#upload_stat_skipped_hover').html(statusSummary(filecount_status,[STATUS_SUCCESS]));
                    
                    
                },
                
                send: function(e, data) {
                    
                    //add the file to the table once the transfer begins and include a second row for its progress indicator
                    $('#files > tbody').prepend('<tr><td class=upload_filename>'+data.files[0].name+'</td><td class=upload_status>&nbsp;</td></tr><tr class=upload_table_progress_row><td colspan=2 class=upload_table_progress_container><div class="progress-bar upload_table_progress">&nbsp;</div></td></tr>');
               
               },
               
                progress: function(e, data) {
                    
                    //update the individual file progress indicator
                    let progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#files').find('.progress-bar').css('width', progress + '%');
                    
                },
                
                progressall: function (e, data) {
                    
                    //update the overall progress indicator
                    let progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress .progress-bar').css('width', progress + '%');
                    
                }
            });
            
            $('#upload_stat_skipped_toggle').hover(
                function(e) { hoverdiv(e,'upload_stat_skipped_hover'); },
                function(e) { hoverdiv(e,'upload_stat_skipped_hover'); }
            );
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
            $("#container_fileupload_dir_text,#container_fileupload_dir").removeClass('hidden');
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
            
            $("#"+divid).toggle();
            return false;
        }

    </script>
@endsection
