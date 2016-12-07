
$(function () {

    var track_template = require("../../views/projet/track.hbs");
    var note_template = require("../../views/projet/note.hbs");
    var repr_template = require("../../views/projet/repr.hbs");

    function addTrack(name, url){
        var newTrack = track_template({
            sample: {
                name: name,
                url: url
            }
        });
        $("#grid").append(newTrack);
        resetTimebarSize();
    }

    // pareil pour $(".removetrack, .removenote").click()
    // on trigger l'évènement sur l'élément #tracks, et on descend jusqu'à un élément .remove*
    $("#tracks").on("click",".removetrack, .removenote",function () {
        $(this).parent().remove();
    });

    $(".modal-body").on("click",".sample",function () {
        addTrack($(this).attr("sampleName"),$(this).attr("sampleUrl"));
        $("#myModal").modal("hide");
    });

    function replaceTracks(repr){
        $("#repr").html(repr_template(repr));
    }


    $(".save").click(function () {
        var repr ={
            tempo: $("#tempo").val(),
            tracks:[]
        };
        $(".track").each(function(i) {
            var track = {
                sample:{
                    url:$(this).children(".sample_url").val(),
                    name:$(this).children(".sample_name").val()
                },
                notes:[]
            };

            $(this).find(".note").each(function(j){
                var note = {
                    pos: $(this).children(".note_pos").val(),
                    len: $(this).children(".note_len").val()
                };
                track.notes.push(note);
            });

            repr.tracks.push(track);
        });

        $.ajax({
            type: "PUT",
            url: projectUrl,
            data: {
                repr:repr,
                version:versionActuelle
            }
        }).done(function(data) {
            if(data.version != "undefined"){
                versionActuelle=data.version;
                $("#version").html(versionActuelle);
            }
            console.log(data.message);
            info(data.message);
        }).fail(function() {
            console.log("request failed");
        });
        //location.reload();
    });

    $(".refresh").click(refresh);


    function info(data){
        $("#infos").text(data);
        $("#infos").show();
        $("#infos").fadeOut(1500);
    }

    function refresh() {
        $.ajax({
            type: "GET",
            url: projectUrl + "/" + versionActuelle
        }).done(function (data) {
            if (data == 0) {
                info("déjà à jour");
            } else {
                versionActuelle = data.numero;
                replaceTracks(data.repr);
                $("#version").html(versionActuelle);
            }
        }).fail(function () {
            console.log("request failed");
        });
    }

    var interval;

    $("#autoRefresh").change(function(){
        if($(this).prop('checked')){
            interval = setInterval(refresh, 2000);
        }else{
            clearInterval(interval);
        }
    });






    /* Time bars */
    for(var i=0;i<100;i+=100/32){
        type= (i%(100/8)==0?(i%(100/4)==0?"gridbar4":"gridbar8"):"gridbar32");
        $(".barline").append('<div class="gridbar '+type+'" style="left:'+i+'%"></div>')
    }

    function resetTimebarSize(){
        $(".gridbar").height($("#grid").height()-1);
    }

    resetTimebarSize();


    /* Notes positions */

    function placeNotes(){
        $('.note').each(function(){
            placeNote($(this));
        });
    }
    function placeNote(n){
        var left = n.attr('pos')*100/32;
        var width= n.attr('len')*100/32;
        n.css({'left':left+'%','width':width+'%','top':0})
    }
    placeNotes();

    /* create and remove notes */

    $("#grid").on("dblclick",".line",function(e){
        var pw = $(this).width();
        var pos = parseInt(32*e.offsetX/pw);
        var newNote= note_template({
            pos:pos,len:1
        });
        $(this).append(newNote);
        // todo: placeNote(newNote) avec newNote un élément dom
        placeNotes();
    });

    $("#grid").on("dblclick",".note",function(e){
        $(this).remove();
        e.stopPropagation();
    });


    /* jquery.ui draggable and resisable */

    function makeDraggableAndResizable(){
        $( ".note" ).draggable({
            axis:"x",
            //snap: true,
            //snap: ".gridbar",
            //snapTolerance: 5,
            stop:function(event,ui){
                var pw = $(this).parent().width();
                var x=ui.position.left;
                var pos = parseInt((32*x/pw)+0.5);
                $(this).attr('pos',pos);
                if(x<0 || x>= pw)
                    $(this).remove();
                placeNotes();
            }
        }).resizable({
            handles: "e", // ne prend en charge que le côté droit (east)
            stop:function(event,ui){
                var pw = $(this).parent().width();
                var len = parseInt((32*ui.size.width/pw)+.5);
                if(len==0)len=1;
                $(this).attr("len",len);
                placeNotes();
            }
        });
    }
    makeDraggableAndResizable();
});
