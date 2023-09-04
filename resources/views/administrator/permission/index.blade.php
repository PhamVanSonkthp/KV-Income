@extends('administrator.layouts.master')

@section('name')
    <h4 class="page-title">{{ $page }}</h4>
@endsection

@section('css')

@endsection

@section('content')

    @can('permissions-list')
    <div class="content-main">
        <div class="content-main__header bg-primary">
            <label for="time" class="fw-bold">Created time</label>
            <div class="action__form-search">
                <div class="form__search">
                    <input type="text" name="daterange" class="form-control" id="time" autocomplete="off">
                </div>
                <div class="action__form">
                    <button class="custom_button btn__create">Create</button>
                    <button class="custom_button btn__filter">Filter</button>
                    <button class="custom_button btn__reset">Reset</button>
                </div>
            </div>
        </div>
        <div class="content-main__body">
            <div class="total__show">
                <div class="pagination-container">
                    <nav>
                        <ul class="pagination">
                            <li class="page-item">
                                <a class="page-link" href="">
                                    <i class="fa-solid fa-chevron-left text-secondary"></i>
                                </a>
                            </li>
                            <li class="page-item">
                                <input type="text" value="1" name="paged" class="form-control input__field text-center">
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="">
                                    <i class="fa-solid fa-chevron-right text-secondary"></i>
                                </a>
                            </li>
                            <span class="page-item total__value">of {{ count($items) }}</span>
                        </ul>
                    </nav>
                </div>
                <div class="number__show">
                    <select class="form-control" name="show">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="70">70</option>
                        <option value="100">100</option>
                    </select>
                    <span class="icon__down">
                            <i class="fa-solid fa-chevron-down text-secondary"></i>
                        </span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead style="background-color: #ADB1B9;">
                        <th scope="col" width="3%">
                            <label class="control control--checkbox">
                                <input type="checkbox" class="js-check-all">
                                <div class="control__indicator"></div>
                            </label>
                        </th>
                        <th scope="col" width="7%">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Created time</th>
                        <th scope="col" width="45%">Role</th>
                    </thead>
                    <tbody>
                    @php
                        $i = ($items->perPage() * $items->currentPage() - ($items->perPage() - 1));
                    @endphp
                    @foreach($items as $item)
                        <tr id="{{ $item->id }}">
                            <th scope="row">
                                <label class="control control--checkbox">
                                    <input type="checkbox" value="{{ $item->id }}">
                                    <div class="control__indicator"></div>
                                </label>
                            </th>
                            <td>{{ $i++ }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ \App\Models\Helper::convert_date_from_db($item->created_at) }}</td>
                            <td>123</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="total__show footer__table">
                <div class="pagination-container">
                    <nav>
                        <ul class="pagination">
                            <li class="page-item">
                                <a class="page-link" href="">
                                    <i class="fa-solid fa-chevron-left text-secondary"></i>
                                </a>
                            </li>
                            <li class="page-item">
                                <input type="text" value="1" name="paged" class="form-control input__field text-center">
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="">
                                    <i class="fa-solid fa-chevron-right text-secondary"></i>
                                </a>
                            </li>
                            <span class="page-item total__value">of {{ count($items) }}</span>
                        </ul>
                    </nav>

                </div>
                <div class="number__show">
                    <select class="form-control" name="show">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="70">70</option>
                        <option value="100">100</option>
                    </select>
                    <span class="icon__down">
                        <i class="fa-solid fa-chevron-down text-secondary"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endcan

@endsection

@section('js')

@endsection
