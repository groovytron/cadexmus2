<audio id="preview"></audio>
<table class="table table-condensed table-hover table-striped">
    <tr>
        <th>name</th>
        <th style="width:50%">tags</th>
        <th style="width:100px">action</th>
    </tr>
@foreach ($samples as $s)
    <tr class="sample">
        <td>{{ $s->nom }}</td>
        <td>{{ $s->type }}</td>
        <td class="actions-cell">
            <div class="btn-group" data-sample-url="{{asset("uploads")}}/{{$s->url}}">
                <button type="button" class="btn previewsample">Preview</button>
                <button type="button" class="btn btn-primary choosesample" data-sample-name="{{$s->nom}}" data-asset-url="{{$s->url}}">Choose</button>
            </div>
        </td>
    </tr>
@endforeach
</table>