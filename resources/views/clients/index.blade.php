@extends('layouts.app')

@section('title', 'ลูกค้า | AgencySuit')

@section('content')
    @if ($clients->isEmpty())
        <div class="as-page-head">
            <div>
                <h1 class="as-page-title">ลูกค้า</h1>
                <p class="as-page-subtitle">ความต้องการที่ควรจำไว้ให้ทัน</p>
            </div>
        </div>
        <div class="as-surface overflow-hidden">
            <x-empty-state title="ยังไม่มีลูกค้าในระบบ" description="เพิ่มลูกค้ารายแรก เพื่อเริ่มดูทรัพย์ที่ตรงความต้องการและตั้งงานติดตาม" action="เพิ่มลูกค้า" :action-url="route('clients.create')" />
        </div>
    @else
        <div class="as-page-head">
            <div>
                <h1 class="as-page-title">ลูกค้าของคุณ</h1>
                <p class="as-page-subtitle">{{ $clients->count() }} คนที่กำลังติดตาม</p>
            </div>
            <a href="{{ route('clients.create') }}" class="as-inline-action"><x-icon name="plus" size="18" />เพิ่มลูกค้า</a>
        </div>

        <ul class="as-list-surface">
            @foreach ($clients as $client)
                <li>
                    <a href="{{ route('clients.show', $client) }}" class="as-list-row">
                        <span class="as-icon-box mt-0.5"><x-icon name="users" size="20" /></span>
                        <span class="as-list-copy">
                            <span class="as-list-title-line">
                                <span class="as-list-title">{{ $client->name }}</span>
                                <span class="as-status">{{ $client->transactionLabel() }}</span>
                            </span>
                            <span class="as-list-meta"><strong>งบ {{ $client->formattedBudget() }}</strong></span>
                            <span class="as-list-meta"><x-icon name="map-pin" size="14" class="mr-1 inline" />{{ $client->locations }}</span>
                        </span>
                        <x-icon name="chevron-right" size="19" class="as-row-chevron" />
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
