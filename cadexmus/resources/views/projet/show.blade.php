@extends('layouts.app')

@section('content')
    <?php $version = $projet->versions[0] ?>
    <h1>Projet {{ $projet->nom }}</h1>
    <h2>Version {{$version->numero}}</h2>

    <p>tempo : <input id="tempo" type="number" value="{{$version->repr["tempo"]}}"></p>
    <ul id="tracks">
    @foreach ($version->repr["tracks"] as $track)
        @include("projet.track", $track)
    @endforeach
    </ul>
    <p>
        New track :
        <input type="hidden" id="samplename">
        <input type="hidden" id="sampleurl" value="samples/native/kick1.wav">
        <button data-toggle="modal" data-target="#myModal">choose sample</button>
    </p>


    <button class="save">Save</button>


    <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Choose sample</h4>
                </div>
                <div class="modal-body" style="max-height:50vh;overflow: auto">
                    liste de samples ici
                </div>
            </div>

        </div>
    </div>

    <script>
        $(function () {

            // charge le contenu de la boîte modale
            $.get("{{ route('sample.index') }}").done(function(data){
                $(".modal-body").html(data);
            });

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
                console.log(repr);

                $.ajax({
                    type: "PUT",
                    url: "{{ route('projet.update',$projet->id)}}",
                    data: {
                        repr:repr,
                        version:"{{$version->numero}}"
                    }
                })
                        .done(function(data) {
                            console.log(data)
                        })
                        .fail(function() {
                            console.log("request failed")
                        });
                //location.reload();
            });
        });
    </script>

@endsection
