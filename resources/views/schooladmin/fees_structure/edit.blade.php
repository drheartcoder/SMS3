@extends('schooladmin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{url('/')}}/school_admin/dashboard">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <li>
          <i class="{{$module_icon}}"></i>
          <a href="{{$module_url_path}}">{{$page_title}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <i class="{{$create_icon}}"></i>
        <li class="active">{{$module_title}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i>{{$page_title}}</h1>

    </div>
</div>
<!-- END Page Title -->
              <!-- BEGIN Tiles -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box  box-navy_blue">
                        <div class="box-title">
                            <h3><i class="{{$edit_icon}}"></i>{{$module_title}}</h3>
                            <div class="box-tool">
                            </div>
                        </div>
                        <div class="box-content">
                            @include('schooladmin.layout._operation_status')
                            <form method="POST" action="{{$module_url_path.'/update/'.base64_encode($arr_data[0]["level_id"])}}"  class="form-horizontal" onsubmit="return addLoader()" id="validation-form1" enctype="multipart/form-data" novalidate="novalidate">
                               {{ csrf_field() }}
                               

                                <div class="col-sm-4 col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">{{translation('level')}}<i class="red">*</i></label>
                                    <div class="controls">
                                        <input type="hidden" name="level" value="{{isset($arr_data[0]['level_id']) ? $arr_data[0]['level_id'] : ''}}">
                                        <select name="level_id" class="form-control" data-rule-required="true" disabled>
                                            <option value="">{{translation('select_level')}}</option>
                                            @foreach($arr_levels as $level)
                                                <option value="{{$level['level_id']}}"
                                                    @if(isset($arr_data[0]['level_id']) && $arr_data[0]['level_id'] == $level['level_id'] )
                                                        selected
                                                    @endif
                                                >{{$level['level_details']['level_name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                </div>
                                <div class="clearfix"></div>
                                    
                                    <div class="content">
                                        <section id="one">
                                            <div class="text-block">                                    
                                                <div class="main-col-block">
                                                    <div class="col-sm-4 col-lg-4">
                                                        <div class="form-group">
                                                            <label class="control-label">{{translation('fees')}}<i class="red">*</i></label>
                                                            <div class="controls input-group-block">
                                                                <input type="hidden" name="real_fees_0" id="real_fees_0" value="{{isset($arr_data[0]['fees_id']) ? $arr_data[0]['fees_id'] : ''}}">
                                                                <select name="fees_0" class="form-control" id="fees_0" data-rule-required="true" onchange="changeFees('0')"
                                                                @if(isset($arr_data[0]['fees_id'])) disabled @endif
                                                                >
                                                                    <option value="">{{translation('select_fees')}}</option>
                                                                       @foreach($arr_fees as $fee)
                                                                        <option value="{{$fee['id']}}"
                                                                        @if(isset($arr_data[0]['fees_id']) && $arr_data[0]['fees_id'] == $fee['id'] )
                                                                            selected
                                                                        @endif
                                                                        >{{$fee['title']}}</option>
                                                                    @endforeach
                                                                </select>
                                                                <span for="fees_0" class="help-block"></span>
                                                            </div>
                                                        </div>                                          
                                                    </div>
                                                    <div class="col-sm-4 col-lg-4">
                                                        <div class="form-group">
                                                            <label class="control-label">{{translation('frequency')}}<i class="red">*</i></label>
                                                            <div class="controls input-group-block">
                                                                <select name="frequency_0" class="form-control" id="frequency_0" data-rule-required="true" onchange="changeFrequency('0')">
                                                                    <option value="">{{translation('select_frequency')}}</option>
                                                                    <option value="MONTHLY" 
                                                                        @if(isset($arr_data[0]['frequency']) && $arr_data[0]['frequency'] == 'MONTHLY')
                                                                            selected
                                                                        @endif
                                                                    >{{translation('monthly')}}</option>
                                                                    <option value="BIMONTHLY"
                                                                        @if(isset($arr_data[0]['frequency']) && $arr_data[0]['frequency'] == 'BIMONTHLY')
                                                                            selected
                                                                        @endif
                                                                    >{{translation('bimonthly')}}</option>
                                                                    <option value="QUARTERLY"
                                                                        @if(isset($arr_data[0]['frequency']) && $arr_data[0]['frequency'] == 'QUARTERLY')
                                                                            selected 
                                                                        @endif
                                                                    >{{translation('quarterly')}}</option>
                                                                    <option value="ANNUALLY"
                                                                        @if(isset($arr_data[0]['frequency']) && $arr_data[0]['frequency'] == 'ANNUALLY')
                                                                            selected 
                                                                        @endif
                                                                    >{{translation('annually')}}</option>

                                                                </select>
                                                            </div>
                                                        </div>                                          
                                                    </div>
                                                    <div class="col-sm-1 col-lg-1">
                                                        <div class="form-group">
                                                            <label class="control-label">{{translation('optional')}}</label>
                                                            <input type="hidden"  name="optional_0[]" value="No" >
                                                            <div class="check-box">
                                                                <input type="checkbox" class="filled-in case" name="optional_0[]" id="optional" value="yes" 
                                                                    @if(isset($arr_data[0]['is_optional']) && $arr_data[0]['is_optional'] == '1')
                                                                        checked
                                                                    @endif
                                                                />
                                                                <label for="optional"></label>
                                                            </div>
                                                            
                                                        </div>                                          
                                                    </div>
                                                    <div class="col-sm-3 col-lg-3">
                                                        <div class="form-group">
                                                            <label class="control-label">{{translation('amount')}}<i class="red">*</i></label>
                                                            <div class="controls input-group-block">
                                                                <input class="form-control input-group-block" name="amount_0" type="text" placeholder="{{translation('enter')}} {{translation('amount')}}" data-rule-required="true" data-rule-number="true" value="{{isset($arr_data[0]['amount']) ? $arr_data[0]['amount'] : 0}}" data-rule-min="0">
                                                                <button class="btn btn-success add-remove-btn" type="button" onclick="education_fields();"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> </button>
                                                            </div>
                                                        </div>                                          
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div id="education_fields">
                                                @foreach($arr_data as $key=>$value)
                                                    @if($key!=0)
                                                    <div class="form-group removeclass{{$key}}">
                                                    <div class="col-sm-4 col-lg-4">
                                                        <div class="form-group">
                                                            <div class="controls input-group-block">
                                                                <input type="hidden" name="real_fees_{{$key}}" id="real_fees_{{$key}}" value="{{isset($value['fees_id']) ? $value['fees_id'] : ''}}" >
                                                                <select name="fees_{{$key}}" id="fees_{{$key}}" class="form-control" data-rule-required="true" onchange="changeFees(\'{{$key}}\')" @if(isset($value['fees_id'])) disabled @endif> 
                                                                    <option value="">{{translation('select_fees')}}</option>
                                                                    @foreach($arr_fees as $fee)
                                                                        <option value="{{$fee['id']}} disabled " 
                                                                            @if(isset($value['fees_id']) && $value['fees_id'] == $fee['id'] )
                                                                                selected
                                                                            @endif
                                                                        >{{$fee['title']}}</option>
                                                                    @endforeach    
                                                                </select>
                                                                <span for="fees_{{$key}}" class="help-block"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 col-lg-4">
                                                        <div class="form-group">
                                                            <div class="controls input-group-block">
                                                                <select name="frequency_{{$key}}" id="frequency_{{$key}}" class="form-control" data-rule-required="true" onchange="changeFrequency(\'{{$key}}\')">
                                                                    <option value="">{{translation('select_frequency')}}</option>
                                                                    <option value="MONTHLY" 
                                                                        @if(isset($value['frequency']) && $value['frequency'] == 'MONTHLY')
                                                                            selected
                                                                        @endif
                                                                    >{{translation('monthly')}}</option>
                                                                    <option value="BIMONTHLY"
                                                                        @if(isset($value['frequency']) && $value['frequency'] == 'BIMONTHLY')
                                                                            selected
                                                                        @endif
                                                                    >{{translation('bimonthly')}}</option>
                                                                    <option value="QUARTERLY"
                                                                        @if(isset($value['frequency']) && $value['frequency'] == 'QUARTERLY')
                                                                            selected 
                                                                        @endif
                                                                    >{{translation('quarterly')}}</option>
                                                                    <option value="ANNUALLY"
                                                                        @if(isset($value['frequency']) && $value['frequency'] == 'ANNUALLY')
                                                                            selected 
                                                                        @endif
                                                                    >{{translation('annually')}}</option>
                                                                </select>
                                                                <span for="frequency_{{$key}}" class="help-block"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-1 col-lg-1">
                                                        <div class="form-group">
                                                            <div class="controls input-group-block">
                                                                <div class="check-box">
                                                                    <input type="hidden"  name="optional_{{$key}}[]" value="No" >
                                                                    <input type="checkbox" class="filled-in" value="yes" name="optional_{{$key}}[]" id="mult_changecheck_{{$key}}" @if(isset($value['is_optional']) && $value['is_optional'] == '1')
                                                                        checked
                                                                    @endif>
                                                                    <label for="mult_changecheck_{{$key}}"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-lg-3">
                                                        <div class="form-group">
                                                            <div class="controls input-group-block">

                                                                <input class="form-control" name="amount_{{$key}}" type="text" id="amount_{{$key}}" placeholder="{{translation('enter')}} {{translation('amount')}}" data-rule-required="true" data-rule-number="true" value="{{isset($value['amount']) ? $value['amount'] : 0}}" data-rule-min="0">
                                                                <span for="amount_{{$key}}" class="help-block"></span>
                                                                <button class="btn btn-danger remove-btn-block" type="button" onclick="remove_education_fields('{{$key}}');">  <span class="glyphicon glyphicon-minus" aria-hidden="true"></span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                                    @endif
                                                @endforeach 
                                                </div>
                                                <div class="clearfix"></div>
                                                <input type="hidden" name="count" id="count" value={{isset($arr_data) ? count($arr_data) : 0 }}>
                                            </div>
                                        </section>
                                                                                              
                                    </div>
                               
                                <div style="margin-top: 20px !important" class="form-group">                                    
                                <a href="{{ url($module_url_path) }}" class="btn btn-primary">{{translation('back')}}</a> 
                                    <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('update')}}</button>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                </div>
            
        
        <script>
        var count = {{count($arr_data)}};
        var fees_arr = new Array();

        @foreach($arr_data as $value)
            fees_arr.push("{{$value['fees_id']}}");
        @endforeach

            function education_fields() {
            
                for(var iterator=0 ; iterator<count ; iterator++)
                {

                    var value = $("#fees_"+iterator).val();
                    
                    if(value==''){

                        $('#fees_'+iterator).next('span').html("{{translation('this_field_is_required')}}");
                        return false;
                    }
                    else
                    {

                        $('#fees_'+iterator).next('span').html(" ");
                    }
                }
                var objTo = document.getElementById('education_fields')
                var divtest = document.createElement("div");
                divtest.setAttribute("class", "form-group removeclass"+count);
                var rdiv = 'removeclass'+count;
                var str  = '<div class="col-sm-4 col-lg-4"><div class="form-group"><div class="controls input-group-block"><input type="hidden" name="real_fees_'+count+'" id="real_fees_'+count+'"><select name="fees_'+count+'" id="fees_'+count+'" class="form-control" data-rule-required="true" onchange="changeFees(\''+count+'\')"><option value="">{{translation('select_fees')}}</option>';

                @foreach($arr_fees as $fee)      
                if(fees_arr.length>0 && jQuery.inArray( "{{$fee['id']}}", fees_arr ) == -1)
                {
                   str = str+'<option value="{{$fee['id']}}"';
                   str = str+'>{{$fee['title']}}</option>';
                } 
                   
                @endforeach
                str = str+'</select><span for="fees_'+count+'" class="help-block"></span></div></div></div><div class="col-sm-4 col-lg-4"><div class="form-group"><div class="controls input-group-block"><select name="frequency_'+count+'" id="frequency_'+count+'" class="form-control" data-rule-required="true" onchange="changeFrequency(\''+count+'\')"><option value="">{{translation('select_frequency')}}</option><option value="MONTHLY">{{translation('monthly')}}</option><option value="BIMONTHLY">{{translation('bimonthly')}}</option><option value="QUARTERLY">{{translation('quarterly')}}</option><option value="ANNUALLY">{{translation('annually')}}</option></select><span for="frequency_'+count+'" class="help-block"></span></div></div></div><div class="col-sm-1 col-lg-1"><div class="form-group"><div class="controls input-group-block"><div class="check-box"><input type="hidden"  name="optional_'+count+'[]" value="No" ><input type="checkbox" class="filled-in" value="yes" name="optional_'+count+'[]" id="mult_changecheck_'+count+'"><label for="mult_changecheck_'+count+'"></label></div></div></div></div><div class="col-sm-3 col-lg-3"><div class="form-group"><div class="controls input-group-block"><input class="form-control" name="amount_'+count+'" type="text" id="amount_'+count+'" placeholder="{{translation('enter')}} {{translation('amount')}}" data-rule-required="true" data-rule-number="true" data-rule-min="0"><span for="amount_'+count+'" class="help-block"></span><button class="btn btn-danger remove-btn-block" type="button" onclick="remove_education_fields('+ count +');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button></div></div></div>';
                divtest.innerHTML = str;                
                count++;
                $("#count").val(count);
                objTo.appendChild(divtest)
            }
           function remove_education_fields(rid) {
               var value = $("#fees_"+rid).val(); 
               var i = fees_arr.indexOf(value); 
               fees_arr.splice(i, 1);
               $('.removeclass'+rid).remove();

           }
           function changeFees(id)
           {
                $("#fees_"+id).attr('disabled','disabled');
                var value = $("#fees_"+id).val();
                $("#real_fees_"+id).val(value);
                fees_arr.push(value);
                for(var iterator=0 ; iterator<count ; iterator++)
                {
                    if(id!=iterator){
                        $('#fees_'+iterator+' option[value="'+value+'"]').attr('disabled','disabled');
                    }
                }
           }
        </script>
@endsection