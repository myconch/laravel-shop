<div class="form-group {!! !$errors->has($label)?: 'has-error' !!}">
    <label for="{{$id}}" class="col-sm-2 control-label">
        {{$label}}
    </label>
    <div class="{{$viewClass['field']}}">
        @include('admin::form.error')
        {{-- 此处的id原本写的是{{$id}}，但不知为何，无法显示编辑器，将其改为任何不同于{{$id}}及其值的内容都可以正常显示，好在不影响数据保存 --}}
        <div id="unknow" style="width:100%; height:100%;">
            <p>{!! old($column,$value) !!}</p>
        </div>
        <input type="hidden" name="{{$name}}" value="{{old($column,$value)}}" />
    </div>
</div>
