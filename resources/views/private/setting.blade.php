@extends('base')

@section('css')
<style>
#add-value {
    margin: 15px 0;
}

#added-value-list ul {
    list-style-type: none;
    padding: 0;
}

#added-value-list ul li {
    border: 1px solid #ccc;
    border-radius: 7px;
    display: inline-block;
    margin: 0 10px 10px 0;
    padding: 5px 10px;
}

#added-value-list ul li .glyphicon-remove {
    cursor: pointer;
    margin: 5px 0 0 5px;
}
</style>
@stop

@section('content')
<div class="c w-100">
    <div class="row">
        <form action="{{ route('settings.store') }}" method="POST">
            <div class="card">
               <div class="card-body">
                    {{ csrf_field() }}
                    
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-xs-12" for="pairs">
                            Exchange Pairs <span class="required-icon">*</span>
                        </label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" id="pairs" class="form-control col-xs-9" placeholder="" value="">
                            <button type="button" id="add-value" class="btn btn-default">Add</button>
                            <div id="added-value-list">
                                <ul>
                                    @if (!empty($pairs))
                                        @foreach ($pairs as $pair)
                                            <li>{{ $pair }}<span class="glyphicon glyphicon-remove"></span><input type="hidden" name="pairs[]" value="{{ $pair }}"></li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">{{ trans('admin.submit') }}</button>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="6 col card">
        </div>
        <div class="6 col card">
        </div>
    </div>
</div>
@stop

@section('javascript')
<script>
$(function () {
    $('#add-value').click(function() {
        var added_value = $('#pairs').val();
        if (added_value != '') {
            $('#added-value-list > ul').append('<li>'+added_value+'<span class="glyphicon glyphicon-remove"></span><input type="hidden" name="pairs[]" value="'+added_value+'"></li>');
            $('#pairs').val('');
        } else {
            alert('Please insert your exchange pair');
        }
    });

    $(document).on('click', '#added-value-list .glyphicon-remove', function() {
        $(this).parent('li').remove();
    });
});
</script>
@stop
