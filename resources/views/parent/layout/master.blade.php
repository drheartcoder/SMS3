<!-- HEader -->        
@include('parent.layout._header')    
        
<!-- BEGIN Sidebar -->
@include('parent.layout._sidebar')
<!-- END Sidebaparentr -->

<!-- BEGIN Content -->
<div id="main-content">
    @yield('main_content')
<!-- END Main Content -->

<!-- Footer -->        
@include('parent.layout._footer')    
