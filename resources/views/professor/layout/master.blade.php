<!-- HEader -->        
@include('professor.layout._header')    
        
<!-- BEGIN Sidebar -->
@include('professor.layout._sidebar')
<!-- END Sidebar -->

<!-- BEGIN Content -->
<div id="main-content">
    @yield('main_content')

    <!-- END Main Content -->

<!-- Footer -->        
@include('professor.layout._footer')    
