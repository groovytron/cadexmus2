require('./bootstrap');
require('./projet.show');
require('./invite');

import Invite from './invite'

$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    new Invite('.invite')

    if ($('#title').length) {
        $('.modal-dialog').on('submit','#searchSampleForm', function(event){
            event.preventDefault();

            var pattern = $("#search-pattern").val();

            $.get($(this).attr("action"), {pattern: pattern}).done(function(data){
                $("#sample-list").html(data);
            });
        });
    }

});
