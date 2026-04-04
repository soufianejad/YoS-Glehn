@extends('layouts.dashboard')

@section('title', __('Statistiques'))
@section('header', __('Statistiques détaillées'))

@push('styles')
<style>
    .stat-kpi-card {
        border-radius: 0.5rem;
        border: 0;
        border-left: 4px solid;
        transition: box-shadow 0.2s ease;
    }
    .stat-kpi-card:hover {
        box-shadow: 0 0.35rem 0.85rem rgba(0, 0, 0, 0.08) !important;
    }
    .chart-wrap {
        position: relative;
        height: 300px;
        width: 100%;
    }
    @media (min-width: 992px) {
        .chart-wrap.chart-tall {
            height: 340px;
        }
    }
</style>
@endpush
