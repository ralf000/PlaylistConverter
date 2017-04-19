<div class="content-box-large">
    <div class="panel-heading">
        <div class="panel-title">{{ $title }}</div>
    </div>

    @include('admin.include.errors&messages')

    @include('admin.include.buttons&forms')

    <div class="panel-body">
        <div class="col-md-12">
            @if(!empty($channels) && is_array($channels))
                <form class="form-horizontal" role="form" action="{{ route('channels-update') }}" method="post"
                      id="channels-form">
                    {{ csrf_field() }}
                    <div class="sortable sortable-groups">
                        @foreach ($groups as $group)
                            @include('admin.include.group')
                        @endforeach
                    </div>
                </form>

            @else
                <p>Каналы пока не добавлены</p>
            @endif
        </div>
    </div>
</div>

{{--modals--}}
@include('admin.include.add-channel-modal')
@include('admin.include.add-group-modal')
{{--/modals--}}
<script src="{{ asset('/assets/js/admin/groups.js') }}"></script>
<script src="{{ asset('/assets/js/admin/channels.js') }}"></script>
