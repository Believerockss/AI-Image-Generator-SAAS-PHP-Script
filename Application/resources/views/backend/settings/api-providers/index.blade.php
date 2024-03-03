@extends('backend.layouts.grid')
@section('section', admin_lang('Settings'))
@section('title', admin_lang('API Providers'))
@section('content')
    <div class="card">
        <table class="table ask-datatable w-100">
            <thead>
                <tr>
                    <th class="tb-w-1x">{{ admin_lang('#') }}</th>
                    <th class="tb-w-3x">{{ admin_lang('Logo') }}</th>
                    <th class="tb-w-3x">{{ admin_lang('name') }}</th>
                    <th class="tb-w-3x">{{ admin_lang('API Max Images') }}</th>
                    <th class="tb-w-7x">{{ admin_lang('Status') }}</th>
                    <th class="tb-w-7x">{{ admin_lang('Last Update') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($apiProviders as $apiProvider)
                    <tr class="item">
                        <td>{{ $apiProvider->id }}</td>
                        <td>
                            <a href="{{ route('admin.settings.api-providers.edit', $apiProvider->id) }}">
                                <img src="{{ asset($apiProvider->logo) }}" height="40px" width="40px">
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('admin.settings.api-providers.edit', $apiProvider->id) }}" class="text-dark">
                                {{ $apiProvider->name }}
                                @if ($apiProvider->isDefault())
                                    <span class="badge bg-info ms-1">{{ admin_lang('Default') }}</span>
                                @endif
                            </a>
                        </td>
                        <td>{{ $apiProvider->max }}</td>
                        <td>
                            @if ($apiProvider->status)
                                <span class="badge bg-success">{{ admin_lang('Enabled') }}</span>
                            @else
                                <span class="badge bg-danger">{{ admin_lang('Disabled') }}</span>
                            @endif
                        </td>
                        <td>{{ dateFormat($apiProvider->updated_at) }}</td>
                        <td>
                            <div class="text-end">
                                <button type="button" class="btn btn-sm rounded-3" data-bs-toggle="dropdown"
                                    aria-expanded="true">
                                    <i class="fa fa-ellipsis-v fa-sm text-muted"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-sm-end" data-popper-placement="bottom-end">
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('admin.settings.api-providers.edit', $apiProvider->id) }}"><i
                                                class="fa fa-edit me-2"></i>{{ admin_lang('Edit') }}</a>
                                    </li>
                                    @if (!$apiProvider->isDefault())
                                        <li>
                                            <form
                                                action="{{ route('admin.settings.api-providers.default', $apiProvider->id) }}"
                                                method="POST">
                                                @csrf
                                                <button class="vironeer-form-confirm dropdown-item">
                                                    <i class="fas fa-bookmark me-2"></i>
                                                    {{ admin_lang('Set as default') }}
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
