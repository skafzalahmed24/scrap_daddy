<?php
$content = file_get_contents('resources/views/category.blade.php');
$content = preg_replace('/<!DOCTYPE html>.*?<\/nav>/s', "@extends('layouts.app')\n\n@section('title', \$category->title . ' - Scrap Daddy')\n\n@push('styles')", $content);
$content = str_replace('    </style>', "    </style>\n@endpush\n\n@section('content')", $content);
$content = preg_replace('/\s*<!-- Footer -->.*?<\/script>/s', '', $content);
$content = preg_replace('/<script>\s*const isLoggedIn = @json\(Auth::check\(\)\);/s', "@push('scripts')\n    <script>\n        const isLoggedIn = @json(Auth::check());", $content);
$content = preg_replace('/<\/body>\s*<\/html>/s', "@endpush\n@endsection\n", $content);
file_put_contents('resources/views/category.blade.php', $content);
