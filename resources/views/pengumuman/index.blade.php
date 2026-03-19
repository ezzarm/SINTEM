{{-- resources/views/pengumuman/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Pengumuman – SINTEM')

@section('header', 'Pengumuman')
@section('subheader', 'Pengumuman terbaru, laporan aduan, dan unggahan penemuan')

@section('topbar')
<div style="display:flex; align-items:center; justify-content:space-between; padding: 16px 32px; border-bottom: 1px solid #f0f0f5; background:#ffffff;">
    <p style="font: size 20px;; font-weight:700; color:#1a1a2e;">
        Selamat datang, {{ Auth::user()->name ?? 'Pengguna' }}! 
    </p>
    <a href="{{ route('laporan.buat') }}"
       style="display:inline-flex; align-items:center; gap:7px; padding: 9px 18px; background: linear-gradient(135deg, #9025FB, #4617D3); color:#fff; font-size:13px; font-weight:700; border-radius:8px; text-decoration:none; transition: opacity 0.18s, transform 0.18s; box-shadow: 0 4px 14px rgba(109,40,217,0.25);"
       onmouseover="this.style.opacity='0.88';this.style.transform='translateY(-1px)'"
       onmouseout="this.style.opacity='1';this.style.transform='translateY(0)'">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                  d="M11 4H6a2 2 0 00-2 2v13a2 2 0 002 2h11a2 2 0 002-2v-5"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                  d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        Laporkan
    </a>
</div>
@endsection

@section('content')
    {{-- Content goes here --}}
@endsection