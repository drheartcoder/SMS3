<!-- HEader -->        
@include('schooladmin.layout._header')    
        
<!-- BEGIN Sidebar -->
@include('schooladmin.layout._sidebar')
<!-- END Sidebar -->
{{\Session::set('id',1)}}
<!-- BEGIN Content -->
<div id="main-content">
    @yield('main_content')
    
    <!-- END Main Content -->

<!-- Footer -->        
@include('schooladmin.layout._footer')    
