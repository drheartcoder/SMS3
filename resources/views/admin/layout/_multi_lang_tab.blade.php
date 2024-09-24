@if(isset($arr_lang) && sizeof($arr_lang)>0)
    @foreach($arr_lang as $key => $lang)
        <li class="{{ $key==0?'active':'' }}" >
            <a href="#{{$lang['locale']}}" 
                    @if(isset($edit_mode) && $edit_mode==TRUE)
                        data-toggle="tab"
                    @else
                       {{ $lang['locale']=='fr' ?'data-toggle="tab"':'' }} 
                    @endif
                > 
        		<i class="fa fa-home"></i> 
        		{{$lang['title']}} 
        	</a>
        </li>
    @endforeach
@endif