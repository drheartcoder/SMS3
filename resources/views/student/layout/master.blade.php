<!-- HEader -->        
@include('student.layout._header')    
        
<!-- BEGIN Sidebar -->
@include('student.layout._sidebar')
<!-- END Sidebar -->

<!-- BEGIN Content -->
<div id="main-content">
    @yield('main_content')

    <!-- END Main Content -->

<!-- Footer -->        
@include('student.layout._footer')    
