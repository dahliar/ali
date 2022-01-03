@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')

@if (Auth::check())
<div class="container-fluid">
    <div class="row">
        <ol class="primary-color">
            <li>DONE. Update jumlah stock langsung setelah penambahan detail transaksi</li>
            <li>DONE. Form delete detail transaksi dan mengembalikan jumlah stock</li>
            <li>Status transaksi</li>
            <ol class="primary-color">
                <li>DONE. jika Status transaksi finished atau canceled, button-button action detail transaksi tidak muncul</li>
                <li>DONE. jika Status transaksi finished atau canceled, menu edit transaksi tetap ada, namun select option status disabled, button save disabled, button reset disabled</li>
                <li>DONE. Status transaksi dari On-Progress -> Finished : ubah status transaksi saja</li>
                <li>DONE. Status transaksi dari On-Progress -> Canceled : ubah status transaksi, kembalikan stock</li>
            </ol>
            <li>DONE. print Proforma Invoice -> dari status transaksi 1</li>
            <li>DONE. print Transaksi -> dari status transaksi 2</li>
            <li>Transaction Number
                <ol>
                    <li>DONE. No PI Digenerate pada saat awal membuat transaction</li>
                    <li>DONE. No Transaksi Digenerate pada saat perpindahaan status dari on Progres ke Finished</li>
                </ol>
            </li>
            <li>Detail Transaction 
                <ol>
                    <li>DONE. Penambahan detail. Update payment di table transaksi. Payment =payment+(mengalikan amount*hargaperKg*weight base)</li>
                    <li>DONE. Penghapusan detail. Update payment di table transaksi. Payment =payment-(mengalikan amount*hargaperKg*weight base)</li>
                </ol>
            </li>
            <li>CRUD Species
                <ol>
                    <li>NONEED. List Species</li>
                    <li>NONEED. Tambah Species</li>
                    <li>NONEED. Edit Species</li>
                </ol>
            </li>
            <li>CRUD Items
                <ol>
                    <li>DONE. List Items</li>
                    <li>DONE. Tambah Items</li>
                    <li>DONE. Edit Items</li>
                </ol>
            </li>
            <li>CRUD Size
                <ol>
                    <li>DONE. List Size</li>
                    <li>DONE. Tambah Size</li>
                    <li>DONE. Edit Size</li>
                </ol>
            </li>
            <li>
                Kelola hak akses
                <ol>
                    <li>DONE. Marketing : Transactions</li>
                    <li>DONE. Store : Stock Barang, Master Data</li>
                    <li>DONE. Admin : ALl</li>
                </ol>
            </li>
            <li>DONE. Packed Unpacked</li>
            <li>Struktur Organisasi
                <ol>
                    <li>DONE. Work Position</li>
                    <li>DONE. Structural Position</li>
                    <li>DONE. Org Structure</li>
                </ol>
            </li>
            <li></li>
            <li>INI ADA DI /users/dahliar/DocumentRoot</li>

        </ol>
    </div>
</div>
@else
@include('partial.noAccess')
@endif

@endsection