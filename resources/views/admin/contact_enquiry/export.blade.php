<html>
<head>
    <link rel="stylesheet" href="{{url('/')}}/css/export.css">
</head>
<body>
    <header>
        {{config('app.project.header')}} 
    </header> 
    <footer>
        {{config('app.project.footer')}} 
    </footer>
    <main>
        <table align="center">
            <tr>
                <th>{{translation('sr_no')}}</th>
                <th>{{translation('contact_enquiry')}}</th>
                <th>{{translation('subject')}}</th>
                <th>{{translation('school_name')}}</th>
                <th>{{translation('email')}}</th>
                <th>{{translation('phone')}}</th>
                <th>{{translation('enquiry_number')}}</th>
                <th>{{translation('description')}}</th>
            </tr>
            <?php $count=1; ?>
            @foreach($arr_data as $key=> $contact_enquiry)
            <tr>
                <td>{{$count++}}</td>
                <td>                    {{isset($contact_enquiry['enquiry_category']['title'])?$contact_enquiry['enquiry_category']['title']:'' }}</td>
                <td> {{ isset($contact_enquiry['subject'])?$contact_enquiry['subject']:'' }} </td>  
                <td> {{ isset($contact_enquiry['get_school_admin']['school_admin']['school_id'])?get_school_name($contact_enquiry['get_school_admin']['school_admin']['school_id']):'' }} </td> 
                <td> {{ isset($contact_enquiry['email'])?$contact_enquiry['email']:'' }} </td> 
                <td> {{ isset($contact_enquiry['contact_number'])?$contact_enquiry['contact_number']:'' }} </td> 
                <td> {{ isset($contact_enquiry['enquiry_no'])?$contact_enquiry['enquiry_no']:'' }} </td>
                <td> {{ isset($contact_enquiry['description'])?str_limit($contact_enquiry['description'],125):'' }} </td>
            </tr>
            @endforeach
        </table>
    </main>
</body>
</html>
