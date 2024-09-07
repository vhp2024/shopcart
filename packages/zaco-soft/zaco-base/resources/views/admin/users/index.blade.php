@extends('zaco-base::layout.template')

@section('content')
    <!-- Advanced Search -->
    <section id="advanced-search-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Advanced Search</h4>
                    </div>
                    <!--Search Form -->
                    <div class="card-body mt-2">
                        <form class="dt_adv_search" method="POST">
                            <div class="row g-1 mb-md-1">
                                <div class="col-md-4">
                                    <label class="form-label">Name:</label>
                                    <input type="text" id="input-name" class="form-control" placeholder="Zaco soft"/>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Email or Username:</label>
                                    <input type="text" id="input-id" class="form-control" placeholder="demo@zacosoft.com" />
                                </div>
                            </div>

                            <button class="btn btn-primary btn-search" type="button">Search</button>
                        </form>
                    </div>
                    <hr class="my-0" />
                    <div class="card-datatable">
                        <table class="dt-advanced-search table base-admin-user">
                            <thead>
                                <tr>
                                    <th>{{__('auth.name')}}</th>
                                    <th>{{__('auth.email')}}</th>
                                    <th>{{__('auth.username')}}</th>
                                    <th></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ Advanced Search -->
@endsection

@push('script')
<script type="text/javascript">
    var eleName = $('#input-name');
    var eleId = $('#input-id');
    $(function () {
        var table = $('.base-admin-user').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            searching: false,
            lengthMenu: [25, 50, 75, 100],
            ajax: {
                url: "{{ route('User::getList') }}",
                dataType: "json",
                data: function (d) {
                    var filter = {};
                    var nameVal = eleName.val().trim();
                    if(nameVal !== '') filter['name'] = nameVal;

                    var idVal = eleId.val().trim();
                    if(idVal !== '') filter['id'] = idVal;
                    if(Object.keys(filter).length > 0) d['filter'] = filter;
                    return d;
                }
            },
            columnDefs: [{
                "defaultContent": "",
                "targets": "_all"
            }],
            columns: [
                {
                    data: 'full_name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'username',
                    name: 'username'
                },
                {
                    data: null,
                    render: function (data, type, row) {

                        return '<a href="'+data+'">Download</a>';
                    }
                },
            ]
        });


        $('.btn-search').click(function(e){
            e.preventDefault;
            table.ajax.reload();
        })
    });
</script>
@endpush
